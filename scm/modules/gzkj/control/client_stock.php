<?php
/**
 * 订单结算
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
class client_stockControl extends SCMControl
{
    public function __construct()
    {
        parent::__construct();
    }

    private $links = array(
        array('url'=>'act=order&op=index','text'=>'订单结算'),
        array('url'=>'act=order&op=show_flow','text'=>'退单结算'),
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
        Tpl::output('top_link',$this->sublink($this->links,'index'));
        Tpl::showpage('order.index');
    }
    /**
     * 显示
     */
    public function show_flowOp()
    {
        Tpl::output('top_link',$this->sublink($this->links,'show_flow'));
        Tpl::showpage('order_flow.index');
    }

    public function get_xmlOp()
    {
        $order = SCMModel('gzkj_client_order');
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
                case 'supp_id':
                    $condition['supp_id'] = $q;
                    break;
                case'supp_ch_name':
                    $condition['supp_ch_name'] = $q;
                    break;
            }
        }
        $condition['order_status']=array('in','1,2');
//        $model->field('store_id,count(*) as count')->group('store_id')->select();
        $orders = $order->field('id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,order_status,sum(order_pay) as total')->where($condition)->group('order_no')->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {
            $list = array();
//            $list['operation'].= '<a class="btn red" href="javascript:fg_operation_del('.$info['id'].');"><i class="fa fa-trash-o"></i>删除</a>';
//            $list['operation'].= '<a class="btn blue" href="index.php?act=client&op=client_edit&id='.$info['id'].'"><i class="fa fa-pencil-square-o"></i>'.L('nc_edit').'</a>';
//            $list['id'] = $info['id'];
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $list['clie_ch_name'] = $info['clie_ch_name'];
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['goods_barcode'] = $info['goods_barcode'];
            $list['goods_nm'] = $info['goods_nm'];
            $list['cash_flow'] = '共铸商城->供应商';
            $list['order_pay'] = $info['total'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();

    }
    public function get_xml1Op()
    {
        $order = SCMModel('gzkj_client_order');
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
                case 'supp_id':
                    $condition['supp_id'] = $q;
                    break;
                case'supp_ch_name':
                    $condition['supp_ch_name'] = $q;
                    break;
            }
        }
        $condition['order_status']=4;
        $condition['in_flag']=0;

//        $model->field('store_id,count(*) as count')->group('store_id')->select();
        $orders = $order->field('id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,order_status,sum(order_pay) as total')->where($condition)->group('order_no')->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {
            $list = array();
//            $list['operation'].= '<a class="btn red" href="javascript:fg_operation_del('.$info['id'].');"><i class="fa fa-trash-o"></i>删除</a>';
//            $list['operation'].= '<a class="btn blue" href="index.php?act=client&op=client_edit&id='.$info['id'].'"><i class="fa fa-pencil-square-o"></i>'.L('nc_edit').'</a>';
//            $list['id'] = $info['id'];
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $list['clie_ch_name'] = $info['clie_ch_name'];
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['goods_barcode'] = $info['goods_barcode'];
            $list['goods_nm'] = $info['goods_nm'];
            $list['cash_flow'] = '终端店->供应商';
            $list['order_pay'] = $info['total'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();

    }


}
