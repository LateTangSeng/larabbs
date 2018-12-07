<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WeappRealEstateQueryRequest;

class WeappRealEstateQueryController extends Controller
{

    public function RealEstateQuery(WeappRealEstateQueryRequest $request)
    {
        $ResultArray = array("AveragePrice" => '',"City"=> '', "Community"=>'', "state" => 'false');
        $citywithcommunity = $request->citywithcommunity;

        $city = str_before($citywithcommunity, '+');
        $community = str_after($citywithcommunity, '+');

        $citypinyin = '';


        switch ($city) {
            case '南京':
                $citypinyin = 'nanjing';
                break;

            default:
                $citypinyin = 'beijing';
                break;
        };

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
        if (strlen($data) < 10)
        {
            $ResultArray['AveragePrice'] = $data;
            $ResultArray['state'] = 'true';
            //file_put_contents(__DIR__.'/db.txt', json_encode($data));
        }
        else
        {
            return $ResultArray;
        }

        return $ResultArray;

    }

}
