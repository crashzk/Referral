@php
    $defaultCode = $referralCode ?? session()->get('referral_code') ?? request()->input('ref') ?? '';
    $hasSessionCode = session()->has('referral_code');
@endphp

<div class="referral-field {{ $hasSessionCode ? 'referral-field--active' : '' }}">
    <div class="referral-field__header">
        <div class="referral-field__icon">
            <x-icon path="ph.regular.users-three" />
        </div>
        <div class="referral-field__title">
            {{ __('referral.auth.referral_code') }}
        </div>
    </div>
    
    <div class="referral-field__input-wrap">
        <x-fields.input 
            type="text" 
            class="w-100"
            name="referral_code" 
            id="referral_code"
            value="{{ request()->input('referral_code', $defaultCode) }}"
            placeholder="{{ __('referral.auth.referral_code_placeholder') }}"
            readOnly="{{ $hasSessionCode }}"
        />
        @if($hasSessionCode)
            <div class="referral-field__badge">
                <x-icon path="ph.regular.check-circle" />
            </div>
        @endif
    </div>
    
    @if($hasSessionCode)
        <div class="referral-field__status referral-field__status--success">
            <x-icon path="ph.regular.gift" />
            {{ __('referral.auth.code_applied', ['code' => session()->get('referral_code')]) }}
        </div>
    @else
        <div class="referral-field__hint">
            {{ __('referral.auth.referral_code_help') }}
        </div>
    @endif
</div>
