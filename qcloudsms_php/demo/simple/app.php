<?php

require __DIR__ . "/../../src/index.php";

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;

use Qcloud\Sms\VoiceFileUploader;
use Qcloud\Sms\FileVoiceSender;
use Qcloud\Sms\TtsVoiceSender;


// 短信应用SDK AppID
$appid = 1400154988; // 1400开头

// 短信应用SDK AppKey
$appkey = "2ac5830c1cd93788dcc21aa4a2506d67";

// 需要发送短信的手机号码
$phoneNumbers = ["15951935728", "13070107809"];

// 短信模板ID，需要在短信应用中申请
$templateId = '219207';  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

// 签名
$smsSign = "宁关网络"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`


// 指定模板ID单发短信
try {
    $ssender = new SmsSingleSender($appid, $appkey);
    $params = ["5678","1"];
    $result = $ssender->sendWithParam("86", $phoneNumbers[0], $templateId,
        $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
    $rsp = json_decode($result);
    echo $result;
} catch(\Exception $e) {
    echo var_dump($e);
}

