<?php

namespace Flute\Modules\Referral\Services;

use Flute\Core\Database\Entities\User;
use Flute\Modules\Referral\database\Entities\Referral;
use Flute\Modules\Referral\database\Entities\ReferralCode;

interface ReferralServiceInterface
{
    public function getOrCreateCode(User $user): ReferralCode;

    public function getCodeByString(string $code): ?ReferralCode;

    public function createReferral(User $referrer, User $referred): Referral;

    public function hasReferrer(User $user): bool;

    public function getReferralsForUser(User $user): array;

    public function getReferralStats(User $user): array;

    public function processReferralReward(Referral $referral): void;

    public function getSettings(): array;
}
