<?php
/**
 * 支付入口
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class paymentControl extends SCMControl{

    public function __construct() {
        Language::read('common,home_layout');
    }
    
    /**
     * 通知处理(支付宝异步通知和网银在线自动对账)
     *
     */
    public function notifyOp(){
        switch ($_GET['payment_code']) {
            case 'alipay':
                $success = 'success'; $fail = 'fail'; break;
            case 'chinabank':
                $success = 'ok'; $fail = 'error'; break;
            default:
                exit();
        }

        $order_type = $_POST['extra_common_param'];
        $out_trade_no = $_POST['out_trade_no'];
        $trade_no = $_POST['trade_no'];

        //参数判断
        if(!preg_match('/^\d{18}$/',$out_trade_no)) exit($fail);

        $logic_payment = Logic('payment');
        
        $model_order = SCMModel('scm_client_order');
        $condition['pay_sn1'] = $out_trade_no;
        $condition['pay_sn'] = $out_trade_no;
        $condition['_op'] = 'or';
        $order_info = $model_order->getOrderInfo($condition);
        
        if ($order_type == 'scm_client_order') {

            $pay_flag = $order_info['pay_flag'];
            if ($pay_flag==1) {
                exit($success);
            }
        } else {
            exit();
        }

        $order_pay_info = array();
        $order_pay_info['order_type'] = "scm_client_order";
        $order_pay_info['subject'] = "实物订单".$out_trade_no;
        $order_pay_info['pay_sn'] = $out_trade_no;
//         $order_pay_info['api_pay_amount'] = "0.01";
        $order_pay_info['api_pay_amount'] = $order_info["order_pay"];
        
        //取得支付方式
        $result = $logic_payment->getPaymentInfo('alipay');
        if (!$result['state']) {
            exit($fail);
        }
        $payment_info = $result['data'];

        //创建支付接口对象
        $payment_api = new alipay($payment_info,$order_pay_info);

        //对进入的参数进行远程数据判断
        $verify = $payment_api->notify_verify();
        if (!$verify) {
            exit($fail);
        }

        //购买商品
        $updatestatus = false;
        if ($order_type == 'real_order') {
            $data = array("pay_flag"=>1);
            $updatestatus = $model_order->editOrder($data, $condition);
        } 
        if ($updatestatus) {
            //TODO:记录log
        }

        exit($updatestatus ? $success : $fail);
    }

    /**
     * 支付接口返回
     *
     */
    public function returnOp(){
        $order_type = $_GET['extra_common_param'];
        if ($order_type == 'scm_client_order') {
            $act = 'scm_client_order';
        } else {
            exit();
        }

        $out_trade_no = $_GET['out_trade_no'];
        $trade_no = $_GET['trade_no'];
        $url = SCM_SITE_URL.'/index.php';

        //对外部交易编号进行非空判断
        if(!preg_match('/^\d{18}$/',$out_trade_no)) {
            showMessage('参数错误',$url,'','html','error');
        }

        $logic_payment = Logic('payment');
        
        $model_order = SCMModel('scm_client_order');
        $condition['pay_sn1'] = $out_trade_no;
        $condition['pay_sn'] = $out_trade_no;
        $condition['_op'] = 'or';
        
        $order_info = $model_order->getOrderInfo($condition);
        
        if ($order_type == 'scm_client_order') {
            
            $pay_flag = $order_info['pay_flag'];
            if ($pay_flag==1) {
                $payment_state = 'success';
            }
        } else {
            showMessage("支付类型验证失败", $url, 'html', 'error');
        }

        if ($payment_state != 'success') {
            
            $order_pay_info = array();
            $order_pay_info['order_type'] = "scm_client_order";
            $order_pay_info['subject'] = "实物订单".$out_trade_no;
            $order_pay_info['pay_sn'] = $out_trade_no;
//             $order_pay_info['api_pay_amount'] = "0.01";
            $order_pay_info['api_pay_amount'] = $order_info["order_pay"];
            
            //取得支付方式
            $result = $logic_payment->getPaymentInfo('alipay');
            if (!$result['state']) {
                showMessage("支付方式验证失败",$url,'html','error');
            }
            $payment_info = $result['data'];
            
            //创建支付接口对象
            $payment_api = new alipay($payment_info,$order_pay_info);
            
            //对进入的参数进行远程数据判断
            $verify = $payment_api->return_verify();

            if (!$verify) {
                showMessage('支付数据验证失败',$url,'html','error');
            }
            
            //取得支付结果
            $pay_result = $payment_api->getPayResult($_GET);
            if (!$pay_result) {
                showMessage('非常抱歉，您的订单支付没有成功，请您后尝试',$url,'html','error');
            }
            
            //购买商品
            $updatestatus = false;
            if ($order_type == 'scm_client_order') {
                $data = array("pay_flag"=>1);
                $updatestatus = $model_order->editOrder($data, $condition);
            }
            if ($updatestatus) {
                //TODO:记录log
            } else {
                showMessage('支付状态更新失败',$url,'html','error');
            }
        }

        $pay_ok_url = SCM_SITE_URL . '/index.php';
        redirect($pay_ok_url);
    }
}
