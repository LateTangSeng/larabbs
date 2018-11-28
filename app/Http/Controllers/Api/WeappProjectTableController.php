<?php

namespace App\Http\Controllers\Api;

use App\Models\FundProject;
use App\Models\AllFundInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappProjectTableRequest;

class WeappProjectTableController extends Controller
{
//
    public function ProjectTable(WeappProjectTableRequest $request)
    {
        $ResultArray = array("projectnum" => '0',
            "fundonename" => '',"fundonecode" => '', "fundonebase" => '',
            "fundtwoname" => '',"fundtwocode" => '', "fundtwobase" => '',
            "fundthreename" => '',"fundthreecode" => '', "fundthreebase" => '',
            "baseone" => '', "basetwo" => '', "basethree" => '',
            "state" => 'false');

        $tabletype = $request->type;
        $openid = $request->openid;

        // 找出总共的计划数
        $count = FundProject::where('weixinopenid', $openid)->count();

        if (0 == $count) {
            return $ResultArray;
        }
        else
        {
            // 找出所有的计划
            $ProjectTableArray = FundProject::where('weixinopenid', $openid)->get();

            $ResultArray['projectnum'] = $count;
            $ResultArray['state'] = 'true';

            $ResultArray['fundonename'] = $ProjectTableArray[0]->fundname;
            $ResultArray['fundonecode'] = $ProjectTableArray[0]->fundcode;
            $ResultArray['fundonebase'] = $ProjectTableArray[0]->basemoney;
            $ResultArray['baseone'] = $ProjectTableArray[0]->baseindex;

            if ($count >=2) {
                $ResultArray['fundtwoname'] = $ProjectTableArray[1]->fundname;
                $ResultArray['fundtwocode'] = $ProjectTableArray[1]->fundcode;
                $ResultArray['fundtwobase'] = $ProjectTableArray[1]->basemoney;
                $ResultArray['basetwo'] = $ProjectTableArray[1]->baseindex;
            }

            if ($count >=3) {
                $ResultArray['fundthreename'] = $ProjectTableArray[2]->fundname;
                $ResultArray['fundthreecode'] = $ProjectTableArray[2]->fundcode;
                $ResultArray['fundthreebase'] = $ProjectTableArray[2]->basemoney;
                $ResultArray['basethree'] = $ProjectTableArray[2]->baseindex;
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
