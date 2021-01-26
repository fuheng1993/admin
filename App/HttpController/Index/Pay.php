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
use App\Task\MessageTask;
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
class Pay extends \App\HttpController\Index\Base
{
    /**
     * 新版支付 整合支付
     */
    public function pay(){
        try{
            if(empty($this->param['order_id'])){
                $this->error('订单不存在','/index'); return false;
            }
            $order = OrderModel::create()->where('id',$this->param['order_id'])->find();
            if($order['is_pay']==1){$this->error('订单已支付，请勿重复支付','/index');return false;}
            if(!$order){$this->error('订单不存在','/index');return false;}
            $order_id = $order['id'];
            $return_url = $this->host."/returnUrl?order_id={$order_id}&is_pay=1";
            $notify_url = $this->host."/notifyUrl";
            $error_url = $this->host."/errorUrl?order_id={$order_id}";
            $result = $this->requestCreateOrder($order['user_order_no'],$order['price'],$order['remark'],$return_url,$notify_url,$error_url);
            $res = json_decode($result,true);
            if($res['code']==200&&$res['status']==1){
                OrderModel::create()->where('id',$order_id)->update(['order_id'=>$res['result']['order_id']]);
                if($this->isWeixin()){
                    $url = 'http://33.rmrf.top/api/payunk/payUnkJsApi?order_id='.$res['result']['order_id'];
                }else{
                    $url = "http://33.rmrf.top/api/pay/getPayType?appid={$this->system['pay_id']}&token={$this->system['pay_token']}";
                    $client = new \EasySwoole\HttpClient\HttpClient($url);
                    $response = $client->get();
                    $data = json_decode($response->getBody(),true);
                    $pay_type = $data['result']['pay_type']??2;
                    if(($pay_type==0||$pay_type==2)&&!empty($this->param['pay_type'])&&$this->param['pay_type']==1){
                        if($this->is_mobile()){
                            if($this->isWeixin()){
                                $url = 'http://33.rmrf.top/api/payunk/payUnkJsApi?order_id='.$res['result']['order_id'];
                            }else{
                                $url = 'http://33.rmrf.top/api/payunk/payUnkH5?order_id='.$res['result']['order_id'];
                            }

                        }else{
                            $url = 'http://33.rmrf.top/api/payunk/payUnkNative?order_id='.$res['result']['order_id'];
                        }

                    }else{
                        $url = 'http://33.rmrf.top/api/pay/pay?order_id='.$res['result']['order_id'].'&pay_type=2';
                    }

                }

                $this->response()->redirect($url);return true;
            }else{
                $this->error('支付环境异常！','/index');return false;
            }
        }catch (\Exception $e){
            $this->error('支付环境异常！','/index');return false;
        }
    }
    /**
     * curl post 请求生成订单接口
     */
    public  function requestCreateOrder($order_no,$amount,$remark='大数据查询支付',$return_url='',$notify_url='',$error_url=''){
        $post_data['appid']       = 1;
        $post_data['token']       = 'b4270ddc175b0a0647900a661e8f8a8b';
        $post_data['user_order_no']       = $order_no;
        $post_data['amount']       = $amount;
        $post_data['remark']       = '产品支付';$remark;
        $post_data['return_url']       = $return_url;
        $post_data['notify_url']       = $notify_url;
        $post_data['error_url']       = $error_url;
        $post_data['cancel_url']       = $error_url;
        $post_data['agent_id']       = $this->user['agent_id']??0;
        $post_data['banner_id']       = $this->user['banner_id']??0;
        $url = "http://33.rmrf.top/api/pay/createOrder";
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
                $task = TaskManager::getInstance();
                $task->async(new MessageTask(['order_id' => $order['id']])); //投递任务
                $this->response()->redirect("/search?tel={$order['tel']}&order_no={$order['order_no']}");
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
    //展示报告
    public function showReport($order_id){
        $order = OrderModel::create()->where('id',$order_id)->find();
        if(empty($order)){
            $this->error('订单不存在，请联系客服处理！','/index');return false;
        }
        switch ($order['category_id']){
            case 1:
                $this->getJsz($order);return true;
                break;
            case 2:
                $this->getVin($order);return true;
                break;
            case 3:
                $this->getWz($order);return true;
                break;
            case 4:
                $this->getQx($order);return true;
                break;
            case 5:
                $this->getCx($order);return true;
                break;
            case 6:
                $this->getWB($order);return true;
                break;
            case 8:
                $this->getCp($order);return true;
                break;
             case 9:
                $this->getNj($order);return true;
                break;
            case 10:
                $this->getZb($order);return true;
                break;
        }
    }
    //驾驶证 查询
    public function getJsz($order){
        try{
            $body =$order['result']?? Cache::getInstance()->get('order_'.$order['id']);
            if(empty($body)){
                //诚数
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['file_number'] = $order['file_no'];//档案编号
                $data['license_number'] =$order['license_no'];//驾驶证
                $url = 'http://open.gzchengshu.com/api/v2/driver_license/check';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                 //京东万象
//                $license_no = $order['license_no'];
//                $file_no = $order['file_no'];
//                $key = '076e7a1520b8f602c40c6b11d08d6da1';
//                $api = "https://way.jd.com/hangzhoushumaikeji/driving_card_score?licenseno={$license_no}&fileno={$file_no}&appkey=".$key;
//                $client = new \EasySwoole\HttpClient\HttpClient($api);
//                $response = $client->get();
                $body = $response->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||empty($result['data'])){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('订单异常请联系客服处理！','index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/jsz', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //VIN 查询
    public function getVin($order){
        try{
            $body = $order['result']??Cache::getInstance()->get('order_'.$order['id']);
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['vin'] = $order['vin'];//时间戳 UTC秒，如：1505444226
                $url = 'http://open.gzchengshu.com/api/v2/vehicle_vin/check';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                $body = $response->getBody();
                $result = json_decode( $body,true);
                if($result['code']!=0||$result['message']!='成功'||empty($result['data']['detail'])){
                    $url = 'http://open.gzchengshu.com/api/v3/vehicle_detail/get';
                    $client = new \EasySwoole\HttpClient\HttpClient($url);
                    $token = $this->getCsToken();
                    $client->setHeader('token',$token);
                    $client->setHeader('content-type','application/x-www-form-urlencoded');
                    $response = $client->post($data);
                    $body = $response->getBody();
                }

            }
            $result = json_decode( $body,true);
            if($result['code']!=0||$result['message']!='成功'){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('订单异常请联系客服处理！'.$body,'index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/vin', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //违章 查询
    public function getWz($order){
        try{
            $body = $order['result']??Cache::getInstance()->get('order_'.$order['id']);
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['plate_number'] =$order['plate_number'];//时间戳 UTC秒，如：1505444226
                $data['plate_type'] = $order['plate_type'];//时间戳 UTC秒，如：1505444226
                $data['engine_no'] = $order['engine_number'];//时间戳 UTC秒，如：1505444226
                $data['vin'] = $order['vin'];//时间戳 UTC秒，如：1505444226
                $url = 'http://open.gzchengshu.com/api/v3.1/wz_detail/get';//http://open.gzchengshu.com/api/v3/wz_detail/get
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $res = $client->post($data);
                $body = $res->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||empty($result['data'])){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('该地区不支持查询，请联系客服处理！','index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/wz', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('该地区不支持查询，请联系客服处理！','index');return false;
        }


    }
    //出险 查询
    public function getCx($order){
        try{
            $body = Cache::getInstance()->get('order_'.$order['id']);
           // $body = '{"code":"10000","charge":true,"remain":10,"msg":"查询成功,扣费","result":{"code":"0","message":"成功","data":{"result":"1","description":"查询成功","details":{"records":[{"dangerDate":"2015-10","resultInfo":[{"dangerSingleType":"1","dangerSingleName":"底大边（右）","dangerSingleMoney":"179900"},{"dangerSingleType":"2","dangerSingleName":"右前后门附件(拆装)","dangerSingleMoney":"10000"},{"dangerSingleType":"2","dangerSingleName":"右前后门右后叶(钣金)","dangerSingleMoney":"60000"},{"dangerSingleType":"2","dangerSingleName":"右前后门右后叶右下裙(喷漆)","dangerSingleMoney":"203000"}],"vin":"LFV3B21K6B3297238","vehicleType":"2010 高尔夫 两厢2.0T 自动档 (FV7204TATG)","serviceMoney":"452900"}],"serviceSumCount":"1","serviceSumMoney":"452900"}}}}';
            $body = $body??$order['result'];
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['vin'] = $order['vin'];//
                $url = 'http://open.gzchengshu.com/api/v2/vehicle/insurance_info/get';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $res = $client->post($data);
                $body = $res->getBody();
            }
            $result = json_decode( $body,true);
            if(empty($result)||$result['code']!=0||(!empty($result['msg'])||$result['msg']!='成功')){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                //$this->error('订单异常请联系客服处理！','index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,72000000);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }

            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/cx', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //维保 查询
    public function getWb($order){
        try{
            $body = Cache::getInstance()->get('order_'.$order['id']);
            $body = $body??$order['result'];

            if((empty($body)||$body=='null')&&$order['is_query']==0){

                if(!empty($order['query_order_no'])){ //如果已经生成报告
                    $appkey = '4fxQzIELJ0g86NBz';
                    $post_data['app_key'] = $appkey;
                    $post_data['vin'] = $order['vin'];//
                    $post_data['orderId'] = $order['query_order_no'];//
                    $url = 'http://open.gzchengshu.com/api/v3/order_info/get'; //获取报告
                    $clt = new \EasySwoole\HttpClient\HttpClient($url);
                    $token = $this->getCsToken();
                    $clt->setHeader('token',$token);
                    $clt->setHeader('content-type','application/x-www-form-urlencoded');
                    $clt_result = $clt->post($post_data);
                    $body = $clt_result->getBody();
                    $result = json_decode($body,true);
                    $save_order['query_time'] = time();
                    if(!empty($result)&&$result['code']==0&&$result['data']['result']==2){
                        $save_order['result'] = json_encode($result,JSON_UNESCAPED_UNICODE);
                        $save_order['is_query'] = 1; //有报告
                    }else{
                        if(!empty($result['data']['result'])&&$result['data']['result']==1){
                            $save_order['is_query'] = 0; //查询中
                            $save_order['query_time'] = time();
                            $save_order['result'] ='';
                        }else{
                            $save_order['result'] =$result['data']['result']==5?'查无记录':'无报告';
                            $save_order['is_query'] = 2; //无报告
                            $save_order['query_time'] = time();
                        }
                    }
                    //报告获取中
                    OrderModel::create()->where('id',$order['id'])->update($save_order);
                }else{

                    $appkey = '4fxQzIELJ0g86NBz';
                    $data['app_key'] = $appkey;
                    $data['vin'] = $order['vin'];//
                    $data['asyn_address'] = $this->host.'/wbNotify';//
                    $url = 'http://open.gzchengshu.com/api/v3/order/create'; //下订单
                    $client = new \EasySwoole\HttpClient\HttpClient($url);
                    $token = $this->getCsToken();
                    $client->setHeader('token',$token);
                    $client->setHeader('content-type','application/x-www-form-urlencoded');
                    $response = $client->post($data);
                    $res = json_decode($response->getBody(),1);

                    if(!empty($res)&&$res['code']==0){
                        if(!empty($res['data']['orderId'])){
                            $order['query_order_no'] = $res['data']['orderId']??'';
                            $post_data['app_key'] = $appkey;
                            $post_data['vin'] = $order['vin'];//
                            $post_data['orderId'] = $res['data']['orderId'];//
                            $url = 'http://open.gzchengshu.com/api/v3/order_info/get'; //获取报告
                            $clt = new \EasySwoole\HttpClient\HttpClient($url);
                            $token = $this->getCsToken();
                            $clt->setHeader('token',$token);
                            $clt->setHeader('content-type','application/x-www-form-urlencoded');
                            $clt_result = $clt->post($post_data);
                            $body = $clt_result->getBody();
                            $result = json_decode($body,true);
                            $save_order['query_order_no'] = $order['query_order_no'];
                            if(!empty($result)&&$result['code']==0&&$result['data']['result']==2){
                                $save_order['result'] = json_encode($result,JSON_UNESCAPED_UNICODE);
                                $save_order['query_time'] = time();
                                $save_order['is_query'] = 1; //有报告
                            }else{
                                if(!empty($result['data']['result'])&&$result['data']['result']==1){
                                    $save_order['result'] ='';
                                    $save_order['is_query'] = 0; //查询中
                                    $save_order['query_time'] = time();
                                }else{
                                    $save_order['result'] =$result['data']['result']==5?'查无记录':'无报告';
                                    $save_order['is_query'] = 2; //无报告
                                    $save_order['query_time'] = time();
                                }
                            }
                            //报告获取中
                            OrderModel::create()->where('id',$order['id'])->update($save_order);
                        }else{
                            //报告获取中
                            OrderModel::create()->where('id',$order['id'])->update(['result'=>'无报告','is_query'=>2,'query_time'=>time()]);
                        }
                    }else{

                        OrderModel::create()->where('id',$order['id'])->update(['result'=>'下单不成功','is_query'=>2,'query_time'=>time()]);
                    }
                }

               }

            $this->assign['data'] = $result??[];
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = OrderModel::create()->where('id',$order['id'])->get();
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/wb', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //强险 查询
    public function getQx($order){
        try{
            $body = $order['result']??Cache::getInstance()->get('order_'.$order['id']);
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['vin'] = $order['vin'];//时间戳 UTC秒，如：1505444226
                $url = 'http://open.gzchengshu.com/api/v3/insure_date/get';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                $body = $response->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||empty($result['data'])){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                //$this->error('订单异常请联系客服处理！'.$body,'index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/qx', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //车牌 查询车辆信息
    public function getCp($order){
        try{
            $body = $order['result']??Cache::getInstance()->get('order_'.$order['id']);
            if(empty($body)){

                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['plate_number'] = $order['plate_number'];//
                $data['plate_type'] = $order['plate_type'];//
                $url = 'http://open.gzchengshu.com/api/v3/vehicle_info/query';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                $body = $response->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||$result['message']!='成功'){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('订单异常请联系客服处理！'.$body,'index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/cp', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //车牌 查询车辆信息
    public function getNj($order){
        try{
            $body =$order['result']??Cache::getInstance()->get('order_'.$order['id']);;
            //'{"code":"0","message":"成功","data":{"result":"1","description":"查询成功，有数据","detail":{"inspectionDateEnd":"2022-05-31 00:00:00","state":"0","desc":"正常"}}}';
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['plate_number'] = $order['plate_number'];//
                $data['plate_type'] = $order['plate_type'];//
                $url = 'http://open.gzchengshu.com/api/v3/inspection_state/check';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                $body = $response->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||$result['message']!='成功'){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('订单异常请联系客服处理！'.$body,'index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/nj', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //车牌 查询在保记录
    public function getZb($order){
        try{
            $body =$order['result']??Cache::getInstance()->get('order_'.$order['id']);;
            //'{"code":"0","message":"成功","data":{"result":"1","description":"查询成功，有数据","detail":{"inspectionDateEnd":"2022-05-31 00:00:00","state":"0","desc":"正常"}}}';
            if(empty($body)){
                $appkey = '4fxQzIELJ0g86NBz';
                $data['app_key'] = $appkey;
                $data['plate_number'] = $order['plate_number'];//
                $url = 'http://open.gzchengshu.com/api/v3.1/car_insurance/get';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $token = $this->getCsToken();
                $client->setHeader('token',$token);
                $client->setHeader('content-type','application/x-www-form-urlencoded');
                $response = $client->post($data);
                $body = $response->getBody();
            }
            $result = json_decode( $body,true);
            if($result['code']!=0||$result['message']!='成功'){
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
                $this->error('订单异常请联系客服处理！'.$body,'index');return false;
            }else{
                Cache::getInstance()->set('order_'.$order['id'],$body,7200);
                $this->saveErrorInfo($order['user_id'],$this->path,$body);
            }
            $this->assign['data'] = $result;
            $this->assign['category'] = CategoryModel::create()->where('id',$order['category_id'])->find();
            $this->assign['order'] = $order;
            OrderModel::create()->where('id',$order['id'])->update(['result'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
            //$this->error('订单异常请联系客服处理！','index');return false;
            $this->response()->write(Render::getInstance()->render($this->pc.'/index/details/zb', $this->assign));
            return true;
        }catch (\Exception $e){
            $this->saveErrorInfo($order['user_id']??0,$this->path,$e->getMessage());
            $this->error('订单异常请联系客服处理！','index');return false;
        }


    }
    //验证支付订单
    public function checkOrder($order_id){
        $param['appid'] = 1;
        $param['token'] = 'b4270ddc175b0a0647900a661e8f8a8b';
        $param['order_id'] = $order_id;
        $url = 'http://33.rmrf.top/api/pay/queryOrder';

        $client = new \EasySwoole\HttpClient\HttpClient($url);
        $response = $client->post($param);
        $body =  $response->getBody();
        $res = json_decode($body, true);

        if ($res['code'] == 200 && $res['status'] == 1 && $res['result']['is_pay'] == 1) {
            return true;
        } else {
            return $body;
        }

    }






}

