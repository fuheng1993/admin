<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/6
 * Time: 9:54
 */
namespace App\Utility;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Queue\Job;

class QueueProcess extends AbstractProcess
{

    protected function run($arg)
    {
        go(function (){
            MyQueue::getInstance()->consumer()->listen(function (Job $job){
                $redis=\EasySwoole\Pool\Manager::getInstance()->get('redis')->getObj();
                $key = 'test123213Key';
                $redis->select(0);
                $redis->set($key,$job->getJobId());
                $data = $job->getJobData();
                $value = $redis->lPop('list');
                var_dump($value);
//                $redis = \EasySwoole\RedisPool\Redis::getInstance()->get('redis');
//                $redis->set('job', $job);
//                $redis->lPush('list',$job->getJobId());
                echo '************ 任务'.$job->getJobId().'开始 ************'.PHP_EOL;
                echo  '获取任务ID：'.$job->getJobId().PHP_EOL;
                echo  '任务投放时间：'.date("Y-m-d H:i:s",$job->getJobData()['time']).PHP_EOL;
                echo '已加入队列'.PHP_EOL;
                echo '任务完成'.PHP_EOL;
//                var_dump($redis->lPop('list'));

                echo '************ 任务'.$job->getJobId().'结束 ************'.PHP_EOL;
            });
        });
    }
}