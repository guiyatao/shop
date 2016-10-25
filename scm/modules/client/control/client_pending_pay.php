<?php
/**
 * 交易管理
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
class client_pending_payControl extends SCMControl{
    /**
     * 每次导出订单数量
     * @var int
     */
    const EXPORT_SIZE = 1000;

    protected $user_info;

    public function __construct(){
        parent::__construct();
        Language::read('trade');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp(){
        Tpl::showpage('client_pending_pay.index');
    }

    public function get_xmlOp(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = $this->user_info['supp_clie_id'];
        $condition['order_status'] = array('eq', 0);
        $condition['pay_flag'] = array('eq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_id','order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $field = 'id, order_no, clie_id, supp_id, order_date, order_pay, prepare_flag';
        $order_list = $model_order->getOrderList($condition,$_POST['rp'],$field,$order);
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = $model_order->gettotalnum();
        foreach ($order_list as $order_id => $order_info) {
            $list = array();$operation_detail = '';
            $list['operation'] = "<a class='btn red' href='javascript:void(0);' onclick=\"fg_client_pending_pay('" . $order_info['order_no'] . "')\"><i class='fa fa-ban'></i>取消订单</a>";
            $list['operation'] .= "<a class=\"btn green\" href=\"index.php?act=cancel_order&op=show_order&order_id={$order_info['id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            $list['order_no'] = $order_info['order_no'];
            $list['clie_id'] = $order_info['clie_id'];
            $list['supp_id'] = $order_info['supp_id'];
            $list['order_date'] = $order_info['order_date'];
            $list['order_pay'] = $order_info['order_pay'];
//             $list['start_payment'] = "<a class='btn red alipay' href='javascript:void(0);' onclick=\"fg_client_pending_pay('" . $order_info['order_no'] . "')\">支付</a>";
            $list['start_payment'] = "<a class='btn red alipay' target=_top href='index.php?act=client_pending_pay&op=alipay&order_no=" . $order_info['order_no'] ."'>支付</a>";
			$data['list'][$order_info['id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * 取消商品订单
     */
    public function order_cancel_confirmOp(){
        $model_order = SCMModel('scm_client_order');
        if (chksubmit()) {
            $update = array();
            $update['order_status'] = 3;
            $now = date("Y-m-d H:i:s",time());
            $update['pay_start_time'] = $now;
            $where = array();
            $where['id'] = $_POST['id'];
            $state = $model_order->editOrder($update, $where);
            if ($state) {
                showDialog(L('nc_common_op_succ'), '', 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
            }

        }
        $common_info = $model_order->getOrderGoodsInfoByID($_GET['order_no']);
        $supp_list = SCMModel('scm_supplier')->getList(array('supp_id'=>$common_info['supp_id']));
        Tpl::output('common_info', $common_info);
        Tpl::output('supplier_info', $supp_list[0]);
        Tpl::showpage('order_cancel_confirm.close_remark', 'null_layout');
    }

    public function alipayOp() {
        $model_order = SCMModel('scm_client_order');
        $common_info = $model_order->getOrderGoodsInfoByID($_GET['order_no']);
        $supp_list = SCMModel('scm_supplier')->getList(array('supp_id'=>$common_info['supp_id']));

        $order_info = array();
        
        $logic_payment = Logic('payment');
        
        $result = $logic_payment->getPaymentInfo('alipay');
        
        if(!$result['state']) {
            showMessage($result['msg'], "/index.php", 'html', 'error');
        }
        $payment_info = $result['data'];
        
        $order_info['order_type'] = "scm_client_order";
        $order_info['subject'] ="实物订单".$common_info['pay_sn'];
        $order_info['pay_sn'] = $common_info['pay_sn'];
//         $order_info['api_pay_amount'] = "0.01";
        $order_info['api_pay_amount'] = $common_info["order_pay"];
        $payment_api = new alipay($payment_info, $order_info);

        @header("Location: ".$payment_api->get_payurl());
        exit;
    }

    /**
     * 平台订单状态操作
     *
     */
    public function change_stateOp() {
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0){
            showMessage(L('miss_order_number'),$_POST['ref_url'],'html','error');
        }
        $model_order = Model('order');

        //获取订单详细
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getOrderInfo($condition);

        //取得其它订单类型的信息
        $model_order->getOrderExtendInfo($order_info);

        if ($_GET['state_type'] == 'cancel') {
            $result = $this->_order_cancel($order_info);
        } elseif ($_GET['state_type'] == 'receive_pay') {
            $result = $this->_order_receive_pay($order_info,$_POST);
        }
        if (!$result['state']) {
            showMessage($result['msg'],$_POST['ref_url'],'html','error');
        } else {
            showMessage($result['msg'],$_POST['ref_url']);
        }
    }

    /**
     * 处理搜索条件
     */
    private function _get_condition(& $condition) {
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('order_no','goods_barcode','goods_nm','supp_id'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
    }

    /**
     * 导出
     *
     */
    public function export_step1Op(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = $this->user_info['supp_clie_id'];
        $condition['order_status'] = array('eq', 0);
        $condition['pay_flag'] = array('eq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_id','order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }else{
            $order = "order_date desc";
        }
        $field = 'id, order_no, clie_id, supp_id, order_date, order_pay, prepare_flag';
        if (preg_match('/^[\d,]+$/', $_GET['order_id'])) {
            $_GET['order_id'] = explode(',',trim($_GET['order_id'],','));
            $condition['id'] = array('in',$_GET['order_id']);
        }
        if (!is_numeric($_GET['curpage'])){
            $count = $model_order->getOrderCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=client_pending_pay&op=index');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_order->getOrderList($condition,'',$field,$order,self::EXPORT_SIZE);
                $this->createExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_order->getOrderList($condition,'','*',$order,"{$limit1},{$limit2}");
            $this->createExcel($data);
        }
    }

    /**
     * 生成excel
     *
     * @param array $data
     */
    private function createExcel($data = array()){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'付款金额');
        //data
        foreach ((array)$data as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['order_no']);
            $tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['supp_id']);
            $tmp[] = array('data'=>$order_info['order_date']);
            $tmp[] = array('data'=>$order_info['order_pay']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

}