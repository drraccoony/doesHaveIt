@extends('layouts.app')

@section('title', "Valter's Recent Games — DoesValterHaveIt?")
@section('og_title', "Valter's Recently Played Games")
@section('og_description', "See the 5 games Valter has played most recently on Steam.")

@section('nav')
    <a href="/" class="nav-link">Back</a>
    <a href="/api/docs" class="nav-link">API</a>
    <a href="/premium" class="nav-link nav-link--premium">✦ Premium</a>
@endsection

@section('styles')
        h1 {
            font-size: 2.4rem;
            font-weight: bold;
            color: #66c0f4;
            margin-bottom: 2rem;
            text-align: center;
        }

        .game-list {
            width: 560px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .game-card {
            background: #2a475e;
            border: 1px solid #4891b5;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .game-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: #c6d4df;
        }

        .game-hours {
            font-size: 0.95rem;
            color: #8cb4d0;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .no-games {
            color: #8cb4d0;
            font-size: 1.1rem;
            text-align: center;
        }
@endsection

@section('content')
    <h1>Valter's Recent Games</h1>

    @if (count($games))
        <div class="game-list">
            @foreach ($games as $game)
                @php
                    $hoursTotal  = round($game['playtime_forever'] / 60, 1);
                    $hoursRecent = round($game['playtime_2weeks'] / 60, 1);
                @endphp
                <div class="game-card">
                    <span class="game-name">{{ $game['name'] }}</span>
                    <span class="game-hours">
                        @if ($hoursTotal > 0)
                            {{ $hoursTotal }} hrs total
                            @if ($hoursRecent > 0)
                                &nbsp;·&nbsp; {{ $hoursRecent }} hrs recently
                            @endif
                        @else
                            No hours recorded
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <p class="no-games">No recently played games found.</p>
    @endif
@endsection
