<?php

namespace App\HttpController\Index;
use App\HttpController\Common\Regex;
use App\Model\ArticleModel;
use App\Model\CategoryModel;
use App\Model\CommentModel;
use App\Model\OrderModel;
use App\Model\OrderNotifyModel;
use App\Model\SystemModel;
use App\Model\UserModel;
use App\Model\WechatModel;
use App\Model\WeixinModel;
use App\Task\PayTask;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Template\Render;
use http\Client;


/**
 * Class Users
 * Create With Automatic Generator
 */
class Pay2 extends \App\HttpController\Index\Base
{


    /**
     * 微信扫码支付
     */
    public function pay()
    {
        try{
            if(empty($this->param['order_id'])){
                $this->AjaxJson(0,[],'订单ID必须');return false;
            }
            $order = OrderModel::create()->where('id',$this->param['order_id'])->get();
            $system = $this->system;
            if (Cache::getInstance()->get('pay_qr_code' . $order['id'])) {
                $pay_qr_code = Cache::getInstance()->get('pay_qr_code' . $order['id']);
            } else {
                $host_name = $this->request()->getHeaders()['host'][0];
                $wechatConfig = new \EasySwoole\Pay\WeChat\Config();
                $wechatConfig->setAppId($system['appid']);      // 公众号APPID
                $wechatConfig->setMchId($system['mchid']);          //商户号
                $wechatConfig->setKey($system['key']);//支付key
                $wechatConfig->setNotifyUrl("{$host_name}/wxNotify"); //支付回调地址
                $wechatConfig->setApiClientCert(EASYSWOOLE_ROOT . $system['cert_pem']);//客户端证书
                $wechatConfig->setApiClientKey(EASYSWOOLE_ROOT . $system['key_pem']); //客户端证书秘钥
                $bean = new \EasySwoole\Pay\WeChat\RequestBean\Scan();
                $bean->setOutTradeNo($order['order_no']);
                $bean->setProductId($order['order_no']);
                $bean->setBody( $order['remark']);
                $bean->setTotalFee($order['price']*100);
                $bean->setSpbillCreateIp($this->getRealIp());
                $pay = new \EasySwoole\Pay\Pay();
                $data = $pay->weChat($wechatConfig)->scan($bean);
                $pay_qr_code = $data->getCodeUrl();
            }

            $this->AjaxJson(1,['pay_qr_code'=>$pay_qr_code],'支付生成成功');
            return false;
        }catch (\Throwable $e){
            $this->AjaxJson(0,[],$e->getMessage());
            return false;
        }


    }
    /**
     * 微信H5支付
     */
    public function wxPayH5()
    {
        try{
            if(empty($this->param['order_id'])){
                $this->AjaxJson(0,[],'订单ID必须');return false;
            }
            $order = OrderModel::create()->where('id',$this->param['order_id'])->get();
            if(empty($order)){
                $this->AjaxJson(0,[],'订单不存在');return false;
            }
            if($order['is_pay']==1){
                $this->response()->redirect('/pictures?transaction_id='.$order['transaction_id'].'&order_no='.$order['order_no']);
                return false;
            }


            $system = $this->system;
            if (Cache::getInstance()->get('wx_h5_' . $order['id'])) {
                $url = Cache::getInstance()->get('wx_h5_' . $order['id']);
            } else {
                $host_name = $this->request()->getHeaders()['host'][0];
                $wechatConfig = new \EasySwoole\Pay\WeChat\Config();
                $wechatConfig->setAppId($system['appid']);      // 公众号APPID
                $wechatConfig->setMchId($system['mchid']);          //商户号
                $wechatConfig->setKey($system['key']);//支付key
                $wechatConfig->setNotifyUrl("{$host_name}/wxNotify"); //支付回调地址
                $wechatConfig->setApiClientCert(EASYSWOOLE_ROOT . $system['cert_pem']);//客户端证书
                $wechatConfig->setApiClientKey(EASYSWOOLE_ROOT . $system['key_pem']); //客户端证书秘钥

                $wap = new \EasySwoole\Pay\WeChat\RequestBean\Wap();
                $wap->setOutTradeNo($order['order_no']);
                $wap->setBody($order['remark']);
                $wap->setTotalFee($order['price'] * 100);
                $wap->setSpbillCreateIp($this->getRealIp());
                $pay = new \EasySwoole\Pay\Pay();
                $params = $pay->weChat($wechatConfig)->wap($wap);
                $jsApiParameters = $params->toArray();
                $url = $jsApiParameters['mweb_url'] . '&redirect_url=' . urlencode("{$host_name}/wxPayH5?order_id={$order['id']}");
                Cache::getInstance()->set('wx_h5_' . $order['id'], $url, 300);
            }

            $data['url'] = $url;
            $data['order'] = $order;
            $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
            $this->response()->write(Render::getInstance()->render('/index/pay/h5pay', $data));
            return false;
        }catch (\Throwable $e){
            $this->writeJson(200,[$system['mchid'],$system['appid'],$system['key'],$system['secret']],$e->getMessage());
            return false;
        }


    }
    public function AliPayScan(){
        try{
            $system = $this->system;
            $aliConfig = new \EasySwoole\Pay\AliPay\Config();
            $aliConfig->setGateWay(\EasySwoole\Pay\AliPay\GateWay::NORMAL);
            $aliConfig->setAppId($system['zfb_mchid']);
            $aliConfig->setPublicKey($system['zfb_public_key']);
            $aliConfig->setPrivateKey($system['zfb_private_key']);
            $pay = new \EasySwoole\Pay\Pay();
            $order = new \EasySwoole\Pay\AliPay\RequestBean\Scan();
            $order->setSubject('测试');
            $order->setTotalAmount('0.01');
            $order->setOutTradeNo(time());

            $aliPay = $pay->aliPay($aliConfig);
            $data = $aliPay->scan($order)->toArray();
            $response = $aliPay->preQuest($data);
            $this->writeJson(200,$response,'ok');return false;

        }catch (\Throwable $e){
            $this->writeJson(200,[$system['zfb_mchid'],$system['zfb_private_key'],$system['zfb_public_key']],$e->getMessage());
            return false;
        }

    }
    public function AliPayH5(){
        try{
            //检测订单
            $order_no = 'CN' . date('YmdHis') . rand(1000, 9999);
            $order = [
                'id'=>'id',
                'order_no'=>$order_no,
                'remark'=>'服务费',
                'amount'=>0.01,
            ];
            $system = $this->system;
            $aliConfig = new \EasySwoole\Pay\AliPay\Config();
            $aliConfig->setGateWay(\EasySwoole\Pay\AliPay\GateWay::NORMAL);
            $aliConfig->setAppId($system['zfb_mchid']);
            $aliConfig->setPublicKey($system['zfb_public_key']);
            $aliConfig->setPrivateKey($system['zfb_private_key']);
            $pay = new \EasySwoole\Pay\Pay();

            ## 对象风格
            $pay_order = new  \EasySwoole\Pay\AliPay\RequestBean\Wap();
            $pay_order->setSubject($order['remark']);
            $pay_order->setOutTradeNo($order['order_no']);
            $pay_order->setTotalAmount($order['amount']);
            $res = $pay->aliPay($aliConfig)->wap($pay_order);
            $html = $this->buildPayHtml(\EasySwoole\Pay\AliPay\GateWay::NORMAL, $res->toArray());
            $this->response()->write($html);


        }catch (\Throwable $e){
            $this->writeJson(200,[$system['zfb_mchid'],$system['zfb_private_key'],$system['zfb_public_key']],$e->getMessage());
            return false;
        }

    }
    /**
     * 支付宝支付HTML页面
     */
    public function buildPayHtml($endpoint, $payload)
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $endpoint . "' method='POST'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
    // 检测订单是否存在且未付款
    public function checkOrder($order_id = 0)
    {
        if (empty($order_id)) {
            return '订单不存在';
        }
        $order = OrderModel::create()->where('id', $this->param['order_id'])->find();
        if (empty($order)) {
            return '订单不存在';
        }
        if ($order['is_pay']) {
            return '订单已支付';
        }
        return $order;
    }
    /**
     * curl post 请求生成订单接口
     */
    public  function requestCreateOrder($order_no,$amount,$remark='大数据查询支付',$return_url='',$notify_url='',$error_url=''){
        $post_data['appid']       = 1;
        $post_data['token']       = 'b4270ddc175b0a0647900a661e8f8a8b';
        $post_data['user_order_no']       = $order_no;
        $post_data['amount']       = $amount;
        $post_data['remark']       = '数据查询服务费';$remark;
        $post_data['return_url']       = $return_url;
        $post_data['notify_url']       = $notify_url;
        $post_data['error_url']       = $error_url;
        $post_data['cancel_url']       = $error_url;
        $post_data['agent_id']       = $this->user['agent_id']??0;
        $post_data['banner_id']       = $this->user['banner_id']??0;
        $url = "http://22.rmrf.top/api/pay/createOrder";
        $client = new \EasySwoole\HttpClient\HttpClient($url);
        $response = $client->post($post_data);
        return $response->getBody();
    }
    //支付成功跳转
    public function returnUrl(){
        try{
            //记录一下支付信息
            $order_id = $this->param['order_id']??0; //我自己的订单ID
            $is_pay = $this->param['is_pay']??0;
            $model = OrderModel::create();
            $order = $model->where('id',$order_id)->find();
            if(!$order){
                $this->error('支付成功，但是系统内没有找到相应的订单！','/index'); return false;
            }
            $res = $this->checkOrder($order['order_id']);

            if(($order['is_pay']==1 && $res==true)||$order['user_id']==1){
                if(empty($order['pay_time'])){
                    $save['pay_time'] = time();
                    $order['pay_time']= time();
                }

                $save['is_pay']=1;
                $model->where('id',$order_id)->update($save);
                return true;
            }else{

               $this->error('订单异常，请联系客服处理!','/index');return false;
            }
        }catch (\Exception $e){
            $this->error('订单异常，请联系客服处理！','/index');return false;
        }

    }
    //异步跳转--只处理状态
    public function notifyUrl(){
        try{
            //记录一下支付信息
            $order_no = $this->param['user_order_no']; //自定义单号
            $order_id = $this->param['order_id'];

            if ($this->checkOrder($order_id)&&$this->param['result_code']=="SUCCESS") {
                $model = OrderModel::create();
                $order =$model->where('user_order_no',$order_no)->find();
                if (!$order) {
                    $this->response()->write('订单不存在'); return false;
                }
                //订单信息
                $save['user_order_no'] = $order_no; //订单号
                $save['transaction_id'] = $this->param['transaction_id']??''; //微信支付订单号
                $save['order_no'] = $this->param['order_no']; //微信支付订单号
                $save['order_id'] = $order_id; //支付系统订单ID
                $save['pay_way'] = $this->param['pay_way']??1;
                $save['is_pay'] = 1;
                if($order['pay_time']==0){
                    $save['pay_time'] = $this->param['pay_time']??time();
                }

                if ( $model->where('id',$order['id'])->update($save)) {
                    $this->response()->write('OK'); return false;
                } else {
                    $this->response()->write('更新失败'); return false;
                }
            }
        }catch (\Exception $e){
            $this->response()->write($e->getMessage()); return false;
        }

    }
    //订单支付异常处理
    public function errorUrl()
    {
        $this->response()->redirect('/returnUrl?order_id='.$this->param['order_id']); return false;
        $this->error('订单未支付或订单已超时！','/scanSuccess?order_id='.$this->param['order_id']);return false;
    }

