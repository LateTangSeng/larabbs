<?php



namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\WeappPhoneNumberCryptRequest;
use App\Http\Controllers\Api\wxBizDataCrypt;


class PhoneNumberCryptController extends Controller
{
    //
    public function weappPhoneDecode(WeappPhoneNumberCryptRequest $request)
    {
        $appid = 'wx5e8de3f72d718434';

        $name = $request->name;
        $gender = $request->gender;
        $code = $request->code;
        $iv = $request->iv;
        $encryptedData = $request->encryptedData;
        $AuthDone = 'false';

        switch ($gender) {
            case '0':
                $gender = '未设置';
                break;

            case '1':
                $gender = '男';
                break;

            case '2':
                $gender = '女';
                break;

            default:
                $gender = 'null';
                break;
        }

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $user = User::where('weixin_openid', $data['openid'])->first();

        $openid = $data['openid'];
        $weixin_session_key = $data['session_key'];


        $pc = new WXBizDataCrypt($appid, $weixin_session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $phonedata);


        if (0 == $errCode) {

            $phonenumber = str_after($phonedata,'purePhoneNumber":"');
            $phonenumber = str_before($phonenumber, '"');

            // 未找到对应用户则需要提交用户名密码进行用户绑定
            if (!$user) {

            // 获取对应的用户
            //$user = Auth::guard('api')->getUser();
            //$attributes['weixin_openid'] = $data['openid'];

            // 插入数据 返回插入数据的bool值
             $insert_bool = User::insert(['name'=>$name,'gender'=>$gender,'phone'=>$phonenumber,'weixin_openid'=>$openid,'weixin_session_key'=>$weixin_session_key,'first_log_at'=>now(),'last_log_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
             if($insert_bool)
             {
                // 找到 openid 对应的用户
                $user = User::where('weixin_openid', $data['openid'])->first();
                $AuthDone = 'true';
             }
            }
            else
            {
                $update_bool = User::where('weixin_openid', $data['openid'])->update(['weixin_session_key'=>$weixin_session_key,'last_log_at'=>now()]);
                if ($update_bool) {
                    // 找到 openid 对应的用户
                    $user = User::where('weixin_openid', $data['openid'])->first();
                    $AuthDone = 'true';
                }
            }

        }


        return $this->respondWithAuth($AuthDone)->setStatusCode(201);
        //return $openid;


        //return $this->response->array([
        //    'openid' => $openid,
        //    'weixin_session_key' => $weixin_session_key,
        //    'insert_bool' => $insert_bool,
        //]);
    }

    protected function respondWithAuth($AuthDone)
    {
        return $this->response->array([
            'result' => $AuthDone,
            'writer' => 'L.SHENG'
        ]);
    }


}
