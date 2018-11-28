<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappStockIndexRequest;

class WeappStockIndexController extends Controller
{
    public function StockIndex(WeappStockIndexRequest $request)
    {
        $ResultArray = array("stockindex" => '',"state" => 'false');
        $stocktype = $request->type;
        $url = "http://hq.sinajs.cn/list=sh000001";

        $fp = fopen($url, 'r');
        $result = null;
        stream_get_meta_data($fp);
        while (!feof($fp)) {
            $result .= fgets($fp, 1024);
        }

        $result = iconv('gb2312','utf-8',$result);

        fclose($fp);

        if (strlen($result)>24) {
            $ResultArray['state'] = 'true';



            $netvaluetemp = str_after($result,'上证指数,');
            //$ResultArray['fundnetvalue'] = str_before($netvaluetemp,',');

            $indextemp = str_before(str_after(str_after($netvaluetemp,','),','),',');
            //$ResultArray['fundchangerate'] = str_before($changeratetemp,',');

            //$vauledaytemp = str_after($changeratetemp,',');
            //$ResultArray['fundvalueday'] = str_before($vauledaytemp,'"');

            $ResultArray['stockindex'] = $indextemp;


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
