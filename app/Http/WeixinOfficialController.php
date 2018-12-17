<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cars;
use App\Models\UserLimit;
use App\Models\House;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
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
        //file_put_contents(__DIR__.'/db1.txt', json_encode($config));

        $app = Factory::officialAccount($config);

        // $buttons = [
        //     [
        //         "type" => "view",
        //         "name" => "历史文章",
        //         "url"  => "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzUxNDk1MzQyNA==#wechat_redirect/"
        //     ],
        //     [
        //         "type" => "view",
        //         "name" => "定投专栏",
        //         "url"  => "http://mp.weixin.qq.com/s?__biz=MzUxNDk1MzQyNA==&mid=100000292&idx=1&sn=a9d3ea613006910e2bf7e901b8ce005c&chksm=79bf5b9b4ec8d28d5c2c8da65fed625d617a89fc776b330cf22b298e9ba966b6402e3a3b2b00&scene=18#wechat_redirect/"
        //     ],
        //     [
        //         "type" => "view",
        //         "name" => "粉丝交流",
        //         "url"  => "http://mp.weixin.qq.com/s?__biz=MzUxNDk1MzQyNA==&mid=100000294&idx=1&sn=7d2bcc8fd7bd4c9925d9e64e218c54a3&chksm=79bf5b994ec8d28f30d8b4e47ceddb9cf997ef162a93f44c514b2e65ff5eb0a3c433ea83d89a&scene=18#wechat_redirect/"
        //     ],
        // ];

        //$app->menu->create($buttons);

        //$list = $app->material->list('news', 95, 20);
        //file_put_contents(__DIR__.'/db1.txt', json_encode($list));
        //定投小程序二维码
        //{"media_id":"s_Q-2NgdVGy5Njd7N-zBEtMsXSOt0pE09xw1DfwBNIs","name":"gh_bb3b541ee41b_258 (1).jpg","update_time":1544093277,"url":"http:\/\/mmbiz.qpic.cn\/mmbiz_jpg\/cCEKGw42aBlFPlFUmqpnMNr7aEzFPPBnUDEaYHpPyqBrmTxGbSrkRKYFo30082T6LcGT1eoUKjr5mUh7J37sTQ\/0?wx_fmt=jpeg"}


        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return $this->EventProc($message);
                    break;
                case 'text':
                    if ('定投' == $message['Content'])
                    {
                        if ($this->FreqLimit('定投', $message))
                        {
                            $items = [
                                new NewsItem([
                                    'title'       => '无敌定投专栏',
                                    'description' => '',
                                    'url'         => "http://mp.weixin.qq.com/s?__biz=MzUxNDk1MzQyNA==&mid=100000292&idx=1&sn=a9d3ea613006910e2bf7e901b8ce005c&chksm=79bf5b9b4ec8d28d5c2c8da65fed625d617a89fc776b330cf22b298e9ba966b6402e3a3b2b00&scene=18#wechat_redirect/",
                                    'image'       => 'https://mmbiz.qpic.cn/mmbiz_jpg/p6sEJEar2ruCGBgpAiafF7PxUq7qNzN07YN4F1pqQ3lLXPSTiaQDbHmY9Zo5tComFsZlBGicdTNbrEibFnhK5UIPfQ/0?wx_fmt=jpeg',
                                    // ...
                                ]),
                            ];

                            $news = new News($items);
                            return $news;
                        }
                        else
                        {
                            return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                        }
                    }
                    else if ('小程序' == $message['Content'])
                    {
                        if ($this->FreqLimit('小程序', $message))
                        {
                            $mediaId = 's_Q-2NgdVGy5Njd7N-zBEtMsXSOt0pE09xw1DfwBNIs';
                            return new Image($mediaId);
                        }
                        else
                        {
                            return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                        }
                    }
                    else
                        return $this->ContentProc($message);
                    break;
                case 'image':
                    if ($this->FreqLimit('image', $message))
                    {
                        return '收到图片消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                case 'voice':
                    if ($this->FreqLimit('voice', $message))
                    {
                        return '收到语音消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                case 'video':
                    if ($this->FreqLimit('video', $message))
                    {
                        return '收到视频消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                case 'location':
                    if ($this->FreqLimit('location', $message))
                    {
                        return '收到位置消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                case 'link':
                    if ($this->FreqLimit('link', $message))
                    {
                        return '收到链接消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                case 'file':
                    if ($this->FreqLimit('file', $message))
                    {
                        return '收到文件消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
                default:
                    if ($this->FreqLimit('其他', $message))
                    {
                        return '收到其他消息,独行侠会尽快联系您的,请稍等';
                    }
                    else
                    {
                        return '为了确保公众号能够正常运行，请您确保输入间隔5-10秒以上，谢谢您的支持与理解';
                    }
                    break;
            }
        });

        $response = $app->server->serve();

        // 将响应输出
        return $response;// Laravel 里请使用：return $response;
    }

    public function FreqLimit($TextValue, $message)
    {
        $proved = false;
        $secs = 0;

        $OfficialAccountUser = UserLimit::where('OpenID', $message['FromUserName'])->first();

        if (!$OfficialAccountUser)
        {
            // 插入数据 返回插入数据的bool值
             $insert_bool = UserLimit::insert([
                'CreateTime' => $message['CreateTime'],
                'OpenID' => $message['FromUserName'],
                'RealEstateCount' => 0,
                'CarPriceCount' => 0,
                'KeyWordsCount' => 0,
                'MiniProgramCount' => 0,
                'OtherTypeCount' => 0,
                'LastMsgType' => $message['MsgType'],
                'LimitCount' => 0,
                'UpdateTime' => $message['CreateTime'],
                'olduser' => 0
            ]);

            if($insert_bool)
            {
                $proved = true;
            }
        }
        else
        {
            $update_bool = false;
            $intervalimit = 5;

            if (1 == $OfficialAccountUser->olduser)
            {
                $intervalimit = 10;
            }

            $interval = $message['CreateTime'] - $OfficialAccountUser->UpdateTime;
            $secs = $interval;

            if ($interval <= $intervalimit)
            {
                $LimitCount = $OfficialAccountUser->LimitCount + 1;
                if ($LimitCount >= 100000)
                {
                    $LimitCount = 0;
                }

                switch ($TextValue)
                {
                    case '定投':
                        $KeyWordsCount = $OfficialAccountUser->KeyWordsCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['KeyWordsCount'=>$KeyWordsCount,'LimitCount'=>$LimitCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '小程序':
                        $MiniProgramCount = $OfficialAccountUser->MiniProgramCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['MiniProgramCount'=>$MiniProgramCount,'LimitCount'=>$LimitCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '房价':
                        $RealEstateCount = $OfficialAccountUser->RealEstateCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['RealEstateCount'=>$RealEstateCount,'LimitCount'=>$LimitCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '车价':
                        $CarPriceCount = $OfficialAccountUser->CarPriceCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['CarPriceCount'=>$CarPriceCount,'LimitCount'=>$LimitCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    default:
                        $OtherTypeCount = $OfficialAccountUser->OtherTypeCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['OtherTypeCount'=>$OtherTypeCount,'LimitCount'=>$LimitCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>(int)$message['CreateTime']]);
                        break;
                }
            }
            else
            {
                switch ($TextValue)
                {
                    case '定投':
                        $KeyWordsCount = $OfficialAccountUser->KeyWordsCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['KeyWordsCount'=>(int)$KeyWordsCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '小程序':
                        $MiniProgramCount = $OfficialAccountUser->MiniProgramCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['MiniProgramCount'=>(int)$MiniProgramCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '房价':
                        $RealEstateCount = $OfficialAccountUser->RealEstateCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['RealEstateCount'=>(int)$RealEstateCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    case '车价':
                        $CarPriceCount = $OfficialAccountUser->CarPriceCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['CarPriceCount'=>(int)$CarPriceCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;

                    default:
                        $OtherTypeCount = $OfficialAccountUser->OtherTypeCount + 1;
                        $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['OtherTypeCount'=>$OtherTypeCount,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
                        break;
                }

                if ($update_bool)
                {
                    $proved = true;
                }
            }
        }
        return $proved;
    }

    public function ContentProc($message)
    {
        $IsCity = $this->CheckCity($message['Content']);

        if ($IsCity)
        {
            return $this->RealEState($message);
        }
        else
        {
            if ($this->CheckProvice($message['Content']))
            {
                return '抱歉，您的输入有误，请不要输入省份，请直接输入城市+小区，确保城市名和小区名之间有 "+-/_," 中间的任一一种英文符号(请不要使用中文符号)；或者在中间添加空格，感谢您的理解和支持';
            }
            else
            {
                return $this->CarPrice($message);
            }
        }
    }

    public function RealEState($message)
    {
        if ($this->FreqLimit('房价', $message))
        {
            $AveragePrice = '抱歉，后台数据库升级中，暂时没有您所查询的小区信息，请尝试其他输入';

            $CharArray = "+- ,/_";
            $CharIndex = 0;
            $IsFind = false;
            $community = '';
            $city = '';

            for ($i=0; $i < 6; $i++)
            {
                $CharTemp = substr($CharArray, $i, 1);
                if (false != strpos($message['Content'], $CharTemp))
                {
                    $CharIndex = $i;
                    $IsFind = true;
                    break;
                }
            }

            if ($IsFind)
            {
                $city = str_before($message['Content'], substr($CharArray, $CharIndex, 1));
                $city = trim($city);
                $community = str_after($message['Content'], substr($CharArray, $CharIndex, 1));
                $community = trim($community);
            }
            else
            {
                $AveragePrice = '抱歉，您的输入有误，请确保城市名和小区名之间有 "+-/_," 中间的任一一种英文符号(请不要使用中文符号)；或者在中间添加空格，感谢您的理解和支持';
                return $AveragePrice;
            }

            $citypinyin = $this->CheckPinYin($city);

            if (strlen($citypinyin) < 2)
            {
                return  $AveragePrice ;
            }

            else
            {
                $HouseCount = House::where('KeyWord', $city.' '.$community)->count();

                if ($HouseCount !=0)
                {
                    if (1 == $HouseCount)
                    {
                        $HousePrice = House::where('KeyWord', $city.' '.$community)->first();
                        $AveragePrice = '为您查询到：

'.$HousePrice->City.' '.$HousePrice->Community.'; 地理位置:'.$HousePrice->Location.'； 当前均价为：'.$HousePrice->Price.' 元/平方米；现有房源：'.$HousePrice->HouseCount.' 套；'.$HousePrice->BuildYear.'

数据更新日期:'.$HousePrice->UpdateTime;
                    }
                }
                else
                {
                    $AveragePrice = $this->HousePrice($city, $citypinyin, $community);
                }

            }

            return $AveragePrice;
        }
        else
        {
            return '为了确保公众号能够正常运行，请您确保输入间隔5秒以上，谢谢您的支持与理解';
        }
    }

    public function CarPrice($message)
    {
        if ($this->FreqLimit('车价', $message))
        {
            $pricereply = '抱歉，后台数据库还在升级中，暂时没有您所查询的车系信息，请尝试其他车系。如不知道车系，可以先尝试输入车辆品牌，比如输入阿斯顿，可以获取阿斯顿·马丁旗下所有车系';

            $cars = Cars::where('name', $message['Content'])->first();

            if (!$cars)
            {
                $count = Cars::where('name', 'like','%'.$message['Content'].'%')->count();
                if ($count != 0)
                {
                    $carbrand = Cars::where('name', 'like','%'.$message['Content'].'%')->get();
                    $pricereply = '您所查询的品牌旗下所有车系为:';
                    for($i = 0; $i < $count; $i++)
                    {
                        $pricereply = $pricereply.$carbrand[$i]->name.';';
                    }

                    $pricereply = $pricereply.' 您可以复制具体车系名称，输入至对话框查询该车系价格范围。';
                }
                return $pricereply;
            }
            else
            {
                if (0 == $cars->minprice) {
                    return '抱歉，您所查询的车系暂时没有报价，请尝试其他车系。';
                }
                else
                {
                    $pricereply = $message['Content'].' 厂家指导价为:'.$cars->minprice.' 至 '.$cars->maxprice.'元';
                }
            }

            return $pricereply;
        }
        else
        {
            return '为了确保公众号能够正常运行，请您确保输入间隔5秒以上，谢谢您的支持与理解';
        }
    }

    public function HousePrice($city, $citypinyin, $community)
    {
        //目标网站
        $pageorigin = "https://city.anjuke.com/community/?kw=communityname&from=sugg_hot";
        $AveragePrice = '';

        $pagecity = str_replace('city', $citypinyin, $pageorigin);
        $page = str_replace('communityname', $community, $pagecity);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $page);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_REFERER, $page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $ResultCount = str_before(str_after($result, '</em> 小区 <em>'), '</em> 个</span>');

        switch ($ResultCount)
        {
            case '0':

                $AveragePrice = '抱歉，后台数据库升级中，暂时没有您所查询的小区信息，请尝试其他输入';
                //return $AveragePrice;
                break;

            case '1':

                $data = str_before(str_after($result, '<strong>'), '</strong>');
                $actualcommunity = str_before(str_after(str_after($result, '<!--小区列表start-->'), 'alt="'), '"');

                $housecount = str_before(str_after(str_after($result, 'bot-tag'), '>('), ')</a>');

                $buildyear = trim('竣工日期'.str_before(str_after($result, '竣工日期'), '<!-- '));

                $houseaddressori = trim(str_before(str_after($result, '<address>'), '</address>'));
                $houseaddress = '';

                if (false == strpos($houseaddressori, '-'))
                {
                    $houseaddress = $houseaddressori;
                }
                else
                {
                    $houseaddress = str_before($houseaddressori, '-').'］'.str_after($houseaddressori, '］');
                }

                $AveragePrice = '为您查询到：

'.$city.' '.$actualcommunity.'; 地理位置:'.$houseaddress.'； 当前均价为：'.$data.' 元/平方米；现有房源：'.$housecount.' 套；'.$buildyear.'

数据更新日期：'.date('Y年m月d日',time());
        //         //return $AveragePrice;
                break;

            default:
                $StringTemp = $result;
                $ReturnCount = (int)$ResultCount;
                $ReturnHead = '为您查询到：

';
                $ReturnBody = '';

                if ((int)$ResultCount > 5)
                {
                    $ReturnCount = 5;
                    $ReturnHead = '查询结果过多，为您列出最佳匹配的前5项:

';
                }

                for ($i=0; $i <$ReturnCount ; $i++)
                {
                    $PartResult = str_before(str_after($StringTemp, 'li-info'), '<!-- 默认向上箭头，price-down向下箭头，price-no没有价格 -->');

                    $actualcommunity = str_before(str_after($PartResult, 'target="_blank">'), '</a>');

                    $houseaddressori = trim(str_before(str_after($PartResult, '<address>'), '</address>'));
                    $houseaddress = '';
                    if (false == strpos($houseaddressori, '-'))
                    {
                        $houseaddress = $houseaddressori;
                    }
                    else
                    {
                        $houseaddress = str_before($houseaddressori, '-').'］'.str_after($houseaddressori, '］');
                    }

                    $data = str_before(str_after($PartResult, '<strong>'), '</strong>');

                    $housecount = str_before(str_after(str_after($PartResult, 'bot-tag'), '>('), ')</a>');

                    $buildyear = trim('竣工日期'.str_before(str_after($PartResult, '竣工日期'), '<!-- '));

                    $StringTemp = str_after($StringTemp, '<!-- 默认向上箭头，price-down向下箭头，price-no没有价格 -->');

                    if (($ReturnCount - 1) == $i) {
                        $ReturnBody = $ReturnBody.$city.' '.$actualcommunity.'; 地理位置:'.$houseaddress.'； 当前均价为：'.$data.' 元/平方米；现有房源：'.$housecount.' 套；'.$buildyear.'

数据更新日期：'.date('Y年m月d日',time());
                    }
                    else
                    {
                        $ReturnBody = $ReturnBody.$city.' '.$actualcommunity.'; 地理位置:'.$houseaddress.'； 当前均价为：'.$data.' 元/平方米；现有房源：'.$housecount.' 套；'.$buildyear.'

';
                    }
                }

                $AveragePrice = $ReturnHead.$ReturnBody;
                break;
        }

        return $AveragePrice;
    }

    public function EventProc($message)
    {
        $ResultArray = array("AveragePrice" => '',"City"=> '', "Community"=>'', "state" => 'false');

        if ($message['Event'] == 'subscribe')
        {
            $ResultArray['AveragePrice'] = '您好，感谢您的关注！独行侠长期关注股票，基金，楼市等各类财经热点，关注独行侠，让投资理财变的如此简单。

想知道你家现在的房价吗？最近有买房卖房的打算吗？后台回复城市名 小区名，如：南京 白云园，赶紧试一下吧！

商务合作交流请加个人微信：yxp19891026';
            $ResultArray['state'] = 'true';
            //file_put_contents(__DIR__.'/db.txt', json_encode($data));
        }

        elseif ($message['Event'] == 'unsubscribe')
        {
            $OfficialAccountUser = UserLimit::where('OpenID', $message['FromUserName'])->first();

            if ($OfficialAccountUser)
            {
                // 插入数据 返回插入数据的bool值
                 $update_bool = UserLimit::where('OpenID', $message['FromUserName'])->update(['olduser'=>1,'LastMsgType'=>$message['MsgType'], 'UpdateTime'=>$message['CreateTime']]);
            }
        }

        return $ResultArray['AveragePrice'];

    }

    public function CheckCity($CityName)
    {
        $result = true;

        $CitySet = '鞍山;安阳;安庆;安康;安顺;阿坝;阿克苏;阿里;阿拉尔;阿拉善盟;澳门;安达;安丘;安宁;安国;阿尔山;阿图什;安陆;北京;保定;包头;滨州;宝鸡;蚌埠;本溪;北海;巴音郭楞;巴中;巴彦淖尔市;亳州;白银;白城;百色;白山;博尔塔拉;毕节;保山;霸州;北安;北票;泊头;博乐;北流;成都;重庆;长沙;常州;长春;沧州;昌吉;赤峰;常德;郴州;承德;长治;池州;滁州;朝阳;潮州;楚雄;巢湖;昌都;长葛;崇左;常熟;昌邑;常宁;赤壁;岑溪;赤水;慈溪;崇州;大连;东莞;德阳;大理;德州;东营;大庆;丹东;大同;达州;大丰;德宏;定州;迪庆;定西;大兴安岭;东台;邓州;德惠;当阳;东方;儋州;敦化;丹阳;大石桥;灯塔;敦煌;德令哈;大冶;都匀;东兴;东阳;德兴;丹江口;都江堰;东港;登封;鄂尔多斯;恩施;鄂州;恩平;峨眉山;佛山;福州;阜阳;抚顺;阜新;抚州;防城港;肥城;丰城;丰镇;汾阳;阜康;福泉;福清;福安;凤城;福鼎;广州;贵阳;桂林;赣州;广安;贵港;广元;甘孜;甘南;馆陶;果洛;固原;公主岭;高邮;高密;广水;盖州;格尔木;广汉;个旧;桂平;贵溪;高安;高州;高要;古交;高碑店;杭州;合肥;哈尔滨;海口;惠州;邯郸;呼和浩特;黄冈;淮南;黄山;鹤壁;衡阳;湖州;衡水;汉中;淮安;黄石;菏泽;怀化;淮北;葫芦岛;河源;红河;哈密;鹤岗;呼伦贝尔;海北;海东;海南;河池;黑河;和县;贺州;海拉尔;霍邱;和田;黄南;海西;桦甸;鹤山;海林;海城;珲春;黄骅;河间;韩城;华阴;侯马;汉川;华蓥;合山;辉县;化州;霍州;洪湖;洪江;和龙;海门;海宁;海阳;济南;嘉兴;吉林;江门;荆门;锦州;景德镇;吉安;济宁;金华;揭阳;晋中;九江;焦作;晋城;荆州;佳木斯;酒泉;鸡西;济源;金昌;嘉峪关;江阴;靖江;简阳;金坛;津市;界首;吉首;景洪;晋江;建瓯;江山;井冈山;蛟河;胶州;句容;建德;冀州;江油市;昆明;昆山;开封;喀什;克拉玛依;垦利;克孜勒苏;库尔勒;奎屯;凯里;开平;开原;开远;兰州;廊坊;洛阳;柳州;莱芜;六安;泸州;丽江;临沂;聊城;连云港;丽水;娄底;乐山;辽阳;拉萨;临汾;龙岩;漯河;凉山;六盘水;辽源;来宾;临沧;临夏;临猗;林芝;陇南;吕梁;临海;龙海;醴陵;临清;龙口;莱阳;耒阳;溧阳;龙井;临江;凌源;林州;灵宝;潞城;利川;冷水江;涟源;阆中;潞西;兰溪;乐昌;廉江;雷州;陆丰;连州;罗定;临湘;龙泉;乐平;乐陵;莱州;浏阳;老河口;莱西;绵阳;茂名;马鞍山;牡丹江;眉山;梅州;明港;密山;梅河口;满洲里;孟州;麻城;绵竹;明光;汨罗;南京;宁波;南昌;南宁;南通;南充;南阳;宁德;内江;南平;那曲;怒江;南安;宁安;宁国;南康;南雄;讷河;南宫;攀枝花;平顶山;盘锦;萍乡;濮阳;莆田;普洱;平凉;普宁;普兰店;凭祥;邳州;蓬莱;平湖;平度;彭州;青岛;秦皇岛;泉州;曲靖;齐齐哈尔;衢州;清远;钦州;庆阳;黔东南;潜江;清徐;黔南;七台河;黔西南;迁安;青州;清镇;琼海;青铜峡;沁阳;曲阜;邛崃;启东;日照;日喀则;瑞安;汝州;任丘;瑞金;乳山;仁怀;瑞昌;瑞丽;如皋;荣成市;上海;深圳;苏州;石家庄;沈阳;三亚;绍兴;汕头;十堰;三门峡;三明;韶关;商丘;宿迁;绥化;邵阳;遂宁;上饶;四平;石河子;顺德;宿州;松原;沭阳;石嘴山;随州;朔州;汕尾;三沙;商洛;山南;神农架;双鸭山;石狮;三河;尚志;寿光;嵊州;绥芬河;什邡;四会;邵武;松滋;石首;韶山;深州;沙河;天津;太原;泰州;唐山;泰安;台州;铁岭;通辽;铜陵;天水;通化;台山;铜川;吐鲁番;天门;图木舒克;桐城;铜仁;台湾;太仓;泰兴;滕州;洮南;铁力;桐乡;天长;武汉;无锡;威海;潍坊;乌鲁木齐;温州;芜湖;梧州;渭南;乌海;文山;武威;乌兰察布;瓦房店;五家渠;武夷山;吴忠;五指山;温岭;武安;舞钢;五常;卫辉;武冈;文昌;五大连池;乌兰浩特;武穴;万源;吴川;万宁;西安;厦门;徐州;湘潭;襄阳;新乡;信阳;咸阳;邢台;孝感;西宁;许昌;忻州;宣城;咸宁;兴安盟;新余;西双版纳;香港;湘西;仙桃;锡林郭勒盟;新泰;新乐;湘乡;锡林浩特;辛集;新民;兴化;兴平;兴义;宣威;兴宁;项城;信宜;孝义;兴城;新沂;荥阳;新郑;新密;烟台;扬州;宜昌;银川;阳江;永州;玉林;盐城;岳阳;运城;宜春;营口;榆林;宜宾;益阳;义乌;玉溪;伊犁;阳泉;延安;鹰潭;延边;云浮;雅安;阳春;鄢陵;伊春;玉树;乐清;禹州;永新;永康;榆树;永安;宜都;仪征;延吉;扬中;牙克石;伊宁;永济;应城;宜州;英德;玉门;禹城;余姚;偃师;永城;宜兴;宜城;沅江;郑州;珠海;中山;镇江;淄博;张家口;株洲;漳州;湛江;肇庆;枣庄;舟山;遵义;驻马店;自贡;资阳;周口;章丘;张家界;诸城;庄河;正定;张北;张掖;昭通;中卫;赵县;邹城;遵化;肇东;张家港;枝江;招远;钟祥;资兴;樟树;扎兰屯;诸暨;涿州;枣阳;漳平;阿坝州;大邑;金堂;栖霞;淳安;富阳;临安;桐庐;铜梁;丰都;长寿;涪陵;南川;永川;綦江;黔江;万州;江津;合川;普兰店;平阴;济阳;商河;中牟;巩义;宁乡;无极;辛集;元氏;即墨;胶南;周至;户县;蓝田;宁海;象山;肥东;肥西;庐江;长丰;长乐;连江;平潭;安宁;宜良;清镇;辽中;新民;进贤;新建;溧阳;嘉善;莱阳;龙口;招远;白沙县;儋州市;澄迈县;定安;琼中;屯昌;文昌市;农安;陵水;琼海;保亭;东方市;博罗;惠东;龙门;昌邑;永登;榆中;文安;孟津;汝阳;新安;伊川;宜阳;宾阳;横县;海安;启东;如东;安溪;惠安;永春;晋安;上虞;兴化;乐亭;滦南;滦县;迁西;玉田;安丘;昌乐;高密;青州;寿光;丰县;沛县;睢宁;宝应;高邮;江都;仪征;当阳;宜都;枝江;丹阳市;扬中市;邹平;广饶;玉环;肇源;东海;德清;长兴;建湖;当涂;宁国;巴州';

        if (strlen($CityName) > 6)
        {
            $CityName = substr($CityName,0,6);
            //$CityName = iconv("UTF-8","gb2312//IGNORE",$CityName);
        }

        if (false == strpos($CitySet, $CityName))
        {
            $result = false;
        }
        return $result;
    }

    public function CheckProvice($City)
    {
        $result = false;
        $ProviceSet = '山东;江苏;浙江;安徽;福建;江西;广东;广西;海南;河南;湖南;湖北;河北;山西;内蒙古;宁夏;青海;陕西;甘肃;新疆;四川;贵州;云南;西藏;辽宁;吉林;黑龙江;台湾';

        if (strlen($City) > 6)
        {
            $City = substr($City,0,6);
            //$CityName = iconv("UTF-8","gb2312//IGNORE",$CityName);
        }

        if (false != strpos($ProviceSet, $City))
        {
            $result = true;
        }
        return $result;
    }

    public function CheckPinYin($city)
    {
        $PinYin = '';

        switch ($city) {
            case '鞍山' :
            return 'anshan';
            break;
            case '安阳' :
            return 'anyang';
            break;
            case '安庆' :
            return 'anqing';
            break;
            case '安康' :
            return 'ankang';
            break;
            case '安顺' :
            return 'anshun';
            break;
            case '阿坝' :
            return 'aba';
            break;
            case '阿克苏' :
            return 'akesu';
            break;
            case '阿里' :
            return 'ali';
            break;
            case '阿拉尔' :
            return 'alaer';
            break;
            case '阿拉善盟' :
            return 'alashanmeng';
            break;
            case '澳门' :
            return 'aomen';
            break;
            case '安达' :
            return 'anda';
            break;
            case '安丘' :
            return 'anqiu';
            break;
            case '安宁' :
            return 'anning';
            break;
            case '安国' :
            return 'anguo';
            break;
            case '阿尔山' :
            return 'aershan';
            break;
            case '阿图什' :
            return 'atushi';
            break;
            case '安陆' :
            return 'anlu';
            break;
            case '北京' :
            return 'beijing';
            break;
            case '保定' :
            return 'baoding';
            break;
            case '包头' :
            return 'baotou';
            break;
            case '滨州' :
            return 'binzhou';
            break;
            case '宝鸡' :
            return 'baoji';
            break;
            case '蚌埠' :
            return 'bengbu';
            break;
            case '本溪' :
            return 'benxi';
            break;
            case '北海' :
            return 'beihai';
            break;
            case '巴音郭楞' :
            return 'bayinguoleng';
            break;
            case '巴中' :
            return 'bazhong';
            break;
            case '巴彦淖尔市' :
            return 'bayannaoer';
            break;
            case '亳州' :
            return 'bozhou';
            break;
            case '白银' :
            return 'baiyin';
            break;
            case '白城' :
            return 'baicheng';
            break;
            case '百色' :
            return 'baise';
            break;
            case '白山' :
            return 'baishan';
            break;
            case '博尔塔拉' :
            return 'boertala';
            break;
            case '毕节' :
            return 'bijie';
            break;
            case '保山' :
            return 'baoshan';
            break;
            case '霸州' :
            return 'bazh';
            break;
            case '北安' :
            return 'beian';
            break;
            case '北票' :
            return 'beipiao';
            break;
            case '泊头' :
            return 'botou';
            break;
            case '博乐' :
            return 'bole';
            break;
            case '北流' :
            return 'beiliu';
            break;
            case '成都' :
            return 'chengdu';
            break;
            case '重庆' :
            return 'chongqing';
            break;
            case '长沙' :
            return 'cs';
            break;
            case '常州' :
            return 'cz';
            break;
            case '长春' :
            return 'cc';
            break;
            case '沧州' :
            return 'cangzhou';
            break;
            case '昌吉' :
            return 'changji';
            break;
            case '赤峰' :
            return 'chifeng';
            break;
            case '常德' :
            return 'changde';
            break;
            case '郴州' :
            return 'chenzhou';
            break;
            case '承德' :
            return 'chengde';
            break;
            case '长治' :
            return 'changzhi';
            break;
            case '池州' :
            return 'chizhou';
            break;
            case '滁州' :
            return 'chuzhou';
            break;
            case '朝阳' :
            return 'chaoyang';
            break;
            case '潮州' :
            return 'chaozhou';
            break;
            case '楚雄' :
            return 'chuxiong';
            break;
            case '巢湖' :
            return 'chaohu';
            break;
            case '昌都' :
            return 'changdu';
            break;
            case '长葛' :
            return 'changge';
            break;
            case '崇左' :
            return 'chongzuo';
            break;
            case '常熟' :
            return 'changshushi';
            break;
            case '昌邑' :
            return 'changyi';
            break;
            case '常宁' :
            return 'changning';
            break;
            case '赤壁' :
            return 'chibi';
            break;
            case '岑溪' :
            return 'cengxi';
            break;
            case '赤水' :
            return 'chishui';
            break;
            case '慈溪' :
            return 'cixi';
            break;
            case '崇州' :
            return 'chongzhou';
            break;
            case '大连' :
            return 'dalian';
            break;
            case '东莞' :
            return 'dg';
            break;
            case '德阳' :
            return 'deyang';
            break;
            case '大理' :
            return 'dali';
            break;
            case '德州' :
            return 'dezhou';
            break;
            case '东营' :
            return 'dongying';
            break;
            case '大庆' :
            return 'daqing';
            break;
            case '丹东' :
            return 'dandong';
            break;
            case '大同' :
            return 'datong';
            break;
            case '达州' :
            return 'dazhou';
            break;
            case '大丰' :
            return 'dafeng';
            break;
            case '德宏' :
            return 'dehong';
            break;
            case '定州' :
            return 'dingzhou';
            break;
            case '迪庆' :
            return 'diqing';
            break;
            case '定西' :
            return 'dingxi';
            break;
            case '大兴安岭' :
            return 'dxanling';
            break;
            case '东台' :
            return 'dongtai';
            break;
            case '邓州' :
            return 'dengzhou';
            break;
            case '德惠' :
            return 'dehui';
            break;
            case '当阳' :
            return 'dangyang';
            break;
            case '东方' :
            return 'dongfang';
            break;
            case '儋州' :
            return 'danzhou';
            break;
            case '敦化' :
            return 'dunhuashi';
            break;
            case '丹阳' :
            return 'danyang';
            break;
            case '大石桥' :
            return 'dashiqiao';
            break;
            case '灯塔' :
            return 'dengta';
            break;
            case '敦煌' :
            return 'dunhuang';
            break;
            case '德令哈' :
            return 'delingha';
            break;
            case '大冶' :
            return 'daye';
            break;
            case '都匀' :
            return 'duyun';
            break;
            case '东兴' :
            return 'dongxing';
            break;
            case '东阳' :
            return 'dongyang';
            break;
            case '德兴' :
            return 'dexing';
            break;
            case '丹江口' :
            return 'danjiangkou';
            break;
            case '都江堰' :
            return 'dujiangyan';
            break;
            case '东港' :
            return 'donggang';
            break;
            case '登封' :
            return 'dengfeng';
            break;
            case '鄂尔多斯' :
            return 'eerduosi';
            break;
            case '恩施' :
            return 'enshi';
            break;
            case '鄂州' :
            return 'ezhou';
            break;
            case '恩平' :
            return 'enping';
            break;
            case '峨眉山' :
            return 'emeishan';
            break;
            case '佛山' :
            return 'foshan';
            break;
            case '福州' :
            return 'fz';
            break;
            case '阜阳' :
            return 'fuyang';
            break;
            case '抚顺' :
            return 'fushun';
            break;
            case '阜新' :
            return 'fuxin';
            break;
            case '抚州' :
            return 'fuzhoushi';
            break;
            case '防城港' :
            return 'fangchenggang';
            break;
            case '肥城' :
            return 'feichengshi';
            break;
            case '丰城' :
            return 'fengchengshi';
            break;
            case '丰镇' :
            return 'fengzhen';
            break;
            case '汾阳' :
            return 'fenyang';
            break;
            case '阜康' :
            return 'fukang';
            break;
            case '福泉' :
            return 'fuquan';
            break;
            case '福清' :
            return 'fuqing';
            break;
            case '福安' :
            return 'fuan';
            break;
            case '凤城' :
            return 'fengcheng';
            break;
            case '福鼎' :
            return 'fuding';
            break;
            case '广州' :
            return 'guangzhou';
            break;
            case '贵阳' :
            return 'gy';
            break;
            case '桂林' :
            return 'guilin';
            break;
            case '赣州' :
            return 'ganzhou';
            break;
            case '广安' :
            return 'guangan';
            break;
            case '贵港' :
            return 'guigang';
            break;
            case '广元' :
            return 'guangyuan';
            break;
            case '甘孜' :
            return 'ganzi';
            break;
            case '甘南' :
            return 'gannan';
            break;
            case '馆陶' :
            return 'guantao';
            break;
            case '果洛' :
            return 'guoluo';
            break;
            case '固原' :
            return 'guyuan';
            break;
            case '公主岭' :
            return 'gongzhulingshi';
            break;
            case '高邮' :
            return 'gaoyou';
            break;
            case '高密' :
            return 'gaomishi';
            break;
            case '广水' :
            return 'guangshui';
            break;
            case '盖州' :
            return 'gaizhou';
            break;
            case '格尔木' :
            return 'geermu';
            break;
            case '广汉' :
            return 'guanghan';
            break;
            case '个旧' :
            return 'gejiu';
            break;
            case '桂平' :
            return 'guiping';
            break;
            case '贵溪' :
            return 'guixi';
            break;
            case '高安' :
            return 'gaoanshi';
            break;
            case '高州' :
            return 'gaozhou';
            break;
            case '高要' :
            return 'gaoyaoshi';
            break;
            case '古交' :
            return 'gujiao';
            break;
            case '高碑店' :
            return 'gaobeidian';
            break;
            case '杭州' :
            return 'hangzhou';
            break;
            case '合肥' :
            return 'hf';
            break;
            case '哈尔滨' :
            return 'heb';
            break;
            case '海口' :
            return 'haikou';
            break;
            case '惠州' :
            return 'huizhou';
            break;
            case '邯郸' :
            return 'handan';
            break;
            case '呼和浩特' :
            return 'huhehaote';
            break;
            case '黄冈' :
            return 'huanggang';
            break;
            case '淮南' :
            return 'huainan';
            break;
            case '黄山' :
            return 'huangshan';
            break;
            case '鹤壁' :
            return 'hebi';
            break;
            case '衡阳' :
            return 'hengyang';
            break;
            case '湖州' :
            return 'huzhou';
            break;
            case '衡水' :
            return 'hengshui';
            break;
            case '汉中' :
            return 'hanzhong';
            break;
            case '淮安' :
            return 'huaian';
            break;
            case '黄石' :
            return 'huangshi';
            break;
            case '菏泽' :
            return 'heze';
            break;
            case '怀化' :
            return 'huaihua';
            break;
            case '淮北' :
            return 'huaibei';
            break;
            case '葫芦岛' :
            return 'huludao';
            break;
            case '河源' :
            return 'heyuan';
            break;
            case '红河' :
            return 'honghe';
            break;
            case '哈密' :
            return 'hami';
            break;
            case '鹤岗' :
            return 'hegang';
            break;
            case '呼伦贝尔' :
            return 'hulunbeier';
            break;
            case '海北' :
            return 'haibei';
            break;
            case '海东' :
            return 'haidong';
            break;
            case '海南' :
            return 'hainan';
            break;
            case '河池' :
            return 'hechi';
            break;
            case '黑河' :
            return 'heihe';
            break;
            case '和县' :
            return 'hexian';
            break;
            case '贺州' :
            return 'hezhou';
            break;
            case '海拉尔' :
            return 'hailaer';
            break;
            case '霍邱' :
            return 'huoqiu';
            break;
            case '和田' :
            return 'hetian';
            break;
            case '黄南' :
            return 'huangnan';
            break;
            case '海西' :
            return 'hexi';
            break;
            case '桦甸' :
            return 'huadian';
            break;
            case '鹤山' :
            return 'heshan';
            break;
            case '海林' :
            return 'hailin';
            break;
            case '海城' :
            return 'haicheng';
            break;
            case '珲春' :
            return 'hunchun';
            break;
            case '黄骅' :
            return 'huanghua';
            break;
            case '河间' :
            return 'hejian';
            break;
            case '韩城' :
            return 'hancheng';
            break;
            case '华阴' :
            return 'huaying';
            break;
            case '侯马' :
            return 'houma';
            break;
            case '汉川' :
            return 'hanchuan';
            break;
            case '华蓥' :
            return 'huaying2';
            break;
            case '合山' :
            return 'heshanshi';
            break;
            case '辉县' :
            return 'huixian';
            break;
            case '化州' :
            return 'huazhou';
            break;
            case '霍州' :
            return 'huozhou';
            break;
            case '洪湖' :
            return 'honghu';
            break;
            case '洪江' :
            return 'hongjiang';
            break;
            case '和龙' :
            return 'helong';
            break;
            case '海门' :
            return 'haimen';
            break;
            case '海宁' :
            return 'haining';
            break;
            case '海阳' :
            return 'haiyang';
            break;
            case '济南' :
            return 'jinan';
            break;
            case '嘉兴' :
            return 'jx';
            break;
            case '吉林' :
            return 'jilin';
            break;
            case '江门' :
            return 'jiangmen';
            break;
            case '荆门' :
            return 'jingmen';
            break;
            case '锦州' :
            return 'jinzhou';
            break;
            case '景德镇' :
            return 'jingdezhen';
            break;
            case '吉安' :
            return 'jian';
            break;
            case '济宁' :
            return 'jining';
            break;
            case '金华' :
            return 'jinhua';
            break;
            case '揭阳' :
            return 'jieyang';
            break;
            case '晋中' :
            return 'jinzhong';
            break;
            case '九江' :
            return 'jiujiang';
            break;
            case '焦作' :
            return 'jiaozuo';
            break;
            case '晋城' :
            return 'jincheng';
            break;
            case '荆州' :
            return 'jingzhou';
            break;
            case '佳木斯' :
            return 'jiamusi';
            break;
            case '酒泉' :
            return 'jiuquan';
            break;
            case '鸡西' :
            return 'jixi';
            break;
            case '济源' :
            return 'jiyuan';
            break;
            case '金昌' :
            return 'jinchang';
            break;
            case '嘉峪关' :
            return 'jiayuguan';
            break;
            case '江阴' :
            return 'jiangyin';
            break;
            case '靖江' :
            return 'jingjiang';
            break;
            case '简阳' :
            return 'jianyangshi';
            break;
            case '金坛' :
            return 'jintan';
            break;
            case '津市' :
            return 'jinshi';
            break;
            case '界首' :
            return 'jieshou';
            break;
            case '吉首' :
            return 'jishou';
            break;
            case '景洪' :
            return 'jinghong';
            break;
            case '晋江' :
            return 'jinjiangshi';
            break;
            case '建瓯' :
            return 'jianou';
            break;
            case '江山' :
            return 'jiangshan';
            break;
            case '井冈山' :
            return 'jinggangshan';
            break;
            case '蛟河' :
            return 'jiaohe';
            break;
            case '胶州' :
            return 'jiaozhoux';
            break;
            case '句容' :
            return 'jurong';
            break;
            case '建德' :
            return 'jiande';
            break;
            case '冀州' :
            return 'jizhoushi';
            break;
            case '江油市' :
            return 'jiangyoushi';
            break;
            case '昆明' :
            return 'km';
            break;
            case '昆山' :
            return 'ks';
            break;
            case '开封' :
            return 'kaifeng';
            break;
            case '喀什' :
            return 'kashi';
            break;
            case '克拉玛依' :
            return 'kelamayi';
            break;
            case '垦利' :
            return 'kenli';
            break;
            case '克孜勒苏' :
            return 'lezilesu';
            break;
            case '库尔勒' :
            return 'kuerle';
            break;
            case '奎屯' :
            return 'kuitun';
            break;
            case '凯里' :
            return 'kaili';
            break;
            case '开平' :
            return 'kaiping';
            break;
            case '开原' :
            return 'kaiyuan';
            break;
            case '开远' :
            return 'kaiyuan2';
            break;
            case '兰州' :
            return 'lanzhou';
            break;
            case '廊坊' :
            return 'langfang';
            break;
            case '洛阳' :
            return 'luoyang';
            break;
            case '柳州' :
            return 'liuzhou';
            break;
            case '莱芜' :
            return 'laiwu';
            break;
            case '六安' :
            return 'luan';
            break;
            case '泸州' :
            return 'luzhou';
            break;
            case '丽江' :
            return 'lijiang';
            break;
            case '临沂' :
            return 'linyi';
            break;
            case '聊城' :
            return 'liaocheng';
            break;
            case '连云港' :
            return 'lianyungang';
            break;
            case '丽水' :
            return 'lishui';
            break;
            case '娄底' :
            return 'loudi';
            break;
            case '乐山' :
            return 'leshan';
            break;
            case '辽阳' :
            return 'liaoyang';
            break;
            case '拉萨' :
            return 'lasa';
            break;
            case '临汾' :
            return 'linfen';
            break;
            case '龙岩' :
            return 'longyan';
            break;
            case '漯河' :
            return 'luohe';
            break;
            case '凉山' :
            return 'liangshan';
            break;
            case '六盘水' :
            return 'liupanshui';
            break;
            case '辽源' :
            return 'liaoyuan';
            break;
            case '来宾' :
            return 'laibin';
            break;
            case '临沧' :
            return 'lingcang';
            break;
            case '临夏' :
            return 'linxia';
            break;
            case '临猗' :
            return 'linyishi';
            break;
            case '林芝' :
            return 'linzhi';
            break;
            case '陇南' :
            return 'longnan';
            break;
            case '吕梁' :
            return 'lvliang';
            break;
            case '临海' :
            return 'linhaishi';
            break;
            case '龙海' :
            return 'longhaishi';
            break;
            case '醴陵' :
            return 'lilingshi';
            break;
            case '临清' :
            return 'linqing';
            break;
            case '龙口' :
            return 'longkou';
            break;
            case '莱阳' :
            return 'laiyang';
            break;
            case '耒阳' :
            return 'leiyang';
            break;
            case '溧阳' :
            return 'liyang';
            break;
            case '龙井' :
            return 'longjing';
            break;
            case '临江' :
            return 'linjiang';
            break;
            case '凌源' :
            return 'lingyuan';
            break;
            case '林州' :
            return 'linzhoushi';
            break;
            case '灵宝' :
            return 'lingbao';
            break;
            case '潞城' :
            return 'lucheng';
            break;
            case '利川' :
            return 'lichuan';
            break;
            case '冷水江' :
            return 'lengshuijiang';
            break;
            case '涟源' :
            return 'lianyuan';
            break;
            case '阆中' :
            return 'langzhong';
            break;
            case '潞西' :
            return 'luxishi';
            break;
            case '兰溪' :
            return 'lanxi';
            break;
            case '乐昌' :
            return 'lechang';
            break;
            case '廉江' :
            return 'lianjiangshi';
            break;
            case '雷州' :
            return 'leizhou';
            break;
            case '陆丰' :
            return 'lufengshi';
            break;
            case '连州' :
            return 'lianzhou';
            break;
            case '罗定' :
            return 'luoding';
            break;
            case '临湘' :
            return 'linxiang';
            break;
            case '龙泉' :
            return 'longquan';
            break;
            case '乐平' :
            return 'leping';
            break;
            case '乐陵' :
            return 'laoling';
            break;
            case '莱州' :
            return 'laizhoushi';
            break;
            case '浏阳' :
            return 'liuyang';
            break;
            case '老河口' :
            return 'laohekou';
            break;
            case '莱西' :
            return 'laixi';
            break;
            case '绵阳' :
            return 'mianyang';
            break;
            case '茂名' :
            return 'maoming';
            break;
            case '马鞍山' :
            return 'maanshan';
            break;
            case '牡丹江' :
            return 'mudanjiang';
            break;
            case '眉山' :
            return 'meishan';
            break;
            case '梅州' :
            return 'meizhou';
            break;
            case '明港' :
            return 'minggang';
            break;
            case '密山' :
            return 'mishan';
            break;
            case '梅河口' :
            return 'meihekou';
            break;
            case '满洲里' :
            return 'manzhouli';
            break;
            case '孟州' :
            return 'mengzhou';
            break;
            case '麻城' :
            return 'macheng';
            break;
            case '绵竹' :
            return 'mianzhu';
            break;
            case '明光' :
            return 'mingguang';
            break;
            case '汨罗' :
            return 'miluo';
            break;
            case '南京' :
            return 'nanjing';
            break;
            case '宁波' :
            return 'nb';
            break;
            case '南昌' :
            return 'nc';
            break;
            case '南宁' :
            return 'nanning';
            break;
            case '南通' :
            return 'nantong';
            break;
            case '南充' :
            return 'nanchong';
            break;
            case '南阳' :
            return 'nanyang';
            break;
            case '宁德' :
            return 'ningde';
            break;
            case '内江' :
            return 'neijiang';
            break;
            case '南平' :
            return 'nanping';
            break;
            case '那曲' :
            return 'naqu';
            break;
            case '怒江' :
            return 'nujiang';
            break;
            case '南安' :
            return 'nananshi';
            break;
            case '宁安' :
            return 'ninganshi';
            break;
            case '宁国' :
            return 'ningguo';
            break;
            case '南康' :
            return 'nankang';
            break;
            case '南雄' :
            return 'nanxiong';
            break;
            case '讷河' :
            return 'nehe';
            break;
            case '南宫' :
            return 'nangong';
            break;
            case '攀枝花' :
            return 'panzhihua';
            break;
            case '平顶山' :
            return 'pingdingsha';
            break;
            case '盘锦' :
            return 'panjin';
            break;
            case '萍乡' :
            return 'pingxiang';
            break;
            case '濮阳' :
            return 'puyang';
            break;
            case '莆田' :
            return 'putian';
            break;
            case '普洱' :
            return 'puer';
            break;
            case '平凉' :
            return 'pingliang';
            break;
            case '普宁' :
            return 'puning';
            break;
            case '普兰店' :
            return 'pulandian';
            break;
            case '凭祥' :
            return 'pingxiangshi';
            break;
            case '邳州' :
            return 'pizhou';
            break;
            case '蓬莱' :
            return 'penglaishi';
            break;
            case '平湖' :
            return 'pinghu';
            break;
            case '平度' :
            return 'pingdu';
            break;
            case '彭州' :
            return 'pengzhou';
            break;
            case '青岛' :
            return 'qd';
            break;
            case '秦皇岛' :
            return 'qinhuangdao';
            break;
            case '泉州' :
            return 'quanzhou';
            break;
            case '曲靖' :
            return 'qujing';
            break;
            case '齐齐哈尔' :
            return 'qiqihaer';
            break;
            case '衢州' :
            return 'quzhou';
            break;
            case '清远' :
            return 'qingyuan';
            break;
            case '钦州' :
            return 'qinzhou';
            break;
            case '庆阳' :
            return 'qingyang';
            break;
            case '黔东南' :
            return 'qiandongnan';
            break;
            case '潜江' :
            return 'qianjiang';
            break;
            case '清徐' :
            return 'qingxu';
            break;
            case '黔南' :
            return 'qiannan';
            break;
            case '七台河' :
            return 'qitaihe';
            break;
            case '黔西南' :
            return 'qianxinan';
            break;
            case '迁安' :
            return 'qiananshi';
            break;
            case '青州' :
            return 'qingzhoushi';
            break;
            case '清镇' :
            return 'qingzhen';
            break;
            case '琼海' :
            return 'qionghai';
            break;
            case '青铜峡' :
            return 'qingtongxia';
            break;
            case '沁阳' :
            return 'qinyangshi';
            break;
            case '曲阜' :
            return 'qufu';
            break;
            case '邛崃' :
            return 'qionglai';
            break;
            case '启东' :
            return 'qidong';
            break;
            case '日照' :
            return 'rizhao';
            break;
            case '日喀则' :
            return 'rikeze';
            break;
            case '瑞安' :
            return 'ruian';
            break;
            case '汝州' :
            return 'ruzhoushi';
            break;
            case '任丘' :
            return 'renqiushi';
            break;
            case '瑞金' :
            return 'ruijin';
            break;
            case '乳山' :
            return 'rushan';
            break;
            case '仁怀' :
            return 'renhuai';
            break;
            case '瑞昌' :
            return 'ruichang';
            break;
            case '瑞丽' :
            return 'ruili';
            break;
            case '如皋' :
            return 'rugao';
            break;
            case '荣成市' :
            return 'rongchengshi';
            break;
            case '上海' :
            return 'shanghai';
            break;
            case '深圳' :
            return 'shenzhen';
            break;
            case '苏州' :
            return 'suzhou';
            break;
            case '石家庄' :
            return 'sjz';
            break;
            case '沈阳' :
            return 'sy';
            break;
            case '三亚' :
            return 'sanya';
            break;
            case '绍兴' :
            return 'shaoxing';
            break;
            case '汕头' :
            return 'shantou';
            break;
            case '十堰' :
            return 'shiyan';
            break;
            case '三门峡' :
            return 'sanmenxia';
            break;
            case '三明' :
            return 'sanming';
            break;
            case '韶关' :
            return 'shaoguan';
            break;
            case '商丘' :
            return 'shangqiu';
            break;
            case '宿迁' :
            return 'suqian';
            break;
            case '绥化' :
            return 'suihua';
            break;
            case '邵阳' :
            return 'shaoyang';
            break;
            case '遂宁' :
            return 'suining';
            break;
            case '上饶' :
            return 'shangrao';
            break;
            case '四平' :
            return 'siping';
            break;
            case '石河子' :
            return 'shihezi';
            break;
            case '顺德' :
            return 'shunde';
            break;
            case '宿州' :
            return 'suzhoushi';
            break;
            case '松原' :
            return 'songyuan';
            break;
            case '沭阳' :
            return 'shuyang';
            break;
            case '石嘴山' :
            return 'shizuishan';
            break;
            case '随州' :
            return 'suizhou';
            break;
            case '朔州' :
            return 'shuozhou';
            break;
            case '汕尾' :
            return 'shanwei';
            break;
            case '三沙' :
            return 'sansha';
            break;
            case '商洛' :
            return 'shangluo';
            break;
            case '山南' :
            return 'shannan';
            break;
            case '神农架' :
            return 'shennongjia';
            break;
            case '双鸭山' :
            return 'shuangyashan';
            break;
            case '石狮' :
            return 'shishi';
            break;
            case '三河' :
            return 'sanheshi';
            break;
            case '尚志' :
            return 'shangzhi';
            break;
            case '寿光' :
            return 'shouguang';
            break;
            case '嵊州' :
            return 'shengzhou';
            break;
            case '绥芬河' :
            return 'suifenhe';
            break;
            case '什邡' :
            return 'shifang';
            break;
            case '四会' :
            return 'sihui';
            break;
            case '邵武' :
            return 'shaowu';
            break;
            case '松滋' :
            return 'songzi';
            break;
            case '石首' :
            return 'shishou';
            break;
            case '韶山' :
            return 'shaoshan';
            break;
            case '深州' :
            return 'shenzhou';
            break;
            case '沙河' :
            return 'shahe';
            break;
            case '天津' :
            return 'tianjin';
            break;
            case '太原' :
            return 'ty';
            break;
            case '泰州' :
            return 'taizhou';
            break;
            case '唐山' :
            return 'tangshan';
            break;
            case '泰安' :
            return 'taian';
            break;
            case '台州' :
            return 'taiz';
            break;
            case '铁岭' :
            return 'tieling';
            break;
            case '通辽' :
            return 'tongliao';
            break;
            case '铜陵' :
            return 'tongling';
            break;
            case '天水' :
            return 'tianshui';
            break;
            case '通化' :
            return 'tonghua';
            break;
            case '台山' :
            return 'taishan';
            break;
            case '铜川' :
            return 'tongchuan';
            break;
            case '吐鲁番' :
            return 'tulufan';
            break;
            case '天门' :
            return 'tianmen';
            break;
            case '图木舒克' :
            return 'tumushuke';
            break;
            case '桐城' :
            return 'tongcheng';
            break;
            case '铜仁' :
            return 'tongren';
            break;
            case '台湾' :
            return 'taiwan';
            break;
            case '太仓' :
            return 'taicang';
            break;
            case '泰兴' :
            return 'taixing';
            break;
            case '滕州' :
            return 'tengzhoushi';
            break;
            case '洮南' :
            return 'taonan';
            break;
            case '铁力' :
            return 'tieli';
            break;
            case '桐乡' :
            return 'tongxiang';
            break;
            case '天长' :
            return 'tianchang';
            break;
            case '武汉' :
            return 'wuhan';
            break;
            case '无锡' :
            return 'wuxi';
            break;
            case '威海' :
            return 'weihai';
            break;
            case '潍坊' :
            return 'weifang';
            break;
            case '乌鲁木齐' :
            return 'wulumuqi';
            break;
            case '温州' :
            return 'wenzhou';
            break;
            case '芜湖' :
            return 'wuhu';
            break;
            case '梧州' :
            return 'wuzhou';
            break;
            case '渭南' :
            return 'weinan';
            break;
            case '乌海' :
            return 'wuhai';
            break;
            case '文山' :
            return 'wenshan';
            break;
            case '武威' :
            return 'wuwei';
            break;
            case '乌兰察布' :
            return 'wulanchabu';
            break;
            case '瓦房店' :
            return 'wafangdian';
            break;
            case '五家渠' :
            return 'wujiaqu';
            break;
            case '武夷山' :
            return 'wuyishan';
            break;
            case '吴忠' :
            return 'wuzhong';
            break;
            case '五指山' :
            return 'wuzhishan';
            break;
            case '温岭' :
            return 'wnelingshi';
            break;
            case '武安' :
            return 'wuanshi';
            break;
            case '舞钢' :
            return 'wugang';
            break;
            case '五常' :
            return 'wuchang';
            break;
            case '卫辉' :
            return 'weihui';
            break;
            case '武冈' :
            return 'wugangshi';
            break;
            case '文昌' :
            return 'wenchang';
            break;
            case '五大连池' :
            return 'wudalianchi';
            break;
            case '乌兰浩特' :
            return 'wulanhaote';
            break;
            case '武穴' :
            return 'wuxue';
            break;
            case '万源' :
            return 'wanyuan';
            break;
            case '吴川' :
            return 'wuchuan';
            break;
            case '万宁' :
            return 'wanning';
            break;
            case '西安' :
            return 'xa';
            break;
            case '厦门' :
            return 'xm';
            break;
            case '徐州' :
            return 'xuzhou';
            break;
            case '湘潭' :
            return 'xiangtan';
            break;
            case '襄阳' :
            return 'xiangyang';
            break;
            case '新乡' :
            return 'xinxiang';
            break;
            case '信阳' :
            return 'xinyang';
            break;
            case '咸阳' :
            return 'xianyang';
            break;
            case '邢台' :
            return 'xingtai';
            break;
            case '孝感' :
            return 'xiaogan';
            break;
            case '西宁' :
            return 'xining';
            break;
            case '许昌' :
            return 'xuchang';
            break;
            case '忻州' :
            return 'xinzhou';
            break;
            case '宣城' :
            return 'xuancheng';
            break;
            case '咸宁' :
            return 'xianning';
            break;
            case '兴安盟' :
            return 'xinganmeng';
            break;
            case '新余' :
            return 'xinyu';
            break;
            case '西双版纳' :
            return 'bannan';
            break;
            case '香港' :
            return 'xianggang';
            break;
            case '湘西' :
            return 'xiangxi';
            break;
            case '仙桃' :
            return 'xiantao';
            break;
            case '锡林郭勒盟' :
            return 'xilinguole';
            break;
            case '新泰' :
            return 'xintaishi';
            break;
            case '新乐' :
            return 'xinle';
            break;
            case '湘乡' :
            return 'xiangxiang';
            break;
            case '锡林浩特' :
            return 'xilinhaote';
            break;
            case '辛集' :
            return 'xinji';
            break;
            case '新民' :
            return 'xinmin';
            break;
            case '兴化' :
            return 'xinghua';
            break;
            case '兴平' :
            return 'xingping';
            break;
            case '兴义' :
            return 'xingyi';
            break;
            case '宣威' :
            return 'xuanwei';
            break;
            case '兴宁' :
            return 'xingning';
            break;
            case '项城' :
            return 'xiangcheng';
            break;
            case '信宜' :
            return 'xinyi';
            break;
            case '孝义' :
            return 'xiaoyi';
            break;
            case '兴城' :
            return 'xingcheng';
            break;
            case '新沂' :
            return 'xinyishi';
            break;
            case '荥阳' :
            return 'xingyang';
            break;
            case '新郑' :
            return 'xinzheng';
            break;
            case '新密' :
            return 'xinmi';
            break;
            case '烟台' :
            return 'yt';
            break;
            case '扬州' :
            return 'yangzhou';
            break;
            case '宜昌' :
            return 'yichang';
            break;
            case '银川' :
            return 'yinchuan';
            break;
            case '阳江' :
            return 'yangjiang';
            break;
            case '永州' :
            return 'yongzhou';
            break;
            case '玉林' :
            return 'yulinshi';
            break;
            case '盐城' :
            return 'yancheng';
            break;
            case '岳阳' :
            return 'yueyang';
            break;
            case '运城' :
            return 'yuncheng';
            break;
            case '宜春' :
            return 'yichun';
            break;
            case '营口' :
            return 'yingkou';
            break;
            case '榆林' :
            return 'yulin';
            break;
            case '宜宾' :
            return 'yibin';
            break;
            case '益阳' :
            return 'yiyang';
            break;
            case '义乌' :
            return 'yiwu';
            break;
            case '玉溪' :
            return 'yuxi';
            break;
            case '伊犁' :
            return 'yili';
            break;
            case '阳泉' :
            return 'yangquan';
            break;
            case '延安' :
            return 'yanan';
            break;
            case '鹰潭' :
            return 'yingtan';
            break;
            case '延边' :
            return 'yanbian';
            break;
            case '云浮' :
            return 'yufu';
            break;
            case '雅安' :
            return 'yaan';
            break;
            case '阳春' :
            return 'yangchun';
            break;
            case '鄢陵' :
            return 'yanling';
            break;
            case '伊春' :
            return 'yichunshi';
            break;
            case '玉树' :
            return 'yushu';
            break;
            case '乐清' :
            return 'yueqing';
            break;
            case '禹州' :
            return 'yuzhou';
            break;
            case '永新' :
            return 'yongxin';
            break;
            case '永康' :
            return 'yongkangshi';
            break;
            case '榆树' :
            return 'yushushi';
            break;
            case '永安' :
            return 'yongan';
            break;
            case '宜都' :
            return 'yidou';
            break;
            case '仪征' :
            return 'yizheng';
            break;
            case '延吉' :
            return 'yanji';
            break;
            case '扬中' :
            return 'yangzhong';
            break;
            case '牙克石' :
            return 'yakeshi';
            break;
            case '伊宁' :
            return 'yining';
            break;
            case '永济' :
            return 'yongji';
            break;
            case '应城' :
            return 'yingchengshi';
            break;
            case '宜州' :
            return 'yizhou';
            break;
            case '英德' :
            return 'yingde';
            break;
            case '玉门' :
            return 'yumen';
            break;
            case '禹城' :
            return 'yucheng';
            break;
            case '余姚' :
            return 'yuyao';
            break;
            case '偃师' :
            return 'yanshishi';
            break;
            case '永城' :
            return 'yongcheng';
            break;
            case '宜兴' :
            return 'yixing';
            break;
            case '宜城' :
            return 'yicheng';
            break;
            case '沅江' :
            return 'yuanjiang';
            break;
            case '郑州' :
            return 'zhengzhou';
            break;
            case '珠海' :
            return 'zh';
            break;
            case '中山' :
            return 'zs';
            break;
            case '镇江' :
            return 'zhenjiang';
            break;
            case '淄博' :
            return 'zibo';
            break;
            case '张家口' :
            return 'zhangjiakou';
            break;
            case '株洲' :
            return 'zhuzhou';
            break;
            case '漳州' :
            return 'zhangzhou';
            break;
            case '湛江' :
            return 'zhanjiang';
            break;
            case '肇庆' :
            return 'zhaoqing';
            break;
            case '枣庄' :
            return 'zaozhuang';
            break;
            case '舟山' :
            return 'zhoushan';
            break;
            case '遵义' :
            return 'zunyi';
            break;
            case '驻马店' :
            return 'zhumadian';
            break;
            case '自贡' :
            return 'zigong';
            break;
            case '资阳' :
            return 'ziyang';
            break;
            case '周口' :
            return 'zhoukou';
            break;
            case '章丘' :
            return 'zhangqiu';
            break;
            case '张家界' :
            return 'zhangjiajie';
            break;
            case '诸城' :
            return 'zhucheng';
            break;
            case '庄河' :
            return 'zhuanghe';
            break;
            case '正定' :
            return 'zhengding';
            break;
            case '张北' :
            return 'zhangbei';
            break;
            case '张掖' :
            return 'zhangye';
            break;
            case '昭通' :
            return 'zhaotong';
            break;
            case '中卫' :
            return 'weizhong';
            break;
            case '赵县' :
            return 'zhaoxian';
            break;
            case '邹城' :
            return 'zouchengshi';
            break;
            case '遵化' :
            return 'zunhua';
            break;
            case '肇东' :
            return 'zhaodong';
            break;
            case '张家港' :
            return 'zhangjiagang';
            break;
            case '枝江' :
            return 'zhijiang';
            break;
            case '招远' :
            return 'zhaoyuanshi';
            break;
            case '钟祥' :
            return 'zhongxiang';
            break;
            case '资兴' :
            return 'zixing';
            break;
            case '樟树' :
            return 'zhangshu';
            break;
            case '扎兰屯' :
            return 'zhalandun';
            break;
            case '诸暨' :
            return 'zhuji';
            break;
            case '涿州' :
            return 'zhuozhoushi';
            break;
            case '枣阳' :
            return 'zaoyangshi';
            break;
            case '漳平' :
            return 'zhangping';
            break;
            case '阿坝州' :
            return 'chengdu';
            break;
            case '大邑' :
            return 'chengdu';
            break;
            case '金堂' :
            return 'chengdu';
            break;
            case '栖霞' :
            return 'nanjing';
            break;
            case '淳安' :
            return 'hangzhou';
            break;
            case '富阳' :
            return 'hangzhou';
            break;
            case '临安' :
            return 'hangzhou';
            break;
            case '桐庐' :
            return 'hangzhou';
            break;
            case '铜梁' :
            return 'chongqing';
            break;
            case '丰都' :
            return 'chongqing';
            break;
            case '长寿' :
            return 'chongqing';
            break;
            case '涪陵' :
            return 'chongqing';
            break;
            case '南川' :
            return 'chongqing';
            break;
            case '永川' :
            return 'chongqing';
            break;
            case '綦江' :
            return 'chongqing';
            break;
            case '黔江' :
            return 'chongqing';
            break;
            case '万州' :
            return 'chongqing';
            break;
            case '江津' :
            return 'chongqing';
            break;
            case '合川' :
            return 'chongqing';
            break;
            case '普兰店' :
            return 'dalian';
            break;
            case '平阴' :
            return 'jinan';
            break;
            case '济阳' :
            return 'jinan';
            break;
            case '商河' :
            return 'jinan';
            break;
            case '中牟' :
            return 'zhengzhou';
            break;
            case '巩义' :
            return 'zhengzhou';
            break;
            case '宁乡' :
            return 'cs';
            break;
            case '无极' :
            return 'sjz';
            break;
            case '辛集' :
            return 'sjz';
            break;
            case '元氏' :
            return 'sjz';
            break;
            case '即墨' :
            return 'qd';
            break;
            case '胶南' :
            return 'qd';
            break;
            case '周至' :
            return 'xa';
            break;
            case '户县' :
            return 'xa';
            break;
            case '蓝田' :
            return 'xa';
            break;
            case '宁海' :
            return 'nb';
            break;
            case '象山' :
            return 'nb';
            break;
            case '肥东' :
            return 'hf';
            break;
            case '肥西' :
            return 'hf';
            break;
            case '庐江' :
            return 'hf';
            break;
            case '长丰' :
            return 'hf';
            break;
            case '长乐' :
            return 'fz';
            break;
            case '连江' :
            return 'fz';
            break;
            case '平潭' :
            return 'fz';
            break;
            case '安宁' :
            return 'km';
            break;
            case '宜良' :
            return 'km';
            break;
            case '清镇' :
            return 'gy';
            break;
            case '辽中' :
            return 'sy';
            break;
            case '新民' :
            return 'sy';
            break;
            case '进贤' :
            return 'nc';
            break;
            case '新建' :
            return 'nc';
            break;
            case '溧阳' :
            return 'cz';
            break;
            case '嘉善' :
            return 'jx';
            break;
            case '莱阳' :
            return 'yt';
            break;
            case '龙口' :
            return 'yt';
            break;
            case '招远' :
            return 'yt';
            break;
            case '白沙县' :
            return 'haikou';
            break;
            case '儋州市' :
            return 'haikou';
            break;
            case '澄迈县' :
            return 'haikou';
            break;
            case '定安' :
            return 'haikou';
            break;
            case '琼中' :
            return 'haikou';
            break;
            case '屯昌' :
            return 'haikou';
            break;
            case '文昌市' :
            return 'haikou';
            break;
            case '农安' :
            return 'cc';
            break;
            case '陵水' :
            return 'sanya';
            break;
            case '琼海' :
            return 'sanya';
            break;
            case '保亭' :
            return 'sanya';
            break;
            case '东方市' :
            return 'sanya';
            break;
            case '博罗' :
            return 'huizhou';
            break;
            case '惠东' :
            return 'huizhou';
            break;
            case '龙门' :
            return 'huizhou';
            break;
            case '昌邑' :
            return 'jilin';
            break;
            case '永登' :
            return 'lanzhou';
            break;
            case '榆中' :
            return 'lanzhou';
            break;
            case '文安' :
            return 'langfang';
            break;
            case '孟津' :
            return 'luoyang';
            break;
            case '汝阳' :
            return 'luoyang';
            break;
            case '新安' :
            return 'luoyang';
            break;
            case '伊川' :
            return 'luoyang';
            break;
            case '宜阳' :
            return 'luoyang';
            break;
            case '宾阳' :
            return 'nanning';
            break;
            case '横县' :
            return 'nanning';
            break;
            case '海安' :
            return 'nantong';
            break;
            case '启东' :
            return 'nantong';
            break;
            case '如东' :
            return 'nantong';
            break;
            case '安溪' :
            return 'quanzhou';
            break;
            case '惠安' :
            return 'quanzhou';
            break;
            case '永春' :
            return 'quanzhou';
            break;
            case '晋安' :
            return 'quanzhou';
            break;
            case '上虞' :
            return 'shaoxing';
            break;
            case '兴化' :
            return 'taizhou';
            break;
            case '乐亭' :
            return 'tangshan';
            break;
            case '滦南' :
            return 'tangshan';
            break;
            case '滦县' :
            return 'tangshan';
            break;
            case '迁西' :
            return 'tangshan';
            break;
            case '玉田' :
            return 'tangshan';
            break;
            case '安丘' :
            return 'weifang';
            break;
            case '昌乐' :
            return 'weifang';
            break;
            case '高密' :
            return 'weifang';
            break;
            case '青州' :
            return 'weifang';
            break;
            case '寿光' :
            return 'weifang';
            break;
            case '丰县' :
            return 'xuzhou';
            break;
            case '沛县' :
            return 'xuzhou';
            break;
            case '睢宁' :
            return 'xuzhou';
            break;
            case '宝应' :
            return 'yangzhou';
            break;
            case '高邮' :
            return 'yangzhou';
            break;
            case '江都' :
            return 'yangzhou';
            break;
            case '仪征' :
            return 'yangzhou';
            break;
            case '当阳' :
            return 'yichang';
            break;
            case '宜都' :
            return 'yichang';
            break;
            case '枝江' :
            return 'yichang';
            break;
            case '丹阳市' :
            return 'zhenjiang';
            break;
            case '扬中市' :
            return 'zhenjiang';
            break;
            case '邹平' :
            return 'binzhou';
            break;
            case '广饶' :
            return 'dongying';
            break;
            case '玉环' :
            return 'taiz';
            break;
            case '肇源' :
            return 'daqing';
            break;
            case '东海' :
            return 'lianyungang';
            break;
            case '德清' :
            return 'huzhou';
            break;
            case '长兴' :
            return 'huzhou';
            break;
            case '建湖' :
            return 'yancheng';
            break;
            case '当涂' :
            return 'maanshan';
            break;
            case '宁国' :
            return 'xuancheng';
            break;
            case '巴州' :
            return 'bazhong';
            break;


            default:
                # code...
                break;
        }
    }

}