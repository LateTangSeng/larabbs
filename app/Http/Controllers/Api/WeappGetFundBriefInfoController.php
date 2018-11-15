<?php



namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\AllFundInfo;
use Illuminate\Http\Request;
use App\Http\Requests\Api\WeappGetFundBriefInfoRequest;

class WeappGetFundBriefInfoController extends Controller
{
    //
    public function GetFundInfo(WeappGetFundBriefInfoRequest $request)
    {
        $ResultArray = array("fundname" => '',"fundcode" => '',"fundchangerate" => '',"fundnetvalue" => '',"fundtype" => '',"fundrisklevel" => '',"fundvalueday" => '',"state" => 'false',"color" => 'true');
        $code = $request->code;
        $url = "http://hq.sinajs.cn/list=of";
        $url = $url.$code;

        $fp = fopen($url, 'r');
        $result = null;
        stream_get_meta_data($fp);
        while (!feof($fp)) {
            $result .= fgets($fp, 1024);
        }

        fclose($fp);

        if (strlen($result)>24) {
            $ResultArray['state'] = 'true';
            $ResultArray['fundcode'] = $code;
            $findcode = 'Code:'.$code;

            // 找到 code 对应的基金
            $Fund = AllFundInfo::where('FundCode', $findcode)->first();

            if (!$Fund) {
                $ResultArray['state'] = 'false';
                $ResultArray['fundname'] = '查询数据库';
                $ResultArray['fundtype'] = '数据库查询';
                $ResultArray['fundrisklevel'] = '数据库查询';
            }
            else
            {
                $ResultArray['fundname'] = $Fund->FundName;
                $ResultArray['fundtype'] = $Fund->FundType;
                $ResultArray['fundrisklevel'] = $Fund->FundRiskLevel;
            }

            $netvaluetemp = str_after($result,',');
            $ResultArray['fundnetvalue'] = str_before($netvaluetemp,',');

            $changeratetemp = str_after(str_after(str_after($netvaluetemp,','),','),',');
            $ResultArray['fundchangerate'] = str_before($changeratetemp,',');

            $vauledaytemp = str_after($changeratetemp,',');
            $ResultArray['fundvalueday'] = str_before($vauledaytemp,'"');

            if (floatval($ResultArray['fundchangerate'])<0) {
                $ResultArray['color'] = 'false';
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
