<?php

namespace App\Http\Controllers\Api;

use App\Models\FundProject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappProjectDeletionRequest;


class WeappProjectDeletionController extends Controller
{
    //
    public function ProjectDeletion(WeappProjectDeletionRequest $request)
    {
        $ResultArray = array("fundname" => '',"fundcode" => '',"fundchangerate" => '',"fundnetvalue" => '',"fundtype" => '',"fundrisklevel" => '',"fundvalueday" => '',"state" => 'false',"color" => 'true');
        $openid = $request->openid;
        $fundcode = $request->fundcode;
        $baseindexold = $request->baseindexold;

        // 找到 code 对应的基金
        $FundProject = FundProject::where(['fundcode'=>$fundcode, 'weixinopenid'=>$openid, 'baseindex'=>$baseindexold])->first();

        if (!$FundProject) {
            return $ResultArray;
        }
        else
        {
            // 插入数据 返回插入数据的bool值
            $delete_bool = FundProject::where(['weixinopenid'=>$openid,'fundcode'=>$fundcode, 'baseindex'=>$baseindexold])->delete();
            if($delete_bool)
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
