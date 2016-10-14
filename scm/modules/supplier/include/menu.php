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
$_menu['supplier'] = array (
        'name' => '供应商',
        'child' => array (
                array(
                        'name' => "基础设置",
                        'child' => array(
                                'account' => "供应商基本信息",
                                'settlement' => "结算管理",
                                'supp_clie' => "合作终端店",
                        )
                ),
                array(
                        'name' => "商品管理",
                        'child' => array(
                                'goods' => "商品管理",
                        )
                ),
                array(
                       'name' => "订单管理" ,
                       'child' => array(
                               'client_order' => "未发货",
                               'delivering' => "已发货",
                               'delivered' => "历史订单" ,
                               'delivering_refund' => "未入库退货单",
//                               'delivered_refund' => "已入库退货单",
//                               'all_delivered_refund' => "全部已入库退货单"
                       )
                ),
                array(
                        'name' => "活动管理",
                        'child' => array(
                             'activity' => '活动管理',
                        )
                ),
                array(
                       'name' => "预警",
                       'child' => array(
                           'client' => "近效期/缺货/滞销",
                       )
                ),
            array(
                'name' => "统计报表",
                'child' => array(
                    'statistics_goods' => "商品分析",
                    'statistics_sale' => "运营分析",
                )
            ),
        ),
        'role' => '3'//供应商可见
);
