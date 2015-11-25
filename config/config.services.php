<?php return array_map(function($item) {
    return str_replace(__DIR__ . '/', '', $item) . '\\Module';
}, glob(__DIR__ . '/*', GLOB_ONLYDIR));

/*return [
    'Account\Module',
    'System\Module'
];*/