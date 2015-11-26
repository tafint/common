<?php return [
    'app' => [
        // common
        'datetimeFormat' => 'Y-m-d H:i:s',
        'dateFormat' => 'Y-m-d',
        'timeFormat' => 'H:i:s',
        'charset' => 'UTF-8',
        'defaultPassword' => '******',
        'imageAllowedExt' => 'gif,jpeg,jpg,png',
        'imageMaxSize' => 2097152, // 2MB
        'itemsPerPage' => 10,
        'maxItemsPerPage' => 100,
        'minSearchChars' => 4,
        // specific
        'copyright' => 'Copyright 2015 by My Company. All Rights Reserved.',
        'title' => 'Admin Panel',
        'theme' => 'homer', // homer, classic
        'defaultRoute' => 'setting.index',
    ],
    'paths' => [
        'apiPath' => __DIR__ . '/../public/api',
        'uploadPath' => __DIR__ . '/../public/uploads'
    ]
];