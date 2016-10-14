<?php
/**
 * 入口文件
 *
 * 统一入口，进行初始化信息
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城 Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com/
 * @link       http://www.gongzhuying.com/
 * @since      File available since Release v1.1
 */

error_reporting(E_ALL & ~E_NOTICE);
define('BASE_ROOT_PATH',str_replace('\\','/',dirname(__FILE__)));
define('BASE_CORE_PATH',BASE_ROOT_PATH.'/core');
define('BASE_DATA_PATH',BASE_ROOT_PATH.'/data');
define("BASE_UPLOAD_PATH", BASE_ROOT_PATH . "/data/upload");
define("BASE_RESOURCE_PATH", BASE_ROOT_PATH . "/data/resource");
define('BASE_SCM_PATH',BASE_ROOT_PATH.'/scm');

/**
 * 安装判断
 */
if (!is_file(BASE_ROOT_PATH."/shop/install/lock") && is_file(BASE_ROOT_PATH."/shop/install/index.php")){
    if (ProjectName != 'shop'){
        @header("location: ../shop/install/index.php");
    }else{
        @header("location: install/index.php");
    }
    exit;
}


/**
 * 初始化
 */
Shopnc\Core::initializeApplication(require BASE_DATA_PATH . '/config/config.ini.php');

define('DIR_SHOP','shop');
define('DIR_CMS','cms');
define('DIR_CIRCLE','circle');
define('DIR_MICROSHOP','microshop');
define('DIR_ADMIN','admin');
define('DIR_API','api');
define('DIR_MOBILE','mobile');
define('DIR_WAP','wap');
define('DIR_RESOURCE','data/resource');
define('DIR_UPLOAD','data/upload');

define('ATTACH_PATH','shop');
define('ATTACH_COMMON','shop/common');
define('ATTACH_AVATAR','shop/avatar');
define('ATTACH_EDITOR','shop/editor');
define('ATTACH_MEMBERTAG','shop/membertag');
define('ATTACH_STORE','shop/store');
define('ATTACH_GOODS','shop/store/goods');
define('ATTACH_STORE_DECORATION','shop/store/decoration');
define('ATTACH_LOGIN','shop/login');
define('ATTACH_ARTICLE','shop/article');
define('ATTACH_BRAND','shop/brand');
define('ATTACH_GOODS_CLASS','shop/goods_class');
define('ATTACH_ADV','shop/adv');
define('ATTACH_ACTIVITY','shop/activity');
define('ATTACH_WATERMARK','shop/watermark');
define('ATTACH_POINTPROD','shop/pointprod');
define('ATTACH_GROUPBUY','shop/groupbuy');
define('ATTACH_SLIDE','shop/store/slide');
define('ATTACH_VOUCHER','shop/voucher');
define('ATTACH_REDPACKET','shop/redpacket');
define('ATTACH_STORE_JOININ','shop/store_joinin');
define('ATTACH_REC_POSITION','shop/rec_position');
define('ATTACH_CONTRACTICON','shop/contracticon');
define('ATTACH_CONTRACTPAY','shop/contractpay');
define('ATTACH_WAYBILL','shop/waybill');
define('ATTACH_MOBILE','mobile');
define('ATTACH_CIRCLE','circle');
define('ATTACH_CMS','cms');
define('ATTACH_LIVE','live');
define('ATTACH_MALBUM','shop/member');
define('ATTACH_MICROSHOP','microshop');
define('ATTACH_DELIVERY','delivery');
define('ATTACH_CHAIN', 'chain');
define('ATTACH_ADMIN_AVATAR','admin/avatar');
define('TPL_SHOP_NAME','default');
define('TPL_CIRCLE_NAME', 'default');
define('TPL_MICROSHOP_NAME', 'default');
define('TPL_CMS_NAME', 'default');
define('TPL_ADMIN_NAME', 'default');
define('TPL_DELIVERY_NAME', 'default');
define('TPL_CHAIN_NAME', 'default');
define('TPL_MEMBER_NAME', 'default');
define('ADMIN_MODULES_SYSTEM', 'modules/system');
define('ADMIN_MODULES_SHOP', 'modules/shop');
define('ADMIN_MODULES_CMS', 'modules/cms');
define('ADMIN_MODULES_CIECLE', 'modules/circle');
define('ADMIN_MODULES_MICEOSHOP', 'modules/microshop');
define('ADMIN_MODULES_MOBILE', 'modules/mobile');

define('SCM_MODULES_SUPPLIER', 'modules/supplier');
define('SCM_MODULES_CLIENT', 'modules/client');
define('SCM_MODULES_GZKJ', 'modules/gzkj');


/*
 * 商家入驻状态定义
 */
