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
     * 
     * @param unknown $openid            
     * @return void|multitype:multitype:mixed |boolean
     */
    public function checkIfStoreOwner($openid)
    {
        addToLog($this->db, "checkIfStoreOwner, openid=" . $openid);
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
     * 
     * @param unknown $where            
     */
    public function getStoreOwner($where)
    {
        addToLog($this->db, "getStoreOwner");
        $client = $this->db->get("scm_client", "*", $where);
        if ($client) {
            return $client;
        }
        return false;
    }

    /**
     * 按条件获取未入库的订单
     * 
     * @param unknown $where            
     * @return multitype:multitype:mixed
     */
    public function getUnStorageList($where)
    {
        addToLog($this->db, "getunStorageList, clie_id=" . $where['clie_id']);
        $join = "scm_client_order AS scm_client_order left join " . TABLEPRE . "scm_supplier AS scm_supplier ON scm_client_order.supp_id = scm_supplier.supp_id ";
        $field = "scm_client_order.id, scm_client_order.order_no, scm_client_order.supp_id, scm_supplier.supp_ch_name, refund_flag";
        $unstoragelist = $this->db->get($join, $field, $where);
        return $unstoragelist;
    }

    /**
     * 根据id获取单个订单
     * 
     * @param unknown $id            
     */
    public function getClientOrderById($id)
    {
        addToLog($this->db, "getClientOrderById, id=" . $id);
        if ($id == "")
            return;
        $order = $this->db->get("scm_client_order", "*", array(
            "id" => $id
        ));
        // 获取订单下的商品
        $order[0]['goods'] = $this->db->get("scm_order_goods", "*", array(
            "order_id" => $id
        ));
        return $order;
    }

    /**
     * 更新单个订单信息
     * 
     * @param unknown $where            
     */
    public function updateClientOrder($data, $where)
    {
        addToLog($this->db, "updateClientOrder");
        if (isset($where["id"]))
            $condition = " id =" . $where["id"];
        $order = $this->db->get("scm_client_order", "*", array(
            "id" => $where["id"]
        ));
        // 更新商品库存等信息
        if ($order[0]["in_flag"] == 1 && $order[0]["order_status"] == 1) { // 已经在web端入库了
            return 0;
        } else {
            $result = $this->db->update("scm_client_order", $data, $condition);
            $order_goods_list = $this->db->get("scm_order_goods", "*", array(
                "order_id" => $where['id']
            ));
            foreach ($order_goods_list as $goods) {
                $now = date("Y-m-d H:i:s", time());
                $temp_goods = $this->db->get("scm_client_stock", "*", array(
                    'goods_barcode' => $goods['goods_barcode'],
                    'clie_id' => $where['clie_id']
                ));
                if (count($temp_goods) > 0) { // 库存表中有该商品修改商品酷讯
                    $update_goods = array(
                        'goods_stock' => $temp_goods[0]['goods_stock'] + $goods['set_num'] * $goods['unit_num'],
                        'production_date' => $goods['production_date'],
                        'shelf_life' => $goods['shelf_life'],
                        'goods_nm' => $goods['goods_nm'],
                        'supp_id' => $goods['supp_id']
                    );
                    $this->db->update("scm_client_stock", $update_goods, " goods_barcode = " . $goods['goods_barcode'] . " AND clie_id = '" . $where['clie_id'] . "'");
                } else { // 库存表中无该商品增加新商品
                    $new_goods = array(
                        'clie_id' => $where['clie_id'],
                        'supp_id' => $goods['supp_id'],
                        'goods_barcode' => $goods['goods_barcode'],
                        'goods_nm' => $goods['goods_nm'],
                        'goods_stock' => $goods['set_num'] * $goods['unit_num'],
                        'goods_low_stock' => 10,
                        'drug_remind' => 30,
                        'production_date' => $goods['production_date'],
                        'valid_remind' => $goods['valid_remind'],
                        'shelf_life' => $goods['shelf_life']
                    );
                    $this->db->insert("scm_client_stock", $new_goods);
                }
                $instock_info = array(
                    'clie_id' => $where['clie_id'],
                    'supp_id' => $goods['supp_id'],
                    'order_id' => $goods['order_id'],
                    'goods_nm' => $goods['goods_nm'],
                    'set_num' => $goods['set_num'],
                    'unit_num' => $goods['unit_num'],
                    'goods_unit' => $goods['goods_unit'],
                    'goods_spec' => $goods['goods_spec'],
                    'goods_barcode' => $goods['goods_barcode'],
                    'production_date' => $goods['production_date'],
                    'valid_remind' => $goods['valid_remind'],
                    'shelf_life' => $goods['shelf_life'],
                    'in_stock_date' => $now
                );
                
                $this->db->insert("scm_instock_info", $instock_info);
            }
            
            return 1;
        }
    }

    public function updateLocation($postObj)
    {
        addToLog($this->db, json_encode($postObj));
        $openid = $postObj->FromUserName;
        if ($openid == "")
            return;
        $Latitude = $postObj->Latitude;
        $Longitude = $postObj->Longitude;
        $data = array(
            "lon" => $Longitude,
            "lat" => $Latitude
        );
        
        $this->db->update("scm_client", $data, " openid='" . $openid . "' ");
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

    private $db = null;

    public function __construct()
    {
        $options = array(
            'token' => WECHAT_TOKEN, // 填写你设定的key
            'appid' => WECHAT_APPID, // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret' => WECHAT_APPSECRET
        ); // 填写高级调用功能的密钥

        $this->db = new DB();
        $this->db->connect();
        $this->options = $options;
        $this->wxoauth();
        session_start();
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
            ); // 填写高级调用功能的密钥

            $we_obj = new Wechat($options);
            if ($code) {
                $json = $we_obj->getOauthAccessToken();
                if (! $json) {
                    unset($_SESSION['wx_redirect']);
                    //die('获取用户授权失败，请重新确认');
                    throw new Exception('获取用户授权失败，请重新确认');
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
//                 die('获取用户授权失败');
                throw new Exception('获取用户授权失败');
            }
            $oauth_url = $we_obj->getOauthRedirect($url, "wxbase", $scope);
            addToLog($this->db, "redirect:".$oauth_url);
            
            header('Location: ' . $oauth_url);
        }
    }
}
?>