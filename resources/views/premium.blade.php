@extends('layouts.app')

@section('title', 'Premium — DoesValterHaveIt?')
@section('og_title', 'DoesValterHaveIt? Premium')
@section('og_description', 'Unlock next-level Valter surveillance with DoesValterHaveIt? Premium.')

@section('nav')
    <a href="/" class="nav-link">Back</a>
    <a href="/api/docs" class="nav-link">API</a>
@endsection

@section('styles')
        h1 {
            font-size: 2.4rem;
            font-weight: bold;
            color: #66c0f4;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .tagline {
            font-size: 1rem;
            color: #8cb4d0;
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .tiers {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            width: 820px;
            max-width: 100%;
        }

        .tier {
            flex: 1;
            background: #2a475e;
            border: 1px solid #4891b5;
            border-radius: 8px;
            padding: 1.75rem 1.5rem;
            position: relative;
        }

        .tier.premium {
            border-color: #f4a020;
            background: #2a3b1e;
        }

        .tier-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: bold;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.2rem 0.55rem;
            border-radius: 3px;
            margin-bottom: 0.75rem;
            background: #1e3d56;
            color: #66c0f4;
        }

        .tier.premium .tier-badge {
            background: #5c3d00;
            color: #f4a020;
        }

        .tier-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #c6d4df;
            margin-bottom: 0.35rem;
        }

        .tier.premium .tier-name {
            color: #f4a020;
        }

        .tier-price {
            font-size: 0.95rem;
            color: #8cb4d0;
            margin-bottom: 1.5rem;
        }

        .tier.premium .tier-price {
            color: #a8c87a;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            margin: 0;
            padding: 0;
        }

        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.95rem;
            color: #c6d4df;
            line-height: 1.4;
        }

        .feature-icon {
            flex-shrink: 0;
            margin-top: 0.05rem;
            font-size: 1rem;
        }

        .divider {
            width: 100%;
            border: none;
            border-top: 1px solid #3d6b8a;
            margin: 1.25rem 0;
        }

        .tier.premium .divider {
            border-color: #5c4a1e;
        }

        .ratelimit-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9rem;
            color: #8cb4d0;
        }

        .tier.premium .ratelimit-row {
            color: #a8c87a;
        }

        .premium-cta {
            display: block;
            margin-top: 1.75rem;
            width: 100%;
            padding: 0.85rem;
            font-size: 1rem;
            font-family: Arial, sans-serif;
            font-weight: bold;
            background: #f4a020;
            color: #1b2838;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background 0.15s;
        }

        .premium-cta:hover {
            background: #e8941a;
        }

        .disclaimer {
            font-size: 0.72rem;
            color: #4a6174;
            text-align: center;
            margin-top: 0.6rem;
        }

        @media (max-width: 640px) {
            .tiers { flex-direction: column; }
        }
@endsection

@section('content')
    <h1>DoesValterHaveIt? Premium</h1>
    <p class="tagline">Next-level Valter surveillance, for those who truly need to know.</p>

    <div class="tiers">

        {{-- Free Tier --}}
        <div class="tier">
            <span class="tier-badge">Current</span>
            <div class="tier-name">Basic</div>
            <div class="tier-price">Always free</div>

            <hr class="divider">

            <ul class="feature-list">
                <li>
                    <span class="feature-icon">🎮</span>
                    <span>Look up whether Valter owns any game on Steam</span>
                </li>
                <li>
                    <span class="feature-icon">🕐</span>
                    <span>View Valter's 5 most recently played games</span>
                </li>
                <li>
                    <span class="feature-icon">🔗</span>
                    <span>Full REST API access for game ownership checks</span>
                </li>
            </ul>

            <hr class="divider">

            <div class="ratelimit-row">
                <span>⚡</span>
                <span><strong>30</strong> API requests per minute</span>
            </div>
        </div>

        {{-- Premium Tier --}}
        <div class="tier premium">
            <span class="tier-badge">✦ Premium</span>
            <div class="tier-name">Premium</div>
            <div class="tier-price">$79.99 / month</div>

            <hr class="divider">

            <ul class="feature-list">
                <li>
                    <span class="feature-icon">🎮</span>
                    <span>Everything in Basic</span>
                </li>
                <li>
                    <span class="feature-icon">😤</span>
                    <span><strong>Valter Mood Predictor™</strong> Analyzes his recent playtime patterns to estimate his current emotional state (Bored, Sweaty, On a Grind, Existential Crisis, etc.)</span>
                </li>
                <li>
                    <span class="feature-icon">😴</span>
                    <span><strong>Valter Sleep Tracker</strong> Reverse-engineers Valter's sleep schedule from Steam login timestamps, accurate to ±40 minutes</span>
                </li>
                <li>
                    <span class="feature-icon">🏆</span>
                    <span><strong>Am I Better Than Valter?</strong> Submit your own Steam profile and receive a completely objective, scientifically rigorous verdict on whose library is superior (spoiler: it's Valter)</span>
                </li>
                <li>
                    <span class="feature-icon">🔔</span>
                    <span><strong>Valter Watch™ Live Alerts</strong> Real-time push notifications the very instant Valter launches any game, so you can immediately know what he's doing instead of working</span>
                </li>
            </ul>

            <hr class="divider">

            <div class="ratelimit-row">
                <span>⚡</span>
                <span><strong>300</strong> API requests per minute</span>
            </div>

            <a href="#" class="premium-cta">Upgrade to Premium</a>
            {{-- <p class="disclaimer">This is a joke page. No payment will be processed. Valter has not consented to any of this.</p> --}}
        </div>

    </div>
@endsection
