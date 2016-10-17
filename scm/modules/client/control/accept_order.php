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
class accept_orderControl extends SCMControl{
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
        Tpl::showpage('online_order.index');
    }

    public function get_xmlOp(){
        $model_order = SCMModel('scm_online_order');
        $condition  = array();
        $condition['scm_online_order.clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        //订单状态：0(已取消); 10(默认):未付款; 20:待发货; 30:已发货 ;40:已收货; 50:已接单; 60已弃单
        $condition['scm_online_order.order_state'] = 20;
        //只获取订单状态为未退款的
        $condition['orders.refund_state'] = array('neq', 2);
        $this->_get_condition($condition);

        $sort_fields = array('order_id','order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $field = "scm_online_order.*,orders.refund_state,orders.order_state AS status,orders.payment_code AS pay_way";
        $order_list = $model_order->getOrderListOn($condition,$_POST['rp'],$field,$order);
        //print_r($order_list);die;
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = count($model_order->getOrderListOn($condition,null,$field,$order));
        foreach ($order_list as $order_id => $order_info) {
            $list = array();
            if($order_info['lock_state'] == 1 || $order_info['status'] == 0)
                $list['operation'] = "<a class=\"btn\" style='background-color: #EEEEEE;'  href='javascript:void(0);'><i class=\"fa fa-list-alt\"></i>查看</a>";
            else
                $list['operation'] = "<a class=\"btn green\" href=\"index.php?act=accept_order&op=show_order&order_id={$order_info['order_id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            $list['order_sn'] = $order_info['order_sn'];
            $list['clie_id'] = $order_info['clie_id'];
            $list['buyer_name'] = $order_info['buyer_name'];
            $list['buyer_phone'] = $order_info['buyer_phone'];
            $list['buyer_address'] = $order_info['buyer_address'];
            $list['add_time'] = date("Y-m-d H:i:s", $order_info['add_time']);
            if($order_info['pay_way'] == 'offline')
                $list['payment_code'] = "货到付款";
            elseif($order_info['pay_way'] == 'alipay')
                $list['payment_code'] = "支付宝";
            elseif($order_info['pay_way'] == 'wx_saoma')
                $list['payment_code'] = "微信支付";
            elseif($order_info['pay_way'] == 'online')
                $list['payment_code'] = "在线支付未付款";
            if($order_info['pickup_mode'] == 0)
                $list['pickup_mode'] = "自提";
            elseif($order_info['pickup_mode'] == 1)
                $list['pickup_mode'] = "上门送";
            $list['order_amount'] = $order_info['order_amount'];
            $list['order_state'] = Order::getShopOrderStatusByID($order_info['order_state']);
            if($order_info['refund_state'] == 2)
                $list['refund_state'] = "<span style='color:red;'>已申请全部退款</span>";
            else if($order_info['refund_state'] == 0)
                $list['refund_state'] = "未申请退款";
            elseif($order_info['refund_state'] == 1){   //部分退款
                $refund_flag = $model_order->getOrderRefundState($order_info);
                if($refund_flag == 0)
                    $list['refund_state'] = "未申请退款";
                elseif($refund_flag == 1)
                    $list['refund_state'] = "<span style='color:red;'>已申请部分退款</span>";
                elseif($refund_flag == 2)
                    $list['refund_state'] = "已申请全部退款";
            }
			$data['list'][$order_info['id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * 接受商品订单
     */
    public function order_acceptOp() {
        $model_order = SCMModel('scm_online_order');
        $model_member_order = Model('order');
        if (chksubmit()) {
            $update = array();
            $update['order_state'] = 50;
            $update['finnshed_time'] = strtotime(date('Y-m-d H:i:s',time()));
            $where = array();
            $where['order_id'] = $_POST['order_id'];
            $where['clie_id'] = $this->user_info['supp_clie_id'];

            $model_order->editOrder($update, $where);
            $goods_array = $model_order->getOrderGoodsList(array('order_id'=>$_POST['order_id']), array('goods_num', 'goods_barcode'));
            foreach ($goods_array as $goods) {
                $model_order->editStock($goods, '-', $this->user_info['supp_clie_id']);
            }
            //jinyu 2016/09/12 遍历相同的订单号order_sn，如果所有订单状态都为50(已接单),gzkj_orders的订单状态为30(待收货)
            $order = $model_order->getOrderInfo(array('order_id'=> $_POST['order_id'] ),null,'*');
            $order_list = $model_order->getOrderList(array('order_sn'=>$order['order_sn']),null,'*');
            $flag = true;
            $shipping_time =0;
            foreach($order_list as $k => $v){
                if($v['order_state'] != 50){
                    $flag = false;
                }
                if($shipping_time < $v['finnshed_time']){
                    $shipping_time =  $v['finnshed_time'];
                }
            }
            if($flag){  //修改gzkj_orders的订单状态为30(待收货)
                $update = $model_member_order->editOrder(array('order_state'=>ORDER_STATE_SEND),array('order_sn' =>$order['order_sn']));
            }
            $delay_time = $shipping_time + 24*3600;
            $model_member_order->editOrder(array('delay_time'=>$delay_time),array('order_sn' =>$order['order_sn']));
            $temp_order = $model_member_order->getOrderInfo(array('order_sn' =>$order['order_sn']));
            $model_member_order->editOrderCommon(array('shipping_time'=>$shipping_time),array('order_id' =>$temp_order['order_id']));

            showDialog(L('nc_common_op_succ'), urlSCMClient('accept_order', 'index'), 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
        }
        $common_info = $model_order->getOrderGoodsInfoByID($_GET['order_id']);
        if ($common_info) {
            $common_info['add_time'] = date("Y-m-d H:i:s", $common_info['add_time']);
            Tpl::output('common_info', $common_info);
            Tpl::showpage('order_accept.close_remark', 'null_layout');
        }
    }

    /**
     * 放弃商品订单
     */
    public function order_abandonOp() {
        $model_order = SCMModel('scm_online_order');

        $model_wx = SCMModel('scm_wechat');
        
$client = 'cail';
$msg = 'test ok';

$model_wx->wxMsgSend($client, $msg, 'o1KiwwzsnIPMOUXIp0EdyUCpSn4k');
        if (chksubmit()) {
            $update = array();
            $update['order_state'] = 60;

            $where = array();
            $where['order_id'] = $_POST['order_id'];
            $where['clie_id'] = $this->user_info['supp_clie_id'];

            $model_order->editOrder($update, $where);

            //todo 弃单操作
            showDialog(L('nc_common_op_succ'), '', 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
        }
        $common_info = $model_order->getOrderGoodsInfoByID($_GET['order_id']);
        
        Tpl::output('common_info', $common_info);
        Tpl::showpage('order_abandon.close_remark', 'null_layout');
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
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0 ){
            showMessage(L('miss_order_number'));
        }
        $model_order = SCMModel('scm_online_order');
        $condition  = array();
        $condition['order_id']  = $order_id;
        $condition['clie_id']  = $this->user_info['supp_clie_id'];
        $order_info = $model_order->getOrderInfo($condition, array('order_goods','order_common'));
        foreach ($order_info['extend_order_goods'] as $value) {
           $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
           $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
           $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
           $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            
           $goods = Model('goods')->getGoodsList(array('goods_barcode'=> $value['goods_barcode'] ));
           $value['goods_id'] = $goods[0]['goods_id'];
           if ($value['goods_type'] == 5) {
               $order_info['zengpin_list'][] = $value;
           } else {
               $order_info['goods_list'][] = $value;
           }
        }

//        if (empty($order_info['zengpin_list'])) {
//            $order_info['goods_count'] = count($order_info['goods_list']);
//        } else {
//            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
//        }

        //取得订单其它扩展信息
//        $model_order->getOrderExtendInfo($order_info);

        //订单变更日志
//        $log_list   = $model_order->getOrderLogList(array('order_id'=>$order_info['order_id']));
//        Tpl::output('order_log',$log_list);

        //退款退货信息
//        $model_refund = Model('refund_return');
//        $condition = array();
//        $condition['order_id'] = $order_info['order_id'];
//        $condition['seller_state'] = 2;
//        $condition['admin_time'] = array('gt',0);
//        $return_list = $model_refund->getReturnList($condition);
//        Tpl::output('return_list',$return_list);

        //退款信息
//        $refund_list = $model_refund->getRefundList($condition);
//        Tpl::output('refund_list',$refund_list);

        //商家信息
//        $store_info = Model('store')->getStoreInfo(array('store_id'=>$order_info['store_id']));
//        Tpl::output('store_info',$store_info);

        //商家发货信息
//        if (!empty($order_info['extend_order_common']['daddress_id'])) {
//            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
//            Tpl::output('daddress_info',$daddress_info);
//        }

        //显示快递信息
//        if ($order_info['shipping_code'] != '') {
//            $express = rkcache('express',true);
//            $order_info['express_info']['e_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
//            $order_info['express_info']['e_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
//            $order_info['express_info']['e_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_url'];
//        }

        //如果订单已取消，取得取消原因、时间，操作人
//        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
//            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id'],'log_orderstate'=>ORDER_STATE_CANCEL),'log_id desc');
//        }

        //如果订单已支付，取支付日志信息(主要是第三方平台支付单号)
//        if ($order_info['order_state'] == ORDER_STATE_PAY) {
//            $order_info['pay_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id'],'log_orderstate'=>ORDER_STATE_PAY),'log_id desc');
//        }
        if($order_info['pickup_mode'] == 0)
            $order_info['pickup_mode'] = "自提";
        elseif($order_info['pickup_mode'] == 1)
            $order_info['pickup_mode'] = "上门送";
        Tpl::output('order_info',$order_info);
        Tpl::output('order_id',$order_id);
        Tpl::showpage('online_order.view');
    }

    /**
     * 导出
     *
     */
    public function export_step1Op(){
        $model_order = SCMModel('scm_online_order');
        $condition  = array();
        $condition['scm_online_order.clie_id'] = array('eq', $this->user_info['supp_clie_id']);
        //订单状态：0(已取消)10(默认):未付款;20:待发货;30:已发货;40:已收货;50:已接单;60已弃单
        $condition['scm_online_order.order_state'] = 20;
        $this->_get_condition($condition);

        $sort_fields = array('order_id','order_no','clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_discount_price','order_num','order_pay','goods_rate','goods_tax','goods_stock','goods_low_stock','gift_barcode','gift_nm','gift_num','order_date','valid_date','out_date','in_date','pay_date','cycle_flag','cycle_num','warn_flag','order_flag','out_flag','in_flag','pay_flag','comments');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }else{
            $order = 'order_id desc';
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
                $data = $model_order->getOrderListOn($condition,'','scm_online_order.*,orders.refund_state,orders.order_state AS status,orders.payment_code AS pay_way',$order,self::EXPORT_SIZE);
                $this->createExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_order->getOrderList($condition,'','scm_online_order.*,orders.refund_state,orders.order_state AS status,orders.payment_code AS pay_way',$order,"{$limit1},{$limit2}");
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'买家姓名');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'买家电话');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'买家地址');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'支付方式');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单金额(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'退款状态');
        //data
        foreach ((array)$data as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['order_sn']);
			$tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['buyer_name']);
            $tmp[] = array('data'=>$order_info['buyer_phone']);
            $tmp[] = array('data'=>$order_info['buyer_address']);
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$order_info['add_time']));
            if($order_info['payment_code'] == 'alipay')
                $tmp[] = array('data'=>"支付宝");
            else if($order_info['payment_code'] == 'offline')
                $tmp[] = array('data'=>"货到付款");
			$tmp[] = array('data'=>$order_info['order_amount']);
            $tmp[] = array('data'=>Order::getShopOrderStatusByID($order_info['order_state']));
            $flag = 0;
            if($order_info['refund_state'] == 2)
                $flag = 2;
            else if($order_info['refund_state'] == 0)
                $flag = 0;
            elseif($order_info['refund_state'] == 1){   //部分退款
                $refund_flag = SCMModel('scm_online_order')->getOrderRefundState($order_info);
                if($refund_flag == 0)
                    $flag = 0;
                elseif($refund_flag == 1)
                    $flag = 1;
                elseif($refund_flag == 2)
                    $flag = 2;
            }
            if($flag == 0)
                $tmp[] = array('data'=>"未申请退款");
            elseif($flag == 1)
                $tmp[] = array('data'=>"已申请部分退款");
            elseif($flag == 2)
                $tmp[] = array('data'=>"已申请全部退款");
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
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('scm_online_order.order_sn','scm_online_order.buyer_name','scm_online_order.buyer_phone'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
    }

}
