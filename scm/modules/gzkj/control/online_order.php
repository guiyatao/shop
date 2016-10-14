<?php
/**
 * 商城订单结算
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
class online_orderControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }
    private $links = array(
        array('url' => 'act=online_order&op=index', 'text' => '商城订单结算'),
        array('url' => 'act=online_order&op=show_payed', 'text' => '交易清单'),
    );


    public function indexOp()
    {
        return $this->showOp();
    }
    public function showOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('online_order.index');
    }

    /**
     * 显示
     */
    public function show_payedOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_payed'));
        Tpl::showpage('online_order_end.index');
    }


    public function get_xmlOp()
    {
        $pre=C('tablepre');
        $order = SCMModel('gzkj_online_order');
        $page_num = $_POST['rp'];
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;
        if($_GET['pay_end']==1){
            $sql = "SELECT id,order_sn,clie_id,order_state,pay_flag,SUM(order_amount)as order_amount from ".$pre."scm_online_order WHERE order_state=40 AND pay_flag=1 GROUP BY clie_id limit ".$index.",".$page_num;
            $sql_total_count = "SELECT id,order_sn,clie_id,order_state,pay_flag,SUM(order_amount)as order_amount from ".$pre."scm_online_order  WHERE order_state=40 pay_flag=1 GROUP BY clie_id ";
            $data['total_num'] = count($order->execute_sql($sql_total_count));
            $orders = $order->execute_sql($sql);
        }else{

            if(25<date('j')){

                $sql = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			NOW(),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m'),
	'-01 00:00:00'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id limit ".$index.",".$page_num;
                $sql_total_count ="SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			NOW(),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m'),
	'-01 00:00:00'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(1<=date('j')&&date('j')<=7){

                $sql = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			DATE_SUB(NOW(), INTERVAL 1 MONTH),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-01 00:00:00'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id limit ".$index.",".$page_num;
                $sql_total_count ="SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			DATE_SUB(NOW(), INTERVAL 1 MONTH),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-01 00:00:00'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(7<date('j')&&date('j')<=16){
                $sql = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-09 23:59:59'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	 FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-09 23:59:59'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(16<date('j')&&date('j')<=25){
                $sql = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(NOW(), '%Y-%m'),
		'-10 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-18 23:59:59'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	SUM(order_amount) AS order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(NOW(), '%Y-%m'),
		'-10 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-18 23:59:59'
)
AND order_state = 40
AND pay_flag = 0
GROUP BY
	clie_id ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }

        }
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                $list = array();
                if($_GET['pay_end']==1){
                    $index++;
                    $list['number'] = $index;
                    $list['clie_id'] = $info['clie_id'];
                    $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'], 'clie_ch_name');
                    $list['order_sn'] = $info['order_sn'];
                    $list['order_amount'] = $info['order_amount'];
                    $list['cash_flow'] = '共铸商城->终端店';
                    $list['order_amount'] = $info['order_amount'];
                    if($info['pay_flag']==1){
                        $list['pay_flag'] = '已结算';
                    }else{
                        $list['pay_flag'] = '未结算';
                    }
                }else{
                    $list['operation'] .= "<a  class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['clie_id'] . "')\">查看订单</a></li>";
                    $list['clie_id'] = $info['clie_id'];
                    $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'], 'clie_ch_name');
                    $list['order_amount'] = $info['order_amount'];
                    $list['cash_flow'] = '共铸商城->终端店';
                    $list['order_amount'] = $info['order_amount'];
                    if($info['pay_flag']==1){
                        $list['pay_flag'] = '已结算';
                    }else{
                        $list['pay_flag'] = '未结算';
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
                $data['list'][$info['id']] = $list;
            }
        }
        echo Tpl::flexigridXML($data);
        exit();

    }

    public  function show_goodsOp(){

        $goods = SCMModel('gzkj_online_order_goods');
        $condition=array();
        $condition['order_id']=$_GET['order_id'];
        $condition['clie_id']=$_GET['clie_id'];
        $list = $goods->where($condition)->select();

        Tpl::output('goods_list', $list);
        Tpl::showpage('online_order.goods_list', 'null_layout');
    }




    public function show_ordersOp()
    {
        Tpl::output('clie_id', $_GET['clie_id']);
        Tpl::showpage('online_order.orders_list');
    }
    public function get_order_xmlOp()
    {
        $pre=C('tablepre');
        $order = SCMModel('gzkj_online_order');

        $page_num = $_POST['rp'];
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;

        $clie_id = $_GET['clie_id'];

        if($clie_id){
            if(25<date('j')){
                $sql = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			NOW(),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m'),
	'-01 00:00:00'
)
AND clie_id = '".$clie_id."'
AND order_state = 40 limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			DATE_SUB(NOW(), INTERVAL 1 MONTH),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-01 00:00:00'
)
AND clie_id = '".$clie_id."'
AND order_state = 40  ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(1<=date('j')&&date('j')<=7){
                $sql = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			DATE_SUB(NOW(), INTERVAL 1 MONTH),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-01 00:00:00'
)
AND clie_id = '".$clie_id."'
AND order_state = 40 limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(
			DATE_SUB(NOW(), INTERVAL 1 MONTH),
			'%Y-%m'
		),
		'-19 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-01 00:00:00'
)
AND clie_id = '".$clie_id."'
AND order_state = 40  ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(7<=date('j')&&date('j')<=16){
                $sql = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	 FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-09 23:59:59'
)
AND clie_id = '".$clie_id."'
AND order_state = 40  limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-09 23:59:59'
)
AND clie_id = '".$clie_id."'
AND order_state = 40 ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }
            if(16<=date('j')&&date('j')<=25){
                $sql = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(NOW(), '%Y-%m'),
		'-10 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-18 23:59:59'
)
AND clie_id = '".$clie_id."'
AND order_state = 40  limit ".$index.",".$page_num;
                $sql_total_count = "SELECT
	id,
	order_id,
	order_sn,
	clie_id,
	order_state,
	pay_flag,
	order_amount
FROM
	".$pre."scm_online_order
WHERE
	FROM_UNIXTIME(payment_time) >= CONCAT(
		DATE_FORMAT(NOW(), '%Y-%m'),
		'-10 00:00:00'
	)
AND FROM_UNIXTIME(payment_time) <= CONCAT(
	DATE_FORMAT(NOW(), '%Y-%m'),
	'-18 23:59:59'
)
AND clie_id = '".$clie_id."'
AND order_state = 40  ";
                $data['total_num'] = count($order->execute_sql($sql_total_count));
                $orders = $order->execute_sql($sql);
            }

        }
        if(!empty($orders)){
            foreach ($orders as $k => $info) {

                $list = array();
                $list['operation'] .= "<li><a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('".$info['order_id']."','".$info['clie_id']."')\">查看商品</a>";
                $list['clie_id'] = $info['clie_id'];
                $list['order_sn'] = $info['order_sn'];
                $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
                $list['order_amount'] = $info['order_amount'];
                $list['cash_flow'] = '共铸商城->终端店';
                $list['order_amount'] = $info['order_amount'];

                $list['pay_flag'] = '未结算';
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
                $data['list'][$info['id']] = $list;
            }
        }

        echo Tpl::flexigridXML($data);
        exit();
    }
}
