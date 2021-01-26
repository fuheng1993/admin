<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9519,
        'SERVER_TYPE' =>  EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 4,
            'reload_async' => true,
            'max_wait_time'=>3,
            'package_max_length' => 104857600000, //文件上传最大限制10M
            'document_root' => '/www/wwwroot/win.go1s.cn/', // v4.4.0以下版本, 此处必须为绝对路径
            'enable_static_handler' => true,
        ],
        'TASK'=>[
            'workerNum'=>2,
            'maxRunningNum'=>64,
            'timeout'=>15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'PSW_STR'=>'potato', //密码加密字符串
    'HOST_NAME'=>'http://win.go1s.cn/',
    /*################ MYSQL CONFIG ##################*/
    'MYSQL'         => [
        //数据库配置
        'host'                 => '127.0.0.1',//数据库连接ip
        'user'                 => 'win_go1s_cn',//数据库用户名
        'password'             => 'LTkc6fytcbWF2pJT',//数据库密码
        'database'             => 'win_go1s_cn',//数据库
        'port'                 => '3306',//端口
        'timeout'              => '30',//超时时间
        'connect_timeout'      => '10',//连接超时时间
        'charset'              => 'utf8',//字符编码
        'strict_type'          => false, //开启严格模式，返回的字段将自动转为数字类型
        'fetch_mode'           => false,//开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
        'alias'                => '',//子查询别名
        'isSubQuery'           => false,//是否为子查询
        'max_reconnect_times ' => '3',//最大重连次数
        //连接池配置
        'intervalCheckTime'    => 30 * 1000,//定时验证对象是否可用以及保持最小连接的间隔时间
        'maxIdleTime'          => 15,//最大存活时间,超出则会每$intervalCheckTime/1000秒被释放
        'maxObjectNum'         => 100,//最大创建数量
        'minObjectNum'         => 20,//最小创建数量 最小创建数量不能大于等于最大创建数量,否则报错 每$intervalCheckTime/1000秒去判断最小连接数
        'getObjectTimeout'     => 6.0,//连接池对象获取连接对象时的等待时间
    ],
    /**##################     JWT 配置     #############*/
    'JWT' => [
        'iss' => 'potato', // 发行人
        'exp' => 172800, // 过期时间 默认2天 2*24*60*60=172800
        'sub' => 'potato', // 主题
        'nbf' => NULL, // 在此之前不可用
        'key' => 'potato', // 签名用的key
    ],
    /**################ REDIS CONFIG ##################*/
    'REDIS'         => [
        'host'          => '127.0.0.1',
//        'password'          => 'tudou',
        'port'          => '6379',
        'auth'          => 'tudou',
        'POOL_MAX_NUM'  => '6',
        'POOL_TIME_OUT' => '0.1',
    ],
  

];
