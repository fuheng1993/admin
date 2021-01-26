<?php

namespace App\HttpController\Admin;

use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\SystemModel;
use App\Model\WechatModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Order extends Base
{
    /**
     * 订单列表
     */
    public function lists()
    {
        $page = (int)($this->param['page']??1);
        $limit = (int)($this->param['limit']??10);
        $model = OrderModel::create();
        if(!empty($this->param['is_pay'])){
            $where_field = $this->param['is_pay']==1?'o.pay_time':'o.create_time';
            $model->where('o.is_pay',$this->param['is_pay']==1?1:0);
        }else{
            //$model->where('o.is_pay',1);
            $where_field = 'o.create_time';
        }

        if(!empty($this->param['category_id'])){ $model->where('o.category_id',$this->param['category_id']); }
        if(!empty($this->param['is_refund'])){ $model->where('o.is_refund',$this->param['is_refund']==1?1:0); }
        if(!empty($this->param['user_id'])){ $model->where('o.user_id',explode(',',$this->param['user_id']) ,'in'); }
        if(!empty($this->param['user_order_no'])){ $model->where('o.user_order_no',"%{$this->param['user_order_no']}%",'like'); }
        if(!empty($this->param['order_no'])){ $model->where('o.order_no',"%{$this->param['order_no']}%",'like'); }
        if(!empty($this->param['url'])){
            if($this->param['url']=='空白'){
                $model->where('o.url','标识： 词：');

            }else{
                $model->where('o.url',"%{$this->param['url']}%",'like');
            }

        }
        if(!empty($this->param['bs'])){ $model->where('o.url',"%{$this->param['bs']}%",'like'); }
        if(!empty($this->param['remark'])){ $model->where('o.remark',"%{$this->param['remark']}%",'like'); }
        if(!empty($this->param['name'])){ $model->where("(o.tel like '%{$this->param['name']}%')"); }
        if(!empty($this->param['city'])){ $model->where('o.city',"%{$this->param['city']}%",'like'); }
        if(!empty($this->param['province'])){ $model->where('o.province',"%{$this->param['province']}%",'like'); }
        if(!empty($this->param['start'])){ $model->where($where_field,strtotime($this->param['start']),'>='); }
        if(!empty($this->param['end'])){ $model->where($where_field,strtotime($this->param['end']),'<='); }
        $list = $model->withTotalCount()->field('o.*,u.nickname as user_name')->alias('o')
            ->join('td_user u','o.user_id=u.id','LEFT')
            ->order('o.id','desc')->limit($limit * ($page - 1), $limit)->all();
        $total = $model->lastQueryResult()->getTotalCount();
        if(is_object( $list)){  $list =   $list->toArray();}
        foreach ($list as $k=>$v){
            $url = explode('词：',$v['url']);
            if(!empty($url[0])){
                $bs = explode('标识：',$url[0]);
                $list[$k]['bs'] = $bs[1]??$url[0];
                $list[$k]['keyword'] = $url[1]??'';
            }

        }
        $this->writeJson(Status::CODE_OK, ['total'=>$total,'list'=>$list,'sql'=>$model->lastQuery()->getLastPrepareQuery()], 'OK');
        return true;
    }
    /**
     * 订单发货
     */
    public function send(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            $model =  OrderModel::create();
            if($model->where('is_pay',0)->where('id',$ids,'in')->findOne()){
                $this->AjaxJson(0, [], '未付款订单不可发货');  return false;
            }
            if($model->where('status',1,'<>')->where('id',$ids,'in')->findOne()){
                $this->AjaxJson(0, [], '只有新订单才可发货');  return false;
            }
            if( OrderModel::create()->where('id',$ids,'in')->update(['status'=>2,'send_time'=>time()])){
                $this->AjaxJson(1, ['status'=>1], '订单发货成功');return false;
            }else{
                $this->writeJson(Status::CODE_OK, ['status'=>0], '订单发货失败');return false;
            }
        }else{
            $this->AjaxJson(0,$this->param, '请选择要发货的订单');  return false;
        }
        return false;
    }
    /**
     * 订单完成 不可再修改
     */
    public function finish(){
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            $model =  OrderModel::create();
            if($model->where('is_pay',0)->where('id',$ids,'in')->findOne()){
                $this->AjaxJson(0, [], '未付款订单不可关闭');  return false;
            }
            if( OrderModel::create()->where('id',$ids,'in')->update(['is_finish'=>1,'is_close'=>1,'update_time'=>time()])){
                $this->AjaxJson(1, ['status'=>1], '订单关闭成功');return false;
            }else{
                $this->AjaxJson(0, ['status'=>0], '订单关闭失败');return false;
            }
        }else{
            $this->AjaxJson(0,$this->param, '请选择要关闭的订单');  return false;
        }
        return false;
    }
    /**
     * 百度OCPC回传
     */
    public function doOcpc(){
        $model = OrderModel::create();
        if(!empty($this->param['is_pay'])){
            $where_field = 'pay_time';
            $model->where('is_pay',$this->param['is_pay']==1?1:0);
        }else{
            $where_field = 'create_time';
        }
        if(!empty($this->param['category_id'])){ $model->where('category_id',$this->param['category_id']); }
        if(!empty($this->param['is_refund'])){ $model->where('is_refund',$this->param['is_refund']==1?1:0); }
        if(!empty($this->param['user_id'])){ $model->where('user_id',explode(',',$this->param['user_id']) ,'in'); }
        if(!empty($this->param['user_order_no'])){ $model->where('user_order_no',"%{$this->param['user_order_no']}%",'like'); }
        if(!empty($this->param['order_no'])){ $model->where('order_no',"%{$this->param['order_no']}%",'like'); }
        if(!empty($this->param['url'])){ $model->where('url',"%{$this->param['url']}%",'like'); }
        if(!empty($this->param['bs'])){ $model->where('url',"%{$this->param['bs']}%",'like'); }
        if(!empty($this->param['city'])){ $model->where('city',"%{$this->param['city']}%",'like'); }
        if(!empty($this->param['province'])){ $model->where('province',"%{$this->param['province']}%",'like'); }
        if(!empty($this->param['remark'])){ $model->where('remark',"%{$this->param['remark']}%",'like'); }
        if(!empty($this->param['name'])){ $model->where("(tel like '%{$this->param['name']}%')"); }
        if(!empty($this->param['start'])){ $model->where($where_field,strtotime($this->param['start']),'>='); }
        if(!empty($this->param['end'])){ $model->where($where_field,strtotime($this->param['end']),'<='); }
        $list = $model->withTotalCount()->field('create_time,url')->order('id','desc')->limit(0,5000)->all();
        $conversionTypes = [];
        $system = SystemModel::create()->where('id',1)->get();
        foreach ($list as $k=>$v){
            $keywords = explode('词：',$v['url']);
            $bs = $keywords[0]??'';
            if($bs){
                $bs = explode('标识：',$bs);
                $bs = $bs[1]??'';
            }

            $conversionTypes[] = [
                "logidUrl"=> $system['bd_host'].'?'.$bs,
                "newType"=>$system['bd_type']??3,
                'conversionTime'=>strtotime($v['create_time'])-60
            ];
        }

        $url = "https://ocpc.baidu.com/ocpcapi/api/uploadConvertData";
        $client = new \EasySwoole\HttpClient\HttpClient($url);
        $token = $system['bd_ocpc_token'];

        $reqData = array('token' => $token, 'conversionTypes' => $conversionTypes);
        $reqData = json_encode($reqData);
        $client->setHeader('content-type','application/json');
        $res = $client->postJson($reqData);
        $this->writeJson(200,'回传提交成功,总共提交：'.count($conversionTypes).' 回传结果：'.$res->getBody(),'OK');
        return false;
    }
    /**
     * 测试导出10条数据到csv
     */
    public function expUser(){
        $model = OrderModel::create();
        if(!empty($this->param['is_pay'])){
            $where_field = 'pay_time';
            $model->where('is_pay',$this->param['is_pay']==1?1:0);
        }else{
            $where_field = 'create_time';
        }
        if(!empty($this->param['category_id'])){ $model->where('category_id',$this->param['category_id']); }
        if(!empty($this->param['is_refund'])){ $model->where('is_refund',$this->param['is_refund']==1?1:0); }
        if(!empty($this->param['user_id'])){ $model->where('user_id',explode(',',$this->param['user_id']) ,'in'); }
        if(!empty($this->param['user_order_no'])){ $model->where('user_order_no',"%{$this->param['user_order_no']}%",'like'); }
        if(!empty($this->param['order_no'])){ $model->where('order_no',"%{$this->param['order_no']}%",'like'); }
        if(!empty($this->param['url'])){ $model->where('url',"%{$this->param['url']}%",'like'); }
        if(!empty($this->param['bs'])){ $model->where('url',"%{$this->param['bs']}%",'like'); }
        if(!empty($this->param['city'])){ $model->where('city',"%{$this->param['city']}%",'like'); }
        if(!empty($this->param['province'])){ $model->where('province',"%{$this->param['province']}%",'like'); }
        if(!empty($this->param['remark'])){ $model->where('remark',"%{$this->param['remark']}%",'like'); }
        if(!empty($this->param['name'])){ $model->where("(tel like '%{$this->param['name']}%')"); }
        if(!empty($this->param['start'])){ $model->where($where_field,strtotime($this->param['start']),'>='); }
        if(!empty($this->param['end'])){ $model->where($where_field,strtotime($this->param['end']),'<='); }
        $list = $model->withTotalCount()->field('id,url,ip,province,city,price,pay_time,create_time,remark,vin')->order('id','desc')->limit(0,5000)->all();
        $arrData = [];
        $title = [['项目','车架号','标识','关键字','省份','城市','价格','付款时间','访问时间']];
        foreach ($list as $k=>$v){
            $keywords = explode('词：',$v['url']);
            $bs = $keywords[0]??'';
            if($bs){
                $bs = explode('标识：',$bs);
                $bs = $bs[1]??'';
            }
            $kw = $keywords[1]??'';
            $arrData[] = [
                'xm'=>$v['remark'],
                'vin'=>$v['vin'],
                'bs'=>$bs,
                'kw'=>$kw,
                'sf'=>$v['province'],
                'ct'=>$v['city'],
                'price'=>$v['price'],
                'fk'=>$v['pay_time'],
                'xd'=>$v['create_time'],
            ];
        }

        $arrData = array_merge($title, $arrData);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // 设置单元格格式 可以省略
        $styleArray = ['font' => ['bold' => true, 'size' => 14,],];
        $spreadsheet->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
//        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);
//        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $spreadsheet->getActiveSheet()->fromArray($arrData);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        //这里可以写绝对路径，其他框架到这步就结束了
        $writer->save(EASYSWOOLE_ROOT.'/public/test.xlsx');
        //关闭连接，销毁变量
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        //生成文件后，使用response输出
        $this->response()->write(file_get_contents(EASYSWOOLE_ROOT.'/public/test.xlsx'));
        $this->response()->withHeader('Content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response()->withHeader('Content-Disposition', 'attachment;filename="test.csv"');
        $this->response()->withHeader('Cache-Control','max-age=0');
        $this->response()->end();
        return false;
    }
    /**
     * 订单退款
     */
    public function refund(){
        if(!empty($this->param['id'])){
            $model =  OrderModel::create();
            $order=  $model->where('id',$this->param['id'])->get();
            if (empty($order)) {
                $this->AjaxJson(0, [], '订单不存在');
                return false;
            }
            if ($order->is_refund == 1) {
                $this->AjaxJson(0, [], '订单已关闭，不能退款！');
                return false;
            }
            $post_data['appid']       = 1;
            $post_data['token']       = 'b4270ddc175b0a0647900a661e8f8a8b';
            $post_data['order_id'] = $order['order_id'];
            $url = "http://22.rmrf.top/api/pay/refundOrder";
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            //设置head头
            $client->setHeaders([
                //'Content-type' => 'application/json;charset=utf-8',
            ]);
            $response = $client->post($post_data);
            $res = json_decode($response->getBody(),true);
            if($res['code']==200&&$res['status']==1&&$res['result']['refund_trade_no']){
                OrderModel::create()->where('id',$order['id'])->update(['is_refund'=>1,'refund_amount'=>$order['price'],'refund_trade_no'=>$res['result']['refund_trade_no'],'refund_time'=>$res['result']['refund_time']]);

                $this->AjaxJson(1,$res,'退款成功');
            }else{
                if($res['msg']=='此订单已退款'){
                    OrderModel::create()->where('id',$order['id'])->update(['is_refund'=>1,'refund_amount'=>$order['price'],'refund_trade_no'=>'','refund_time'=>time()]);
                    $this->AjaxJson(1,$res,'订单已退款');
                }else{
                    $this->AjaxJson(0,$res,$res['msg']);
                }

            }
        }else{
            $this->AjaxJson(0,$this->param, '请选择要退款的订单');  return false;
        }

    }
    /**
     * 删除订单
     */
    public function del()
    {
        if(!empty($this->param['ids'])){
            $ids = is_array($this->param['ids'])?$this->param['ids']:explode(',',$this->param['ids']);
            $model =  OrderModel::create();
            if($model->where('is_pay',1)->where('id',$ids,'in')->get()){
                $this->AjaxJson(0, [], '未付款订单才能删除');  return false;
            }
            if($model->where('id',$ids,'in')->destroy()){
                $this->AjaxJson(1, [], '订单删除成功');return false;
            }else{
                $this->AjaxJson(0,[], '订单删除失败');return false;
            }
        }else{
            $this->AjaxJson(0,$this->param, '请选择要删除的订单');  return false;
        }
        return false;
    }
    /**
     * 每日收款统计
     */
    public function getDayChart(){
        //select FROM_UNIXTIME(create_time,'%Y-%m-%d') as `day`,sum(amount) as pay_money,sum(refund_amount) as refund_money from td_order GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d') LIMIT 12
        $field = "FROM_UNIXTIME(create_time,'%Y-%m-%d') as `day`,sum(amount) as pay_money,sum(refund_amount) as refund_money";
        $list =  OrderModel::create()->where('is_pay',1)->field($field)->group("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->limit('0',12)->select();
        $x = $y_one = $y_two=[];
        foreach ($list as $k=>$v){
            $x[] = $v['day'];
            $y_one[] = $v['pay_money'];
            $y_two[] = $v['pay_money'];
        }
        $this->AjaxJson(1,['x'=>$x,'y_one'=>$y_one,'y_two'=>$y_two],'OK');
        return true;
    }

    /**
     * 每月收款统计
     */
    public function getMonthChart(){
        $field = "FROM_UNIXTIME(create_time,'%Y-%m') as `day`,sum(amount) as pay_money,sum(refund_amount) as refund_money";
        $list =  OrderModel::create()->where('is_pay',1)->field($field)->group("FROM_UNIXTIME(create_time,'%Y-%m')")->limit('0',12)->select();
        $x = $y_one = $y_two=[];
        foreach ($list as $k=>$v){
            $x[] = $v['day'];
            $y_one[] = $v['pay_money'];
            $y_two[] = $v['pay_money'];
        }
        $this->AjaxJson(1,['x'=>$x,'y_one'=>$y_one,'y_two'=>$y_two],'OK');
        return true;
    }
    /**
     * 当日收款统计
     */
    public function getTodayChart(){
        $start = strtotime(date('Y-m-d').'00:00:00');
        $end = strtotime(date('Y-m-d').'23:59:59');
        $res = OrderModel::create()->where('is_pay=1')
                ->where('create_time',$start,'>=')
                ->where('create_time',$end,'<=')
                ->field('sum(amount) as pay_money,count(*) as pay_num,sum(refund_amount) as refund_money,sum(is_refund) as refund_num')->get();
        $this->AjaxJson(1,$res,'OK');
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

