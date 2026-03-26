<?php

namespace Flute\Modules\Referral\Listeners;

use Flute\Core\Modules\Auth\Events\RegisterFormRenderingEvent;

/**
 * Adds referral code field to registration form.
 */
class ReferralFormListener
{
    public static function handle(RegisterFormRenderingEvent $event): void
    {
        $settings = config('referral');

        if (!( $settings['enabled'] ?? true )) {
            return;
        }

        $event->addAfterFields(
            'referral::components.registration-field',
            [
                'referralCode' => session()->get('referral_code') ?? request()->input('ref'),
            ],
            100,
        );
    }
}
