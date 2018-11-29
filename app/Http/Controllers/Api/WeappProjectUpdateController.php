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
        $baseindexold = $request->baseindexold;
        $fundcode = $request->fundcode;
        $toUpdate = false;

        // 找出总共的计划数
        $count = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid])->count();

        // 查询是否已经建立该基金的定投计划
        $FundProject = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid])->get();

        // 找不到对应的基金
        if (0 == $count)
        {
            return $ResultArray;
        }

        if (1 == $count)
        {
            // 更新定投计划
            $update_bool = FundProject::where(['weixinopenid'=>$openid, 'fundcode'=>$fundcode, 'baseindex'=>$baseindexold])->update(['baseindex'=>$baseindex,'basemoney'=>$basemoney]);
            if ($update_bool)
            {
                // 更新成功
                $ResultArray['state'] = 'true';
            }
        }

        if (2 == $count)
        {
            // 找到本条记录
            $OldRecord = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid, 'baseindex'=>$baseindexold])->first();

            if (!$OldRecord)
            {
                return $ResultArray;
            }
            else
            {
                if ($OldRecord->id == $FundProject[0]->id)
                {
                    if ($FundProject[1]->baseindex == $baseindex)
                    {
                        return $ResultArray;
                    }
                    else
                        $toUpdate = true;
                }
                else
                {
                    if ($FundProject[0]->baseindex == $baseindex)
                    {
                        return $ResultArray;
                    }
                    else
                        $toUpdate = true;
                }
            }

            if ($toUpdate)
            {
                // 更新定投计划
                $update_bool = FundProject::where(['weixinopenid'=>$openid, 'fundcode'=>$fundcode, 'baseindex'=>$baseindexold])->update(['baseindex'=>$baseindex,'basemoney'=>$basemoney]);
                if ($update_bool)
                {
                    // 更新成功
                    $ResultArray['state'] = 'true';
                }
            }
        }

        if (3 == $count)
        {
            // 找到本条记录
            $OldRecord = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid, 'baseindex'=>$baseindexold])->first();

            if (!$OldRecord)
            {
                return $ResultArray;
            }
            else
            {
                if ($OldRecord->id == $FundProject[0]->id)
                {
                    if ($FundProject[1]->baseindex == $baseindex || $FundProject[2]->baseindex == $baseindex)
                    {
                        return $ResultArray;
                    }
                    else
                        $toUpdate = true;
                }
                else if ($OldRecord->id == $FundProject[1]->id)
                {
                    if ($FundProject[0]->baseindex == $baseindex || $FundProject[2]->baseindex == $baseindex)
                    {
                        return $ResultArray;
                    }
                    else
                        $toUpdate = true;
                }
                else
                {
                    if ($FundProject[0]->baseindex == $baseindex || $FundProject[1]->baseindex == $baseindex)
                    {
                        return $ResultArray;
                    }
                    else
                        $toUpdate = true;
                }
            }

            if ($toUpdate)
            {
                // 更新定投计划
                $update_bool = FundProject::where(['weixinopenid'=>$openid, 'fundcode'=>$fundcode, 'baseindex'=>$baseindexold])->update(['baseindex'=>$baseindex,'basemoney'=>$basemoney]);
                if ($update_bool)
                {
                    // 更新成功
                    $ResultArray['state'] = 'true';
                }
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
