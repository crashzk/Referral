<?php

use Flute\Core\Router\Router;
use Flute\Modules\Referral\Admin\Package\Screens\ReferralListScreen;
use Flute\Modules\Referral\Admin\Package\Screens\ReferralSettingsScreen;
use Flute\Modules\Referral\Admin\Package\Screens\ReferralStatsScreen;
use Flute\Modules\Referral\Admin\Package\Screens\ReferralUserScreen;

Router::screen('/admin/referral', ReferralListScreen::class);
Router::screen('/admin/referral/settings', ReferralSettingsScreen::class);
Router::screen('/admin/referral/stats', ReferralStatsScreen::class);
Router::screen('/admin/referral/user', ReferralUserScreen::class);
