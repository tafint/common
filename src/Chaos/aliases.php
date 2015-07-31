<?php // path to entity/service classes

$aliases = [];

foreach (glob(MODULE_PATH . '/*/{Entities,Events,Models,Services}/*.php', GLOB_BRACE) as $v)
{
    $aliases[basename($v, '.php')] = str_replace([MODULE_PATH . '/', '.php', '/'], ['', '', '\\'], $v);
}

return ['di' => $aliases];

/*return [
    // account
    'Permission' => 'Account\Entities\Permission',
    'PermissionListener' => 'Account\Events\PermissionListener',
    'PermissionService' => 'Account\Service\PermissionService',
    'Role' => 'Account\Entities\Role',
    'RoleListener' => 'Account\Events\RoleListener',
    'RoleService' => 'Account\Service\RoleService',
    'User' => 'Account\Entities\User',
    'UserListener' => 'Account\Events\UserListener',
    'UserService' => 'Account\Service\UserService',
    'UserRole' => 'Account\Entities\UserRole',
    'UserRoleListener' => 'Account\Events\UserRoleListener',
    'UserRoleService' => 'Account\Service\UserRoleService',
];*/