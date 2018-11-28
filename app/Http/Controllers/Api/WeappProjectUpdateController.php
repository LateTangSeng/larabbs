<?php

namespace App\Http\Controllers\Api;

use App\Models\FundProject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappProjectUpdateRequest;

class WeappProjectUpdateController extends Controller
{
    //
    public function ProjectUpdate(WeappProjectUpdateRequest $request)
    {
        $ResultArray = array("fundname" => '',"fundcode" => '',"fundchangerate" => '',"fundnetvalue" => '',"fundtype" => '',"fundrisklevel" => '',"fundvalueday" => '',"state" => 'false',"color" => 'true');

        $openid = $request->openid;
        $basemoney = $request->basemoney;
        $baseindex = $request->baseindex;
        $fundcode = $request->fundcode;

        // 查询是否已经建立该基金的定投计划
        $FundProject = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid])->first();

        // 找不到对应的基金
        if (!$FundProject)
        {
            return $ResultArray;
        }
        else
        {
            // 更新定投计划
            $update_bool = FundProject::where(['weixinopenid'=>$openid, 'fundcode'=>$fundcode])->update(['baseindex'=>$baseindex,'basemoney'=>$basemoney]);
            if ($update_bool)
            {
                // 更新成功
                $ResultArray['state'] = 'true';
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
