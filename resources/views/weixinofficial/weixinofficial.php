<?php

use EasyWeChat\Factory;

$config = [
    'app_id' => 'wxef3fbbcc37d080dc',
    'secret' => '70030a6818198fc16383e54995f97ff2',
    'token' => 'WeixinOfficialAccount',
    'response_type' => 'array',
    //...
];

$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 将响应输出
return $response;exit; // Laravel 里请使用：return $response;