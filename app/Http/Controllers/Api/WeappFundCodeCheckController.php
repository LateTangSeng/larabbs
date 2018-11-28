<?php

namespace App\Http\Controllers\Api;

use App\Models\FundProject;
use App\Models\AllFundInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappFundCodeCheckRequest;

class WeappFundCodeCheckController extends Controller
{
    //
    public function FundCodeCheck(WeappFundCodeCheckRequest $request)
    {
        $ResultArray = array("state" => 'false', "description" => '');
        $openid = $request->openid;
        $fundcode = $request->fundcode;
        $findcode = 'Code:'.$fundcode;

        // 找到 code 对应的基金
        $Fund = AllFundInfo::where('FundCode', $findcode)->first();

        // 找不到对应的基金
        if (!$Fund) {
            $ResultArray['description'] = '找不到对应的基金';
            return $ResultArray;
        }
        else {
            // 查询是否已经建立该基金的定投计划
            $FundProject = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid])->first();

            // 找不到对应的基金
            if (!$FundProject) {
                $ResultArray['state'] = 'true';
            }
            else {
                $ResultArray['description'] = '找不到对应的计划';
                return $ResultArray;
            }
        }

        return $ResultArray;

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
