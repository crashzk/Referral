<?php

namespace Flute\Modules\Referral\Services;

use DateTimeImmutable;
use Flute\Core\Database\Entities\User;
use Flute\Modules\Referral\database\Entities\Referral;
use Flute\Modules\Referral\database\Entities\ReferralCode;

class ReferralService implements ReferralServiceInterface
{
    public function getOrCreateCode(User $user): ReferralCode
    {
        $code = ReferralCode::findOne(['user_id' => $user->id]);

        if (!$code) {
            $code = new ReferralCode();
            $code->user = $user;
            $code->code = $this->generateUniqueCode();
            $code->saveOrFail();
        }

        return $code;
    }

    public function getCodeByString(string $code): ?ReferralCode
    {
        return ReferralCode::query()
            ->where('code', $code)
            ->where('active', true)
            ->load('user')
            ->fetchOne();
    }

    public function createReferral(User $referrer, User $referred): Referral
    {
        $referral = new Referral();
        $referral->referrer = $referrer;
        $referral->referred = $referred;
        $referral->saveOrFail();

        $code = ReferralCode::findOne(['user_id' => $referrer->id]);
        if ($code) {
            $code->incrementUses();
        }

        return $referral;
    }

    public function hasReferrer(User $user): bool
    {
        return Referral::findOne(['referred_id' => $user->id]) !== null;
    }

    public function getReferralsForUser(User $user): array
    {
        return Referral::query()
            ->where('referrer_id', $user->id)
            ->load('referred')
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }

    public function getReferralStats(User $user): array
    {
        $referrals = $this->getReferralsForUser($user);
        $totalReferrals = count($referrals);
        $claimedRewards = 0;
        $totalEarnings = 0;

        foreach ($referrals as $referral) {
            if ($referral->reward_claimed) {
                $claimedRewards++;
                $totalEarnings += $referral->reward_amount;
            }
        }

        $code = $this->getOrCreateCode($user);

        return [
            'total_referrals' => $totalReferrals,
            'claimed_rewards' => $claimedRewards,
            'pending_rewards' => $totalReferrals - $claimedRewards,
            'total_earnings' => $totalEarnings,
            'referral_code' => $code->code,
            'referral_link' => $code->getLink(),
            'referrals' => $referrals,
        ];
    }

    public function processReferralReward(Referral $referral): void
    {
        if ($referral->reward_claimed) {
            return;
        }

        $settings = $this->getSettings();
        $rewardAmount = (float) ( $settings['referrer_reward'] ?? 0 );

        if ($rewardAmount <= 0) {
            return;
        }

        $referrer = $referral->referrer;
        $referrer->balance += $rewardAmount;
        $referrer->saveOrFail();

        $referral->claimReward($rewardAmount);
    }

    public function processReferredBonus(User $referred): void
    {
        $settings = $this->getSettings();
        $bonusAmount = (float) ( $settings['referred_bonus'] ?? 0 );

        if ($bonusAmount <= 0) {
            return;
        }

        $referral = Referral::findOne(['referred_id' => $referred->id]);
        if (!$referral || ( $referral->referred_bonus_claimed ?? false )) {
            return;
        }

        $referred->balance += $bonusAmount;
        $referred->saveOrFail();

        if (property_exists($referral, 'referred_bonus_claimed')) {
            $referral->referred_bonus_claimed = true;
            $referral->saveOrFail();
        }
    }

    public function getSettings(): array
    {
        return [
            'enabled' => (bool) config('referral.enabled', true),
            'referrer_reward' => (float) config('referral.referrer_reward', 10),
            'referred_bonus' => (float) config('referral.referred_bonus', 5),
            'auto_reward' => (bool) config('referral.auto_reward', true),
            'min_activity_days' => (int) config('referral.min_activity_days', 0),
        ];
    }

    public function getAllReferrals(): array
    {
        return Referral::query()
            ->load('referrer')
            ->load('referred')
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }

    public function getTotalStats(): array
    {
        $referrals = $this->getAllReferrals();
        $totalReferrals = count($referrals);
        $totalRewardsPaid = 0;
        $uniqueReferrers = [];

        foreach ($referrals as $referral) {
            if ($referral->reward_claimed) {
                $totalRewardsPaid += $referral->reward_amount;
            }
            $uniqueReferrers[$referral->referrer->id] = true;
        }

        $topReferrers = $this->getTopReferrers(10);

        return [
            'total_referrals' => $totalReferrals,
            'total_rewards_paid' => $totalRewardsPaid,
            'top_referrers' => $topReferrers,
            'active_referrers' => count($uniqueReferrers),
        ];
    }

    public function getTopReferrers(int $limit = 10): array
    {
        $referrals = Referral::query()->load('referrer')->fetchAll();

        $referrerCounts = [];
        foreach ($referrals as $referral) {
            $referrerId = $referral->referrer->id;
            if (!isset($referrerCounts[$referrerId])) {
                $referrerCounts[$referrerId] = [
                    'user' => $referral->referrer,
                    'count' => 0,
                    'earnings' => 0,
                ];
            }
            $referrerCounts[$referrerId]['count']++;
            if ($referral->reward_claimed) {
                $referrerCounts[$referrerId]['earnings'] += $referral->reward_amount;
            }
        }

        usort($referrerCounts, static fn($a, $b) => $b['count'] <=> $a['count']);

        return array_slice($referrerCounts, 0, $limit);
    }