    /**
     * 支付宝异步回调
     */
    public function alipayNotify()
    {
        try {
            if (!($this->param['trade_status'] == 'TRADE_FINISHED' || $this->param['trade_status'] == 'TRADE_SUCCESS')) {
                \EasySwoole\Pay\AliPay\AliPay::fail();
                return false;
            }
            if (isset($this->param['out_trade_no']) && isset($this->param['trade_no'])) {
                $model = OrderModel::create();
                $order = $model->where('order_no', $this->param['out_trade_no'])->get();
                if (empty($order)) {
                    \EasySwoole\Pay\AliPay\AliPay::fail();
                    return false;
                }

                $res = $this->alipayQueryOrderFind($order, $order->order_no);
                if (!empty($res['biz_content'])) {
                    $res = json_decode($res['biz_content'], true);
                    if (empty($res['out_trade_no'])) {
                        \EasySwoole\Pay\AliPay\AliPay::fail();
                        return false;
                    }
                    /**
                     * 异步处理支付
                     */

                    $trade_no = $this->param['trade_no'];
                    $order_id = $order['id'];
                    //订单更新
                    $save_order['transaction_id'] = $trade_no;
                    $save_order['is_pay'] = 1;
                    if ($order['pay_time'] == 0) {
                        $save_order['pay_time'] = !empty($this->param['gmt_payment']) ? strtotime($this->param['gmt_payment']) : time();
                    }
                    OrderModel::create()->where('id', $order_id)->update($save_order);
                    \EasySwoole\Pay\AliPay\AliPay::success();//成功响应
                } else {
                    \EasySwoole\Pay\AliPay\AliPay::fail();
                    return false;
                }

            } else {
                \EasySwoole\Pay\AliPay\AliPay::fail();
                return false;
            }
        } catch (\Throwable $e) {
            \EasySwoole\Pay\AliPay\AliPay::fail();
            return false;
        }
    }

