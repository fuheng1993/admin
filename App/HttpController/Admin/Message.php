<?php

namespace App\HttpController\Admin;



use App\Model\MessageModel;
use App\Model\SystemModel;
use App\Model\WechatModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Smtp\Mailer;
use EasySwoole\Smtp\MailerConfig;
use EasySwoole\Smtp\Message\Html;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Message extends \App\HttpController\Admin\Base
{
    /**
     * 获取链路记录列表
     */

    public function lists(){

        $model = MessageModel::create();
        if(!empty($this->param['title']) ){$model->where('m.title',"%{$this->param['title']}%",'like');}
        if(!empty($this->param['name']) ){$model->where('u.name',"%{$this->param['name']}%",'like');}
        $limit =$this->param['limit']??10;
        $p =$this->param['page']??1;
        $list = $model->withTotalCount()->alias('m')->field('m.*,u.name as user')
        ->join('td_user u','u.id = m.user_id','LEFT')
        ->limit($limit * ($p - 1), $limit)->order('id','desc')->all();
        $total = $model->lastQueryResult()->getTotalCount();
        if(is_object($list)){$list = $list->toArray();}

        $this->writeJson(Status::CODE_OK, ['total'=>$total,'list'=>$list], 'success');
        return true;

    }
    /**
     * 删除品牌
     */
    public function send(){
        if(!empty($this->param['id'])){
            try {
                $message = MessageModel::create()->where('id',$this->param['id'])->get();
                $system = SystemModel::create()->where('id', 1)->get();
                $config = new MailerConfig();
                $config->setServer('smtp.qq.com');
                $config->setPort('25');
                $config->setSsl(false);
                $config->setUsername($system->email);
                $config->setPassword($system->email_password);
                $config->setMailFrom($system->email);
                $config->setTimeout(10);//设置客户端连接超时时间
                $config->setMaxPackage(1024 * 1024 * 5);//设置包发送的大小：5M
                $mimeBean = new Html(); //设置文本或者html格式
                $mimeBean->setSubject("【收款异常通知】{$message->title}");
                $mimeBean->setBody($message->contents);
                $mailer = new Mailer($config);
                $res = $mailer->sendTo($message->email, $mimeBean); //发送邮件
                //标记已发送
                MessageModel::create()->where('id', $message->id)->update(['is_send' => 1]);
                $this->AjaxJson(1,[],'邮件发送成功'); return true;
            } catch (\Exception $e) {
                $this->AjaxJson(0,[],"邮件发送异常：{$e->getMessage()}");return false;
            }
        }else{
            $this->AjaxJson(0, ['param'=>$this->param], '邮件ID必须');
        }
        return false;
    }

}

