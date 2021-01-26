<?php

namespace App\HttpController\Index;

use App\Model\ArticleModel;
use App\Model\CategoryModel;
use App\Model\CommentModel;
use App\Model\ErrorInfoModel;
use App\Model\OrderModel;
use App\Model\RefererKeywordsModel;
use App\Model\RefererModel;
use App\Model\SystemModel;
use App\Model\TrackerPoint\TrackerPointModel;
use App\Model\UserModel;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\Session\Session;
use EasySwoole\Template\Render;
use EasySwoole\Tracker\Point;
use EasySwoole\Tracker\PointContext;


/**
 * BaseController
 * Class Base
 * Create With Automatic Generator
 */
abstract class Base extends \EasySwoole\Http\AbstractInterface\Controller
{
    private $basicAction = [
        '/index/user',
        '/index/user/info',
        '/index/user/details',
        '/index/user/history',
        '/index/user/index',

    ];

    protected $param;  //请求参数
    protected $ip;     //客户端IP
    protected $system; //系统配置
    protected $path;   //当前请求路径
    protected $assign; //模板变量渲染
    protected $host;  //获取当前域名
    protected $user;  //用户信息
    protected $num;
    protected $is_mobile;
    protected $pc;


    public function actionNotFound(?string $action){
        $this->response()->redirect('/index');
        $this->error('你请求的页面不存在','/index');
        return true;
    }

    public function onRequest(?string $action): ?bool
	{

        $query =urldecode($this->request()->getServerParams()['query_string']??'');
        $query_param = array();
        if(!empty($query)){
            $param = explode('&',$query);//转成数组
            foreach ($param as $k => $v) {
                $arr = explode('=', $v);
                $query_param[$arr[0]] = $arr[1]??'';
            }
            $this->param = $query_param;
        }else{
            $this->param = $this->request()->getRequestParam();
        }
		if (!parent::onRequest($action)) {return false;};
        $http = $this->request()->getHeader('http')[0]??'http';
        $this->host = $http.'://'.$this->request()->getHeaders()['host'][0];//$this->request()->getHeader('http').

        $this->ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($this->request()->getSwooleRequest()->fd)['remote_ip'];
        $this->user = \EasySwoole\Session\Session::getInstance()->get('user');
        $this->system = $this->getSystem();
        if($this->is_mobile()){
            $this->pc='';
        }else{
            $this->pc='/pc';
        }
        $this->pc='';
        $this->path = $this->request()->getUri()->getPath();
        $this->assign = [
            'is_mobile'=>$this->is_mobile()?1:0,//$this->is_mobile()?1:0
            'user'=>$this->user,
            'ip'=>$this->ip,
            'system'=>$this->system,
            'host'=>$this->host,
            'num' => time()%10000000,
            'baidu'=>$this->assign['system']['baidu']??''
        ];
        $this->path = $this->pc.$this->request()->getUri()->getPath();

		return true;
	}
    /**
     * 检测PC手机端
     */
    public function is_mobile() {
        $request = $this->request()->getHeaders();
        $user_agent = empty($request['user-agent'][0])?'':$request['user-agent'][0];
        if ( empty($user_agent) ) {
            $is_mobile = false;
        } elseif ( strpos($user_agent, 'Mobile') !== false
            || strpos($user_agent, 'Android') !== false
            || strpos($user_agent, 'Silk/') !== false
            || strpos($user_agent, 'Kindle') !== false
            || strpos($user_agent, 'BlackBerry') !== false
            || strpos($user_agent, 'Opera Mini') !== false
            || strpos($user_agent, 'Opera Mobi') !== false ) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        return $is_mobile;
    }

