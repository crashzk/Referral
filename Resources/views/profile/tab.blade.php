@php
    $currency = config('lk.currency_view', __('def.currency_symbol'));
@endphp

<div class="ref-profile">
    <div class="ref-profile__link-section">
        <label class="ref-profile__label" for="profileReferralLink">{{ __('referral.profile.link_title') }}</label>
        <div class="ref-profile__link-row">
            <div class="input__field-container ref-profile__link-input">
                <x-icon path="ph.bold.link-bold" class="input__prefix" />
                <input type="text"
                    class="input__field"
                    id="profileReferralLink"
                    value="{{ $stats['referral_link'] }}"
                    readonly>
            </div>
            <x-button type="primary" size="small" id="profileCopyLinkBtn" data-copy="{{ $stats['referral_link'] }}">
                <x-icon path="ph.bold.copy-bold" />
                {{ __('referral.link.copy') }}
            </x-button>
        </div>
        <div class="ref-profile__code-row">
            <span class="ref-profile__code-label">{{ __('referral.profile.your_code') }}:</span>
            <code class="ref-profile__code" data-copy="{{ $stats['referral_code'] }}" data-tooltip="{{ __('referral.link.copy') }}">{{ $stats['referral_code'] }}</code>
        </div>
    </div>

    <x-metrics>
        <x-metric
            :label="__('referral.profile.stats.total')"
            :value="$stats['total_referrals']"
            icon="ph.bold.users-bold"
            color="primary" />
        <x-metric
            :label="__('referral.profile.stats.claimed')"
            :value="$stats['claimed_rewards']"
            icon="ph.bold.check-circle-bold"
            color="success" />
        <x-metric
            :label="__('referral.profile.stats.earned')"
            :value="number_format($stats['total_earnings'], 2)"
            :suffix="$currency"
            icon="ph.bold.coin-bold"
            color="warning" />
    </x-metrics>

    <div class="ref-profile__rewards">
        <div class="ref-profile__reward">
            <span class="ref-profile__reward-icon ref-profile__reward-icon--accent">
                <x-icon path="ph.bold.gift-bold" />
            </span>
            <div class="ref-profile__reward-body">
                <span class="ref-profile__reward-label">{{ __('referral.profile.reward_per_invite') }}</span>
                <span class="ref-profile__reward-value">+{{ number_format($settings['referrer_reward'], 2) }} {{ $currency }}</span>
            </div>
        </div>
        <div class="ref-profile__reward">
            <span class="ref-profile__reward-icon ref-profile__reward-icon--primary">
                <x-icon path="ph.bold.user-plus-bold" />
            </span>
            <div class="ref-profile__reward-body">
                <span class="ref-profile__reward-label">{{ __('referral.profile.bonus_for_friend') }}</span>
                <span class="ref-profile__reward-value">+{{ number_format($settings['referred_bonus'], 2) }} {{ $currency }}</span>
            </div>
        </div>
    </div>

    @if (!empty($stats['referrals']))
        <x-card withoutPadding>
            <x-slot:header>
                <div class="ref-profile__list-header">
                    <h5 class="card-title">{{ __('referral.profile.your_referrals') }}</h5>
                    <x-badge type="primary">{{ count($stats['referrals']) }}</x-badge>
                </div>
            </x-slot:header>

            <div class="ref-profile__list">
                @foreach ($stats['referrals'] as $referral)
                    <div class="ref-profile__list-item">
                        <div class="ref-profile__list-user">
                            <img src="{{ $referral->referred->avatar ?? '/assets/img/default-avatar.webp' }}"
                                alt="{{ $referral->referred->name }}"
                                class="ref-profile__list-avatar"
                                loading="lazy">
                            <div class="ref-profile__list-info">
                                <span class="ref-profile__list-name">{{ $referral->referred->name }}</span>
                                <time class="ref-profile__list-date" datetime="{{ $referral->createdAt->format('c') }}">{{ $referral->createdAt->format('d.m.Y') }}</time>
                            </div>
                        </div>
                        @if ($referral->reward_claimed)
                            <x-badge type="success" icon="ph.bold.check-bold">
                                +{{ number_format($referral->reward_amount, 2) }} {{ $currency }}
                            </x-badge>
                        @else
                            <x-badge type="warning" icon="ph.bold.clock-bold">
                                {{ __('referral.profile.pending') }}
                            </x-badge>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
    @else
        <div class="ref-profile__empty">
            <div class="ref-profile__empty-icon">
                <x-icon path="ph.bold.users-three-bold" />
            </div>
            <h5 class="ref-profile__empty-title">{{ __('referral.profile.no_referrals') }}</h5>
            <p class="ref-profile__empty-desc">{{ __('referral.profile.share_link') }}</p>
            <x-button type="outline-accent" size="small" data-copy="{{ $stats['referral_link'] }}">
                <x-icon path="ph.bold.copy-bold" />
                {{ __('referral.profile.copy_and_share') }}
            </x-button>
        </div>
    @endif
</div>
