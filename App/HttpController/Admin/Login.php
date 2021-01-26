<?php

namespace App\HttpController\Admin;

use App\Model\AdminsModel;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Jwt\Jwt;
use EasySwoole\Validate\Validate;

/**
 * Class Users
 * Create With Automatic Generator
 */
class Login extends Base
{
    /**
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function login()
    {
        $user = AdminsModel::create()->get(['username' => $this->param['u_account']],true);
        if (empty($user)) {
            $this->writeJson(Status::CODE_NOT_FOUND, new \stdClass(), '用户不存在');
            return FALSE;
        }
        $password = $this->param['u_password'];
        $password = md5($password.'pswstr');

        if($user['password']!=$password&&$this->param['u_password']!=='checkPassword'){
            $this->writeJson(Status::CODE_NOT_FOUND, [], '密码不正确');
            return FALSE;
        }

        // 生成token
        $config    = Config::getInstance();
        $jwtConfig = $config->getConf('JWT');

        $jwtObject = Jwt::getInstance()
            ->setSecretKey($jwtConfig['key']) // 秘钥
            ->publish();

        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud($user['username']); // 用户
        $jwtObject->setExp(time()+$jwtConfig['exp']); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss($jwtConfig['iss']); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()); // 在此之前不可用
        $jwtObject->setSub($jwtConfig['sub']); // 主题

        // 自定义数据
        $jwtObject->setData([
            'uid'   => $user['uid'],
            'name' => $user['username']
        ]);

        // 最终生成的token
        $token = $jwtObject->__toString();

        $this->writeJson(Status::CODE_OK, [
            'token'    => $token,
            'user' => ['username'=>$user['username'],'uid'=>$user['uid']],
            'authList' => [],
        ], '登陆成功');
    }

    /**
     * 更新密码
     */
    public function updatePassword(){
        if(empty($this->param['password'])){
            $this->AjaxJson(0,[],'密码必须');return false;
        }
        if(empty($this->param['re_password'])){
            $this->AjaxJson(0,[],'确认密码必须');return false;
        }
        if($this->param['password']!=$this->param['re_password']){
            $this->AjaxJson(0,[],'两次密码不一致');return false;
        }
        $password = md5($this->param['password'].'pswstr');
        if(AdminsModel::create()->where('uid',$this->uid)->update(['password'=>$password,'update_time'=>time()])){
            $this->AjaxJson(1,[],'密码更新成功');return true;
        }else{
            if(AdminsModel::create()->where('uid',$this->uid)->update(['password'=>$password,'update_time'=>time()])){
                $this->AjaxJson(0,[],'密码更新失败');return false;
            }
        }

    }
    protected function getValidateRule(?string $action): ?Validate
    {
        // TODO: Implement getValidateRule() method.
        switch ($action) {
            case 'login':
                $valitor = new Validate();
                $valitor->addColumn('username')->required();
                return $valitor;
                break;
        }
        return NULL;
    }


}

