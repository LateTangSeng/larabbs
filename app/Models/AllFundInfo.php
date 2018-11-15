<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllFundInfo extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'FundInfo';

    /**
     * 设定主键
     *
     * @var string
     */
    protected $primaryKey = 'FundCode';
    //protected $keyType = string;
    public $incrementing = false;

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

}
