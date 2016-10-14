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
class client_orderControl extends SCMControl{
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

    private $links = array(
        array('url' => 'act=client_order&op=index', 'text' => '已结算批发订单'),
        array('url' => 'act=client_order&op=unsettlement', 'text' => '未结算批发订单'),
    );


    public function indexOp(){
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('client_order.index');
    }

    /**
     * 未结算订单界面
     */
    public function unsettlementOp(){
        Tpl::output('top_link', $this->sublink($this->links, 'unsettlement'));
        Tpl::showpage('client_order.unsettlement');
    }

    public function get_xmlOp(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        //已结算订单
        $condition['pay_flag'] = array('neq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $order_list = $model_order->getOrderList($condition,$_POST['rp'],'*',$order);
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = $model_order->gettotalnum();
        foreach ($order_list as $order_id => $order_info) {
            $list = array();
//            $list['operation'] = "<a class=\"btn green\" href=\"index.php?act=client_order&op=show_order&order_id={$order_info['id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            $list['clie_id'] = $order_info['clie_id'];
            $list['order_no'] = $order_info['order_no'];
            $list['supp_id'] = $order_info['supp_id'];
            $list['order_pay'] = ncPriceFormat($order_info['order_pay']);
            $list['order_date'] = $order_info['order_date'];
            $list['order_status'] = Order::getClientOrderStatusByID($order_info['order_status']);
            $list['pay_flag'] = "已结算";
            if($order_info['pay_flag'] == 1 ){
                $list['cash_flow'] = '终端店->共铸商城';
            }elseif($order_info['pay_flag'] == 2){
                $list['cash_flow'] = '共铸商城->供应商';
            }elseif($order_info['pay_flag'] == 3){
                $list['cash_flow'] = '共铸商城->终端店';
            }
            $data['list'][$order_info['id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    public function get_unsettlement_xmlOp(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        $condition['pay_flag'] = array('eq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $order_list = $model_order->getOrderList($condition,$_POST['rp'],'*',$order);
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = $model_order->gettotalnum();
        foreach ($order_list as $order_id => $order_info) {
            $list = array();
//            $list['operation'] = "<a class=\"btn green\" href=\"index.php?act=client_order&op=show_order&order_id={$order_info['id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            $list['clie_id'] = $order_info['clie_id'];
            $list['order_no'] = $order_info['order_no'];
            $list['supp_id'] = $order_info['supp_id'];
            $list['order_pay'] = ncPriceFormat($order_info['order_pay']);
            $list['order_date'] = $order_info['order_date'];
            $list['order_status'] = Order::getClientOrderStatusByID($order_info['order_status']);
            $list['pay_flag'] = "未结算";

            $data['list'][$order_info['id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
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
     * 系统取消订单
     */
    private function _order_cancel($order_info) {
        $order_id = $order_info['order_id'];
        $model_order = Model('order');
        $logic_order = Logic('order');
        $if_allow = $model_order->getOrderOperateState('system_cancel',$order_info);
        if (!$if_allow) {
            return callback(false,'无权操作');
        }
        if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
            $_hour = ceil(($order_info['api_pay_time']+86400-TIMESTAMP)/3600);
            exit(json_encode(array('state'=>false,'msg'=>'该订单曾尝试使用第三方支付平台支付，须在'.$_hour.'小时以后才可取消')));
        }
        if ($order_info['order_type'] == 2) {
            //预定订单
            $result = Logic('order_book')->changeOrderStateCancel($order_info, 'admin', $this->admin_info['name']);
        } else {
            $cancel_condition = array();
            if ($order_info['payment_code'] != 'offline') {
                $cancel_condition['order_state'] = ORDER_STATE_NEW;
            }
            $result =  $logic_order->changeOrderStateCancel($order_info,'admin', $this->admin_info['name'],'',true,$c);
        }
        if ($result['state']) {
            $this->log(L('order_log_cancel').','.L('order_number').':'.$order_info['order_sn'],1);
        }
        if ($result['state']) {
            exit(json_encode(array('state'=>true,'msg'=>'取消成功')));
        } else {
            exit(json_encode(array('state'=>false,'msg'=>'取消失败')));
        }
    }

    /**
     * 系统收到货款
     * @throws Exception
     */
    private function _order_receive_pay($order_info, $post) {
        $order_id = $order_info['order_id'];
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_info['if_system_receive_pay'] = $model_order->getOrderOperateState('system_receive_pay',$order_info);

        if (!$order_info['if_system_receive_pay']) {
            return callback(false,'无权操作');
        }

        if (!chksubmit()) {
            Tpl::output('order_info',$order_info);
            //显示支付接口列表
            $payment_list = Model('payment')->getPaymentOpenList();
            //去掉预存款和货到付款
            foreach ($payment_list as $key => $value){
                if ($value['payment_code'] == 'predeposit' || $value['payment_code'] == 'offline') {
                   unset($payment_list[$key]);
                }
            }
            Tpl::output('payment_list',$payment_list);
            Tpl::showpage('order.receive_pay');
            exit();
        }
        //预定支付尾款时需要用到已经支付的状态
        $order_list = $model_order->getOrderList(array('pay_sn'=>$order_info['pay_sn'],'order_state'=>array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY))));

        //取订单其它扩展信息
        $result = Logic('payment')->getOrderExtendList($order_list,'admin');
        if (!$result['state']) {
            return $result;
        }
        $result = $logic_order->changeOrderReceivePay($order_list,'admin',$this->admin_info['name'],$post);
        if ($result['state']) {
            $this->log('将订单改为已收款状态,'.L('order_number').':'.$order_info['order_sn'],1);
            //记录消费日志
            $api_pay_amount = $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
            QueueClient::push('addConsume', array('member_id'=>$order_info['buyer_id'],'member_name'=>$order_info['buyer_name'],
            'consume_amount'=>$api_pay_amount,'consume_time'=>TIMESTAMP,'consume_remark'=>'管理员更改订单为已收款状态，订单号：'.$order_info['order_sn']));
        }
        return $result;
    }

    /**
     * 查看订单
     *
     */
    public function show_orderOp(){
        $order_no = $_GET['order_no'];
        if(empty($order_no)){
            showMessage(L('miss_order_number'));
        }
        $model_order = SCMModel('scm_client_order');
        $order_info = $model_order->getOrderInfo(array('order_no'=>$order_no));

        Tpl::output('order_info',$order_info);
        Tpl::showpage('client_order.view');
    }

    /**
     * 导出
     *
     */
    public function export_step1Op(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        //已结算订单
        $condition['pay_flag'] = array('neq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }else{
            $order = "order_date desc";
        }
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
                Tpl::output('murl','index.php?act=order&op=index');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_order->getOrderList($condition,'','*',$order,self::EXPORT_SIZE);
                $this->createExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_order->getOrderList($condition,'','*',$order,"{$limit1},{$limit2}");
            $this->createExcel($data);
        }
    }

    public function export_step2Op(){
        $model_order = SCMModel('scm_client_order');
        $condition  = array();
        $condition['clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        $condition['pay_flag'] = array('eq', 0);
        $this->_get_condition($condition);
        $sort_fields = array('order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }else{
            $order = "order_date desc";
        }
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
                Tpl::output('murl','index.php?act=order&op=index');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_order->getOrderList($condition,'','*',$order,self::EXPORT_SIZE);
                $this->createUnsettlementExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_order->getOrderList($condition,'','*',$order,"{$limit1},{$limit2}");
            $this->createUnsettlementExcel($data);
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'付款金额(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'结算状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'资金流向');
        //data
        foreach ((array)$data as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['clie_id']);
			$tmp[] = array('data'=>$order_info['order_no']);
            $tmp[] = array('data'=>$order_info['supp_id']);
            $tmp[] = array('data'=>$order_info['order_pay']);
			$tmp[] = array('data'=>$order_info['order_date']);
            if($order_info['order_status'] == 0)
                $tmp[] = array('data'=>'未完成');
            else if($order_info['order_status'] == 1)
                $tmp[] = array('data'=>'已完成');
            else if($order_info['order_status'] == 2)
                $tmp[] = array('data'=>'半单');
            else if($order_info['order_status'] == 3)
                $tmp[] = array('data'=>'订单已取消');
            else if($order_info['order_status'] == 4)
                $tmp[] = array('data'=>'退货成功');
            else if($order_info['order_status'] == 5)
                $tmp[] = array('data'=>'退货失败');
			$tmp[] = array('data'=>'已结算');
            if($order_info['pay_flag'] == 1 ){
                $tmp[] = array('data'=> '终端店->共铸商城');
            }elseif($order_info['pay_flag'] == 2){
                $tmp[] = array('data'=> '共铸商城->供应商');
            }elseif($order_info['pay_flag'] == 3){
                $tmp[] = array('data'=> '共铸商城->终端店');
            }
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    private function createUnsettlementExcel($data = array()){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'付款金额(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'结算状态');
        //data
        foreach ((array)$data as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['order_no']);
            $tmp[] = array('data'=>$order_info['supp_id']);
            $tmp[] = array('data'=>$order_info['order_pay']);
            $tmp[] = array('data'=>$order_info['order_date']);
            if($order_info['order_status'] == 0)
                $tmp[] = array('data'=>'未完成');
            else if($order_info['order_status'] == 1)
                $tmp[] = array('data'=>'已完成');
            else if($order_info['order_status'] == 2)
                $tmp[] = array('data'=>'半单');
            else if($order_info['order_status'] == 3)
                $tmp[] = array('data'=>'订单已取消');
            else if($order_info['order_status'] == 4)
                $tmp[] = array('data'=>'退货成功');
            else if($order_info['order_status'] == 5)
                $tmp[] = array('data'=>'退货失败');
            $tmp[] = array('data'=>'未结算');
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 处理搜索条件
     */
    private function _get_condition(& $condition) {
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('order_no','store_name','buyer_name','pay_sn'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
        
        if ($_GET['keyword'] != '' && in_array($_GET['keyword_type'],array('order_sn','store_name','buyer_name','pay_sn','shipping_code'))) {
            if ($_GET['jq_query']) {
                $condition[$_GET['keyword_type']] = $_GET['keyword'];
            } else {
                $condition[$_GET['keyword_type']] = array('like',"%{$_GET['keyword']}%");
            }
        }
        if (!in_array($_GET['qtype_time'],array('add_time','payment_time','finnshed_time'))) {
            $_GET['qtype_time'] = null;
        }
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_date']): null;
        if ($_GET['qtype_time'] && ($start_unixtime || $end_unixtime)) {
            $condition[$_GET['qtype_time']] = array('time',array($start_unixtime,$end_unixtime));
        }
        if($_GET['payment_code']) {
            if ($_GET['payment_code'] == 'wxpay') {
                $condition['payment_code'] = array('in',array('wxpay','wx_saoma','wx_jsapi'));
            } elseif($_GET['payment_code'] == 'alipay') {
                $condition['payment_code'] = array('in',array('alipay','ali_native'));
            } else {
                $condition['payment_code'] = $_GET['payment_code'];
            }
        }
        if(in_array($_GET['order_state'],array('0','10','20','30','40'))){
            $condition['order_state'] = $_GET['order_state'];
        }
        if (!in_array($_GET['query_amount'],array('order_amount','shipping_fee','refund_amount'))) {
            $_GET['query_amount'] = null;
        }
        if (floatval($_GET['query_start_amount']) > 0 && floatval($_GET['query_end_amount']) > 0 && $_GET['query_amount']) {
            $condition[$_GET['query_amount']] = array('between',floatval($_GET['query_start_amount']).','.floatval($_GET['query_end_amount']));
        }
        if(in_array($_GET['order_from'],array('1','2'))){
            $condition['order_from'] = $_GET['order_from'];
        }
    }

}
