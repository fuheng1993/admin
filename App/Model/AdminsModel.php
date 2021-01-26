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

class AdminsModel extends BaseModel
{
    protected $tableName = 'td_admins';
    /**
     * @getAll
     * @param  int  $page  1
     * @param  int  $pageSize  10
     * @param  string  $field  *
     * @return array[total,list]
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


}