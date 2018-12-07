<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\officialaccountsetting;

//require __DIR__.'/vender/autoload.php';

class WeixinOfficialController extends Controller
{
    public function show()
    {

        $temp = '1';

        $pc = new officialaccountsetting($temp);
        $config = $pc->OAS($temp);
        file_put_contents(__DIR__.'/db1.txt', json_encode($config));

        $app = Factory::officialAccount($config);

        $app->server->push(function ($message) {
            return "您好！欢迎使用 EasyWeChat!";
        });

        $response = $app->server->serve();

        // 将响应输出
        return $response;// Laravel 里请使用：return $response;
    }

}