<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class officialaccountsetting extends Controller
{
    private $tempvalue;

    public function __construct( $appid_temp)
    {
        $this->tempvalue = $appid_temp;
    }

    public function OAS( $temp )
    {
        return [
        'app_id' => 'wxef3fbbcc37d080dc',
        'secret' => '70030a6818198fc16383e54995f97ff2',
        'token' => 'WeixinOfficialAccount',
        'response_type' => 'array',
        'log' => [
        'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
        'channels' => [
                // 测试环境
                'dev' => [
                    'driver' => 'single',
                    'path' => __DIR__.'/easywechat.log',
                    'level' => 'debug',
                ],
                // 生产环境
                'prod' => [
                    'driver' => 'daily',
                    'path' => '/var/www/larabbs/app/Http/Controllers/easywechat.log',
                    'level' => 'info',
                ],
            ],
        ],
    ];
    }
}

