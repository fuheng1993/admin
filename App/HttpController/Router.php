<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午10:39
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {

        $routeCollector->addGroup('/api',function (RouteCollector $routeCollector){
            $routeCollector->get('/index', '/index/index/index');
            $routeCollector->get('/test', '/index/index/test');
        });
        //Index.php
        $routeCollector->get('/', '/index/index/index');
        $routeCollector->get('/viewReload', '/index/index/viewReload');
        $routeCollector->get('/index', '/index/index/index');
        $routeCollector->addRoute(['GET','POST'],'/agreement', '/index/index/isAgreement');
        $routeCollector->get('/license/{id:\d+}',  '/index/license/index');
        //Pay.php
        $routeCollector->addRoute(['GET','POST'],'/pay', '/index/pay/pay');
        $routeCollector->addRoute(['GET','POST'],'/returnUrl', '/index/pay/returnUrl');
        $routeCollector->addRoute(['GET','POST'],'/notifyUrl', '/index/pay/notifyUrl');
        $routeCollector->addRoute(['GET','POST'],'/errorUrl', '/index/pay/errorUrl');
        $routeCollector->addRoute(['GET','POST'],'/doOrder', '/index/index/doOrder');
        $routeCollector->get('/service', '/index/login/service');
        $routeCollector->addRoute(['GET','POST'],'/checkOrderIsPay', '/index/index/checkOrderIsPay');
        $routeCollector->addRoute(['GET','POST'],'/search', '/index/index/search');
        $routeCollector->addRoute(['GET','POST'],'/getMyOrder', '/index/index/getMyOrder');


//        $this->setGlobalMode(false);
//        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
//            $response->write('未找到处理方法');
//            return false;//结束此次响应
//        });
//        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
//            $response->withHeader('Content-type', 'text/html;charset=utf-8');
//            $response->write('未找到路由匹配!!!');
//            return '/index/index/test';//重定向到index路由
//            return false;
//        });



    }
}