//新申请
define('STORE_JOIN_STATE_NEW', 10);
//完成付款
define('STORE_JOIN_STATE_PAY', 11);
//初审成功
define('STORE_JOIN_STATE_VERIFY_SUCCESS', 20);
//初审失败
define('STORE_JOIN_STATE_VERIFY_FAIL', 30);
//付款审核失败
define('STORE_JOIN_STATE_PAY_FAIL', 31);
//开店成功
define('STORE_JOIN_STATE_FINAL', 40);

//默认颜色规格id(前台显示图片的规格)
define('DEFAULT_SPEC_COLOR_ID', 1);


//会员登录注册发送短信间隔（单位为秒）
define('DEFAULT_CONNECT_SMS_TIME', 60);
//会员登录注册时每个手机号发送短信个数
define('DEFAULT_CONNECT_SMS_PHONE', 5);
//会员登录注册时每个IP发送短信个数
define('DEFAULT_CONNECT_SMS_IP', 20);

/**
 * 商品图片
 */
define('GOODS_IMAGES_WIDTH', '60,240,360,1280');
define('GOODS_IMAGES_HEIGHT', '60,240,360,12800');
define('GOODS_IMAGES_EXT', '_60,_240,_360,_1280');

/**
 *  订单状态
 */
//已取消
define('ORDER_STATE_CANCEL', 0);
//已产生但未支付
define('ORDER_STATE_NEW', 10);
//已支付
define('ORDER_STATE_PAY', 20);
//已发货
define('ORDER_STATE_SEND', 30);
//已收货，交易成功
define('ORDER_STATE_SUCCESS', 40);
//订单超过N小时未支付自动取消
define('ORDER_AUTO_CANCEL_TIME', 1);
//订单超过N天未收货自动收货
define('ORDER_AUTO_RECEIVE_DAY', 10);

//预订尾款支付期限(小时)
define('BOOK_AUTO_END_TIME', 72);

//门店支付订单支付提货期限(天)
define('CHAIN_ORDER_PAYPUT_DAY', 7);
/**
 * 订单删除状态
 */
//默认未删除
define('ORDER_DEL_STATE_DEFAULT', 0);
//已删除
define('ORDER_DEL_STATE_DELETE', 1);
//彻底删除
define('ORDER_DEL_STATE_DROP', 2);

/**
 * 文章显示位置状态,1默认网站前台,2买家,3卖家,4全站
 * @var unknown
 */
define('ARTICLE_POSIT_SHOP', 1);
define('ARTICLE_POSIT_BUYER', 2);
define('ARTICLE_POSIT_SELLER', 3);
define('ARTICLE_POSIT_ALL', 4);

//兑换码过期后可退款时间，15天
define('CODE_INVALID_REFUND', 15);

$_GET['act'] = is_string($_GET['act']) ? strtolower($_GET['act']) : (is_string($_POST['act']) ? strtolower($_POST['act']) : null);
$_GET['op'] = is_string($_GET['op']) ? strtolower($_GET['op']) : (is_string($_POST['op']) ? strtolower($_POST['op']) : null);

if (empty($_GET['act'])){
    require_once(BASE_CORE_PATH.'/framework/core/route.php');
    new Route($config);
}
//统一ACTION
$_GET['act'] = preg_match('/^[\w]+$/i',$_GET['act']) ? $_GET['act'] : 'index';
$_GET['op'] = preg_match('/^[\w]+$/i',$_GET['op']) ? $_GET['op'] : 'index';

//对GET POST接收内容进行过滤,$ignore内的下标不被过滤
$ignore = array('article_content','pgoods_body','doc_content','content','sn_content','g_body','store_description','p_content','groupbuy_intro','remind_content','note_content','adv_pic_url','adv_word_url','adv_slide_url','appcode','mail_content', 'message_content','member_gradedesc');
if (!class_exists('Security')) require(BASE_CORE_PATH.'/framework/libraries/security.php');
$_GET = !empty($_GET) ? Security::getAddslashesForInput($_GET,$ignore) : array();
$_POST = !empty($_POST) ? Security::getAddslashesForInput($_POST,$ignore) : array();
$_REQUEST = !empty($_REQUEST) ? Security::getAddslashesForInput($_REQUEST,$ignore) : array();
$_SERVER = !empty($_SERVER) ? Security::getAddSlashes($_SERVER) : array();
//启用ZIP压缩
if (\Shopnc\Core::getConfig('gip') && function_exists('ob_gzhandler') && $_GET['inajax'] != 1){
    ob_start('ob_gzhandler');
}else {
    ob_start();
}

require_once(BASE_CORE_PATH.'/framework/libraries/queue.php');
