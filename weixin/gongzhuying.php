<?php
require_once (dirname(__FILE__) . "/./lib/common.php");
require_once (dirname(__FILE__) . "/./lib/db/db.php");
require_once ("./lib/wechat.class.php");
class gongzhuying
{

    private $db = null;

    private $user = null;

    function __construct()
    {
        $this->db = new DB();
        $this->db->connect();
    }
    /**
     * 查看是否是终端店的店主
     * @param unknown $openid
     * @return void|multitype:multitype:mixed  |boolean
     */
    public function checkIfStoreOwner($openid)
    {
        addToLog($this->db, "checkIfStoreOwner, openid=".$openid);
        if ($openid == "")
            return;
        $existaccount = $this->db->get("scm_client", "*", array(
            "wechat_id" => $openid
        ));
        if ($existaccount) {
            return $existaccount;
        }
        return false;
    }
    
    /**
     * 按条件获取终端店信息
     * @param unknown $where
     */
    
    public function getStoreOwner($where){
        addToLog($this->db, "checkIfStoreOwner");
        $client = $this->db->get("scm_client", "*", $where);
        if ($client) {
            return $client;
        }
        return false;
    }
    
    /**
     * 按条件获取未入库的订单
     * @param unknown $where
     * @return multitype:multitype:mixed
     */
    public function getUnStorageList($where){
        addToLog($this->db, "getunStorageList, clie_id=".$where['clie_id']);
        $join = "scm_client_order AS scm_client_order left join ".TABLEPRE."scm_supplier AS scm_supplier ON scm_client_order.supp_id = scm_supplier.supp_id ";
        $field = "scm_client_order.id, scm_client_order.order_no, scm_client_order.supp_id, scm_supplier.supp_ch_name, refund_flag";
        $unstoragelist = $this->db->get($join,$field,$where);
        return $unstoragelist;
    }
    /**
     * 根据id获取单个订单
     * @param unknown $id
     */
    public function getClientOrderById($id){
        addToLog($this->db, "getClientOrderById, id=".$id);
        if($id == "")
            return;
        $order = $this->db->get("scm_client_order","*",array(
            "id" => $id
        ));
        //获取订单下的商品
        $order[0]['goods'] = $this->db->get("scm_order_goods","*",array(
            "order_id" => $id
        ));
        return $order;
        
    }
    
    /**
     * 更新单个订单信息
     * @param unknown $where
     */
    public function updateClientOrder($data,$where){
        addToLog($this->db, "updateClientOrder");
        if(isset($where['id']))
            $condition = " id =".$where['id'];
        $result = $this->db->update("scm_client_order",$data,$condition);
        return $result;
        
    }
    
    public function updateLocation($postObj) {
        addToLog($this->db, json_encode($postObj));
        $openid = $postObj->FromUserName;
        if ($openid == "")
            return;
        $Latitude=$postObj->Latitude;
        $Longitude=$postObj->Longitude;
        $data = array("lon"=>$Longitude,"lat"=>$Latitude);
        
        $this->db->update("scm_client",$data, " openid='".$openid."' ");
    }

    private function getToken()
    {
        return JSSDK::getInstance()->getAccessToken();
    }


    private function getWechatUser($token, $openid)
    {
        $user_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $token . "&openid=" . $openid . "&lang=zh_CN";
        $json = file_get_contents($user_url);
        $result = json_decode($json, true);
        return $result;
    }

    private function getUserByOpenID($openid)
    {
        $result = $this->db->get("user", "*", array(
            "openid" => $openid
        ));
        if (count($result) > 0) {
            return $result[0]['id'];
        }
        return "";
    }
}

class wxauth
{

    private $options;

    public $open_id;

    public $wxuser;

    public function __construct()
    {
        $options = array(
            'token' => WECHAT_TOKEN, // 填写你设定的key
            'appid' => WECHAT_APPID, // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret' => WECHAT_APPSECRET
        ) // 填写高级调用功能的密钥
;
        
        $this->options = $options;
        $this->wxoauth();
        // session_start();
    }

    public function wxoauth()
    {
        $scope = 'snsapi_base';
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $token_time = isset($_SESSION['token_time']) ? $_SESSION['token_time'] : 0;
        if (! $code && isset($_SESSION['open_id']) && isset($_SESSION['user_token']) && $token_time > time() - 3600) {
            if (! $this->wxuser) {
                $this->wxuser = $_SESSION['wxuser'];
            }
            $this->open_id = $_SESSION['open_id'];
            return $this->open_id;
        } else {
            $options = array(
                'token' => $this->options["token"], // 填写你设定的key
                'appid' => $this->options["appid"], // 填写高级调用功能的app id
                'appsecret' => $this->options["appsecret"]
            ) // 填写高级调用功能的密钥
;
            $we_obj = new Wechat($options);
            if ($code) {
                $json = $we_obj->getOauthAccessToken();
                if (! $json) {
                    unset($_SESSION['wx_redirect']);
                    die('获取用户授权失败，请重新确认');
                }
                $_SESSION['open_id'] = $this->open_id = $json["openid"];
                $access_token = $json['access_token'];
                $_SESSION['user_token'] = $access_token;
                $_SESSION['token_time'] = time();
                $userinfo = $we_obj->getUserInfo($this->open_id);
                if ($userinfo && ! empty($userinfo['nickname'])) {
                    $this->wxuser = array(
                        'open_id' => $this->open_id,
                        'nickname' => $userinfo['nickname'],
                        'sex' => intval($userinfo['sex']),
                        'location' => $userinfo['province'] . '-' . $userinfo['city'],
                        'avatar' => $userinfo['headimgurl']
                    );
                } elseif (strstr($json['scope'], 'snsapi_userinfo') !== false) {
                    $userinfo = $we_obj->getOauthUserinfo($access_token, $this->open_id);
                    if ($userinfo && ! empty($userinfo['nickname'])) {
                        $this->wxuser = array(
                            'open_id' => $this->open_id,
                            'nickname' => $userinfo['nickname'],
                            'sex' => intval($userinfo['sex']),
                            'location' => $userinfo['province'] . '-' . $userinfo['city'],
                            'avatar' => $userinfo['headimgurl']
                        );
                    } else {
                        return $this->open_id;
                    }
                }
                if ($this->wxuser) {
                    $_SESSION['wxuser'] = $this->wxuser;
                    $_SESSION['open_id'] = $json["openid"];
                    unset($_SESSION['wx_redirect']);
                    return $this->open_id;
                }
                $scope = 'snsapi_userinfo';
            }
            if ($scope == 'snsapi_base') {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $_SESSION['wx_redirect'] = $url;
            } else {
                $url = $_SESSION['wx_redirect'];
            }
            if (! $url) {
                unset($_SESSION['wx_redirect']);
                die('获取用户授权失败');
            }
            $oauth_url = $we_obj->getOauthRedirect($url, "wxbase", $scope);
            header('Location: ' . $oauth_url);
        }
    }
}
?>