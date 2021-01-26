<?php

namespace App\HttpController\Admin;


use App\Model\OrderModel;
use App\Model\UserBindWechatModel;
use App\Model\UserModel;
use App\Model\WechatModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class User extends Base
{
    /**
     * 用户列表
     */
    public function lists(){
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $model = UserModel::create();
        if(!empty($this->param['type'])){ $model->where('type',$this->param['type']); }
        if(!empty($this->param['username'])){ $model->where('username',"%{$this->param['username']}%",'like'); }
        if(!empty($this->param['tel'])){ $model->where('tel',"%{$this->param['tel']}%",'like'); }
        if(!empty($this->param['nickname'])){ $model->where('nickname',"%{$this->param['nickname']}%",'like'); }
        if(!empty($this->param['start'])){ $model->where('create_time',strtotime($this->param['start'].' 00:00:00'),'>='); }
        if(!empty($this->param['end'])){ $model->where('create_time',strtotime($this->param['end'].' 23:59:59'),'<='); }
        $field = "*";
        //排序
        if(!empty($this->param['order'])){
            $order = explode(' ',$this->param['order']);
            if(empty($order[1])||$order[1]=='null'){
                $order = ['id','desc'];
            }
        }else{
            $order = ['id','desc'];
        }
        $list = $model->withTotalCount()
            ->order($order[0], $order[1])->field($field)->limit($limit * ($page - 1), $limit)->all();
        $total = $model->lastQueryResult()->getTotalCount();;
        $this->writeJson(Status::CODE_OK, ['total'=>$total,'list'=>$list,'sql'=>$model->lastQuery()->getLastPrepareQuery()], 'success');
        return true;
    }

    /**
     * 新增用户
     */
    public function add(){
        $data['username'] = $this->param['username'];
        $data['password'] = $this->param['password'];
        $data['nickname'] = $this->param['nickname'];
        $data['type'] =2;
        $data['tel'] = $this->param['tel'];
        $data['status'] = $this->param['status'];
        $data['avatar'] = $this->param['avatar'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        if(empty($data['username'])){$this->AjaxJson(0, $this->param, '账号必须');return false; }
        if(empty($data['password'])){$this->AjaxJson(0, $this->param, '密码必须');return false; }
        if(empty($data['nickname'])){$this->AjaxJson(0, $this->param, '昵称必须');return false; }
        if(empty($data['tel'])){$this->AjaxJson(0, $this->param, '手机号必须');return false; }
        if(empty($data['avatar'])){$this->AjaxJson(0, $this->param, '头像必须');return false; }
        $config    = Config::getInstance();
        $pswstr = $config->getConf('PSW_STR');
        $data['password'] = md5($data['password'].$pswstr);
        $data['token'] ='';
        $model = UserModel::create();
        if($model->where('username',$this->param['username'])->get()){
            $this->AjaxJson(0, $this->param, '账号已存在');return false;
        }
        if($model->where('tel',$this->param['tel'])->get()){
            $this->AjaxJson(0, $this->param, '手机号已绑定其他账号');return false;
        }

        try{
            if($user_id = UserModel::create()->data($data)->save()){
                $this->AjaxJson(1, $this->param, '新增账号成功');return false;
            }else{
                $this->AjaxJson(0, ['status'=>0], '新增账号失败');return false;
            }
        }catch (\Exception $e){
            $this->AjaxJson(0, ['status'=>0], '插入数据库错误：'.$e->getMessage());return false;
        }

        return false;
    }
    /**
     * 获取全部账号列表
     */
    public function all(){
        $model = new UserModel();
        $field = "id as value, username";
        $list = $model->withTotalCount()
            ->order('id', 'DESC')->field($field)->all();
        $this->writeJson(Status::CODE_OK, $list, 'success');
        return true;
    }

    /**
     * 更新账号
     */
    public function edit(){

        if(!empty($this->param['id'])){
            $data['username'] = $this->param['username'];
            $data['nickname'] = $this->param['nickname'];
            $data['type'] = $this->param['type']??1;
            $data['tel'] = $this->param['tel'];
            $data['status'] = $this->param['status'];
            $data['avatar'] = $this->param['avatar'];
            $data['update_time'] = time();
            if(empty($data['username'])){$this->AjaxJson(0, $this->param, '账号必须');return false; }

            if(empty($data['nickname'])){$this->AjaxJson(0, $this->param, '昵称必须');return false; }
            if(empty($data['tel'])){$this->AjaxJson(0, $this->param, '手机号必须');return false; }
            if(empty($data['avatar'])){$this->AjaxJson(0, $this->param, '头像必须');return false; }
            if(!empty($this->param['password'])){
                $config    = Config::getInstance();
                $pswstr = $config->getConf('PSW_STR');
                $data['password'] = md5($this->param['password'].$pswstr);
            }
            $model = UserModel::create();
            if($model->where('username',$data['username'])->where('id',$this->param['id'],'<>')->get()){
                $this->AjaxJson(Status::CODE_BAD_REQUEST, ['status'=>0], '账号已存在');return false;
            }
            if($model->where('tel',$data['tel'])->where('id',$this->param['id'],'<>')->get()){
                $this->AjaxJson(Status::CODE_BAD_REQUEST, ['status'=>0], '手机号已存在');return false;
            }
            try{
                if($model->where('id',$this->param['id'])->update($data)){
                    $this->AjaxJson(1,$data, '更新账号成功');return false;
                }else{
                    $this->AjaxJson(0, ['status'=>0], '更新账号失败');return false;
                }
            }catch (\Exception $e){
                $this->AjaxJson(0, ['status'=>0], '更新出错：'.$e->getMessage());
            }

        }else{
            $this->AjaxJson(0 ,['status'=>0], '账号ID不存在');
        }
        return false;
    }


    /**
     * 获取我绑定的支付账号
     */
    public function getMyWechat(){
        $wechat = WechatModel::create()->field('id as value,name as title,status')->all();
        if($wechat){
            $wechat = is_object($wechat)?$wechat->toArray():$wechat;
            foreach ($wechat as $k=>$v){
                $wechat[$k]['title'] = $v['status']?$v['title']:'【异常】'.$v['title'];
            }
        }
        $my_wechat_id = [];
        if($this->param['user_id']){
            $my_wechat_id = UserBindWechatModel::create()->where('user_id',$this->param['user_id'])->column('wechat_id');
        }
        $data  = ['left_data'=>$wechat,'right_data'=>$my_wechat_id??[],'user_id'=>$this->param];
        $this->AjaxJson(1,$data,'success');
        return true;
    }
    /**
     * 更新账号状态
     * param id
     * param status
     * return bool
     */
    public function doStatus(){
        $id = $this->param['id'];
        if(empty($id)){ $this->AjaxJson(0,['status'=>0], '账号ID必须'); return false;}
        $value = (int)$this->param['value']??0;
        $msg = $value==1?'恢复账号':'禁用账号';
        if(UserModel::create()->update(['status'=>$value,'update_time'=>time()],['id'=>$id])){
            $this->AjaxJson(1,['status'=>1], $msg.'成功');
        }else{
            $this->AjaxJson(0,['status'=>0], $msg.'失败');
        }
        return true;
    }
    /**
     * 设置测试员身份
     * param id
     * param status
     * return bool
     */
    public function doIsTest(){
        $id = $this->param['id'];
        if(empty($id)){ $this->AjaxJson(0,['status'=>0], '账号ID必须'); return false;}
        $value = (int)$this->param['value']??0;
        $msg = $value==1?'开通测试员身份':'关闭测试员身份';
        if(UserModel::create()->update(['is_test'=>$value,'update_time'=>time()],['id'=>$id])){
            $this->AjaxJson(1,['status'=>1], $msg.'成功');
        }else{
            $this->AjaxJson(0,['status'=>0], $msg.'失败');
        }
        return true;
    }
    /**
     * 删除用户
     */
    public function del(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            $data['is_deleted'] = 1;
            if( UserModel::create()->where('id',$ids,'in')->destroy()){
                $this->writeJson(Status::CODE_OK, ['status'=>1], '删除账号成功');return false;
            }else{
                $this->writeJson(Status::CODE_OK, ['status'=>0], '删除账号失败');return false;
            }
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST, ['status'=>0,'param'=>$this->param], '请选择要删除的账号');
        }
        return false;
    }

    /**
     *用户收款统计
     */
    public function getPayChart(){

        $value = $this->param['value']?$this->param['value']:date('Y-m-d');
        $start = strtotime($value.'00:00:00');
        $end = strtotime($value.'23:59:59');
        $model = UserModel::create();
        $list = $model->alias('u')
            ->join("( select user_id,sum(amount) as pay_money from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by user_id )  o",'u.id=o.user_id','LEFT')
            ->field('u.id,u.name,o.pay_money')->order('o.pay_money','desc')->select();
        $all_money = array_sum(array_column($list,'pay_money'));
        foreach ($list as $k=>$v){
            if($v['pay_money']>0){
                $list[$k]['r'] = (round($v['pay_money']/$all_money,2)*100).'%';
            }else{
                $v['pay_money'] = 0;
                $list[$k]['r'] = '0%';
            }

        }
        $this->AjaxJson(1,$list,'OK');return true;
        $list = UserModel::create()->where('')->order('pay_money','DESC')->field('id,name,pay_money')->limit(0,10)->select();
        $all_money = array_sum(array_column($list,'pay_money'));
        $this->AjaxJson(1,$list,'OK');
    }
    /**
     *用户收款统计 扇形图
     */
    public function getPayBieChart(){
        $type = $this->param['type']?$this->param['type']:1;
        if($type==1){
            $value = $this->param['value']?$this->param['value']:date('Y-m-d');
            $start = strtotime($value.'00:00:01');
            $end = strtotime($value.'23:59:59');
        }else{
            $month = !empty($this->param['value'])?$this->param['value']:date('Y-m');
            $start  = strtotime($month.'-01 00:00:01');
            $end=mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
        }
        $model = UserModel::create();
        $list = $model->alias('u')
            ->join("( select user_id,sum(amount) as pay_money from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by user_id )  o",'u.id=o.user_id','LEFT')
            ->field('u.id,u.name,o.pay_money')->order('o.pay_money','desc')->select();
        $all_money = array_sum(array_column($list,'pay_money'));
        $title = [];
        foreach ($list as $k=>$v){
            if($v['pay_money']>0){
                $list[$k]['r'] = (round($v['pay_money']/$all_money,2)*100).'%';
            }else{
                $v['pay_money'] = 0;
                $list[$k]['r'] = '0%';
            }
            $title[]  = $v['name'];
            $data[$k]['value'] = $v['pay_money']?$v['pay_money']:0;
            $data[$k]['name'] = $v['name'];
            $data[$k]['r'] = $list[$k]['r'];
        }
        $result =['title'=>$title,'data'=>$data,'all_money'=>round($all_money,2),'sql'=>$month];
        $this->AjaxJson(1,$result,'OK');return true;
    }
    /**
     *用户收款统计 表格
     */
    public function getTableChart(){
        $type = $this->param['type']??1;

        if($type==1){
            $value = $this->param['value']?$this->param['value']:date('Y-m-d');
            $start = strtotime($value.'00:00:00');
            $end = strtotime($value.'23:59:59');
        }else{
            $month = $this->param['value']??date('Y-m');
            $value=$month;
            $start  = strtotime($month.'-01 00:00:01');
            $end=mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
        }

        $model = UserModel::create();
        $list = $model->alias('u')
            ->join("( select user_id,sum(amount) as pay_money,count(*) as pay_num,sum(refund_amount) as refund_money,sum(is_refund) as refund_num from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by user_id )  o",'u.id=o.user_id','LEFT')
            ->field('u.id,u.name,o.pay_money,o.pay_num,o.refund_money,o.refund_num')->order('o.pay_money','desc')->select();

        foreach ($list as $k=>$v){
            $all = OrderModel::create()->where("user_id={$v['id']}  and create_time>={$start} and create_time<={$end}")->field('sum(amount) as money,count(*) num')->get();
            $list[$k]['num']  = $all['num']?$all['num']:0;
            $list[$k]['money']  = $all['money']?round($all['money'],2):0;
            if($v['pay_num']>0&&$all['num']>0){
                $list[$k]['refund_r'] = (round($v['refund_num']/$v['pay_num'],2)*100).'%';
                $list[$k]['pay_r'] = (round($v['pay_num']/$all['num'],2)*100).'%';
                $list[$k]['no_pay_r'] = ((100 - round($v['pay_num'] / $all['num'], 2) * 100)) . '%';
            }else{
                $list[$k]['pay_num'] = 0 ;
                $list[$k]['pay_money'] = 0;
                $list[$k]['refund_num'] = 0 ;
                $list[$k]['refund_money'] = 0;
                $list[$k]['refund_r'] = '0%';
                $list[$k]['pay_r'] = '0%';
                $list[$k]['no_pay_r'] = '0%';
            }
            $list[$k]['no_pay_num'] = $list[$k]['num'] -$list[$k]['pay_num'] ;
            $list[$k]['no_pay_money'] = round($list[$k]['money'] -$list[$k]['pay_money'],2) ;
            $list[$k]['date'] = $value;
            $list[$k]['all'] = $all;
        }
        $this->writeJson(Status::CODE_OK, ['total'=>count($list),'list'=>$list,'sql'=>$model->lastQuery()->getLastPrepareQuery()], 'OK');return true;

    }
    /**
     * 获取全部栏目
     */
    public function getAllUser(){
        $model = UserModel::create();
        $model->where('is_test',1);
        $field = "id as value,nickname as name";
        $order = ['id','desc'];
        $list = $model->order($order[0], $order[1])->field($field)->limit(0,20)->all();
        $this->AjaxJson(1, $list, 'success');
        return true;
    }
    protected function getValidateRule(?string $action): ?Validate
    {
        // TODO: Implement getValidateRule() method.
        switch ($action) {
            case 'login':
                $valitor = new Validate();
                $valitor->addColumn('u_account')->required();
                $valitor->addColumn('u_password')->required();
                return $valitor;
                break;
        }
        return NULL;
    }


}

