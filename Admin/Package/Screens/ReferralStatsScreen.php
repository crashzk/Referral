<?php

namespace Flute\Modules\Referral\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Referral\Services\ReferralService;

class ReferralStatsScreen extends Screen
{
    public ?string $name = 'referral.admin.title.stats';

    public ?string $description = 'referral.admin.title.stats_description';

    public ?string $permission = 'admin.referral';

    public array $stats = [];

    public array $dailyReferralsChart = [];

    public array $dailyRewardsChart = [];

    public array $monthlyReferralsChart = [];

    public array $monthlyRewardsChart = [];

    public function mount(): void
    {
        breadcrumb()->add(__('def.admin_panel'), url('/admin'))->add(
            __('referral.admin.title.list'),
            url('/admin/referral'),
        )->add(__('referral.admin.title.stats'));

        $service = app(ReferralService::class);
        $this->stats = $service->getTotalStats();
        $this->dailyReferralsChart = $service->getReferralsChartData(14);
        $this->dailyRewardsChart = $service->getRewardsChartData(14);
        $this->monthlyReferralsChart = $service->getMonthlyReferralsChartData(9);
        $this->monthlyRewardsChart = $service->getMonthlyRewardsChartData(9);
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('def.back'))
                ->type(Color::OUTLINE_SECONDARY)
                ->icon('ph.bold.arrow-left-bold')
                ->redirect(url('/admin/referral')),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::view('admin-referral::stats', [
                'stats' => $this->stats,
                'dailyReferralsChart' => $this->dailyReferralsChart,
                'dailyRewardsChart' => $this->dailyRewardsChart,
                'monthlyReferralsChart' => $this->monthlyReferralsChart,
                'monthlyRewardsChart' => $this->monthlyRewardsChart,
            ]),
        ];
    }
}
