<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/26
 * Time: 10:35
 */
namespace App\Producer;

use EasySwoole\Component\Process\AbstractProcess;

class NsqProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function () {
            $config = new \EasySwoole\Nsq\Config();
            $topic  = "topic.test";
            $nsqlookup = new \EasySwoole\Nsq\Lookup\Nsqlookupd($config->getNsqdUrl());
            $hosts = $nsqlookup->lookupHosts($topic);

            foreach ($hosts as $host) {
                $nsq = new \EasySwoole\Nsq\Nsq();
                for ($i = 0; $i < 10; $i++) {
                    $msg = new \EasySwoole\Nsq\Message\Message();
                    $msg->setPayload("test$i");
                    $nsq->push(
                        new \EasySwoole\Nsq\Connection\Producer($host, $config),
                        $topic,
                        $msg
                    );
                }
            }
        });
    }
}