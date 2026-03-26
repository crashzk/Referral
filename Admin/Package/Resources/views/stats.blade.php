@php
    $currency = config('lk.currency_view', __('def.currency_symbol'));
    $settings = app(\Flute\Modules\Referral\Services\ReferralService::class)->getSettings();
    $totalCodes = \Flute\Modules\Referral\database\Entities\ReferralCode::query()->count();
    $activeReferrers = $stats['active_referrers'] ?? count($stats['top_referrers'] ?? []);
@endphp

<div class="referral-stats-page">
    <div class="referral-stats-page__metrics">
        <div class="card">
            <div class="card-body">
                <div class="referral-metric">
                    <x-icon path="ph.bold.users-bold" />
                    <div class="referral-metric__content">
                        <span class="referral-metric__value">{{ $stats['total_referrals'] }}</span>
                        <span class="referral-metric__label">{{ __('referral.admin.stats.total_referrals') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="referral-metric referral-metric--success">
                    <x-icon path="ph.bold.coin-bold" />
                    <div class="referral-metric__content">
                        <span class="referral-metric__value">{{ number_format($stats['total_rewards_paid'], 2) }} {{ $currency }}</span>
                        <span class="referral-metric__label">{{ __('referral.admin.stats.total_rewards') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="referral-metric referral-metric--info">
                    <x-icon path="ph.bold.gift-bold" />
                    <div class="referral-metric__content">
                        <span class="referral-metric__value">{{ number_format($settings['referrer_reward'], 2) }} {{ $currency }}</span>
                        <span class="referral-metric__label">{{ __('referral.admin.stats.reward_per_referral') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="referral-metric referral-metric--accent">
                    <x-icon path="ph.bold.user-plus-bold" />
                    <div class="referral-metric__content">
                        <span class="referral-metric__value">{{ number_format($settings['referred_bonus'], 2) }} {{ $currency }}</span>
                        <span class="referral-metric__label">{{ __('referral.admin.stats.bonus_per_user') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="referral-stats-page__charts">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('referral.admin.charts.daily_referrals') }}</h5>
                <span class="card-subtitle">{{ __('referral.admin.charts.daily_referrals_desc') }}</span>
            </div>
            <div class="card-body">
                @if (!empty($dailyReferralsChart['labels']))
                    @php
                        $chart1 = new \Flute\Core\Charts\FluteChart();
                        $chart1->setType('area')
                            ->setHeight(280)
                            ->setDataset($dailyReferralsChart['series'] ?? [])
                            ->setLabels($dailyReferralsChart['labels'] ?? []);
                    @endphp
                    {!! $chart1->container() !!}
                    {!! $chart1->script() !!}
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
                <h5 class="card-title">{{ __('referral.admin.charts.daily_rewards') }}</h5>
                <span class="card-subtitle">{{ __('referral.admin.charts.daily_rewards_desc') }}</span>
            </div>
            <div class="card-body">
                @if (!empty($dailyRewardsChart['labels']))
                    @php
                        $chart2 = new \Flute\Core\Charts\FluteChart();
                        $chart2->setType('bar')
                            ->setHeight(280)
                            ->setDataset($dailyRewardsChart['series'] ?? [])
                            ->setLabels($dailyRewardsChart['labels'] ?? []);
                    @endphp
                    {!! $chart2->container() !!}
                    {!! $chart2->script() !!}
                @else
                    <div class="referral-empty-chart">
                        <x-icon path="ph.regular.chart-bar" />
                        <p>{{ __('referral.admin.stats.no_data') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="referral-stats-page__charts">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('referral.admin.charts.monthly_referrals') }}</h5>
                <span class="card-subtitle">{{ __('referral.admin.charts.monthly_referrals_desc') }}</span>
            </div>
            <div class="card-body">
                @if (!empty($monthlyReferralsChart['labels']))
                    @php
                        $chart3 = new \Flute\Core\Charts\FluteChart();
                        $chart3->setType('line')
                            ->setHeight(280)
                            ->setDataset($monthlyReferralsChart['series'] ?? [])
                            ->setLabels($monthlyReferralsChart['labels'] ?? []);
                    @endphp
                    {!! $chart3->container() !!}
                    {!! $chart3->script() !!}
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
                <h5 class="card-title">{{ __('referral.admin.charts.monthly_rewards') }}</h5>
                <span class="card-subtitle">{{ __('referral.admin.charts.monthly_rewards_desc') }}</span>
            </div>
            <div class="card-body">
                @if (!empty($monthlyRewardsChart['labels']))
                    @php
                        $chart4 = new \Flute\Core\Charts\FluteChart();
                        $chart4->setType('area')
                            ->setHeight(280)
                            ->setDataset($monthlyRewardsChart['series'] ?? [])
                            ->setLabels($monthlyRewardsChart['labels'] ?? []);
                    @endphp
                    {!! $chart4->container() !!}
                    {!! $chart4->script() !!}
                @else
                    <div class="referral-empty-chart">
                        <x-icon path="ph.regular.chart-bar" />
                        <p>{{ __('referral.admin.stats.no_data') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="referral-stats-page__info">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('referral.admin.stats.system_status') }}</h5>
            </div>
            <div class="card-body">
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.fields.enabled') }}</span>
                    @if ($settings['enabled'])
                        <span class="badge success">{{ __('def.yes') }}</span>
                    @else
                        <span class="badge error">{{ __('def.no') }}</span>
                    @endif
                </div>
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.fields.auto_reward') }}</span>
                    @if ($settings['auto_reward'])
                        <span class="badge success">{{ __('def.yes') }}</span>
                    @else
                        <span class="badge warning">{{ __('def.no') }}</span>
                    @endif
                </div>
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.fields.show_in_profile') }}</span>
                    @if ($settings['show_in_profile'] ?? true)
                        <span class="badge success">{{ __('def.yes') }}</span>
                    @else
                        <span class="badge warning">{{ __('def.no') }}</span>
                    @endif
                </div>
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.fields.min_activity_days') }}</span>
                    <span class="badge">{{ $settings['min_activity_days'] ?? 0 }} {{ __('def.days') }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('referral.admin.stats.conversion') }}</h5>
            </div>
            <div class="card-body">
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.stats.total_codes') }}</span>
                    <span class="badge">{{ $totalCodes }}</span>
                </div>
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.stats.active_referrers') }}</span>
                    <span class="badge success">{{ $activeReferrers }}</span>
                </div>
                @php
                    $avgPerReferrer = $activeReferrers > 0 ? round($stats['total_referrals'] / $activeReferrers, 1) : 0;
                @endphp
                <div class="referral-info-row">
                    <span>{{ __('referral.admin.stats.avg_per_referrer') }}</span>
                    <span class="badge">{{ $avgPerReferrer }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <x-icon path="ph.bold.trophy-bold" class="text-warning" />
                {{ __('referral.admin.stats.top_referrers') }}
            </h5>
        </div>
        <div class="card-body withoutPadding">
            @if (!empty($stats['top_referrers']))
                <div class="referral-top-list">
                    @foreach ($stats['top_referrers'] as $index => $item)
                        <a href="{{ url('/admin/referral/user?id=' . $item['user']->id) }}" class="referral-top-list__item">
                            <span class="referral-top-list__rank @if($index < 3) referral-top-list__rank--top @endif" data-rank="{{ $index + 1 }}">
                                @if ($index === 0)
                                    <x-icon path="ph.bold.crown-bold" />
                                @elseif ($index === 1)
                                    <x-icon path="ph.bold.medal-bold" />
                                @elseif ($index === 2)
                                    <x-icon path="ph.bold.medal-bold" />
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>
                            <img src="{{ url($item['user']->avatar ?? config('profile.default_avatar')) }}" alt="{{ $item['user']->name }}" class="referral-top-list__avatar">
                            <div class="referral-top-list__info">
                                <span class="referral-top-list__name">{{ $item['user']->name }}</span>
                                <span class="referral-top-list__count">{{ $item['count'] }} {{ __('referral.admin.stats.referrals_count') }}</span>
                            </div>
                            <span class="referral-top-list__earnings">+{{ number_format($item['earnings'], 2) }} {{ $currency }}</span>
                            <x-icon path="ph.regular.caret-right" class="referral-top-list__arrow" />
                        </a>
                    @endforeach
                </div>
            @else
                <div class="referral-empty-list">
                    <x-icon path="ph.regular.trophy" />
                    <p>{{ __('referral.admin.stats.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
