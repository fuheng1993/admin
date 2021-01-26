<?php
/**
 * Created by PhpStorm.
 * Users: Administrator
 * Date: 2019/12/18 0018
 * Time: 19:58
 */

namespace App\Model;

class BaseModel extends \EasySwoole\ORM\AbstractModel
{
    public function select(){
        $data = $this->all();
        return is_object($data)?$data->toArray():$data;
    }
    public function find(){
        $data = $this->get();
        return is_object($data)?$data->toArray():$data;
    }
}