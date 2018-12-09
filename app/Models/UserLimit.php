<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLimit extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'UserLimit';

    /**
     * 设定主键
     *
     * @var string
     */
    protected $primaryKey = 'id';
    //protected $keyType = string;
    public $incrementing = true;

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'CreateTime', 'OpenID','RealEstateCount', 'CarPriceCount','KeyWordsCount','MiniProgramCount','OtherTypeCpunt','LastMsgType', 'LimitCount', 'UpdateTime',
    ];
}
