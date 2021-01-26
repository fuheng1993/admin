<?php

namespace App\HttpController\Admin;

use App\Model\SystemModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;
use EasySwoole\Smtp\Mailer;
use EasySwoole\Smtp\MailerConfig;
use EasySwoole\Smtp\Message\Html;

/**
 * Class Users
 * Create With Automatic Generator
 */
class System extends Base
{

    /**
     * 系统配置
     */
    public function getSystem(){
        $system = SystemModel::create()->where('id',1)->get();
        $this->writeJson(Status::CODE_OK, $system, 'OK');
        return true;
    }
    //更新系统配置信息
    public function saveSystem(){
//        if(empty($this->param['system_name'])){ $this->AjaxJson(0,$this->param,'系统名称必须');return false;}
        if(empty($this->param['title'])){ $this->AjaxJson(0,$this->param,'网站名称必须');return false;}
        if(empty($this->param['keywords'])){ $this->AjaxJson(0,$this->param,'网站关键字必须');return false;}
        if(empty($this->param['description'])){ $this->AjaxJson(0,$this->param,'网站描述必须');return false;}
        if(empty($this->param['copyright'])){ $this->AjaxJson(0,$this->param,'网站版权必须');return false;}
        if(empty($this->param['icp'])){ $this->AjaxJson(0,$this->param,'网站备案号必须');return false;}
        if(empty($this->param['address'])){ $this->AjaxJson(0,$this->param,'网站地址必须');return false;}
        if(empty($this->param['tel'])){ $this->AjaxJson(0,$this->param,'客服电话必须');return false;}
        if(empty($this->param['qq'])){ $this->AjaxJson(0,$this->param,'客服QQ必须');return false;}
//        if(empty($this->param['qr_code'])){ $this->AjaxJson(0,$this->param,'客服微信必须');return false;}
//        $data['system_name']  = $this->param['system_name'];
        $data['title']  =  $this->param['title'];
        $data['keywords']  =  $this->param['keywords'];
        $data['description']  =  $this->param['description'];
        $data['copyright']  =  $this->param['copyright'];
        $data['address']  =  $this->param['address'];
        $data['icp']  =  $this->param['icp'];
        $data['qq'] =   $this->param['qq'];
        $data['tel'] =  $this->param['tel'];
        $data['phone'] =  $this->param['phone'];
        $data['appid'] =  $this->param['appid']??'';
        $data['secret'] =  $this->param['secret']??'';
        $data['service_url'] =  $this->param['service_url']??'';
        $data['cdn'] =  $this->param['cdn']??'';
        $data['name'] =  $this->param['name']??0;
        $data['en_name'] =  $this->param['en_name']??0;
        $data['account'] =  $this->param['account']??0;
        $data['entry_switch'] =  $this->param['entry_switch']??0;
        $data['open_cdn'] =  $this->param['open_cdn']??0;
        $data['is_sign'] =  $this->param['is_sign']??0;
        $data['is_music'] =  $this->param['is_music']??0;
        $data['is_identity'] =  $this->param['is_identity']??0;
        $data['version'] =  $this->param['version']??'';
        $data['bd_host'] =  $this->param['bd_host']??'';
        $data['bd_type'] =  $this->param['bd_type']??3;
        $data['bd_ocpc_token'] =  $this->param['bd_ocpc_token']??'';
        $data['bd_token'] =  $this->param['bd_token']??'';
        $data['update_time'] =  time();
        $data['mchid'] =  $this->param['mchid']??'';
        $data['key'] =  $this->param['key']??'';
        $data['cert_pem'] =  $this->param['cert_pem']??'';
        $data['key_pem'] =  $this->param['key_pem']??'';
        $data['zfb_mchid'] =  $this->param['zfb_mchid']??'';
        $data['zfb_private_key'] =  $this->param['zfb_private_key']??'';
        $data['zfb_public_key'] =  $this->param['zfb_public_key']??'';
        $data['zfb_alipay_key'] =  $this->param['zfb_alipay_key']??'';
        $data['app_key'] =  $this->param['app_key']??'';
        $data['app_secret'] =  $this->param['app_secret']??'';
        $data['app_code'] =  $this->param['app_code']??'';


        if(SystemModel::create()->update($data,['id'=>1])){
            Cache::getInstance()->set('system',SystemModel::create()->where('id',1)->get()); //更新缓存
            $this->AjaxJson(1, Cache::getInstance()->get('system'),'更新配置成功');return false;
        }else{
            $this->AjaxJson(0,$data,'更新配置失败');return false;
        }
    }
    //更新邮箱配置
    public function saveEmail(){
        if(empty($this->param['email'])){ $this->AjaxJson(0,$this->param,'邮箱账号必须');return false;}
        if(empty($this->param['email_password'])){ $this->AjaxJson(0,$this->param,'邮箱授权密码必须');return false;}
        $data['email']=$this->param['email'];
        $data['email_password']=$this->param['email_password'];
        $data['update_time'] = time();
        if(SystemModel::create()->update($data,['id'=>1])){
            Cache::getInstance()->set('system',SystemModel::create()->where('id',1)->find()); //更新缓存
            $this->AjaxJson(1,$data,'更新邮箱配置成功');return false;
        }else{
            $this->AjaxJson(0,$data,'更新邮箱配置失败');return false;
        }
    }

