@extends('layouts.app')

@section('title', isset($gameName) ? $gameName . ' — DoesValterHaveIt?' : 'DoesValterHaveIt?')

@section('og_title', isset($gameName) ? 'Does Valter have ' . $gameName . '?' : 'DoesValterHaveIt?')
@section('og_description', isset($result)
    ? (($result ? 'YES' : 'NO') . '! Valter ' . ($result ? 'owns' : 'does not own') . ' ' . $gameName . ' on Steam.')
    : 'Check whether Valter owns a game on Steam.')

@if(isset($result, $gameName))
@section('og_image', isset($tinyImage) && $tinyImage
    ? $tinyImage
    : url('/og-image?' . http_build_query(['title' => $gameName, 'owned' => $result ? '1' : '0'])))
@endif

@section('nav')
    <a href="/recent" class="nav-link">Recent</a>
@endsection

@section('styles')
        h1 {
            font-size: 2.8rem;
            font-weight: bold;
            color: #66c0f4;
            margin-bottom: 2.5rem;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .form-wrapper {
            width: 500px;
            max-width: 100%;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.85rem 1rem;
            font-size: 1.1rem;
            font-family: Arial, sans-serif;
            background: #2a475e;
            border: 1px solid #4891b5;
            color: #c6d4df;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.15s;
        }

        input[type="text"]:focus {
            border-color: #66c0f4;
        }

        input[type="text"]::placeholder {
            color: #7097af;
        }

        .autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #1e3448;
            border: 1px solid #4891b5;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 280px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .autocomplete-item {
            padding: 0.65rem 1rem;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .autocomplete-badge {
            font-size: 0.65rem;
            font-weight: bold;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.1rem 0.4rem;
            border-radius: 3px;
            flex-shrink: 0;
        }
        .autocomplete-badge.dlc  { background: #4a3060; color: #c09fdf; }
        .autocomplete-badge.mod  { background: #1e3d56; color: #66c0f4; }
        .autocomplete-badge.music { background: #1e3d26; color: #5ba32b; }

        .autocomplete-item:hover,
        .autocomplete-item.active {
            background: #2a4f6e;
            color: #66c0f4;
        }

        button[type="submit"] {
            display: block;
            margin-top: 0.75rem;
            width: 100%;
            padding: 0.85rem;
            font-size: 1.1rem;
            font-family: Arial, sans-serif;
            font-weight: bold;
            background: #5c7e10;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
        }

        button[type="submit"]:hover {
            background: #739114;
        }

        .result-section {
            margin-top: 3rem;
            text-align: center;
        }

        .result {
            font-size: 9rem;
            font-weight: bold;
            font-family: Arial, sans-serif;
            line-height: 1;
        }

        .result.yes { color: #5ba32b; }
        .result.no  { color: #c94728; }

        .game-label {
            margin-top: 0.75rem;
            font-size: 1.3rem;
            color: #8cb4d0;
            font-family: Arial, sans-serif;
        }
@endsection

@section('content')
    <h1>DoesValterHaveIt?</h1>

    <form action="/" method="POST" id="game-form" class="form-wrapper">
        @csrf
        <div class="search-wrapper">
            <input
                type="text"
                id="game-search"
                name="game_name"
                placeholder="Search for a game..."
                autocomplete="off"
                value="{{ isset($gameName) ? e($gameName) : '' }}"
            />
            <input type="hidden" id="app-id" name="app_id" />
            <input type="hidden" id="tiny-image" name="tiny_image" />
            <div class="autocomplete-list" id="autocomplete-list"></div>
        </div>
        <button type="submit">Does Valter have it?</button>
    </form>

    @isset($result)
        <div class="result-section">
            <div class="result {{ $result ? 'yes' : 'no' }}">
                {{ $result ? 'YES' : 'NO' }}
            </div>
            <div class="game-label">{{ $gameName }}</div>
        </div>
    @endisset

    <script>
        const input    = document.getElementById('game-search');
        const list     = document.getElementById('autocomplete-list');
        const appIdInput = document.getElementById('app-id');

        let debounceTimer = null;
        let activeIndex   = -1;
        let suggestions   = [];
        const queryCache  = new Map();

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            appIdInput.value = '';
            const q = this.value.trim();
            if (q.length < 2) { hideList(); return; }
            if (queryCache.has(q)) {
                suggestions = queryCache.get(q);
                renderList();
                return;
            }
            debounceTimer = setTimeout(() => fetchSuggestions(q), 150);
        });

        async function fetchSuggestions(q) {
            try {
                const res = await fetch('/autocomplete?q=' + encodeURIComponent(q));
                suggestions = await res.json();
                queryCache.set(q, suggestions);
                renderList();
            } catch (_) {
                hideList();
            }
        }

        function renderList() {
            list.innerHTML = '';
            activeIndex = -1;
            if (!suggestions.length) { hideList(); return; }
            suggestions.forEach((item, i) => {
                const div = document.createElement('div');
                div.className = 'autocomplete-item';

                const name = document.createElement('span');
                name.textContent = item.name;
                div.appendChild(name);

                if (item.type && item.type !== 'app' && item.type !== 'game') {
                    const badge = document.createElement('span');
                    badge.className = 'autocomplete-badge ' + item.type;
                    badge.textContent = item.type;
                    div.appendChild(badge);
                }

                div.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    selectItem(i);
                });
                list.appendChild(div);
            });
            list.style.display = 'block';
        }

        function selectItem(i) {
            input.value      = suggestions[i].name;
            appIdInput.value = suggestions[i].id;
            document.getElementById('tiny-image').value = suggestions[i].tiny_image || '';
            hideList();
        }

        function hideList() {
            list.style.display = 'none';
            activeIndex = -1;
        }

        input.addEventListener('keydown', function (e) {
            const items = list.querySelectorAll('.autocomplete-item');
            if (!items.length || list.style.display === 'none') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActive(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActive(items);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                selectItem(activeIndex);
            } else if (e.key === 'Escape') {
                hideList();
            }
        });

        function updateActive(items) {
            items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-wrapper')) hideList();
        });

        document.getElementById('game-form').addEventListener('submit', function (e) {
            if (!appIdInput.value) {
                e.preventDefault();
                input.focus();
                input.style.borderColor = '#c94728';
                input.placeholder = 'Please select a game from the list';
                setTimeout(() => {
                    input.style.borderColor = '';
                    input.placeholder = 'Search for a game...';
                }, 2000);
            }
        });
    </script>
@endsection
