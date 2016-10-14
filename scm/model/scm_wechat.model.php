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
class scm_wechatModel extends Model {

    protected $appid = "wx36b98215337c13ff";
    protected $appsecret = "d4624c36b6795d1d99dcf0547af5443d";
    /**
     * wx
     */
    public final function wxMsgSend($client, $msg, $touser_id, $template_id='Rcx3bVi1-ZvAU5A_v2VKpvRpuF5A3i1ggzV3QNZ8qgo') {       
        $access_token = $this->getAccessToken();
        // $alert_time = date("Y-m-d H:i:s",time());
            if ($access_token && !empty($msg)) {
                $info = array(
                        'touser' => $touser_id,
                        "template_id" => $template_id,
                        "data" => array(
                            "first" => array(
                                    "value" => "尊敬的".$client."店主，您有新的商城订单。",
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
                $jsdata=json_encode($info);
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
    private function _get_wechat_access_token($appid, $appsecret) {
        // 尝试读取缓存的access_token
        $access_token = rkcache('wechat_access_token');
        if($access_token) {
            $access_token = unserialize($access_token);
            // 如果access_token未过期直接返回缓存的access_token
            if($access_token['time'] > TIMESTAMP) {
                return $access_token['token'];
            }
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $url = sprintf($url, $appid, $appsecret);
        $re = http_get($url);
        $result = json_decode($re, true);
        if($result['errcode']) {
            return '';
        }

        // 缓存获取的access_token
        $access_token = array();
        $access_token['token'] = $result['access_token'];
        $access_token['time'] = TIMESTAMP + $result['expires_in'];
        wkcache('wechat_access_token', serialize($access_token));

        return $result['access_token'];
    }

    public function getAccessToken() {
        $appid = "wx134e9c8f60f06f47";
        $appsecret = "ad43034140c565002ee93d3342dc6ea6";
        return $this->_get_wechat_access_token($appid, $appsecret);
    }

    public function insertUserInfo($array) {
        if (!empty($array)){
            return $insert = $this->table('scm_wechat_user')->insertAll($array, true);
        }
        return false;
    }

}
