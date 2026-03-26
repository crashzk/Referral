<?php

namespace Flute\Modules\Referral\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Referral\Services\ReferralService;

class ReferralUserScreen extends Screen
{
    public ?string $name = 'referral.admin.title.user_stats';

    public ?string $description = 'referral.admin.title.user_stats_description';

    public ?string $permission = 'admin.referral';

    public array $userStats = [];

    public array $referralsChart = [];

    protected int $userId;

    public function mount(): void
    {
        $this->userId = (int) request()->input('id', 0);

        if (!$this->userId) {
            $this->flashMessage(__('referral.admin.messages.not_found'), 'error');
            $this->redirectTo('/admin/referral');

            return;
        }

        $service = app(ReferralService::class);
        $this->userStats = $service->getUserReferralStats($this->userId);

        if (empty($this->userStats)) {
            $this->flashMessage(__('referral.admin.messages.not_found'), 'error');
            $this->redirectTo('/admin/referral');

            return;
        }

        $this->referralsChart = $service->getUserReferralsChartData($this->userId, 6);

        $user = $this->userStats['user'];

        breadcrumb()->add(__('def.admin_panel'), url('/admin'))->add(
            __('referral.admin.title.list'),
            url('/admin/referral'),
        )->add($user->name);

        $this->name = $user->name;
        $this->description = __('referral.admin.title.user_stats_description');
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
        if (empty($this->userStats)) {
            return [];
        }

        $currency = config('lk.currency_view', __('def.currency_symbol'));

        return [
            LayoutFactory::view('admin-referral::user-stats', [
                'stats' => $this->userStats,
                'referralsChart' => $this->referralsChart,
                'currency' => $currency,
            ]),
        ];
    }
}
