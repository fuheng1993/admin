<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 12:09 AM
 */

namespace EasySwoole\WeChat\MiniProgram;

use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\WeChat\Exception\RequestError;
use EasySwoole\WeChat\Utility\NetWork;
use EasySwoole\WeChat\Exception\MiniProgramError;

/**
 * 访问令牌管理
 * Class AccessToken
 * @package EasySwoole\WeChat\MiniProgram
 */
class AccessToken extends MinProgramBase
{

    /**
     * 获取访问令牌
     * 默认刷新一次
     * @param int $refreshTimes
     * @return string|null
     * @throws \Throwable
     */
    public function getToken($refreshTimes = 1): ?string
    {
        $lockName = 'MiniProgram_'. $this->getMiniProgram()->getConfig()->getAppId();

        try{
            if(!$this->getMiniProgram()->getConfig()->getStorage()->lock($lockName)){
                return null;
            }
            $data = $this->getMiniProgram()->getConfig()->getStorage()->get('access_token');
            if(!empty($data)){
                return $data;
            }else if($refreshTimes > 0){
                $this->refresh();
                return $this->getToken($refreshTimes -1);
            }
            return null;
        }catch (\Throwable $throwable){
            throw $throwable;
        }finally{
            $this->getMiniProgram()->getConfig()->getStorage()->unlock($lockName);
        }
    }

    /**
     * 刷新访问令牌
     * @return string
     * @throws MiniProgramError
     * @throws RequestError
     * @throws InvalidUrl
     */
    public function refresh(): ?string
    {
        $config = $this->getMiniProgram()->getConfig();
        $url = ApiUrl::generateURL(ApiUrl::AUTH_GET_ACCESS_TOKEN, [
            'APPID'     => $config->getAppId(),
            'APPSECRET' => $config->getAppSecret()
        ]);
        $responseArray = NetWork::getForJson($url);
        $ex = MiniProgramError::hasException($responseArray);
        if ($ex) {
            throw $ex;
        }
        $token = $responseArray['access_token'];
        $config->getStorage()->set('access_token', $token, time() + 7180); // 这里故意设置为7180 提前刷新
        return $token;
    }
}