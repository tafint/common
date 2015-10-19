<?php return [
  // environment
  'datetimeFormat' => 'Y-m-d H:i:s',
  'dateFormat' => 'Y-m-d',
  'timeFormat' => 'H:i:s',
  'timezone' => 'Asia/Singapore',
  'locale' => 'en_SG',
  'charset' => 'UTF-8',
  'defaultPassword' => '******',
  'imageAllowedExt' => 'gif,jpeg,jpg,png',
  'imageMaxSize' => 2097152, // 2MB
  'itemsPerPage' => 10,
  'maxItemsPerPage' => 100,
  'minSearchChars' => 4,
  'superUserId' => 1,
  // application
  'adminEmail' => 'webmaster@example.com',
  'copyright' => 'Copyright 2015 by My Company. All Rights Reserved.',
  'title' => 'Admin Panel',
  'theme' => 'homer', // homer, classic
  'defaultRoute' => 'setting.index',
  'appId' => 'chaos',
  'appKey' => 'SomeRandomString',
  'apiPath' => realpath(__DIR__ . '/../public/api'),
  'apiUrl' => 'http://localhost/chaos/api/',
  'uploadPath' => realpath(__DIR__ . '/../public/uploads'),
  'uploadUrl' => 'http://localhost/chaos/uploads/',
  // vendor
];