<?php

namespace App\Http\Controllers\Api;

use App\Models\FundSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappFundSettingRequest;

class WeappFundSettingController extends Controller
{
    //
    public function FundSetting(WeappFundSettingRequest $request)
    {
        $ResultArray = array("state" => 'false');
        $openid = $request->openid;
        $salary = $request->salary;
        $saving = $request->saving;
        $basemoney = $request->basemoney;

        // 找到 openid 对应的用户
        $user = FundSetting::where('weixinopenid', $openid)->first();

        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {

            // 插入数据 返回插入数据的bool值
             $insert_bool = FundSetting::insert(['weixinopenid'=>$openid,'salary'=>$salary,'saving'=>$saving,'basemoney'=>$basemoney]);
             if($insert_bool)
             {
                // 插入成功
                $ResultArray['state'] = 'true';
             }
        }
        else
        {
            $update_bool = FundSetting::where('weixinopenid', $openid)->update(['salary'=>$salary,'saving'=>$saving,'basemoney'=>$basemoney]);
            if ($update_bool)
            {
                // 更新成功
                $ResultArray['state'] = 'true';
            }
        }

        //$result = iconv("UTF-8","gb2312//IGNORE",$result);

        //return $this->respondWithResult($result)->setStatusCode(201);
        return $ResultArray;


        //return $this->response->array([
        //    'openid' => $openid,
        //    'weixin_session_key' => $weixin_session_key,
        //    'insert_bool' => $insert_bool,
        //]);
    }

    protected function respondWithResult($result)
    {
        return $this->response->array([
            'fundname' => $result,
            'fundcode' => 'L.SHENG',
            'fundchangerate' => '',
            'fundnetvalue' => '',
            'fundtype' => '',
            'fundrisklevel' => '',
            'fundvalueday' => ''
                    ]);
    }

}
