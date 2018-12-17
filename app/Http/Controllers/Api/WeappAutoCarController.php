<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cars;
use App\Models\Auto;
use Illuminate\Http\Request;
use App\Http\Requests\Api\WeappAutoCarRequest;

class WeappAutoCarController extends Controller
{
    //protected $IsProc = false;

    public function AutoCar(WeappAutoCarRequest $request)
    {
        $IsProc = Cars::where('id', 1)->first();
        $name = $request->name;
        $password = $request->password;
        $State = 'true';

        if ('迟到的唐僧' == $name && 'Norman0%5138' == $password && '0' == $IsProc->explain)
        {
            $CarsCount = Cars::where(function($query){
                    $query->where('minprice','>','0');
                })->count();

            $CarsArray = Cars::where(function($query){
                    $query->where('minprice','>','0');
                })->get();

            $update_bool = Cars::where('id', 1)->update(['explain'=>'1']);

            for ($j=0; $j < $CarsCount; $j++)
            {
                $MoveToNext = false;
                $result = '';
                if ($CarsArray[$j]->id >= 1804)
                {
                    $page = "https://www.autohome.com.cn/".$CarsArray[$j]->index_id;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $page);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                    curl_setopt($ch, CURLOPT_REFERER, $page);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $result = iconv("gb2312","UTF-8//IGNORE",$result);

                    if (false != strpos($result, '<!--在售 start-->'))
                    {
                        $result = str_before(str_after($result, '<!--在售 start-->'), '<!--在售 end-->');
                        $MoveToNext = false;
                    }
                    else
                    {
                        $MoveToNext = true;
                    }
                }
                else
                {
                    $MoveToNext = true;
                }



                $ResultArray[] = '';
                $i = 0;
                $CommonFeature = '';
                $CarName[] = '';
                $Feature[] = '';
                $Price[] = '';

                while((false != strpos($result, '</dd>') && (false == $MoveToNext)))
                {
                    $ResultArray[$i] = str_before($result, '</dd>');
                    $result = str_after($result, '</dd>');
                    if (false != strpos($ResultArray[$i], '<dl>'))
                    {
                        $CommonFeature = str_before(str_after($ResultArray[$i], '<span>'), '</span>');
                    }

                    $CarName[$i] = str_before(str_after($ResultArray[$i], 'class="name">'), '</a>');

                    if (false != strpos($ResultArray[$i], '<p class="guidance-price">'))
                    {
                        $Price[$i] = str_before(str_after(str_after($ResultArray[$i], '<p class="guidance-price">'), '<span>'), '</span>');
                    }
                    else
                    {
                        $Price[$i] = '价格未公布';
                    }

                    if (false != strpos($ResultArray[$i], '<span class="type-default">'))
                    {
                        $Feature[$i] = str_before(str_after($ResultArray[$i], '<span class="type-default">'), '</span>');
                        $FeatureTemp = str_after($ResultArray[$i], '<span class="type-default">');
                        if (false != strpos($FeatureTemp, '<span class="type-default">'))
                        {
                            $Feature[$i] = $Feature[$i].' '.str_before(str_after($FeatureTemp, '<span class="type-default">'), '</span>');
                            $FeatureTemp = str_after($ResultArray[$i], '<span class="type-default">');
                            if (false != strpos($FeatureTemp, '<span class="athm-badge athm-badge--grey">'))
                            {
                                $Feature[$i] = $Feature[$i].' '.str_before(str_after($FeatureTemp, '<span class="athm-badge athm-badge--grey">'), '</span>');
                            }
                        }

                        $Feature[$i] = $Feature[$i].' '.$CommonFeature;

                    }
                    else
                    {
                        $Feature[$i] = '参数未公布';
                    }
                    //$ResultArray[$i] = $CarName[$i].' '.$Feature[$i].' '.$CommonFeature.' '.$Price[$i];

                    $insert_bool = Auto::insert([
                        'Auto_ID'=>$CarsArray[$j]->index_id,
                        'Name'=>$CarName[$i],
                        'Feature'=>$Feature[$i],
                        'KeyWord'=>$CarsArray[$j]->name,
                        'MatchIndex'=>($i + 1),
                        'Price'=>$Price[$i],
                        'CreateTime'=>now(),
                        'CTUnix'=>time(),
                        'UpdateTime'=>now(),
                        'UTUnix'=>time(),
                        'SelectCount'=>0,
                        'IsOld'=>0]);

                    if (false == $insert_bool)
                    {
                        $State = 'false';
                        $j = 1000000;
                        break;
                    }

                    $i++;
                }

                if ($CarsArray[$j]->id >= 1804)
                {
                    sleep(2);
                }
            }

            $update_bool = Cars::where('id', 1)->update(['explain'=>'0']);
            return $State;
        }
        else
        {
            return '账号密码不对或者正在抓取';
        }
    }
}