    public function getReferralsChartData(int $days = 30): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$days} days")->setTime(0, 0);

        $dailyData = [];
        $labels = [];

        for ($i = 0; $i < $days; $i++) {
            $dayStart = $startDate->modify("+{$i} day");
            $dayEnd = $dayStart->modify('+1 day');

            $labels[] = \Carbon\Carbon::parse($dayStart)->translatedFormat('d M');

            $count = Referral::query()
                ->where('created_at', '>=', $dayStart)
                ->where('created_at', '<', $dayEnd)
                ->count();

            $dailyData[] = $count;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.new_referrals'),
                    'data' => $dailyData,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getRewardsChartData(int $days = 30): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$days} days")->setTime(0, 0);

        $dailyRewards = [];
        $labels = [];

        for ($i = 0; $i < $days; $i++) {
            $dayStart = $startDate->modify("+{$i} day");
            $dayEnd = $dayStart->modify('+1 day');

            $labels[] = \Carbon\Carbon::parse($dayStart)->translatedFormat('d M');

            $referrals = Referral::query()
                ->where('reward_claimed', true)
                ->where('created_at', '>=', $dayStart)
                ->where('created_at', '<', $dayEnd)
                ->fetchAll();

            $sum = 0;
            foreach ($referrals as $referral) {
                $sum += $referral->reward_amount;
            }

            $dailyRewards[] = $sum;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.rewards_paid'),
                    'data' => $dailyRewards,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getUserReferralStats(int $userId): array
    {
        $user = User::findByPK($userId);
        if (!$user) {
            return [];
        }

        $referrals = $this->getReferralsForUser($user);
        $code = $this->getOrCreateCode($user);

        $totalReferrals = count($referrals);
        $claimedRewards = 0;
        $totalEarnings = 0;
        $pendingRewards = 0;

        foreach ($referrals as $referral) {
            if ($referral->reward_claimed) {
                $claimedRewards++;
                $totalEarnings += $referral->reward_amount;
            } else {
                $pendingRewards++;
            }
        }

        return [
            'user' => $user,
            'code' => $code,
            'total_referrals' => $totalReferrals,
            'claimed_rewards' => $claimedRewards,
            'pending_rewards' => $pendingRewards,
            'total_earnings' => $totalEarnings,
            'referrals' => $referrals,
        ];
    }

    public function getUserReferralsChartData(int $userId, int $months = 6): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$months} months");

        $monthlyData = [];
        $labels = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->modify("+{$i} month");
            $monthEnd = $startDate->modify('+' . ( $i + 1 ) . ' month');

            $labels[] = \Carbon\Carbon::parse($monthStart)->translatedFormat('M Y');

            $count = Referral::query()
                ->where('referrer_id', $userId)
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->count();

            $monthlyData[] = $count;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.referrals'),
                    'data' => $monthlyData,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getUserEarningsChartData(int $userId, int $months = 6): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$months} months");

        $monthlyEarnings = [];
        $labels = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->modify("+{$i} month");
            $monthEnd = $startDate->modify('+' . ( $i + 1 ) . ' month');

            $labels[] = \Carbon\Carbon::parse($monthStart)->translatedFormat('M Y');

            $referrals = Referral::query()
                ->where('referrer_id', $userId)
                ->where('reward_claimed', true)
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->fetchAll();

            $sum = 0;
            foreach ($referrals as $referral) {
                $sum += $referral->reward_amount;
            }

            $monthlyEarnings[] = $sum;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.earnings'),
                    'data' => $monthlyEarnings,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getMonthlyReferralsChartData(int $months = 9): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$months} months");

        $monthlyData = [];
        $labels = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->modify("+{$i} month");
            $monthEnd = $startDate->modify('+' . ( $i + 1 ) . ' month');

            $labels[] = \Carbon\Carbon::parse($monthStart)->translatedFormat('M');

            $count = Referral::query()
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->count();

            $monthlyData[] = $count;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.new_referrals'),
                    'data' => $monthlyData,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getMonthlyRewardsChartData(int $months = 9): array
    {
        $now = new DateTimeImmutable();
        $startDate = $now->modify("-{$months} months");

        $monthlyRewards = [];
        $labels = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->modify("+{$i} month");
            $monthEnd = $startDate->modify('+' . ( $i + 1 ) . ' month');

            $labels[] = \Carbon\Carbon::parse($monthStart)->translatedFormat('M');

            $referrals = Referral::query()
                ->where('reward_claimed', true)
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->fetchAll();

            $sum = 0;
            foreach ($referrals as $referral) {
                $sum += $referral->reward_amount;
            }

            $monthlyRewards[] = $sum;
        }

        return [
            'series' => [
                [
                    'name' => __('referral.admin.charts.rewards_paid'),
                    'data' => $monthlyRewards,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (ReferralCode::findOne(['code' => $code]));

        return $code;
    }
}
