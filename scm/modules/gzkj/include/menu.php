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
$_menu['gzkj'] = array(
    'name' => '共铸平台',
    'child' => array(
        array(
            'name' => "账号",
            'child' => array(
                'user' => "账号分配",
            )
        ),
        array(
            'name' => "结算管理",
            'child' => array(
                'order' => "批发订单结算",
                'online_order'=>"商城订单结算"
            )
        ),
        array(
            'name' => "商品管理",
            'child' => array(
                'supp_stock' => "商品管理",
            )
        ),
        array(
            'name' => "活动管理",
            'child' => array(
                'activity' => "活动管理",
            )
        ),
        array(
            'name' => "预警",
            'child' => array(
                'stock_warn' => "库存预警",
                'date_warn' => "有效期预警",
                'unsale_warn' => "滞销预警",
            )
        ),
        array(
            'name' => "订单管理",
            'child' => array(
                'order_manager' => "批发订单管理",
                'online_order_manager' => "商城订单管理",
//                'delivered' => "订单管理",
            )
        ),
    ),
    'role'=>3,
);