    /**
     * 支付宝成功回调
     */
    public function alipayReturn()
    {
        try {
            if (isset($this->param['out_trade_no']) && isset($this->param['trade_no'])) {
                $model = OrderModel::create();
                $order = $model->where('order_no', $this->param['out_trade_no'])->get();
                if (empty($order)) {
                    $this->error('未查询到订单,请联系客服处理！');
                    return false;
                }

                $res = $this->alipayQueryOrderFind($order, $order->order_no);
                if (!empty($res['biz_content'])) {
                    $res = json_decode($res['biz_content'], true);
                    if (empty($res['out_trade_no'])) {
                        $this->error('订单异常，请联系客服处理！', $order->error_url);
                        return false;
                    }
                    $trade_no = $this->param['trade_no'];
                    $order_id = $order['id'];
                    $save_order['transaction_id'] = $trade_no;
                    $save_order['is_pay'] = 1;
                    $save_order['pay_time'] = empty($this->param['timestamp']) ? time() : strtotime($this->param['timestamp']);
                    OrderModel::create()->where('id', $order_id)->update($save_order);
                    $this->response()->redirect($order->return_url);
                } else {
                    $this->error('订单异常，请联系客服处理！', $order->error_url);
                    return false;
                    //$this->response()->redirect($order->error_url);
                }

            } else {
                $this->error('支付异常');
                return false;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }


    }

    /**
     * 微信支付异步回调
     */
    public function wxNotify()
    {
        try {
            $xml = $this->request()->getBody()->__toString();
            libxml_disable_entity_loader(true);
            $content = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            var_dump($content);
            $order = OrderModel::create()->where('order_no', $content['out_trade_no'])->get();
            $system = $this->system;
            $pay = new \EasySwoole\Pay\Pay();
            $wechatConfig = new \EasySwoole\Pay\WeChat\Config();
            $wechatConfig->setAppId($system['appid']);      // 公众号APPID
            $wechatConfig->setMchId($system['mchid']);          //商户号
            $wechatConfig->setKey($system['key']);//支付key
            $wechatConfig->setNotifyUrl($this->host.'/wxNotify'); //支付回调地址
            $wechatConfig->setApiClientCert(EASYSWOOLE_ROOT . $system['cert_pem']);//客户端证书
            $wechatConfig->setApiClientKey(EASYSWOOLE_ROOT . $system['key_pem']); //客户端证书秘钥
            $data = $pay->weChat($wechatConfig)->verify($xml);

            $data['pay_time'] = strtotime($data['time_end']);
            if ($content['return_code'] == 'SUCCESS' && $content['result_code'] == 'SUCCESS') {
                /**
                 * 异步处理支付
                 */
                $save_order['transaction_id'] = $data['transaction_id'] ;
                $save_order['pay_time'] = $data['pay_time'] ;
                $save_order['is_pay'] = 1 ;
                $model =OrderModel::create();
                if($model->where('id',$order['id'])->update($save_order)){
                    var_dump($model->lastQuery()->getLastPrepareQuery());
                    $this->response()->write(\EasySwoole\Pay\WeChat\WeChat::success());    return false;
                }else{
                    var_dump($model->lastQuery()->getLastPrepareQuery());
                    $this->response()->write(\EasySwoole\Pay\WeChat\WeChat::fail());    return false;
                }
            }else{
                $this->response()->write(\EasySwoole\Pay\WeChat\WeChat::fail());    return false;
            }

        } catch (\Exception $e) {
            //回调异常
            var_dump('回调异常' . $e->getMessage());
            if ($content['return_code'] == 'SUCCESS' && $content['result_code'] == 'SUCCESS') {
                $this->response()->write(\EasySwoole\Pay\WeChat\WeChat::success());
                return true;
            }
            $this->response()->write(\EasySwoole\Pay\WeChat\WeChat::fail());
            return false;
        }

    }









}

