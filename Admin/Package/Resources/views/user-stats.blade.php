@php
    $user = $stats['user'];
    $code = $stats['code'];
    $referrals = $stats['referrals'] ?? [];
    $conversionRate = $code->uses > 0 ? round(($stats['total_referrals'] / $code->uses) * 100, 1) : 0;
    $avgEarnings = $stats['total_referrals'] > 0 ? $stats['total_earnings'] / $stats['total_referrals'] : 0;
@endphp

<div class="referral-user-page">
    <div class="card referral-user-profile">
        <div class="card-body">
            <div class="referral-user-profile__main">
                <img src="{{ url($user->avatar ?? config('profile.default_avatar')) }}" alt="{{ $user->name }}" class="referral-user-profile__avatar">
                <div class="referral-user-profile__info">
                    <h2 class="referral-user-profile__name">{{ $user->name }}</h2>
                    <span class="referral-user-profile__email">{{ $user->email ?? $user->login }}</span>
                    <div class="referral-user-profile__badges">
                        <span class="badge">ID: {{ $user->id }}</span>
                        @if ($code->active)
                            <span class="badge success">{{ __('def.active') }}</span>
                        @else
                            <span class="badge error">{{ __('def.inactive') }}</span>
                        @endif
                    </div>
                </div>
                <div class="referral-user-profile__code-block">
                    <span class="referral-user-profile__code-label">{{ __('referral.admin.user.referral_code') }}</span>
                    <code class="referral-user-profile__code">{{ $code->code }}</code>
                </div>
            </div>
            
            <div class="referral-user-profile__link">
                <label>{{ __('referral.admin.user.referral_link') }}</label>
                <div class="referral-user-profile__link-box">
                    <input type="text" readonly value="{{ $code->getLink() }}" id="userReferralLink">
                    <button type="button" class="btn btn-primary btn-sm" data-copy="{{ $code->getLink() }}" data-tooltip="{{ __('referral.link.copy') }}">
                        <x-icon path="ph.regular.copy" />
                        {{ __('referral.link.copy') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="referral-user-page__stats">
        <div class="card referral-stat-card">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.users-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ $stats['total_referrals'] }}</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.total_referrals') }}</span>
                </div>
            </div>
        </div>

        <div class="card referral-stat-card referral-stat-card--success">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.check-circle-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ $stats['claimed_rewards'] }}</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.claimed_rewards') }}</span>
                </div>
            </div>
        </div>

        <div class="card referral-stat-card referral-stat-card--warning">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.hourglass-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ $stats['pending_rewards'] }}</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.pending_rewards') }}</span>
                </div>
            </div>
        </div>

        <div class="card referral-stat-card referral-stat-card--accent">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.coin-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ number_format($stats['total_earnings'], 2) }} {{ $currency }}</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.total_earnings') }}</span>
                </div>
            </div>
        </div>

        <div class="card referral-stat-card">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.cursor-click-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ $code->uses }}</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.code_uses') }}</span>
                </div>
            </div>
        </div>

        <div class="card referral-stat-card referral-stat-card--info">
            <div class="card-body">
                <div class="referral-stat-card__icon">
                    <x-icon path="ph.bold.percent-bold" />
                </div>
                <div class="referral-stat-card__content">
                    <span class="referral-stat-card__value">{{ $conversionRate }}%</span>
                    <span class="referral-stat-card__label">{{ __('referral.admin.user.conversion_rate') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('referral.admin.charts.referrals_over_time') }}</h5>
        </div>
        <div class="card-body">
            @if (!empty($referralsChart['labels']))
                @php
                    $chart = new \Flute\Core\Charts\FluteChart();
                    $chart->setType('area')
                        ->setHeight(260)
                        ->setDataset($referralsChart['series'] ?? [])
                        ->setLabels($referralsChart['labels'] ?? []);
                @endphp
                {!! $chart->container() !!}
                {!! $chart->script() !!}
            @else
                <div class="referral-empty-chart">
                    <x-icon path="ph.regular.chart-line" />
                    <p>{{ __('referral.admin.stats.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                {{ __('referral.admin.tabs.referrals') }}
                <span class="badge ms-2">{{ count($referrals) }}</span>
            </h5>
        </div>
        <div class="card-body withoutPadding">
            @if (count($referrals) > 0)
                <div class="referral-users-table">
                    <div class="referral-users-table__header">
                        <span>{{ __('referral.admin.fields.referred') }}</span>
                        <span>{{ __('referral.admin.fields.date') }}</span>
                        <span>{{ __('referral.admin.fields.status') }}</span>
                        <span>{{ __('referral.admin.fields.reward') }}</span>
                    </div>
                    @foreach ($referrals as $referral)
                        <div class="referral-users-table__row">
                            <div class="referral-users-table__user">
                                <img src="{{ url($referral->referred->avatar ?? config('profile.default_avatar')) }}" alt="{{ $referral->referred->name }}">
                                <div>
                                    <span class="referral-users-table__name">{{ $referral->referred->name }}</span>
                                    <span class="referral-users-table__id">#{{ $referral->referred->id }}</span>
                                </div>
                            </div>
                            <span class="referral-users-table__date">{{ \Carbon\Carbon::parse($referral->createdAt)->format('d.m.Y H:i') }}</span>
                            <span>
                                @if ($referral->reward_claimed)
                                    <span class="badge success">{{ __('referral.admin.status.claimed') }}</span>
                                @else
                                    <span class="badge warning">{{ __('referral.admin.status.pending') }}</span>
                                @endif
                            </span>
                            <span class="referral-users-table__reward @if($referral->reward_claimed) referral-users-table__reward--claimed @endif">
                                @if ($referral->reward_claimed)
                                    +{{ number_format($referral->reward_amount, 2) }} {{ $currency }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="referral-empty-list">
                    <x-icon path="ph.regular.users" />
                    <p>{{ __('referral.admin.stats.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
