<?php
/**
 * 商城订单管理
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class online_order_managerControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }
    public function indexOp()
    {
        return $this->showOp();
    }
    public function showOp()
    {
        Tpl::showpage('online_order_manager.index');
    }


    public function get_xmlOp()
    {
        $order = Model('orders');
        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['order_state']))) {
                $condition['order_state'] = (int) $q;
            }
        } else{
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'clie_id':
                        $condition['clie_id'] = $q;
                        break;
                    case 'order_no':
                        $condition['order_no'] = $q;
                        break;
                    case'clie_ch_name':
                        $condition['clie_ch_name'] = $q;
                        break;
                }
            }
        }
        $orders = $order->where($condition)->page($_POST['rp'])->order('add_time desc')->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                $list = array();
                if($info['order_state']==10){
                    $list['operation'] .= "<a  class=\"btn \" href='javascript:void(0)' ><i class=\"fa fa-ban\"></i>查看订单</a></li>";
                }else{
                    $list['operation'] .= "<a  class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['order_id'] . "')\">查看订单</a></li>";
                }
                    $list['order_sn'] = $info['order_sn'];
                    $list['order_amount'] = $info['order_amount'];
                    $goods = SCMModel('gzkj_online_order');
                    $where['order_sn']=$info['order_sn'];
                    $list['order_num'] = $goods->where($where)->count();//分单个数
                    $list['buyer_id'] = $info['buyer_id'];
                $list['buyer_name'] = $info['buyer_name'];
                $list['buyer_email'] = $info['buyer_email'];
                $list['buyer_phone'] = $info['buyer_phone'];
                $list['add_time'] = date('Y-m-d H:i:s',$info['add_time']);
                if($info['order_state']==0){
                    $list['order_state'] = '已取消';
                }elseif($info['order_state']==10){
                    $list['order_state'] = '未付款';
                }elseif($info['order_state']==20){
                    $list['order_state'] = '已付款';
                }elseif($info['order_state']==30){
                    $list['order_state'] = '已发货';
                }elseif($info['order_state']==40){
                    $list['order_state'] =  '已收货';
                }
//                if($info['payment_code']=='offline'){
//                    $list['payment_code'] = '货到付款';
//                }else{
//                    $list['payment_code'] = '在线支付';
//                }

                    $data['list'][$info['order_id']] = $list;
                }
            }

        echo Tpl::flexigridXML($data);
        exit();

    }

    public  function show_goodsOp(){

        $goods = SCMModel('gzkj_online_order_goods');
        $condition['order_id']=$_GET['order_id'];
        $online_goods = $goods->where($condition)->select();
        Tpl::output('goods_list', $online_goods);
        Tpl::showpage('online_order_manager.goods_list', 'null_layout');


    }




    public function show_ordersOp()
    {
        Tpl::output('order_id', $_GET['order_id']);
        Tpl::showpage('online_order_manager.list');
    }
    public function get_order_xmlOp()
    {

        $order = SCMModel('gzkj_online_order');
        $condition['order_id']=$_GET['order_id'];
        $orders = $order->where($condition)->page($_POST['rp'])->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {

            $list = array();
            $list['operation'] = "<a class=\"btn green\" href=\"index.php?act=online_order_detail&op=show_order&order_id={$info['order_id']}&clie_id={$info['clie_id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
//            $list['operation'] .= "<li><a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('" . $info['order_id'] . "','" . $info['order_sn'] . "')\">查看商品</a>";
            $list['order_sn'] = $info['order_sn'];
            $list['clie_id'] = $info['clie_id'];
            $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
            $list['buyer_name'] = $info['buyer_name'];
            $list['buyer_phone'] = $info['buyer_phone'];
            $list['buyer_address'] = $info['buyer_address'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }
}
