<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-17
 * Time: 21:14
 */

namespace EasySwoole\Pay\WeChat;

use EasySwoole\Pay\Exceptions\GatewayException;
use EasySwoole\Pay\Exceptions\InvalidArgumentException;
use EasySwoole\Pay\Exceptions\InvalidSignException;
use EasySwoole\Pay\Utility\NewWork;
use EasySwoole\Pay\WeChat\RequestBean\Base;
use EasySwoole\Spl\SplArray;


class Utility
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 生成签名
     * @param array $data
     * @return string
     */
    public function generateSign(array $data): string
    {
        ksort($data);
        $signType = isset($data['sign_type']) ? $data['sign_type'] : 'MD5';
        switch ($signType) {
            case 'HMAC-SHA256':
                $string = hash_hmac('sha256', $this->getSignContent($data) . '&key=' . $this->config->getKey(), $this->config->getKey());
                break;
            default:
                $string = md5($this->getSignContent($data) . '&key=' . $this->config->getKey());
        }
        return strtoupper($string);
    }

    /**
     * 组成签名内容
     * @param array $data
     * @return string
     */
    private function getSignContent(array $data): string
    {
    	unset($data['sign']);
       return  urldecode(http_build_query($data));
       /*
        $buff = '';
        foreach ($data as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
        }
        return trim($buff, '&');
       */
    }

    /**
     * 请求返回数组
     * @param string $endpoint
     * @param Base $bean
     * @param bool $useCert
     * @return SplArray
     * @throws GatewayException
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     */
    public function requestApi(string $endpoint, Base $bean, bool $useCert = false): SplArray
    {
        $result = $this->request($endpoint, $bean, $useCert);
        $result = is_array($result) ? $result : $this->fromXML($result);
        if (!isset($result['return_code']) || $result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            throw new GatewayException('Get Wechat API Error:' . ($result['return_msg'] ?? $result['retmsg']) . ($result['err_code_des'] ?? ''), 20000);
        }
        if (strpos($endpoint, 'mmpaymkttransfers') !== false || $this->generateSign($result) === $result['sign']) {
            return new SplArray($result);
        }
        throw new InvalidSignException('sign is error');
    }

    /**
     * 请求返回原生字符串
     * @param string $endpoint
     * @param Base $bean
     * @param bool $useCert
     * @return string
     * @throws GatewayException
     */
    public function request(string $endpoint, Base $bean, bool $useCert = false): string
    {
        $bean->setAppId($bean instanceof \EasySwoole\Pay\WeChat\RequestBean\MiniProgram ? $this->config->getMiniAppId() : $this->config->getAppId());
        $bean->setMchId($this->config->getMchId());
        $bean->setSign($this->generateSign($bean->toArray()));
        $response = NewWork::postXML($this->config->getGateWay() . $endpoint, (new SplArray($bean->toArray()))->toXML(true), $useCert ? [
            'ssl_cert_file' => $this->config->getApiClientCert(),
            'ssl_key_file' => $this->config->getApiClientKey()]
            : []);
        if ($response->getStatusCode() == 200) {
            return $response->getBody();
        }
        throw new GatewayException('Get Wechat API Error url:' . $this->config->getGateWay() . $endpoint . ' params:' . $bean->__toString(), 20000);
    }

    /**
     * XML转化为array
     * @param $xml
     * @return array
     * @throws InvalidArgumentException
     */
    public function fromXML($xml): array
    {
        if (!$xml) {
            throw new InvalidArgumentException('Convert To Array Error! Invalid Xml!');
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 退款解密
     * @param $contents
     * @return string
     */
    public function decryptRefundContents($contents)
    {
        return openssl_decrypt(
            base64_decode($contents),
            'AES-256-ECB',
            md5($this->config->getKey()),
            OPENSSL_RAW_DATA
        );
    }

    /**
     * 获取共享收货地址js函数需要的参数
     * @param appId  公众号appid
     * @param url 当前网页URL
     * @access_token access_token
     * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters($appId,$url,$access_token)
    {
        $data = array();
        $data["appid"] = $appId;
        $data["url"] = $url;
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = $this->getNonceStr();
        $data["accesstoken"] = $access_token;
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);
        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $appId,
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public  function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}