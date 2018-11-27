<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Http\Requests\Api\AuthorizationRequest;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }

                break;
        }

        return $this->response->array(['token' => $user->id]);
    }


    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;
        $name = $request->name;
        $gender = $request->gender;

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

        //$attributes['weixin_session_key'] = $data['session_key'];

        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {

            // 获取对应的用户
            //$user = Auth::guard('api')->getUser();
            //$attributes['weixin_openid'] = $data['openid'];

            // 插入数据 返回插入数据的bool值
             $insert_bool = User::insert(['name'=>$request->name,'gender'=>$request->gender,'weixin_openid'=>$openid,'weixin_session_key'=>$weixin_session_key,'first_log_at'=>now(),'last_log_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
             if($insert_bool)
             {
                // 找到 openid 对应的用户
                $user = User::where('weixin_openid', $data['openid'])->first();
             }
        }
        else
        {
            $update_bool = User::where('weixin_openid', $data['openid'])->update(['weixin_session_key'=>$weixin_session_key,'last_log_at'=>now(),'name'=>$request->name,'gender'=>$request->gender]);
            if ($update_bool) {
                // 找到 openid 对应的用户
                $user = User::where('weixin_openid', $data['openid'])->first();
            }
        }

        // 更新用户数据
        //$user->update($attributes);

        // 为对应用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);
        //$openid = $data['openid'];

        return $this->respondWithToken($token, $openid)->setStatusCode(201);
        //return $openid;
        //return $data;


        //return $this->response->array([
        //    'openid' => $openid,
        //    'weixin_session_key' => $weixin_session_key,
        //    'insert_bool' => $insert_bool,
        //]);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token, $openid)
    {
        return $this->response->array([
            'access_token' => $token,
            'openid' => $openid,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}