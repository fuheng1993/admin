<?php
/**
 * Created by PhpStorm.
 * User: Double-jin
 * Date: 2019/6/19
 * Email: 605932013@qq.com
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

class RefererKeywordsModel extends BaseModel
{
    protected $tableName = 'td_referer_keywords';



    //获取器
    protected function getCreateTimeAttr($value, $data)
    {
        return $value?date('Y-m-d H:i:s',$value):'';
    }
    //获取器
    protected function getUpdateTimeAttr($value, $data)
    {
        return $value?date('Y-m-d H:i:s',$value):'';
    }


}