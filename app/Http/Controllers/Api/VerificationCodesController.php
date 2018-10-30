<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;


require __DIR__ . "/qcloudsms_php/src/index.php";

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;

use Qcloud\Sms\VoiceFileUploader;
use Qcloud\Sms\FileVoiceSender;
use Qcloud\Sms\TtsVoiceSender;


class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT);


        // 短信应用SDK AppID
        $appid = 1400154988; // 1400开头

        // 短信应用SDK AppKey
        $appkey = "2ac5830c1cd93788dcc21aa4a2506d67";

        // 短信模板ID，需要在短信应用中申请
        $templateId = '219207';  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

        // 签名
        $smsSign = "宁关网络"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`

        $params[0] = $code;
        $params[1] = "5";

        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $result = $ssender->sendWithParam("86", $phone, $templateId,
                $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result);
            echo $result;
        } catch(\Exception $e) {
            $response = $exception->getResponse();
            $result = json_decode($response->getBody()->getContents(), true);
            return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
        }

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}