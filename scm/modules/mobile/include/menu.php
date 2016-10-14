<?php
/**
 * 菜单
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
$_menu['mobile'] = array (
        'name'=>$lang['nc_mobile'],
        'child'=>array(
                array(
                        'name'=>'设置',
                        'child' => array(
                                'mb_setting' => '手机端设置',
                                'mb_special' => '模板设置',
                                'mb_category' => $lang['nc_mobile_catepic'],
                                'mb_app' => '应用安装',
                                'mb_feedback' => $lang['nc_mobile_feedback'],
                                'mb_payment' => '手机支付',
                                'mb_wx' => '微信二维码',
                                'mb_connect' => '第三方登录'
                        )
                )
        )
);