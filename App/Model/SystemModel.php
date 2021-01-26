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

class SystemModel extends BaseModel
{
    protected $tableName = 'td_system';

    /**
     * @param int $page
     * @param int $pageSize
     * @param string $field
     * @return array
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getAll(int $page = 1, int $pageSize = 10, string $field = '*'): array
    {
        $list = $this
            ->withTotalCount()
            ->order($this->schemaInfo()->getPkFiledName(), 'DESC')
            ->field($field)
            ->limit($pageSize * ($page - 1), $pageSize)
            ->all();
        $total = $this->lastQueryResult()->getTotalCount();;
        return ['total' => $total, 'list' => $list];
    }

    //获取器
    protected function getCreateTimeAttr($value, $data)
    {
        return $value?date('Y.m.d H:i:s',$value):'';
    }

}