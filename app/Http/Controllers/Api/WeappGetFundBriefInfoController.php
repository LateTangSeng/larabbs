<?php



namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\WeappGetFundBriefInfoRequest;



class WeappGetFundBriefInfoController extends Controller
{
    //
    public function GetFundInfo(WeappGetFundBriefInfoRequest $request)
    {

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

        //$result = iconv("UTF-8","gb2312//IGNORE",$result);

        //return $this->respondWithResult($result)->setStatusCode(201);
        return $result;


        //return $this->response->array([
        //    'openid' => $openid,
        //    'weixin_session_key' => $weixin_session_key,
        //    'insert_bool' => $insert_bool,
        //]);
    }

    protected function respondWithResult($result)
    {
        return $this->response->array([
            'result' => $result,
            'writer' => 'L.SHENG'
        ]);
    }


}
