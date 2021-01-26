<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/26
 * Time: 10:35
 */
namespace App\Producer;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Kafka\Config\ProducerConfig;
use EasySwoole\Kafka\kafka;

class Process extends AbstractProcess
{
    protected function run($arg)
    {
        go(function () {
            $config = new ProducerConfig();
            $config->setMetadataBrokerList('127.0.0.1:9092,127.0.0.1:9093');
            $config->setBrokerVersion('0.9.0');
            $config->setRequiredAck(1);

            $kafka = new Kafka($config);
            $result = $kafka->producer()->send([
                [
                    'topic' => 'test',
                    'value' => 'message--',
                    'key'   => 'key--',
                ],
            ]);

            var_dump($result);
            var_dump('ok');
        });
    }
}