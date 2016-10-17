<?php
/**
 * 批发订单结算
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

class orderControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }
    private $links = array(
        array('url' => 'act=order&op=index', 'text' => '供应商结算'),
        array('url' => 'act=order&op=show_flow', 'text' => '终端店结算'),
    );

    public function indexOp()
    {
        return $this->showOp();
    }

    /**
     * 显示
     */
    public function showOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('order.index');
    }

    /**
     * 显示
     */
    public function show_flowOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_flow'));
        Tpl::showpage('order_flow.index');
    }

    public function show_payedOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_payed'));
        Tpl::showpage('order_payend.index');
    }

    public function get_xmlOp()
    {
        $order = SCMModel('gzkj_settlement');

        if($_GET['type']==1){
            $where=array();
            $where['scm_settlement.clie_id']= array('neq','');
            $field="DISTINCT scm_settlement.clie_id,scm_settlement.settlement_id ,scm_settlement.amount,scm_settlement.flag,scm_settlement.photo,scm_client.clie_ch_name,scm_settlement.settlement_date";
        }else{
            $where=array();
            $where['scm_settlement.supp_id']= array('neq','');
            $field="DISTINCT scm_settlement.supp_id,scm_settlement.settlement_id ,scm_settlement.amount,scm_settlement.flag,scm_settlement.photo,scm_supplier.supp_ch_name,scm_settlement.settlement_date";
        }

        $orders=$order->getSettlementInfo($where,$field,$_POST['rp']);
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                        $list = array();
                        $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['settlement_id'] . "')\">查看订单</a></li>";
                        if($_GET['type']==1){
                            $list['clie_id'] = $info['clie_id'];
                            $list['clie_ch_name'] = $info['clie_ch_name'];
                            $list['cash_flow'] = '共铸商城->终端店';
                        }else{
                            $list['supp_id'] = $info['supp_id'];
                            $list['supp_ch_name'] = $info['supp_ch_name'];
                            $list['cash_flow'] = '共铸商城->供应商';
                        }

                        $list['order_pay'] = $info['amount'];
                        if($info['flag']==0){
                            $list['pay_flag'] = '未结算';
                        }elseif($info['flag']==2||$info['flag']==3){
                            $list['pay_flag'] = '已结算';
                        }
                        $list['time']=$info['settlement_date'];
                        $list['photo']=$info['photo'];
                $data['list'][$info['settlement_id']] = $list;
                }
            }
        echo Tpl::flexigridXML($data);
        exit();

    }
    public function show_goodsOp()
    {

        $order = SCMModel('gzkj_client_order');
        $condition = array();
        $condition['scm_client_order.id'] = $_GET['id'];
        $list = $order->getGoodJoinList($condition);
        Tpl::output('goods_list', $list);
        Tpl::showpage('order.goods_list', 'null_layout');
    }

    public function show_ordersOp()
    {

        Tpl::output('settlement_id', $_GET['settlement_id']);
        Tpl::output('clie_id', $_GET['clie_id']);
        Tpl::showpage('order.orders_list');
    }
    public function get_order_xmlOp()
    {
        $order = SCMModel('gzkj_client_order');
        $orders=$order->where(array('settlement_id'=>$_GET['settlement_id']))->page($_POST['rp'])->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {
            $list = array();
            $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('" . $info['id'] . "')\">查看商品</a></li>";
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $list['clie_ch_name'] =  SCMModel('gzkj_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = SCMModel('gzkj_supplier')->getfby_supp_id($info['supp_id'],'supp_ch_name');
            $list['order_pay'] = $info['order_pay'];
            $list['time']=SCMModel('gzkj_settlement')->getfby_settlement_id($_GET['settlement_id'],'settlement_date');
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }


}
