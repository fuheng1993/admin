<?php
namespace EasySwoole\EasySwoole;


use App\Crontab\WechatOrderChart;
use App\Producer\Process as ProducerProcess;
use App\Consumer\Process as ConsumerProcess;
use App\Model\TrackerPoint\TrackerPointModel;
use App\Process\HotReload;
use App\Template;
use App\Crontab\UserOrderChart;
use App\Utility\MyQueue;
use App\Utility\QueueProcess;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\AtomicManager;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\Queue\Driver\Redis;
use EasySwoole\Queue\Job;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Template\Render;
use EasySwoole\Tracker\Point;
use EasySwoole\Tracker\PointContext;
use EasySwoole\Utility\Time;
use App\Utility\LogPusher;
use EasySwoole\Console\Console;
use EasySwoole\FastCache\Cache;
use EasySwoole\FastCache\CacheProcessConfig;
use EasySwoole\FastCache\SyncData;
use EasySwoole\Utility\File;



class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, new \App\Logger\Logger());
        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\ORM\Db\Config($configData);
        $config->setGetObjectTimeout(3.0); //设置获取连接池对象超时时间
        $config->setIntervalCheckTime(30*1000); //设置检测连接存活执行回收和创建的周期
        $config->setMaxIdleTime(15); //连接池对象最大闲置时间(秒)
        $config->setMaxObjectNum(100); //设置最大连接池存在连接对象数量
        $config->setMinObjectNum(5); //设置最小连接池存在连接对象数量
        $config->setAutoPing(5); //设置自动ping客户端链接的间隔
        DbManager::getInstance()->addConnection(new Connection($config));
        // 注册一个atomic对象
        AtomicManager::getInstance()->add('second');
    }

    public static function mainServerCreate(EventRegister $register)
    {
////        //redis pool使用请看redis 章节文档
//        $config = new RedisConfig([
//            'host'=>'127.0.0.1',
//            'port' => '6379',
//        ]);
////        $redis = new RedisPool($config);
//        $poolConfig = new \EasySwoole\Pool\Config();
//        \EasySwoole\Pool\Manager::getInstance()->register(new \App\Pool\RedisPool($poolConfig,$config),'redis');
//        /**
//         * 定义节点Redis管理器
//         */
//        $redis = new RedisPool(new RedisConfig([
//            'host'=>'127.0.0.1',
//            'port' => '6379',
//        ]));
//        $driver = new Redis($redis);
//        MyQueue::getInstance($driver);
//        //注册一个消费进程
//        \EasySwoole\Component\Process\Manager::getInstance()->addProcess(new QueueProcess());
//        //模拟生产者，可以在任意位置投递
//        $register->add($register::onWorkerStart,function ($ser,$id){
//            if($id == 0){
//                Timer::getInstance()->loop(2000,function (){
//                    $job = new Job();
//                    $job->setJobData(['time'=>\time()]);
//                    MyQueue::getInstance()->producer()->push($job);
//                });
//            }
//        });

        /**
         * ****************   MYSQL ORM 配置    ****************
         */
        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\ORM\Db\Config($configData);
        DbManager::getInstance()->addConnection(new Connection($config));
        DbManager::getInstance()->onQuery(function ($res, $builder, $start) {
            //var_dump($res->toArray());
            $queryTime = 1;
            if (bcsub(time(), $start, 1) > $queryTime) {
                 var_dump('慢查询');
            }
        });

        /**
         * ****************   服务热重启    ****************
         */
        $swooleServer = ServerManager::getInstance()->getSwooleServer();
        $swooleServer->addProcess((new HotReload('HotReload', ['disableInotify' => false]))->getProcess());

        /**
         * ****************   实例化该Render,并注入你的驱动配置    ****************
         */
        Render::getInstance()->getConfig()->setRender(new Template());
        Render::getInstance()->getConfig()->setTempDir(EASYSWOOLE_TEMP_DIR);
        Render::getInstance()->attachServer(ServerManager::getInstance()->getSwooleServer());

        /**
         * 热重载
         */
        // 配置同上别忘了添加要检视的目录
        $hotReloadOptions = new \EasySwoole\HotReload\HotReloadOptions;
        $hotReload = new \EasySwoole\HotReload\HotReload($hotReloadOptions);
        $hotReloadOptions->setMonitorFolder([EASYSWOOLE_ROOT . '/App']);

        $server = ServerManager::getInstance()->getSwooleServer();
        $hotReload->attachToServer($server);

        /**
         * SESSION
         */
        $handler = new \EasySwoole\Session\SessionFileHandler(EASYSWOOLE_TEMP_DIR);   //可以自己实现一个标准的session handler
        \EasySwoole\Session\Session::getInstance($handler,'easy_session','session_dir'); //表示cookie name   还有save path
        Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
    }
    //请求到达之后最先执行
    public static function onRequest(Request $request, Response $response): bool
    {
        //Session
        $cookie = $request->getCookieParams('easy_session');
        if(empty($cookie)){
            $sid = \EasySwoole\Session\Session::getInstance()->sessionId();
            $response->setCookie('easy_session',$sid);
        }else{
            \EasySwoole\Session\Session::getInstance()->sessionId($cookie);
        }

        // TODO: Implement onRequest() method.
        //跨域处理
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        //$response->withHeader('Content-type', 'application/json;charset=utf-8');
        /**
         * 链路追踪
         */
        $point = PointContext::getInstance()->createStart($request->getUri()->__toString());
        $point->setStartArg([
            'uri'=>$request->getUri()->__toString(),
            'get'=>$request->getQueryParams(),
            'post'=>$request->getRequestParam()
        ]);
        return true;
    }
    //请求结束前执行
    public static function afterRequest(Request $request, Response $response): void
    {


    }

}