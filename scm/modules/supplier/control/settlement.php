<?php
/**
 * 结算管理
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class settlementControl extends SCMControl
{
    const EXPORT_SIZE = 1000;
    protected $supp_info;

    public function __construct()
    {
        parent::__construct();
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
    }

    private $links = array(
        array('url' => 'act=settlement&op=index', 'text' => '已结算订单'),
        array('url' => 'act=settlement&op=unsettlement', 'text' => '未结算订单'),
    );

    /**
     * 已结算订单界面
     */
    public function indexOp(){
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('settlement.index');
    }

    /**
     * 未结算订单界面
     */
    public function unsettlementOp(){
        Tpl::output('top_link', $this->sublink($this->links, 'unsettlement'));
        Tpl::showpage('settlement.unsettlement');
    }

    /**
     * 已结算订单数据
     */
    public function get_xmlOp(){
        $model_supplier_client = SCMModel('supplier_client');
        $supplier = $this->supp_info;
        $condition['supp_id'] = $supplier['supp_id'];
        //已经结算的订单
        $condition['pay_flag'] = array('neq',0);

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,order_status,out_flag,prepare_flag,pay_flag,refund_flag ';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','order_pay','order_date','order_status');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $order_list = $model_supplier_client->getOrderList($condition, $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_supplier_client->shownowpage();
        $temp_list = $model_supplier_client->gettotalnumon($condition,'order_no','scm_client_order,scm_client','order_no','scm_client_order.clie_id = scm_client.clie_id');
        $data['total_num'] = count($temp_list);
        $index = ($data['now_page'] - 1) * $page_num;
        foreach($order_list as $k => $value){
            $param = array();
            $index++;
            $param['number'] = $index;
            $param['order_no'] = $value['order_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = $value['clie_ch_name'];
            $param['order_pay'] = $value['order_pay'];
            $param['total_amount'] = $value['total_amount'];
            $param['order_date'] = $value['order_date'];
            if($value['order_status'] == 0)
                $param['order_status'] = "未完成";
            else if($value['order_status'] == 1)
                $param['order_status'] = "已完成";
            else if($value['order_status'] == 3)
                $param['order_status'] = "订单已取消";
            else if($value['order_status'] == 4)
                $param['order_status'] = "退货成功";
            else if($value['order_status'] == 5)
                $param['order_status'] = "退货失败";
            if($value['refund_flag'] == 0)
                $param['refund_flag'] = "未申请退货";
            elseif($value['refund_flag'] == 1)
                $param['refund_flag'] = "已申请退货";
            $param['pay_flag'] = "已结算";
            if($value['pay_flag'] == 1 ){
                $param['cash_flow'] = '终端店->共铸商城';
            }elseif($value['pay_flag'] == 2){
                $param['cash_flow'] = '共铸商城->供应商';
            }elseif($value['pay_flag'] == 3){
                $param['cash_flow'] = '共铸商城->终端店';
            }
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 未结算订单数据
     */
    public function get_unsettlement_xmlOp(){
        $model_supplier_client = SCMModel('supplier_client');
        $supplier = $this->supp_info;
        $condition['supp_id'] = $supplier['supp_id'];
        //未结算的订单
        $condition['pay_flag'] = 0;
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,order_status,out_flag,prepare_flag,pay_flag ';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','order_pay','order_date','order_status');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $order_list = $model_supplier_client->getOrderList($condition, $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_supplier_client->shownowpage();
        $temp_list = $model_supplier_client->gettotalnumon($condition,'order_no','scm_client_order,scm_client','order_no','scm_client_order.clie_id = scm_client.clie_id');
        $data['total_num'] = count($temp_list);
        $index = ($data['now_page'] - 1) * $page_num;
        foreach($order_list as $k => $value){
            $param = array();
            $index++;
            $param['number'] = $index;
            $param['order_no'] = $value['order_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = $value['clie_ch_name'];
            $param['order_pay'] = $value['order_pay'];
            $param['total_amount'] = $value['total_amount'];
            $param['order_date'] = $value['order_date'];
            if($value['order_status'] == 0)
                $param['order_status'] = "未完成";
            else if($value['order_status'] == 1)
                $param['order_status'] = "已完成";
            else if($value['order_status'] == 2)
                $param['order_status'] = "半单";
            else if($value['order_status'] == 3)
                $param['order_status'] = "订单已取消";
            else if($value['order_status'] == 4)
                $param['order_status'] = "退货成功";
            else if($value['order_status'] == 5)
                $param['order_status'] = "退货失败";
            $param['pay_flag'] = "未结算";
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }
}