<?php

namespace App\Http\Controllers\Api;

use App\Models\FundProject;
use App\Models\AllFundInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappProjectCreationRequest;

class WeappProjectCreationController extends Controller
{
    //
    public function ProjectCreation(WeappProjectCreationRequest $request)
    {
        $ResultArray = array("fundname" => '',"fundcode" => '',"fundchangerate" => '',"fundnetvalue" => '',"fundtype" => '',"fundrisklevel" => '',"fundvalueday" => '',"state" => 'false',"color" => 'true');
        $openid = $request->openid;
        $basemoney = $request->basemoney;
        $baseindex = $request->baseindex;
        $fundcode = $request->fundcode;
        $findcode = 'Code:'.$fundcode;

        // 找到 code 对应的基金
        $Fund = AllFundInfo::where('FundCode', $findcode)->first();

        if (!$Fund) {
            return $ResultArray;
        }
        else
        {
            $ResultArray['fundname'] = $Fund->FundName;
            $ResultArray['fundtype'] = $Fund->FundType;
            $ResultArray['fundrisklevel'] = $Fund->FundRiskLevel;

            // 插入数据 返回插入数据的bool值
            $insert_bool = FundProject::insert(['weixinopenid'=>$openid,'basemoney'=>$basemoney,'baseindex'=>$baseindex,'fundcode'=>$fundcode, 'fundname'=>$ResultArray['fundname']]);
            if($insert_bool)
            {
                // 插入成功
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
