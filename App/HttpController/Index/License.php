<?php

namespace App\HttpController\Index;

use App\Model\CategoryModel;
use App\Model\LicenseModel;
use App\Model\WechatModel;
use EasySwoole\Template\Render;


/**
 * Class Users
 * Create With Automatic Generator
 */
class License extends \App\HttpController\Index\Base
{
    /**
     * 反欺诈分析-简版 样例报告
     */
    public function index(){
        $this->assign['license'] =LicenseModel::create()->where('id',$this->param['id']??1)->find();
        $this->response()->write(Render::getInstance()->render($this->pc.'/index/license/index', $this->assign));
        return true;
    }

}

