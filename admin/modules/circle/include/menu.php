<?php
/**
 * 菜单
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
$_menu['circle'] = array (
        'name' => $lang['nc_circle'],
        'child' => array (
                array (
                        'name' => $lang['nc_config'],
                        'child' => array(
                                'circle_setting' => $lang['nc_circle_setting'],
                                'circle_adv' => '首页幻灯'
                        )
                ),
                array (
                        'name' => '成员',
                        'child' => array(
                                'circle_member' => $lang['nc_circle_membermanage'],
                                'circle_memberlevel' => '成员头衔'
                        )
                ),
                array (
                        'name' => '圈子',
                        'child' => array(
                                'circle_manage' => $lang['nc_circle_manage'],
                                'circle_class' => $lang['nc_circle_classmanage'],
                                'circle_theme' => $lang['nc_circle_thememanage'],
                                'circle_inform' => '举报管理'
                        )
                )
        ) 
);