    //检测微信端
    public function isWeixin(){
        if (!empty($this->request()->getHeaders()['user-agent']) && strpos($this->request()->getHeaders()['user-agent'][0], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
    //获取系统配置信息
    public function getSystem(){
        $system = Cache::getInstance()->get('system');
        if(empty($system)){
            $system = SystemModel::create()->where('id',1)->find();
        }
        if($system['open_cdn']==0){
            $system['cdn'] = '';
        }
        if(!empty($system['cdn'])){
            $http = $this->request()->getHeader('http')[0]??'http';
            $system['cdn'] = $http.'://'.$system['cdn'];
        }
        $system['keyword'] = $system['keywords'];

        $username = $this->user['username']??'';
        $system['service_url'] = $system['service_url'].'?username='.$username;
        //$system['cdn'] = '';
        $system['pay_id'] = $system['pay_id']??1;
        $system['pay_token'] = $system['pay_token']??'b4270ddc175b0a0647900a661e8f8a8b';
        if(!$this->is_mobile()&&($this->request()->getHeaders()['host'][0]=='xc.zsfin.cn')||$this->request()->getHeaders()['host'][0]=='cx.cheliange.com'||$this->request()->getHeaders()['host'][0]=='qc.xvda.top'){
            $system['qr_code'] = $system['logo'];
        }
        return $system;
    }
    //获取系统配置信息
    public function getObject(){
        $object = Cache::getInstance()->get('object');
        if(empty($system)){
            $object = CategoryModel::create()->where('status',1)->order('sort','asc')->select();
            Cache::getInstance()->set('object',$object);
        }
        return $object;
    }

    /**
     * 获取文章缓存
     * is_cache 1获取缓存 0不获取缓存
     */
    public function getArticle($is_cache=1){
        $article = Cache::getInstance()->get('article',7*24*60*60);
        $article = json_decode($article,true);
        if(!$is_cache||empty($article)||!is_array($article)){
            $article = ArticleModel::create()->limit(0,3)->select();
            Cache::getInstance()->set('article',json_encode($article));
        }
        return $article;
    }

    /**
     * 获取评论缓存
     * is_cache 1获取缓存 0不获取缓存
     */
    public function getComment($is_cache = 1)
    {
        $comment = Cache::getInstance()->get('comment', 7 * 24 * 60 * 60);
        $comment = json_decode($comment,true);
        if (!$is_cache || empty($comment) || !is_array($comment)) {
            $comment = CommentModel::create()->limit(0, 10)->select();
            foreach ($comment as $k=>$v){
                $user = UserModel::create()->where('id',$v['user_id'])->get();
                $comment[$k]['nickname'] = $user['nickname'];
                $comment[$k]['avatar'] = $user['avatar'];
                $comment[$k]['username'] = $user['username'];
            }
            Cache::getInstance()->set('comment', json_encode($comment));
        }
        return $comment;
    }
    /**
     * 获取维保报告
     */
    public function getWbReport($vin,$orderId){
        $appkey = '4fxQzIELJ0g86NBz';
        $post_data['app_key'] = $appkey;
        $post_data['vin'] = $vin;//
        $post_data['orderId'] = $orderId;//
        $url = 'http://open.gzchengshu.com/api/v3/order_info/get'; //获取报告
        $clt = new \EasySwoole\HttpClient\HttpClient($url);
        $token = $this->getCsToken();
        $clt->setHeader('token',$token);
        $clt->setHeader('content-type','application/x-www-form-urlencoded');
        $clt_result = $clt->post($post_data);
        $body = $clt_result->getBody();
        return $body;
    }
    /**
     * 获取诚数token
     */
    public function getCsToken(){
        $token =Cache::getInstance()->get('CsToken');
        if(empty($token)){
            $appkey = '4fxQzIELJ0g86NBz';
            $appAecurity = 'gft47HJWgn9k97qhhQjNiPYCCtijgYd2';
            $time = time();
            $data['app_key'] = $appkey;
            $data['timestamp'] = $time;//时间戳 UTC秒，如：1505444226
            $gen="app_key={$appkey}&app_security={$appAecurity}&timestamp={$time}";
            $sign = md5($gen);
            $data['sign'] = $sign;
            $url = 'http://open.gzchengshu.com/api/authorize';
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            $res = $client->post($data);
            $result = json_decode($res->getBody(),1);
            $token =  empty($result['data']['token'])?'':$result['data']['token'];
            Cache::getInstance()->set('CsToken',$token,36000);
        }
        return $token;
    }

    /**
     * 获取随机电话
     */
    public function getRandTel(){
        $list =[];
        for($i=1;$i<=10;$i++){
            $tel_1 = '13'.rand(0,9).'*****'.rand(100,999);
            $tel_2 = '14'.rand(5,9).'*****'.rand(100,999);
            $tel_3 = '15'.rand(0,9).'*****'.rand(100,999);
            $tel_4 = '166*****'.rand(100,999);
            $tel_5 = '17'.rand(0,9).'*****'.rand(100,999);
            $tel_6 = '18'.rand(0,9).'*****'.rand(100,999);
            $tel_7 = '19'.rand(8,9).'*****'.rand(100,999);
            $s = 'tel_'.rand(1,7);
            $list[] = $$s;
        }
        return $list;
    }

    /**
     * 检测是否下过单
     */
    public function checkUserIsPay($user_id=0){
        if ($user_id == 0) {
            return false;
        }
        if (Cache::getInstance()->get('is_pay_' . $user_id) == 1) {
            return true;
        } else {
            if (OrderModel::create()->where('is_pay', 1)->find()) {
                Cache::getInstance()->set('is_pay_' . $user_id, 1);
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * 渲染模板
     */
	public function view($tpl,$data,$is_reload=0){
        if($is_reload){
            Render::getInstance()->restartWorker();
        }
        $this->response()->write(Render::getInstance()->render($tpl, $data));
        return true;
    }
    /**
     * 异常界面 error
     */
    public function error($message,$url=''){
        $this->assign['msg'] = $message;
        $this->assign['url'] = $url;
        $this->response()->write(Render::getInstance()->render('/index/index/error', $this->assign));
        return false;
    }
    /**
     * 请求状态JSON返回
     */
	public function writeJson($statusCode = 200, $result = NULL, $msg = NULL)
    {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => $statusCode,
                "result" => $result,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    /**
     * AJAX请求JSON返回
     */
    public function AjaxJson($status=0,$data=[],$msg='success'){
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => 200,
                "status"=>$status,
                "result" => $data,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            $this->response()->end();
            return true;
        } else {
            return false;
        }
    }
    /**
     * 数据源请求异常
     */
    public function saveErrorInfo($user_id,$path,$result){

        go(function ()use ($user_id,$path,$result){
            $data['user_id'] = $user_id;
            $data['path'] = $path;
            $data['result'] = $result;
            $data['create_time'] = time();
            ErrorInfoModel::create()->data($data)->save();
        });
    }

    /**
     * 异常抛出
     **/
    public function onException(\Throwable $throwable): void
    {
        $path = $this->request()->getUri()->getPath();
        if($this->request()->getServerParams()['request_method']=='GET'){
            $this->error("页面异常，请重试！".$throwable->getMessage(),'/index'); return; false;
        }else{
            $this->writeJson(400,$throwable->getMessage(),'服务器异常');return; false;
        }

        //记录错误信息,等级为FatalError
        //\EasySwoole\EasySwoole\Trigger::getInstance()->error("API异常：{$path}".$throwable->getMessage());
    }

    protected function afterAction(?string $actionName): void
    {
        $referer =urldecode($this->request()->getHeaderLine('referer'));
        $keywords = $this->search_word_from($referer);
        $query_string = $this->request()->getServerParams()['query_string']??'';
        if ((!empty($keywords['from']) && !empty($keywords['keyword'])) || ($keywords['from']=='百度'&&strpos($query_string, 'bd_vid')) ) {
            $keywords['from']=!empty($keywords['from'])?$keywords['from']:'百度';
            $keywords['keyword']=!empty($keywords['keyword'])?$keywords['keyword']:'待回传';
            Session::getInstance()->set('keyword',$keywords['keyword']);
            $data['ip'] = $this->getRealIp();
            $data['url'] = $this->request()->getServerParams()['query_string']??'';
            $data['referer_url'] = urldecode($referer);
            $data['keyword'] = $keywords['keyword'];
            $data['platform'] = urldecode($keywords['from']);
            $data['create_time'] =time();
            go(function ()use ($data){
                RefererModel::create()->data($data)->save();
                $model =  RefererKeywordsModel::create();
                if($model->where('keyword',$data['keyword'])->where('platform',$data['platform'])->get()){
                    $model->where('keyword',$data['keyword'])->where('platform',$data['platform'])->update(
                        [
                            'num' => QueryBuilder::inc(1)
                        ]
                    );
                }else{
                    $model->data([
                        'keyword'=>$data['keyword'],
                        'platform'=>$data['platform'],
                        'num'=>1,
                        'create_time'=>time()
                    ])->save();
                }
            });
        }
        $this->saveIpInfo();

        $query = $this->param; //接收请求参数
        //处理url参数被转码问题
        foreach ($query as $k => $v) {
            $key = urldecode($k);
            if (strpos($key, '=') !== false) {
                parse_str($key, $arr);
                $query[array_keys($arr)[0]] = $arr[array_keys($arr)[0]];
                unset($query[$k]);
            }
        }
        // 真实IP
        $ip = $this->getRealIP();
        // 查看每次请求记录 http://host/index/tracker

        $point = PointContext::getInstance()->startPoint();
        $point->end();
        $array = Point::toArray($point);
        $rsp = $this->response()->getBody();
        $content_type = $this->response()->getHeader('Content-type')[0]??'';//application/json;charset=utf-8
        $referer = '搜索引擎:'.($keywords['from']??'').'，关键词：'.($keywords['keyword']??'');
        foreach ($array as $k=>$v){
            $data['ip'] = $ip;
            $data['pointd'] = $v['pointId'];
            $data['pointName'] = $v['pointName']??'';
            $data['parentId'] = $v['parentId']??'';
            $data['depth'] = $v['depth']??'';
            $data['isNext'] = $v['isNext'];
            $data['startTime'] = $v['startTime'];
            $data['endTime'] = $v['endTime'];
            $data['spendTime'] = $v['endTime']-$v['startTime'];
            $data['status'] = $v['status'];
            $data['result'] = json_encode($v);
            $data['data'] = $content_type=='application/json;charset=utf-8'?$rsp->__tostring():'渲染HTML页面';
            $data['uri'] = $v['startArg']['uri'];
            $data['referer'] = $referer;
            $data['create_date'] = date('Y-m-d H:i:s',time());
            go(function ()use($data){
                TrackerPointModel::create()->data($data, false)->save();
            });


        }

    }


    //获取搜索引擎来源关键词
    public function search_word_from($referer) {

        if(strstr( $referer, 'baidu.com')){ //百度
            preg_match( "|baidu.+wo?r?d=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            //如果是蜘蛛爬虫不记录
            if (ctype_alnum($keyword)||ctype_alpha($keyword)) {  //!preg_match("/([\x81-\xfe][\x40-\xfe])/", $keyword, $match)
                $keyword ='';
                $query_string = $this->request()->getServerParams()['query_string']??'';
                if ($query_string && strstr($query_string, 'bd_vid')) {
                    $keyword = $query_string;
                }
            }
            $from = '百度';
        }elseif(strstr( $referer, 'google.com') or strstr( $referer, 'google.cn')){ //谷歌
            preg_match( "|google.+q=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            $from = '谷歌';
        }elseif(strstr( $referer, 'so.com')){ //360搜索
            preg_match( "|so.+q=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            $from = '360';
        }elseif(strstr( $referer, 'sogou.com')){ //搜狗
            preg_match( "|sogou.com.+keyword=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            if(empty($keyword)){
                preg_match( "|sogou.com.+query=([^\\&]*)|is", $referer, $tmp );
                $keyword = urldecode( $tmp[1]??'' );
            }
            $from = '搜狗';
        }elseif(strstr( $referer, 'soso.com')){ //搜搜
            preg_match( "|soso.com.+w=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            $from = '搜搜';
        }elseif(strstr( $referer, 'sm.cn')){ //搜搜
            preg_match( "|sm.cn.+q=([^\\&]*)|is", $referer, $tmp );
            $keyword = urldecode( $tmp[1]??'' );
            $from = '神马';
        }else {
            $keyword ='';
            $from = '';
        }
        return array('keyword'=>$keyword,'from'=>$from);
    }
    public function saveIpInfo(){
    }
    public function getRealIp(){
        // 真实IP
        $ip = '';
        if (count($this->request()->getHeader('x-real-ip'))) {
            if(!empty($this->request()->getHeader('x-forwarded-for')[0])){
                $ip = explode(',',$this->request()->getHeader('x-forwarded-for')[0])[0];
            }
            $ip = $ip??$this->request()->getHeader('x-real-ip')[0];
        } else {
            $params = $this->request()->getServerParams();
            foreach (['http_client_ip', 'http_x_forward_for', 'x_real_ip', 'remote_addr'] as $key) {
                if (isset($params[$key]) && !strcasecmp($params[$key], 'unknown')) {
                    $ip = $params[$key];
                    break;
                }
            }
        }
        return $ip;
    }


}

