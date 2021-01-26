<?php

namespace App\HttpController\Admin;



use App\Model\SecretModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Secret extends \App\HttpController\Admin\Base
{
    /**
     * 秘钥列表
     */
    public function lists(){
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $model = new SecretModel();
        if(!empty($this->param['category_id'])){ $model->where('s.category_id',$this->param['category_id']); }
        if(!empty($this->param['status'])){ $model->where('s.status',$this->param['status']); }
        $field = "s.*,c.name as category,c.price as category_price,c.image,o.tel,o.order_no";
        $list = $model->withTotalCount()->alias('s')
            ->join('td_order o','o.id=s.order_id','LEFT')
            ->join('td_category c','c.id=s.category_id','LEFT')
            ->order('id', 'DESC')->field($field)->limit($limit * ($page - 1), $limit)->all();
        $total = $model->lastQueryResult()->getTotalCount();;
        $this->AjaxJson(0, ['total'=>$total,'list'=>$list], 'success'.$model->lastQuery()->getLastPrepareQuery());
        return true;
    }
    /**
     * 新增微信秘钥
     */
    public function add(){
        $data['secret_key'] = $this->param['secret_key'];
        $data['price'] = $this->param['price'];
        $data['category_id'] = $this->param['category_id'];
        $data['order_id'] = 0;
        $data['status'] = 1;
        $data['create_time'] = time();
        $data['update_time'] = time();
        if(empty($data['secret_key'])){$this->AjaxJson(0, $this->param, '产品秘钥必须');return false; }
        if(empty($data['category_id'])){$this->AjaxJson(0, $this->param, '所属产品必须');return false; }
        if(empty($data['price'])){$this->AjaxJson(0, $this->param, '价格必须');return false; }

        try{
            $secret_list = array_unique(explode(',',$data['secret_key']));
            $success = $fail = 0;
            foreach ($secret_list as $v){
                if(SecretModel::create()->where('secret_key',$v)->get()){
                    $fail++;
                    continue;
                }
                $data['secret_key'] = trim($v);
                if(empty($data['secret_key'])){
                    continue;
                }
                if(SecretModel::create()->data($data)->save()){
                    $success++;

                }
            }
            $this->AjaxJson(1 ,$secret_list, "新增秘钥成功：{$success}个；失败：{$fail}个");return false;

        }catch (\Exception $e){
            $this->AjaxJson(0, ['status'=>0], '系统错误：'.$e->getMessage());return false;
        }

        return false;
    }

    /**
     * 更新秘钥
     */
    public function edit(){
        if(!empty($this->param['id'])){
            $data['secret_key'] = $this->param['secret_key'];
            $data['price'] = $this->param['price'];
            $data['category_id'] = $this->param['category_id'];
            $data['update_time'] = time();
            if(empty($data['secret_key'])){$this->AjaxJson(0, $this->param, '产品秘钥必须');return false; }
            if(empty($data['category_id'])){$this->AjaxJson(0, $this->param, '所属产品必须');return false; }
            if(empty($data['price'])){$this->AjaxJson(0, $this->param, '价格必须');return false; }
            if(SecretModel::create()->where('secret_key',$data['secret_key'])->where('id',$this->param['id'],'<>')->get()){
                $this->AjaxJson(0, ['status'=>0], '秘钥名称已存在');return false;
            }
            try{
                if(SecretModel::create()->update($data,['id'=>$this->param['id']])){
                    $this->AjaxJson(1, ['status'=>1], '更新秘钥成功');return false;
                }else{
                    $this->AjaxJson(0, ['status'=>0], '更新秘钥失败');return false;
                }
            }catch (\Exception $e){
                $this->AjaxJson(0, ['status'=>0], '更新出错：'.$e->getMessage());
            }

        }else{
            $this->AjaxJson(0, ['status'=>0], '秘钥ID不存在');
        }
        return false;
    }

    /**
     * 更新秘钥状态
     * param id
     * param status
     * return bool
     */
    public function doStatus(){
        $id = $this->param['id'];
        if(empty($id)){ $this->AjaxJson(0,  ['status'=>0], '秘钥ID必须'); return false;}
        $value = (int)$this->param['value']??0;
        $msg = $value==1?'恢复秘钥':'禁用秘钥';
        if(SecretModel::create()->update(['status'=>$value,'update_time'=>time()],['id'=>$id])){
            $this->AjaxJson(0,  ['status'=>1], $msg.'成功');
        }else{
            $this->AjaxJson(0,  ['status'=>0], $msg.'失败');
        }
        return true;
    }



    /**
     * 删除品牌
     */
    public function del(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            if( SecretModel::create()->where('id',$ids,'in')->destroy()){
                $this->AjaxJson(0, ['status'=>1], '删除秘钥成功');return false;
            }else{
                $this->AjaxJson(0, ['status'=>0], '删除秘钥失败');return false;
            }
        }else{
            $this->AjaxJson(0, ['status'=>0,'param'=>$this->param], '请选择要删除的秘钥');
        }
        return false;
    }

    /**
     * 获取全部栏目
     */
    public function all(){
        $model = SecretModel::create();
        $model->where('category_id',$this->param['category_id']);
        $model->where('order_id',0);
        $model->where('status',1);
        $field = "id as value,secret as name";
        $order = ['id','desc'];
        $list = $model->order($order[0], $order[1])->field($field)->limit(0,20)->all();
        $this->AjaxJson(1, $list, 'success');
        return true;
    }

}

