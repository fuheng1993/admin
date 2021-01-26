<?php

namespace App\Model\TrackerPoint;

/**
 * Class TdOrderModel
 * Create With Automatic Generator
 * @property $id int |
 * @property $contents string | 内容
 * @property $type_id int | 类型ID
 * @property $wechat_id int | 微信ID
 * @property $status int | 1待处理 2已处理
 * @property $result string | 处理结果
 * @property $create_time int | 11
 * @property $update_time int |
 * @property $user_id int | 提交人
 */
class TrackerPointModel extends \App\Model\BaseModel
{
	protected $tableName = 'td_api_tracker_point_list';
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

