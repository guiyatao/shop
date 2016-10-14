<?php
/**
 * 支付宝服务器异步通知页面
 *
 * 
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
$_GET['act']	= 'notify_refund';
$_GET['op']		= 'alipay';
require_once(dirname(__FILE__).'/../../../index.php');
?>