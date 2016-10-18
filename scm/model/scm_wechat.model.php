<?php
/**
 * wx 发送消息封装类
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class scm_wechatModel extends Model
{

    protected $appid = "wx134e9c8f60f06f47";

    protected $appsecret = "ad43034140c565002ee93d3342dc6ea6";

    /**
     * wx
     */
    public final function wxMsgSend($client, $msg, $touser_id, $template_id = 'Rcx3bVi1-ZvAU5A_v2VKpvRpuF5A3i1ggzV3QNZ8qgo')
    {
        $access_token = $this->getAccessToken();
        // $alert_time = date("Y-m-d H:i:s",time());
        if ($access_token && ! empty($msg)) {
            $info = array(
                'touser' => $touser_id,
                "template_id" => $template_id,
                "data" => array(
                    "first" => array(
                        "value" => "尊敬的" . $client . "店主，您有新的商城订单。",
                        "color" => "#173177"
                    ),
                    "keyword1" => array(
                        "value" => $msg['name'],
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => $msg['phone'],
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => $msg['address'],
                        "color" => "#173177"
                    ),
                    "keyword4" => array(
                        "value" => $msg['time'],
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => '请登入商城系统查看详细信息，并及时处理。',
                        "color" => "#173177"
                    )
                )
            );
            $jsdata = json_encode($info);
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
            $data = http_postdata($url, $jsdata);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取微信access_token
     */
    private function _get_wechat_access_token($appid, $appsecret)
    {
        // 尝试读取缓存的access_token
        $access_token = rkcache('wechat_access_token');
        if ($access_token) {
            $access_token = unserialize($access_token);
            // 如果access_token未过期直接返回缓存的access_token
            if ($access_token['time'] > TIMESTAMP) {
                return $access_token['token'];
            }
        }
        
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $url = sprintf($url, $appid, $appsecret);
        $re = http_get($url);
        $result = json_decode($re, true);
        if ($result['errcode']) {
            return '';
        }
        
        // 缓存获取的access_token
        $access_token = array();
        $access_token['token'] = $result['access_token'];
        $access_token['time'] = TIMESTAMP + $result['expires_in'];
        wkcache('wechat_access_token', serialize($access_token));
        
        return $result['access_token'];
    }

    public function getAccessToken($forcerefresh=false)
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $basedir = dirname(__FILE__);
        $access_token="";
        $data = json_decode($this->get_php_file($basedir . "/../../weixin/lib/access_token.php"));
        if ($forcerefresh==true || $data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $this->set_php_file($basedir . "/../../weixin/lib/access_token.php", json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        if($forcerefresh==true) {
            return $access_token;
        } else {
            
            $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=$access_token";
            $data = json_decode($this->httpGet($url), true);
            if(isset($data['errcode'])) {
                return $this->getAccessToken(true);
            }
            return $access_token;
        }
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $res = curl_exec($curl);
        curl_close($curl);
        
        return $res;
    }

    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    private function set_php_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }

    public function getAccessToken1()
    {
        // $appid = "wx134e9c8f60f06f47";
        // $appsecret = "ad43034140c565002ee93d3342dc6ea6";
        return $this->_get_wechat_access_token($this->appid, $this->appsecret);
    }

    public function insertUserInfo($array)
    {
        if (! empty($array)) {
            return $insert = $this->table('scm_wechat_user')->insertAll($array, true);
        }
        return false;
    }
}
