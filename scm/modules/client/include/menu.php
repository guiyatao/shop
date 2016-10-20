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
$_menu['client'] = array (
        'name' => '终端店',
        'child' => array (
                array(
                        'name' => "订单管理",
                        'child' => array(
                                'accept_order' => $lang['scm_accept_order'],
                                'online_order' => $lang['scm_online_order'],
                        )
                ),
                array(
                        'name' => "批发管理",
                        'child' => array(
                                'client_purchase' => $lang['scm_client_stock'],
                                'client_pending_pay' => $lang['client_pending_pay'],
                                'cancel_order' => $lang['scm_cancel_order'],
                                'client_storage' => $lang['scm_client_storage'],
                                'done_order' => $lang['scm_done_order'],
                                // 'refund_in' => $lang['scm_refund_in'],
                                // 'refund_not_in' => $lang['scm_refund_not_in'],
                        )
                ),
//                array(
//                        'name' => "结算管理",
//                        'child' => array(
//                                'all_online_order' => $lang['scm_all_online_order'],
//                                'client_order' => $lang['scm_client_order'],
//                        )
//                ),
                array(
                    'name' => "结算管理",
                    'child' => array(
                        'order_settlement' => "批发订单管理",
                        'online_order_settlement' => "商城订单管理",
                    )
                ),
                array(
                        'name' => "商品管理",
                        'child' => array(
                                'good_manage' => $lang['scm_good_manage'],
                        )
                ),
                array(
                        'name' => "供应商管理",
                        'child' => array(
                                'supp_manage' => $lang['scm_supp_manage'],
                        )
                ),
                array(
                        'name' => "统计报表",
                        'child' => array(
                                'goods_analyse' => $lang['goods_analyse'],
                                'sale_analyse' => $lang['sale_analyse'],
                                'goods_flow' => $lang['goods_flow'],
                                'purchase_analyse' => $lang['purchase_analyse'],
                        )
                ),
                array(
                        'name' => "预警",
                        'child' => array(
                                'validity_warn' => $lang['scm_validity_warn'],
                                'stockout_warn' => $lang['scm_stockout_warn'],
                                'unsalable_warn' => $lang['scm_unsalable_warn'],
                        )
                ),

                array(
                        'name' => "基础设置",
                        'child' => array(
                                'client_account' => $lang['scm_account'],
                                // 'stock_warn' => $lang['scm_stock_warn'],
                                // 'warning_set' => $lang['scm_warning_set'],
                        )
                ),

        ),
        'role' => '3'//供应商可见 
);
