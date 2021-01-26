<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/2/11
 * Time: 9:18
 */
namespace App\Utility;


use EasySwoole\Console\Console;
use EasySwoole\Console\ModuleInterface;
use EasySwoole\EasySwoole\Config;

class LogPusher implements ModuleInterface
{

    public function moduleName(): string
    {
        return 'log';
    }

    public function exec(array $arg, int $fd, Console $console)
    {
        /*
         * 此处能这样做是因为easyswoole3.2.5后的版本改为swoole table存储配置了，因此配置不存在进程隔离
         */
        $op = array_shift($arg);
        switch ($op){
            case 'enable':{
                Config::getInstance()->setConf('logPush',true);
                break;
            }
            case "disable":{
                Config::getInstance()->setConf('logPush',false);
                break;
            }
        }
        $status = Config::getInstance()->getConf('logPush');
        $status = $status ? 'enable' : 'disable';
        return "log push is {$status}";
    }

    public function help(array $arg, int $fd, Console $console)
    {
        return 'this is log help';
    }
}