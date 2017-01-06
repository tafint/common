<?php namespace Chaos\Common;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class AbstractLeagueServiceProvider
 * @author ntd1712
 * @deprecated
 */
abstract class AbstractLeagueServiceProvider extends AbstractServiceProvider
{
    /** {@inheritdoc} */
    public function register()
    {
        if (empty($this->provides))
        {
            return;
        }

        foreach ($this->provides as $v)
        {
            if (false !== strpos($v, '\\'))
            {
                $this->container->add($v);

                if (false === stripos($v, '\\events\\'))
                {
                    $this->container->add(shorten($v), $v);
                }
            }
        }
    }
}

// modules/Account/Module.php
// class Module extends AbstractLeagueServiceProvider
// {
//    /** @var array */
//    protected $provides = [
//        // entities
//        'Account\Entities\Permission',
//        'Account\Entities\Role',
//        'Account\Entities\User',
//        'Account\Entities\UserRole',
//        // events
//        'Account\Events\PermissionListener',
//        'Account\Events\RoleListener',
//        'Account\Events\UserListener',
//        'Account\Events\UserRoleListener',
//        // services
//        'Account\Services\PermissionService',
//        'Account\Services\RoleService',
//        'Account\Services\UserService',
//        'Account\Services\UserRoleService',
//        // aliases
//        'PermissionService',
//        'RoleService',
//        'UserService',
//        'UserRoleService'
//    ];
// }