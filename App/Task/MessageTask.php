<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/11/20 0020
 * Time: 10:44
 */

namespace App\Task;

use App\Model\OrderModel;
use App\Model\SecretModel;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Smtp\Mailer;
use EasySwoole\Smtp\MailerConfig;
use EasySwoole\Smtp\Message\Html;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class MessageTask implements TaskInterface
{
    protected $data;
    //通过构造函数,传入数据,获取该次任务的数据
    public function __construct($data)
    {
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {
        var_dump("发送秘钥任务：".$taskId);
        $order_id = $this->data['order_id']; //订单信息
        $order = OrderModel::create()->where('id',$order_id??0)->get();
        //提现中才发起付款
        if($order['is_pay']==1){
            if($order['secret_key']){
                $secret = SecretModel::create()->where('order_id',$order['id'])->get();
                if(empty($secret)){
                    var_dump('秘钥不存在！');
                    return true;
                }
            }else{
                $secret = SecretModel::create()->where('category_id',$order['category_id'])->where('status',1)->get();
                if(empty($secret)){
                    var_dump('秘钥不存在！');
                    return true;
                }
                 SecretModel::create()->where('id',$secret['id'])->update(['order_id'=>$order['id'],'update_time'=>time(),'status'=>2]);
                $res = OrderModel::create()->where('id',$order['id'])->update(['secret_key'=>$secret['secret_key'],'update_time'=>time(),'is_send'=>1]);
                if(!$res){
                    var_dump('订单更新失败！');
                    return true;
                }
            }

            $tel = (string)$order['tel'];
            $message = "【软件激活码】您购买的产品秘钥激活码：{$secret['secret_key']}";
            if($this->sendMessage($tel,$message)){
                var_dump('发送短信成功，手机号：'.$tel.' 短信内容：'.$message);
                return true;
            }else{
                \EasySwoole\Component\Timer::getInstance()->after(5 * 1000, function ()use ($order_id) {
                    var_dump('重新投递异步任务');
                    $task = TaskManager::getInstance();
                    $task->async(new MessageTask(['order_id' => $order_id])); //投递任务
                });
                return '重新投递任务！';
            }
        }else{
            return '订单未支付！';
        }

        return true;
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
    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}