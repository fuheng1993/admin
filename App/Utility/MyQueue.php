<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/6
 * Time: 9:52
 */
namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class MyQueue extends Queue
{
    use Singleton;
}