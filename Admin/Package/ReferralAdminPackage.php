<?php

namespace Flute\Modules\Referral\Admin\Package;

use Flute\Admin\Support\AbstractAdminPackage;

class ReferralAdminPackage extends AbstractAdminPackage
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadRoutesFromFile('routes.php');
        $this->loadViews('Resources/views', 'admin-referral');
        $this->loadTranslations('Resources/lang');
        $this->registerScss('Resources/assets/scss/referral-admin.scss');
    }

    public function getPermissions(): array
    {
        return ['admin', 'admin.referral'];
    }

    public function getMenuItems(): array
    {
        return [
            [
                'title' => __('referral.admin.menu'),
                'icon' => 'ph.bold.users-three-bold',
                'url' => url('/admin/referral'),
                'permission' => 'admin.referral',
            ],
        ];
    }

    public function getPriority(): int
    {
        return 106;
    }
}
