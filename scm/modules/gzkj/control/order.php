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
        array('url' => 'act=order&op=show_payed', 'text' => '交易清单'),
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
        $pre=C('tablepre');
        $order = SCMModel('gzkj_client_order');

        $page_num = $_POST['rp'];
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;
        if ($_GET['pay_end'] == 1) {
            $sql = "SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status, order_pay,pay_flag from ".$pre."scm_client_order WHERE pay_flag=1  limit ".$index.",".$page_num;
            $sql_total_count= "SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status, order_pay,pay_flag from ".$pre."scm_client_order WHERE pay_flag=1  ";
            $data['total_num'] = count($order->execute_sql($sql_total_count));
            $orders = $order->execute_sql($sql);
        }else{

            if ($_GET['type'] == 1) {   //type=1表示退单
                $condition['order_status'] = array('in', '3,4');
                $condition['in_flag'] = 0;
                $group='clie_id';
                $sql = "SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status,pay_flag ,SUM(order_pay)as order_pay from ".$pre."scm_client_order WHERE  (TO_DAYS(NOW()) - TO_DAYS(pay_start_time)) >= 3 AND order_status IN (3, 4) AND pay_flag=0 GROUP BY clie_id limit ".$index.",".$page_num;
                $sql_total_count = "SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status,pay_flag,SUM(order_pay)as order_pay from ".$pre."scm_client_order WHERE  (TO_DAYS(NOW()) - TO_DAYS(pay_start_time)) >= 3 AND order_status IN (3, 4) AND pay_flag=0 GROUP BY clie_id ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            } else {
                if(25<date('j')){
                    $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(NOW(),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH) ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id limit ".$index.",".$page_num;
                    $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id";

                    $data['total_num'] = count($order->execute_sql($sql_total_count));
                    $orders = $order->execute_sql($sql);
                }
                if((1<=date('j')&&date('j'))<=7){
                    $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id limit ".$index.",".$page_num;
                    $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id";

                    $data['total_num'] = count($order->execute_sql($sql_total_count));
                    $orders = $order->execute_sql($sql);
                }
                if(7<date('j')&&date('j')<=16){
                    $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-09 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id limit ".$index.",".$page_num;
                    $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-09 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id";

                    $data['total_num'] = count($order->execute_sql($sql_total_count));
                    $orders = $order->execute_sql($sql);
                }
                if(16<date('j')&&date('j')<=25){
                    $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-18 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id limit ".$index.",".$page_num;
                    $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
SUM(order_pay)as order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-18 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
GROUP BY supp_id";

                    $data['total_num'] = count($order->execute_sql($sql_total_count));
                    $orders = $order->execute_sql($sql);
                }
            }
        }
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                $list = array();
                if($_GET['pay_end']==1){
                    $index++;
                    $list['number']=$index;
                    $list['order_no']=$info['order_no'];
                    $list['clie_id'] = $info['clie_id'];
                    $model = SCMModel('gzkj_client');
                    $list['clie_ch_name'] = $model->getfby_clie_id($info['clie_id'],'clie_ch_name');
                    $list['supp_id'] = $info['supp_id'];
                    $model = SCMModel('gzkj_supplier');
                    $list['supp_ch_name'] = $model->getfby_supp_id($info['supp_id'],'supp_ch_name');
                    if ($info['order_status'] == 3 || $info['order_status'] == 4) {
                        $list['cash_flow'] = '共铸商城->终端店';
                    } else {
                        $list['cash_flow'] = '共铸商城->供应商';
                    }

                    $list['order_pay'] = $info['order_pay'];
                    if ($info['pay_flag'] == 0) {
                        $list['pay_flag'] = '未结算';
                    } else {
                        $list['pay_flag'] = '已结算';
                    }
                }else{
                    if($_GET['type'] == 1){
                        $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku2('" . $info['clie_id'] . "')\">查看订单</a></li>";
                        $list['clie_id'] = $info['clie_id'];
                        $model = SCMModel('gzkj_client');
                        $list['clie_ch_name'] = $model->getfby_clie_id($info['clie_id'],'clie_ch_name');

                        if ($info['order_status'] == 3 || $info['order_status'] == 4) {
                            $list['cash_flow'] = '共铸商城->终端店';
                        } else {
                            $list['cash_flow'] = '共铸商城->供应商';
                        }

                        $list['order_pay'] = $info['order_pay'];
                        if ($info['pay_flag'] == 0) {
                            $list['pay_flag'] = '未结算';
                        } else {
                            $list['pay_flag'] = '已结算';
                        }
                        $list['time']=date('Y-m-d');
                    }else{
                        $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['supp_id'] . "')\">查看订单</a></li>";
                        $list['supp_id'] = $info['supp_id'];
                        $model = SCMModel('gzkj_supplier');
                        $list['supp_ch_name'] = $model->getfby_supp_id($info['supp_id'],'supp_ch_name');

                        if ($info['order_status'] == 3 || $info['order_status'] == 4) {
                            $list['cash_flow'] = '共铸商城->终端店';
                        } else {
                            $list['cash_flow'] = '共铸商城->供应商';
                        }

                        $list['order_pay'] = $info['order_pay'];
                        if ($info['pay_flag'] == 0) {
                            $list['pay_flag'] = '未结算';
                        } else {
                            $list['pay_flag'] = '已结算';
                        }
                        if(25<date('j')){
                            $list['time']=date('Y-m',strtotime(date('Y').'-'.(date('m')+1))).'-7';
                        }
                        if(1<=date('j')&&date('j')<=7){
                            $list['time']=date('Y-m').'-7';
                        }
                        if(7<date('j')&&date('j')<=16){
                            $list['time']=date('Y-m').'-16';
                        }
                        if(16<date('j')&&date('j')<=25){
                            $list['time']=date('Y-m').'-25';
                        }
                    }
                }
                $data['list'][$info['id']] = $list;
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

        Tpl::output('supp_id', $_GET['supp_id']);
        Tpl::output('clie_id', $_GET['clie_id']);
        Tpl::showpage('order.orders_list');
    }
    public function get_order_xmlOp()
    {
        $pre=C('tablepre');
        $order = SCMModel('gzkj_client_order');

        $page_num = $_POST['rp'];
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;

        $supp_id = $_GET['supp_id'];
        $clie_id = $_GET['clie_id'];
        if($supp_id){
            if(25<date('j')){
                $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(NOW(),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH) ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
 limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
";

                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if((1<=date('j')&&date('j'))<=7){
                $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
 limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH),'%Y-%m'),'-19 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND order_status IN (1, 2)
AND pay_flag = 0
";

                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(7<date('j')&&date('j')<=16){
                $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-09 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
 order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time >= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-01 00:00:00')
AND
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-09 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
";

                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(16<date('j')&&date('j')<=25){
                $sql = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
 order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-18 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_no,
	clie_id,
	clie_ch_name,
	supp_id,
	supp_ch_name,
	order_status,
	pay_flag,
 order_pay

FROM
	".$pre."scm_client_order
WHERE
pay_start_time <= CONCAT(DATE_FORMAT(NOW() ,'%Y-%m'),'-18 23:59:59')
AND order_status IN (1, 2)
AND pay_flag = 0
";

                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
        }
        if($clie_id){
            $sql = "SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status,order_pay from ".$pre."scm_client_order WHERE  (TO_DAYS(NOW()) - TO_DAYS(pay_start_time)) >= 3 AND order_status IN (3, 4) AND clie_id = '" . $clie_id . "' limit ".$index.",".$page_num;
            $sql_total_count="SELECT id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,order_status,order_pay from ".$pre."scm_client_order WHERE  (TO_DAYS(NOW()) - TO_DAYS(pay_start_time)) >= 3 AND order_status IN (3, 4) AND clie_id = '" . $clie_id . "'";
            $data['total_num'] = count($order->execute_sql($sql_total_count));
            $orders = $order->execute_sql($sql);
        }

        foreach ($orders as $k => $info) {

            $list = array();
            $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('" . $info['id'] . "')\">查看商品</a></li>";
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $model = SCMModel('gzkj_client');
            $list['clie_ch_name'] = $model->getfby_clie_id($info['clie_id'],'clie_ch_name');
            $list['supp_id'] = $info['supp_id'];
            $model = SCMModel('gzkj_supplier');
            $list['supp_ch_name'] = $model->getfby_supp_id($info['supp_id'],'supp_ch_name');
            $list['order_pay'] = $info['order_pay'];
            if($supp_id){
                if(25<date('j')){
                    $list['time']=date('Y-m',strtotime(date('Y').'-'.(date('m')+1))).'-7';
                }
                if(1<=date('j')&&date('j')<=7){
                    $list['time']=date('Y-m').'-7';
                }
                if(7<date('j')&&date('j')<=16){
                    $list['time']=date('Y-m').'-16';
                }
                if(16<date('j')&&date('j')<=25){
                    $list['time']=date('Y-m').'-25';
                }
            }
            if($clie_id){
                $list['time']=date('Y-m-d');
            }
            $data['list'][$info['id']] = $list;

        }
        echo Tpl::flexigridXML($data);
        exit();
    }


}
