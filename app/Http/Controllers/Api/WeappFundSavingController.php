<?php

namespace App\Http\Controllers\Api;

use App\Models\FundSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappFundSavingRequest;

class WeappFundSavingController extends Controller
{
    //
    public function FundSaving(WeappFundSavingRequest $request)
    {
        $ResultArray = array("state" => 'false', "openid"=> '', "salary"=> '', "saving"=> '', "basemoney"=> '');
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $ResultArray;
        }

        // 找到 openid 对应的用户
        $user = FundSetting::where('weixinopenid', $data['openid'])->first();

        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {
            return $ResultArray;
        }
        else
        {
            // 更新成功
            $ResultArray['state'] = 'true';
            $ResultArray['openid'] = $user->weixinopenid;
            $ResultArray['salary'] = $user->salary;
            $ResultArray['saving'] = $user->saving;
            $ResultArray['basemoney'] = $user->basemoney;
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
