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
        //file_put_contents(__DIR__.'/db1.txt', json_encode($config));

        $app = Factory::officialAccount($config);

        $buttons = [
            [
                "type" => "view",
                "name" => "历史文章",
                "url"  => "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzUxNDk1MzQyNA==#wechat_redirect/"
            ],
            [
                "type" => "view",
                "name" => "定投专栏",
                "url"  => "http://mp.weixin.qq.com/s?__biz=MzUxNDk1MzQyNA==&mid=100000292&idx=1&sn=a9d3ea613006910e2bf7e901b8ce005c&chksm=79bf5b9b4ec8d28d5c2c8da65fed625d617a89fc776b330cf22b298e9ba966b6402e3a3b2b00&scene=18#wechat_redirect/"
            ],
            [
                "type" => "view",
                "name" => "粉丝交流",
                "url"  => "http://mp.weixin.qq.com/s?__biz=MzUxNDk1MzQyNA==&mid=100000294&idx=1&sn=7d2bcc8fd7bd4c9925d9e64e218c54a3&chksm=79bf5b994ec8d28f30d8b4e47ceddb9cf997ef162a93f44c514b2e65ff5eb0a3c433ea83d89a&scene=18#wechat_redirect/"
            ],
        ];

        $app->menu->create($buttons);

        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return $this->EventProc($message['Event']);
                    break;
                case 'text':
                    return $this->RealEstateQuery($message['Content']);
                    break;
                case 'image':
                    return '收到图片消息,独行侠会尽快联系您的,请稍等';
                    break;
                case 'voice':
                    return '收到语音消息,独行侠会尽快联系您的,请稍等';
                    break;
                case 'video':
                    return '收到视频消息,独行侠会尽快联系您的,请稍等';
                    break;
                case 'location':
                    return '收到坐标消息,独行侠会尽快联系您的,请稍等';
                    break;
                case 'link':
                    return '收到链接消息,独行侠会尽快联系您的,请稍等';
                    break;
                case 'file':
                    return '收到文件消息,独行侠会尽快联系您的,请稍等';
                // ... 其它消息
                default:
                    return '收到其它消息,独行侠会尽快联系您的,请稍等';
                    break;
            }
        });

        $response = $app->server->serve();

        // 将响应输出
        return $response;// Laravel 里请使用：return $response;
    }

    public function RealEstateQuery($citywithcommunity)
    {
        $ResultArray = array("AveragePrice" => '',"City"=> '', "Community"=>'', "state" => 'false');

        $city = str_before($citywithcommunity, '+');
        $community = str_after($citywithcommunity, '+');

        $citypinyin = $this->CheckPinYin($city);

        if (strlen($citypinyin) < 2) {
            $ResultArray['AveragePrice'] = '对不起，暂未查询到';
            $ResultArray['state'] = 'false';
        }
        else
        {
            //目标网站
            $pageorigin = "https://city.anjuke.com/community/?kw=communityname&from=sugg_hot";

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
            //return $result;

            //$datatemp = $htmlcontent->getHtml();

            $data = str_before(str_after($result, '<strong>'), '</strong>');
            $actualcommunity = str_before(str_after(str_after($result, '<!--小区列表start-->'), 'alt="'), '"');
            if (strlen($data) < 10)
            {
                $ResultArray['AveragePrice'] = $city.' '.$actualcommunity.' 当前均价为：'.$data.' 元/平方米';
                $ResultArray['state'] = 'true';
                //file_put_contents(__DIR__.'/db.txt', json_encode($data));
            }
            else
            {
                $ResultArray['AveragePrice'] = '对不起，暂未查询到';
                $ResultArray['state'] = 'false';
            }
        }

        return $ResultArray['AveragePrice'];

    }

    public function EventProc($Event)
    {
        $ResultArray = array("AveragePrice" => '',"City"=> '', "Community"=>'', "state" => 'false');

        if ($Event == 'subscribe')
        {
            $ResultArray['AveragePrice'] = '您好，感谢您的关注！独行侠长期关注股票，基金，P2P，楼市等财经热点，并每天在公众号中发布相关信息和个人看法，也会不定期将自己的实盘操作经验分享给大家。有意愿商务合作的或者想进独行侠财经交流群的可以加独行侠个人微信：yxp19891026 交流 。近期推出的基金定投专栏，详情请回复‘定投’，欢迎大家关注，分享，留言，讨论，感谢您的支持！';
            $ResultArray['state'] = 'true';
            //file_put_contents(__DIR__.'/db.txt', json_encode($data));
        }
        else
        {
            $ResultArray['AveragePrice'] = '对不起，暂未查询到';
            $ResultArray['state'] = 'false';
        }

        return $ResultArray['AveragePrice'];

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