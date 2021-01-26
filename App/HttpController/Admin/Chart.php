<?php

namespace App\HttpController\Admin;


use App\Model\CategoryModel;
use App\Model\OrderModel;
use App\Model\TrackerPoint\TrackerPointModel;
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
class Chart extends Base
{


    /**
     * 今日注册用户
     */
    public function getTodayUser(){
        $today = date('Y-m-d');
        $start = strtotime($today.'00:00:00');
        $end = strtotime($today.'23:59:59');
        $model = UserModel::create();
        $TrackerPointModel = TrackerPointModel::create();
        $OrderModel =  OrderModel::create();
        $model->where('create_time',$start,'>=');
        $model->where('create_time',$end,'<=');
        $result['user_num'] = $model->count();
        //$result['amount'] = OrderModel::create()->where('pay_time',$start,'>')->where('pay_time',$end,'<=')->where('is_pay',1)->sum('price');
        $result['amount'] = OrderModel::create()->where('create_time',$start,'>')->where('create_time',$end,'<=')->where('is_pay',1)->sum('price');
        $result['refund_amount'] = OrderModel::create()->where('pay_time',$start,'>=')->where('pay_time',$end,'<=')->where('is_refund',1)->sum('price');
        $tracker = $TrackerPointModel->where('startTime',$start,'>=')->where('startTime',$end,'<=')->field('COUNT(DISTINCT ip) as ip_num ')->get();
        $result['ip_num'] = $tracker['ip_num']??0;
        $result['tracker'] =$tracker;
        $result['sql'] = $TrackerPointModel->lastQuery()->getLastPrepareQuery();
        $result['start'] = $start;
        $result['end'] = $end;
        $this->AjaxJson(1,$result,'OK');return true;

    }

    /**
     *项目收款统计
     */
    public function getPayChart(){

        $value = $this->param['value']?$this->param['value']:date('Y-m-d');
        $start = strtotime($value.'00:00:00');
        $end = strtotime($value.'23:59:59');
        $model = CategoryModel::create();
        $list = $model->alias('c')
            //->join("( select category_id,sum(price) as pay_money from td_order where is_pay=1 and pay_time>={$start} and pay_time<={$end} group by category_id )  o",'c.id=o.category_id','LEFT')
            ->join("( select category_id,sum(price) as pay_money from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by category_id )  o",'c.id=o.category_id','LEFT')
            ->field('c.id,c.name,o.pay_money')->order('o.pay_money','desc')->select();
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
    }
    /**
     *项目收款统计 扇形图
     */
    public function getPayBieChart(){
        $type = $this->param['type']?$this->param['type']:1;
        if($type==1){
            $value = $this->param['value']??date('Y-m-d');
            $start = strtotime($value.'00:00:00');
            $end = strtotime($value.'23:59:59');
        }else{
            $month = $this->param['value']??date('Y-m');
            $start  = strtotime($month.'-01 00:00:00');
            $end=mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
        }
        $model = CategoryModel::create();
        $list = $model->alias('c')
            ->join("( select category_id,sum(price) as pay_money from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by category_id )  o",'c.id=o.category_id','LEFT')
            ->field('c.id,c.name,o.pay_money')->order('o.pay_money','desc')->select();
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
        $result =['title'=>$title,'data'=>$data,'all_money'=>round($all_money,2)];
        $this->AjaxJson(1,$result,'OK');return true;
    }
    /**
     *项目收款统计 表格
     */
    public function getTableChart(){
        $type = $this->param['type']??1;

        if($type==1){
            $value = $this->param['value']??date('Y-m-d');
            $start = strtotime($value.'00:00:00');
            $end = strtotime($value.'23:59:59');
        }else{
            $month = $this->param['value']??date('Y-m');
            $value=$month;
            $start  = strtotime($month.'-01 00:00:01');
            $end=mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
        }

        $model = CategoryModel::create();
        $list = $model->alias('c')
            ->join("( select category_id,sum(price) as pay_money,count(*) as pay_num,sum(refund_amount) as refund_money,sum(is_refund) as refund_num from td_order where is_pay=1 and create_time>={$start} and create_time<={$end} group by category_id )  o",'c.id=o.category_id','LEFT')
            ->field('c.id,c.name,o.pay_money,o.pay_num,o.refund_money,o.refund_num')->order('o.pay_money','desc')->select();

        foreach ($list as $k=>$v){
            $all = OrderModel::create()->where("category_id={$v['id']}  and create_time>={$start} and create_time<={$end}")->field('sum(price) as money,count(*) num')->get();
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
     * 每日收款统计
     */
    public function getDayChart(){
        //select FROM_UNIXTIME(create_time,'%Y-%m-%d') as `day`,sum(amount) as pay_money,sum(refund_amount) as refund_money from td_order GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d') LIMIT 12
        $field = "FROM_UNIXTIME(create_time,'%Y-%m-%d') as `day`,sum(price) as pay_money,sum(refund_amount) as refund_money";
        $model = OrderModel::create();
        if(!empty($this->param['user_id'])&&$this->param['user_id']!='all'){
            $model->where('category_id',$this->param['user_id']);
        }
        $list = $model->where('is_pay',1)->field($field)->group("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->limit('0',12)->order('create_time','DESC')->select();
        $x=$y_one=$y_two=[];
        foreach ($list as $k=>$v){
            array_unshift($x,$v['day']);
            array_unshift($y_one,$v['pay_money']-$v['refund_money']);
            array_unshift($y_two,$v['refund_money']);
        }
        $this->AjaxJson(1,['x'=>$x,'y_one'=>$y_one,'y_two'=>$y_two],'OK');
        return true;
    }

    /**
     * 每月收款统计
     */
    public function getMonthChart(){
        $field = "FROM_UNIXTIME(create_time,'%Y-%m') as `day`,sum(price) as pay_money,sum(refund_amount) as refund_money";
        $model = OrderModel::create();
        if(!empty($this->param['user_id'])&&$this->param['user_id']!='all'){
            $model->where('category_id',$this->param['user_id']);
        }
        $list = $model->where('is_pay',1)->field($field)->group("FROM_UNIXTIME(create_time,'%Y-%m')")->limit('0',12)->order('create_time','DESC')->select();
        $x=$y_one=$y_two=[];
        foreach ($list as $k=>$v){
            array_unshift($x,$v['day']);
            array_unshift($y_one,$v['pay_money']);
            array_unshift($y_two,$v['refund_money']);
        }
        $this->AjaxJson(1,['x'=>$x,'y_one'=>$y_one,'y_two'=>$y_two],'OK');
        return true;
    }


}

