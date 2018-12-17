<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {

        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');

        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');

        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');

        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');

        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');

        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');

        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');

        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');

        // 获取手机号码
        $api->post('weapp/phonedecode', 'PhoneNumberCryptController@weappPhoneDecode')
            ->name('api.weapp.phonenumbercrypt.phonedecode');

        // 获取简易基金信息
        $api->post('weapp/fundbriefinfo', 'WeappGetFundBriefInfoController@GetFundInfo')
            ->name('api.weapp.getfundinfo.getfundinfo');

        // 获取大盘指数
        $api->post('weapp/stockindex', 'WeappStockIndexController@StockIndex')
            ->name('api.weapp.stockindex.stockindex');

        // 建立或更新基金设定表
        $api->post('weapp/fundsetting', 'WeappFundSettingController@FundSetting')
            ->name('api.weapp.fundsetting.fundsetting');

        // 获取基金设定表
        $api->post('weapp/fundsaving', 'WeappFundSavingController@FundSaving')
            ->name('api.weapp.fundsaving.fundsaving');

        // 检查基金号
        $api->post('weapp/fundcodecheck', 'WeappFundCodeCheckController@FundCodeCheck')
            ->name('api.weapp.fundcodecheck.fundcodecheck');

        // 建立定投计划表
        $api->post('weapp/projectcreation', 'WeappProjectCreationController@ProjectCreation')
            ->name('api.weapp.projectcreation.projectcreation');

        // 删除定投计划表
        $api->post('weapp/projectdeletion', 'WeappProjectDeletionController@ProjectDeletion')
            ->name('api.weapp.projectdeletion.projectdeletion');

        // 获取定投计划表
        $api->post('weapp/projecttable', 'WeappProjectTableController@ProjectTable')
            ->name('api.weapp.projecttable.projecttable');

        // 更新定投计划表
        $api->post('weapp/projectupdate', 'WeappProjectUpdateController@ProjectUpdate')
            ->name('api.weapp.projectupdate.projectupdate');

        // 查询房价
        $api->post('weapp/realestatequery', 'WeappRealEstateQueryController@RealEstateQuery')
            ->name('api.weapp.realestatequery.realestatequery');

        // 查询车价测试
        $api->post('weapp/carprice', 'CarPriceController@CarPrice')
            ->name('api.weapp.carprice.carprice');

        // 抓取车价
        $api->post('weapp/autocar', 'WeappAutoCarController@AutoCar')
            ->name('api.weapp.autocar.autocar');
    });
});