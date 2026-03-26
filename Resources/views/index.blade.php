@extends('flute::layouts.app')

@section('title', __('referral.title'))

@push('head')
    @at('Modules/Referral/Resources/assets/scss/referral.scss')
    @at('Modules/Referral/Resources/assets/js/referral.js')
@endpush

@push('content')
    <div class="referral-page">
        <header class="referral-hero">
            <div class="referral-hero__glow"></div>
            <div class="referral-hero__content">
                <div class="referral-hero__badge">
                    <x-icon path="ph.bold.gift-bold" />
                    <span>{{ __('referral.hero.badge') }}</span>
                </div>
                <h1 class="referral-hero__title">{{ __('referral.hero.title') }}</h1>
                <p class="referral-hero__subtitle">{{ __('referral.hero.subtitle') }}</p>
            </div>
        </header>

        <div class="container">
            <div class="referral-content">
                <div class="referral-main">
                    <section class="referral-link-section">
                        <div class="referral-link-card">
                            <div class="referral-link-card__header">
                                <x-icon path="ph.bold.link-bold" />
                                <h2>{{ __('referral.link.title') }}</h2>
                            </div>
                            <div class="referral-link-card__body">
                                <div class="referral-link-input">
                                    <input type="text" 
                                        id="referralLink" 
                                        value="{{ $stats['referral_link'] }}" 
                                        readonly>
                                    <button type="button" class="referral-link-copy" id="copyLinkBtn" data-link="{{ $stats['referral_link'] }}">
                                        <x-icon path="ph.bold.copy-bold" />
                                        <span>{{ __('referral.link.copy') }}</span>
                                    </button>
                                </div>
                                <div class="referral-link-code">
                                    <span class="referral-link-code__label">{{ __('referral.link.code') }}:</span>
                                    <code class="referral-link-code__value">{{ $stats['referral_code'] }}</code>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="referral-stats-section">
                        <h2 class="referral-section-title">{{ __('referral.stats.title') }}</h2>
                        <div class="referral-stats-grid">
                            <div class="referral-stat-card">
                                <div class="referral-stat-card__icon">
                                    <x-icon path="ph.bold.users-bold" />
                                </div>
                                <div class="referral-stat-card__content">
                                    <span class="referral-stat-card__value">{{ $stats['total_referrals'] }}</span>
                                    <span class="referral-stat-card__label">{{ __('referral.stats.total_referrals') }}</span>
                                </div>
                            </div>

                            <div class="referral-stat-card referral-stat-card--success">
                                <div class="referral-stat-card__icon">
                                    <x-icon path="ph.bold.check-circle-bold" />
                                </div>
                                <div class="referral-stat-card__content">
                                    <span class="referral-stat-card__value">{{ $stats['claimed_rewards'] }}</span>
                                    <span class="referral-stat-card__label">{{ __('referral.stats.claimed_rewards') }}</span>
                                </div>
                            </div>

                            <div class="referral-stat-card referral-stat-card--warning">
                                <div class="referral-stat-card__icon">
                                    <x-icon path="ph.bold.clock-bold" />
                                </div>
                                <div class="referral-stat-card__content">
                                    <span class="referral-stat-card__value">{{ $stats['pending_rewards'] }}</span>
                                    <span class="referral-stat-card__label">{{ __('referral.stats.pending_rewards') }}</span>
                                </div>
                            </div>

                            <div class="referral-stat-card referral-stat-card--accent">
                                <div class="referral-stat-card__icon">
                                    <x-icon path="ph.bold.coin-bold" />
                                </div>
                                <div class="referral-stat-card__content">
                                    <span class="referral-stat-card__value">{{ number_format($stats['total_earnings'], 2) }}</span>
                                    <span class="referral-stat-card__label">{{ __('referral.stats.total_earnings') }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    @if (!empty($stats['referrals']))
                        <section class="referral-list-section">
                            <h2 class="referral-section-title">{{ __('referral.list.title') }}</h2>
                            <div class="referral-list">
                                @foreach ($stats['referrals'] as $referral)
                                    <div class="referral-list-item">
                                        <div class="referral-list-item__user">
                                            <img src="{{ $referral->referred->avatar ?? '/assets/img/default-avatar.webp' }}" 
                                                alt="{{ $referral->referred->name }}" 
                                                class="referral-list-item__avatar">
                                            <div class="referral-list-item__info">
                                                <span class="referral-list-item__name">{{ $referral->referred->name }}</span>
                                                <span class="referral-list-item__date">{{ $referral->createdAt->format('d.m.Y H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="referral-list-item__status">
                                            @if ($referral->reward_claimed)
                                                <span class="referral-badge referral-badge--success">
                                                    <x-icon path="ph.bold.check-bold" />
                                                    {{ number_format($referral->reward_amount, 2) }} {{ config('lk.currency_view') }}
                                                </span>
                                            @else
                                                <span class="referral-badge referral-badge--pending">
                                                    <x-icon path="ph.bold.clock-bold" />
                                                    {{ __('referral.list.pending') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="referral-sidebar">
                    <div class="referral-info-card">
                        <div class="referral-info-card__header">
                            <x-icon path="ph.bold.info-bold" />
                            <h3>{{ __('referral.info.title') }}</h3>
                        </div>
                        <div class="referral-info-card__body">
                            <div class="referral-info-item">
                                <x-icon path="ph.bold.gift-bold" />
                                <div>
                                    <strong>{{ __('referral.info.referrer_reward') }}</strong>
                                    <span>{{ number_format($settings['referrer_reward'], 2) }} {{ config('lk.currency_view') }}</span>
                                </div>
                            </div>
                            <div class="referral-info-item">
                                <x-icon path="ph.bold.user-plus-bold" />
                                <div>
                                    <strong>{{ __('referral.info.referred_bonus') }}</strong>
                                    <span>{{ number_format($settings['referred_bonus'], 2) }} {{ config('lk.currency_view') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="referral-how-card">
                        <h3>{{ __('referral.how.title') }}</h3>
                        <ol class="referral-how-list">
                            <li>
                                <span class="referral-how-step">1</span>
                                <span>{{ __('referral.how.step1') }}</span>
                            </li>
                            <li>
                                <span class="referral-how-step">2</span>
                                <span>{{ __('referral.how.step2') }}</span>
                            </li>
                            <li>
                                <span class="referral-how-step">3</span>
                                <span>{{ __('referral.how.step3') }}</span>
                            </li>
                            <li>
                                <span class="referral-how-step">4</span>
                                <span>{{ __('referral.how.step4') }}</span>
                            </li>
                        </ol>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endpush
