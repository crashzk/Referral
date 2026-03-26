<?php

namespace Flute\Modules\Referral;

use Flute\Core\Database\Entities\Permission;
use Flute\Core\ModulesManager\ModuleInformation;
use Flute\Core\Support\AbstractModuleInstaller;

class Installer extends AbstractModuleInstaller
{
    public function install(ModuleInformation &$module): bool
    {
        $permission = Permission::findOne(['name' => 'admin.referral']);

        if (!$permission) {
            $permission = new Permission();
            $permission->name = 'admin.referral';
            $permission->desc = 'referral.admin.permission';
            $permission->save();
        }

        return true;
    }

    public function uninstall(ModuleInformation &$module): bool
    {
        $permission = Permission::findOne(['name' => 'admin.referral']);

        if ($permission) {
            $permission->delete();
        }

        return true;
    }
}
