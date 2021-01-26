<?php

namespace App\HttpController\Index;
use App\HttpController\Admin\Keyword;
use App\HttpController\Common\Regex;
use App\Model\ArticleModel;
use App\Model\CategoryModel;
use App\Model\CommentModel;
use App\Model\OrderModel;
use App\Model\OrderNotifyModel;
use App\Model\RefererModel;
use App\Model\SecretModel;
use App\Model\SystemModel;
use App\Model\UserBindWechatModel;
use App\Model\UserModel;
use App\Model\WechatModel;
use App\Model\WeixinModel;
use App\Task\MessageTask;
use App\Task\PayTask;
use App\Utility\MyQueue;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Message\Status;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;
use EasySwoole\Session\Session;
use EasySwoole\Template\Render;


/**
 * Class Users
 * Create With Automatic Generator
 */
class Index extends \App\HttpController\Index\Base
{

    /**
     * 测试
     */
    public function test(){

        $this->writeJson(200,[],'测试页面');return false;
        $this->writeJson(200,$this->sendMessage('17607696200','【软件激活码】您购买的产品秘钥激活码：509e8fe83f73f7ef69ea8bf440751'),'短信发送');return false;
        $order_id =1;
        \EasySwoole\Component\Timer::getInstance()->after(5 * 1000, function ()use ($order_id) {
            $task = TaskManager::getInstance();
            $task->async(new MessageTask(['order_id' => $order_id])); //投递任务
        });
        $this->writeJson(200,[],'投递任务成功');return false;

        $i=1;$list =$list_1=[];
        while ($i){
            if($i%2==0){
                $list[] = $i;
            }else{
                $list_1[] = $i;
            }
            if($i>100){
                break;
            }
            $i++;
        }
        $redis=\EasySwoole\Pool\Manager::getInstance()->get('redis')->getObj();
        foreach ($list as $v){
            $redis->lPush('list',$v);
        }
        foreach ($list_1 as $v){
            $redis->lPush('list',$v);
        }
        $this->writeJson(200,[],'ok'); return false;
        $job = new Job();
        $job->setJobData(['time'=>time()]);
        $res = MyQueue::getInstance()->producer()->push($job);
    }
    public function sendMessage($tel,$message){
        $url = "https://way.jd.com/chuangxin/dxjk?mobile={$tel}&content={$message}&appkey=076e7a1520b8f602c40c6b11d08d6da1";
        $client = new \EasySwoole\HttpClient\HttpClient($url);
        $client->getClient();
        $res = $client->get();
        $data =json_decode($res->getBody(),true);
        var_dump($data);
        if($data['code']=='10000'&&!empty($data['result']['ReturnStatus'])&&$data['result']['ReturnStatus']=='Success'){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取图片验证码
     */
    public function getImageCode(){
        $config = new \EasySwoole\VerifyCode\Conf();
        $config->setCharset('0123456789');
        $code = new \EasySwoole\VerifyCode\VerifyCode($config);
        $this->response()->withHeader('Content-Type','image/png');
        $obj = $code->DrawCode();
        $this->response()->write($obj->getImageByte());
        \EasySwoole\Session\Session::getInstance()->set('image_code',$obj->getImageCode());
    }

    public function search(){
      //  $this->writeJson(200,'','ok');return false;
        $this->assign['tel'] = $this->param['tel']??'';
        $this->assign['order_no'] = $this->param['order_no']??'';
        $this->view($this->pc.'/index/index/search',$this->assign);
    }
    public function getMyOrder(){
        $page = (int)($this->param['page']??1);
        $limit = (int)($this->param['limit']??10);
        $model = OrderModel::create();
        if(!empty($this->param['tel'])||!empty($this->param['order_no'])){
            if(!empty($this->param['tel'])){ $model->where('o.tel',$this->param['tel']); }
            if(!empty($this->param['order_no'])){ $model->where('o.order_no',$this->param['order_no']); }

            $list = $model->withTotalCount()->field('o.order_no,o.tel,o.price,o.secret_key,o.pay_time,o.create_time,c.name as category')->alias('o')
                ->join('td_category c','c.id=o.category_id','LEFT')
                ->order('o.id','desc')->limit($limit * ($page - 1), $limit)->all();
            $total = $model->lastQueryResult()->getTotalCount();
            if(is_object( $list)){  $list =   $list->toArray();}
            foreach ($list as $k=>$v){
                if(empty($v['secret_key'])){
                    $list[$k]['secret_key'] = SecretModel::create()->where('order_id',$v['id'])->val('secret_key')??'正在拼命发货中,请稍等几分钟...';
                }
            }
            $msg = $list?"查询成功":'未查询到您的订单';
        }else{
            $list=[];$total = 0;$msg = '请输入查询信息后查询';
        }

        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => 0,
                "data" => $list,
                "count" => $total,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
        $this->writeJson(200, ['total'=>$total,'data'=>$list], '获取成功');
        return true;
    }
    /**
     * 授权登录
     */
    public function index()
    {

        $query_string = $this->request()->getServerParams()['query_string']??'';
        if(!empty($query_string)){
            Session::getInstance()->set('string',$query_string);
        }
        $this->assign['category'] =CategoryModel::create()->all();
        $this->view($this->pc.'/index/index/index',$this->assign);
        return true;
    }


    /**
     * 生成订单
     */
    public function doOrder(){

        try{
            if(empty($this->param['category_id'])){
                $this->AjaxJson(0,$this->param,'请选择产品版本');return false;
            }
            $category = CategoryModel::create()->where('id',$this->param['category_id'])->get();
            if(empty($category)){
                $this->AjaxJson(0,$this->param,'产品不存在');return false;
            }
            if(empty($this->param['tel'])||!Regex::is_mobile($this->param['tel'])){
                $this->AjaxJson(0,$this->param,'请输入正确的手机号！');return false;
            }
            $pay_type = $this->param['pay_type']??2;
            $system = $this->getSystem();
            if($this->param['tel']=='13662829560'){
                $category['price'] =0.01;
            }
            $order_id = $this->createOrder($category['id'],$category['remark'],$category['price'],$this->param['tel'],$pay_type);
            if(empty($order_id)){
                $this->AjaxJson(0,[],'下单失败请重试！'); return false;
            }
            $this->AjaxJson(1,['url'=>"/pay?order_id={$order_id}&pay_type={$pay_type}"],'下单成功，正在跳转前往支付！'); //,'photo_key'=>$photo_key,$photoMake,$photoArrange
        }catch (\Throwable $e){
            $this->AjaxJson(0,[$e->getMessage()],'下单失败请重试！'); return false;
        }


    }
    /**
     * 检测订单是否支付
     */
    public function checkOrderIsPay(){
        $referer = $this->request()->getHeaderLine('referer');
        if(strpos($referer,$this->host)===false){
            $this->AjaxJson(0,[], '上传失败');return  false;
        }
        if(empty($this->param['order_id'])){
            $this->AjaxJson(0,[],'没有生成订单');return false;
        }
        $order =OrderModel::create()->where('id',$this->param['order_id'])->get();
        if($order['is_pay']==1){
            $this->AjaxJson(1,['url'=>'/pictures?transaction_id='.$order['transaction_id'].'&order_no='.$order['order_no']],'订单已支付，正在跳转下载照片');return false;
        }
        $this->AjaxJson(0,[],'订单未支付');
    }

    /**
     * 生成订单
     */
    public function createOrder($category_id,$remark,$price,$tel,$pay_type){

        $order['user_order_no'] =  'W' . date('YmdHis') . rand(1000, 9999);
        $order['order_no'] =  '';
        $order['transaction_id'] =  '';
        $order['category_id'] =  $category_id;
        $order['type'] =  $this->isWeixin()==true?1:2;
        $order['pay_way'] =  $pay_type;
        $order['remark'] =  $remark;
        $order['price'] =  $price;
        $order['tel'] = $tel;
        $referer = RefererModel::create()->where('ip',$this->getRealIp())->order('id','desc')->field('keyword,url')->get();
        $url = Session::getInstance()->get('string')??'';
        $keyword = Session::getInstance()->get('keyword')??'';
        $is_mobile = $this->is_mobile()?'yd':'pc';
        $order['url'] = '标识：'.($referer['url']??$url).' 词：'.($referer['keyword']??$keyword).$is_mobile;
        $order['url'] =   $order['url']??'';
        $order['ip'] =   $this->getRealIp()??'';
        $order['other_param'] =  "{'{$is_mobile}'}";
        $order['create_time'] =  time();
        $order['update_time'] =  time();

        DbManager::getInstance()->startTransaction();
        $model = OrderModel::create();
        while ($model->where('user_order_no',$order['user_order_no'])->find()){
            $order['user_order_no'] =  'CN' . date('YmdHis') . rand(1000, 9999);
        }
        $order['order_no']  =$order['user_order_no'];
        if($order_id = $model->data($order)->save()){
            DbManager::getInstance()->commit();
            return $order_id;
        }else{
            DbManager::getInstance()->rollback();
            return false;
        }

    }

    /**
     * 重启模板
     */
    function viewReload(){
        Render::getInstance()->restartWorker();
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $this->response()->write('模板重启成功');
    }
    public function actionNotFound(?string $action){
        $this->error('你请求的页面不存在','/index');
        return true;
    }

}

