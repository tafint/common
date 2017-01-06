<?php return [
    'app' => [
        'dateFormat' => 'Y-m-d',
        'timeFormat' => 'H:i:s',
        'imageAllowedExt' => 'gif,jpeg,jpg,png',
        'imageMaxSize' => 2097152, // 2MB
        'itemsPerPage' => 10,
        // specific
        'copyright' => 'Copyright (c) 2017 ntd1712',
        'title' => 'Admin Panel',
        'theme' => 'homer', // homer, inspinia, classic
        'defaultRoute' => 'setting.index'
    ],
    'paths' => [
        'api' => __DIR__ . '/../public/api',
        'uploads' => __DIR__ . '/../public/uploads'
    ],
    'urls' => [
        'reset' => '/#/reset?k=',
    ]
];