    /**
     * 发送测试邮件
     * @param email          发送邮箱
     * @param email_password 邮箱客户端授权密码
     * @param to_email       接收邮箱
     * @return JSON
     */
    public function sendMessageTest(){
        $system = SystemModel::create()->get(1); //获取系统配置信息 如网站名称等信息
        $email = $this->param['email'];
        $email_password = $this->param['email_password'];
        $to_email = $this->param['to_email'];
        $config = new MailerConfig();
        $config->setServer('smtp.qq.com');
        $config->setPort('25');
        $config->setSsl(false);
        $config->setUsername($email);
        $config->setPassword($email_password);
        $config->setMailFrom($email);
        $config->setTimeout(10);//设置客户端连接超时时间
        $config->setMaxPackage(1024*1024*5);//设置包发送的大小：5M

        $mimeBean = new Html(); //设置文本或者html格式
        $mimeBean->setSubject("【{$system['system_name']}】测试发送邮件");
        $mimeBean->setBody("【{$system['system_name']}】测试发送邮件");
        $mailer = new Mailer($config);
        $res = $mailer->sendTo($to_email, $mimeBean);
        if($res){
            $this->AjaxJson(1,$res,'测试发送成功');return false;
        }else{
            $this->AjaxJson(0,$res,'测试发送失败');return false;
        }
    }

    /**
     * 上传图片
     */
    public function upload(){
        $request=  $this->request();
        $img_file = $request->getUploadedFile('file');//获取一个上传文件,返回的是一个\EasySwoole\Http\Message\UploadFile的对象
        $fileSize = $img_file->getSize();
        //上传图片不能大于5M (1048576*5)
        if($fileSize>1048576*5){
            $this->writeJson(Status::CODE_BAD_REQUEST,['size'=>$fileSize], '图片最大不能超过5MB'); return false;
        }
        $clientFileName = $img_file->getClientFilename();
        $fileName = '_'.MD5(time()).'.'.pathinfo($clientFileName, PATHINFO_EXTENSION);;
        $res = $img_file->moveTo(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName);
        if(file_exists(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName)){
            $data['image'] = 'http://'.$this->request()->getUri()->getHost().'/public/uploads/'.$fileName;
            $model = SystemModel::create();
            if( $model->where('id',1)->update(['qr_code'=>$data['image']])){
                Cache::getInstance()->set('system',$model->where('id',1)->find()); //更新缓存
                $this->AjaxJson(1,  $data, 'success');
            }else{
                $this->AjaxJson(0,  $data, '更新公众号二维码失败');

            }

        }else{
            $this->AjaxJson(0, [], '文件上传失败');
        }
        return true;
    }
    /**
     * 上传图片
     */
    public function uploadLogo(){
        $request=  $this->request();
        $img_file = $request->getUploadedFile('file');//获取一个上传文件,返回的是一个\EasySwoole\Http\Message\UploadFile的对象
        $fileSize = $img_file->getSize();
        //上传图片不能大于5M (1048576*5)
        if($fileSize>1048576*5){
            $this->writeJson(Status::CODE_BAD_REQUEST,['size'=>$fileSize], '图片最大不能超过5MB'); return false;
        }
        $clientFileName = $img_file->getClientFilename();
        $fileName = '_'.MD5(time()).'.'.pathinfo($clientFileName, PATHINFO_EXTENSION);;
        $res = $img_file->moveTo(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName);
        if(file_exists(EASYSWOOLE_ROOT.'/public/uploads/'.$fileName)){
            $data['image'] = 'http://'.$this->request()->getUri()->getHost().'/public/uploads/'.$fileName;
            $model = SystemModel::create();
            if( $model->where('id',1)->update(['logo'=>$data['image']])){
                Cache::getInstance()->set('system',$model->where('id',1)->find()); //更新缓存
                $this->AjaxJson(1, $data, 'success'.$model->lastQuery()->getLastPrepareQuery());
            }else{
                $this->AjaxJson(0, $data, '更新公众号logo失败');

            }

        }else{
            $this->AjaxJson(0, [], '文件上传失败');
        }
        return true;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        // TODO: Implement getValidateRule() method.
        switch ($action) {
            case 'login':
                $valitor = new Validate();
                $valitor->addColumn('u_account')->required();
                $valitor->addColumn('u_password')->required();
                return $valitor;
                break;
        }
        return NULL;
    }
}

