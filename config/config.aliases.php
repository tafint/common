<?php $aliases = [];

foreach (glob(__DIR__ . '/*/[!R]*/*.php') as $v)
{
    $aliases[basename($v, '.php')] = [
        'definition' => str_replace([__DIR__ . '/', '.php', '/'], ['', '', '\\'], $v),
        'singleton' => true
    ];
}

return $aliases;

/*return [
    // account
    'Permission' => ['definition' => 'Account\Entities\Permission', 'singleton' => true],
    'PermissionListener' => ['definition' => 'Account\Events\PermissionListener', 'singleton' => true],
    'PermissionService' => ['definition' => 'Account\Service\PermissionService', 'singleton' => true],
    'Role' => ['definition' => 'Account\Entities\Role', 'singleton' => true],
    'RoleListener' => ['definition' => 'Account\Events\RoleListener', 'singleton' => true],
    'RoleService' => ['definition' => 'Account\Service\RoleService', 'singleton' => true],
    'User' => ['definition' => 'Account\Entities\User', 'singleton' => true],
    'UserListener' => ['definition' => 'Account\Events\UserListener', 'singleton' => true],
    'UserService' => ['definition' => 'Account\Service\UserService', 'singleton' => true],
    'UserRole' => ['definition' => 'Account\Entities\UserRole', 'singleton' => true],
    'UserRoleListener' => ['definition' => 'Account\Events\UserRoleListener', 'singleton' => true],
    'UserRoleService' => ['definition' => 'Account\Service\UserRoleService', 'singleton' => true],
];*/