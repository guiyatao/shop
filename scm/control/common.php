<?php
/**
 * 通用页面
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

class commonControl extends SCMControl{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 图片上传
     *
     */
    public function pic_uploadOp(){
        if (chksubmit()){
            //上传图片
            $upload = new UploadFile();
            $upload->set('thumb_width', 500);
            $upload->set('thumb_height',499);
            $upload->set('thumb_ext','_small');
            $upload->set('max_size',C('image_max_filesize')?C('image_max_filesize'):1024);
            $upload->set('ifremove',true);
            $upload->set('default_dir',$_GET['uploadpath']);

            if (!empty($_FILES['_pic']['tmp_name'])){
                $result = $upload->upfile('_pic');
                if ($result){
                    exit(json_encode(array('status'=>1,'url'=>UPLOAD_SITE_URL.'/'.$_GET['uploadpath'].'/'.$upload->thumb_image)));
                }else {
                    exit(json_encode(array('status'=>0,'msg'=>$upload->error)));
                }
            }
        }
    }

    /**
     * 图片裁剪
     *
     */
    public function pic_cutOp(){
        Language::read('admin_common');
        $lang = Language::getLangContent();

        if (chksubmit()){
            $thumb_width = $_POST['x'];
            $x1 = $_POST["x1"];
            $y1 = $_POST["y1"];
            $x2 = $_POST["x2"];
            $y2 = $_POST["y2"];
            $w = $_POST["w"];
            $h = $_POST["h"];
            $scale = $thumb_width/$w;
            $src = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_POST['url']);
            if (strpos($src, '..') !== false || strpos($src, BASE_UPLOAD_PATH) !== 0) {
                exit();
            }
            if (!empty($_POST['filename'])){
                $save_file2 = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_POST['filename']);
            }else{
                $save_file2 = str_replace('_small.','_sm.',$src);
            }

            \Shopnc\Lib::imager()->createImageFromPath($src)
                ->clip($x1, $y1, $w, $h, $scale)
                ->save($save_file2);

            @unlink($src);
            $pathinfo = pathinfo($save_file2);
            exit($pathinfo['basename']);
        }
        $save_file = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_GET['url']);
        $_GET['resize'] = $_GET['resize'] == '0' ? '0' : '1';

        $image = \Shopnc\Lib::imager()->createImageFromPath($save_file);
        Tpl::output('height', $image->height);
        Tpl::output('width', $image->width);

        Tpl::showpage('common.pic_cut','null_layout');
    }

    /**
     * 查询每月的周数组
     */
    public function getweekofmonthOp(){
        import('function.datehelper');
        $year = $_GET['y'];
        $month = $_GET['m'];
        $week_arr = getMonthWeekArr($year, $month);
        echo json_encode($week_arr);
        die;
    }

    /**
     * 设置常用菜单
     */
    public function common_operationsOp() {
        $type = $_GET['type'];
        $value = $_GET['value'];
        if (!in_array($type, array('add', 'del')) || empty($value)) {
            echo false;exit;
        }
        $quicklink = $this->getQuickLink();
        if (count($quicklink) == 10 && $type == 'add') {
            echo false;exit;
        }
        if ($type == 'add') {
            if (!empty($quicklink)) {
                array_push($quicklink, $value);
            } else {
                $quicklink[] = $value;
            }
        } else {
            $quicklink = array_diff($quicklink, array($value));
        }
        $quicklink = array_unique($quicklink);
        $quicklink = implode(',', $quicklink);

        $this->admin_info['qlink'] = $quicklink;
        $this->systemSetKey($this->admin_info);
        $result = Model('admin')->updateAdmin(array('admin_id' => $this->admin_info['id'], 'admin_quick_link' => $quicklink));
        if ($result) {
            echo true;exit;
        } else {
            echo false;exit;
        }
    }
    
    /**
     * 代办事项
     */
    public function pending_mattersOp() {
        $statistics  = $this->get_pending_matters();
        Tpl::output('statistics', $statistics);

        Tpl::showpage('common.pending_matters','null_layout');
    }
    
    /**
     * 代办事项ajax数据
     */
    public function ajax_pending_mattersOp() {
        $statistics  = $this->get_pending_matters();
        $count = 0;
        foreach ($statistics as $value) {
            $count += $value;
        }
        echo $count;exit();
    }
    
    /**
     * 代办事项数据查询
     * @return array
     */
    private function get_pending_matters() {
        $model_store = Model('store');
        $model_goods = Model('goods');
        $model_order = Model('order');
        $model_refund_return = Model('refund_return');
        $model_vr_refund = Model('vr_refund');
        $model_complain = Model('complain');
        $model_bill = Model('bill');
        $model_vr_bill = Model('vr_bill');
		//获取与当前终端店相关的未处理订单
        $model_supplier = SCMModel('supplier_account');
        $model_supplier_client = SCMModel('supplier_client');
        $adminInfo = $this->getAdminInfo();

        //获取终端店预警信息
        $model_stock = SCMModel('scm_client_stock');
        $model_user = SCMModel('scm_user');
        $user = $model_user->getUserInfo($adminInfo['id']);
        if ($user['user_type'] == 2) {
            $client_warn = $model_stock->getWarnInfo($user['supp_clie_id']);
            $statistics['client_stockout_count'] = $client_warn['stockout_count'];
            $statistics['client_date_warn_count'] = $client_warn['date_warn_count'];
            $statistics['client_unsalable_count'] = $client_warn['unsalable_count'];

            //获取商城订单
            $model_online_order = SCMModel('scm_online_order');
            $online_condition  = array();
            $online_condition['scm_online_order.clie_id'] = $user['supp_clie_id'];
            $online_condition['scm_online_order.order_state'] = 20;
            //只获取订单状态为未退款的
            $online_condition['orders.refund_state'] = array('neq', 2);
            $field = "scm_online_order.*,orders.lock_state,orders.order_state AS status,orders.payment_code AS pay_way";
            $online_order_list = $model_online_order->getOrderListOn($online_condition,null,$field);

            $statistics['client_online_order_count'] = count($online_order_list);
            //获取已发货订单
            $model_client_order = SCMModel('scm_client_order');
            $order_condition  = array();
            $order_condition['clie_id'] = $user['supp_clie_id'];
            $order_condition['order_status'] = array('eq', 0);
            $order_condition['out_flag'] = array('eq', 1);
            $order_condition['in_flag'] = array('neq', 1);
            $order_list = $model_client_order->getOrderList($order_condition);
            $statistics['client_order_count'] = count($order_list);
            $statistics['user_type'] = "client";
        } elseif ($user['user_type'] == 3) {    //获取供应商预警信息
            $statistics['user_type'] = "supplier";
            $supplier = $model_supplier->getSupplier(array('admin.admin_id'=>$adminInfo['id']));
            $condition['supp_id'] = $supplier['supp_id'];
            $condition['order_status'] = 0;
            $condition['out_flag'] = 0;
            $condition['pay_flag'] = 1;
            $temp_list = $model_supplier_client->gettotalnum($condition);
            $statistics['supplier_order'] = count($temp_list);
            //获取与当前终端店相关的未入库退货单
            $condition = array();
            $temp_list = array();
            $condition['supp_id'] = $supplier['supp_id'];
            $condition['order_status'] = 0;
            $condition['out_flag'] = 1;
            $condition['refund_flag'] = 1;
            $condition['in_flag'] = 0;
            $temp_list = $model_supplier_client->gettotalnum($condition);
            $statistics['supplier_refund'] = count($temp_list);
            //获取与当前供应商合作终端店的预警
            $warn = $model_supplier_client->get_warn($supplier['supp_id']);
//        $statistics['supplier_warn'] = $warn['warn'];
            $statistics['supplier_validity_warn'] = $warn['validity_warn'];
            $statistics['supplier_unavailable_warn'] = $warn['unavailable_warn'];
            $statistics['supplier_unsalable_warn'] = $warn['unsalable_warn'];

        }else {
            $model_activity = SCMModel('gzkj_activity');
            $statistics['aclist'] = $model_activity->getActivityCount(array('activity_status' => 2));
            $model_supp = SCMModel('gzkj_supp_stock');
            $statistics['sulist'] = $model_supp->getSuppStockCount(array('status' => 2));

            // 预存款提现
            $statistics['cashlist'] = Model('predeposit')->getPdCashCount(array('pdc_payment_state' => 0));
            // 店铺申请数
            $statistics['store_joinin'] = Model('store_joinin')->getStoreJoininCount(array('joinin_state' => array('in', array(10, 11))));
            //店铺续签申请
            $statistics['store_reopen_applay'] = Model('store_reopen')->getStoreReopenCount(array('re_state' => 1));
            // 经营类目申请
            $statistics['store_bind_class_applay'] = Model('store_bind_class')->getStoreBindClassCount(array('state' => 0));
            // 即将到期
            $statistics['store_expire'] = $model_store->getStoreCount(array('store_state' => 1, 'store_end_time' => array('between', array(TIMESTAMP, TIMESTAMP + 864000))));
            // 已经到期
            $statistics['store_expired'] = $model_store->getStoreCount(array('store_state' => 1, 'store_end_time' => array('between', array(1, TIMESTAMP))));
            // 等待审核
            $statistics['product_verify'] = $model_goods->getGoodsCommonWaitVerifyCount(array());
            // 举报
            $statistics['inform'] = Model('inform')->getInformCount(array('inform_state' => 1));
            // 品牌申请
            $statistics['brand_apply'] = Model('brand')->getBrandCount(array('brand_apply' => '0'));
            // 退款
            $statistics['refund'] = $model_refund_return->getRefundReturn(array('refund_type' => 1, 'refund_state' => 2));
            // 退货
            $statistics['return'] = $model_refund_return->getRefundReturn(array('refund_type' => 2, 'refund_state' => 2));
            // 虚拟订单退款
            $statistics['vr_refund'] = $model_vr_refund->getRefundCount(array('admin_state' => 1));
            // 投诉
            $statistics['complain_new'] = $model_complain->getComplainCount(array('complain_state' => 10));
            // 待仲裁
            $statistics['complain_handle'] = $model_complain->getComplainCount(array('complain_state' => 40));
            // 团购数量
            $statistics['groupbuy_verify'] = Model('groupbuy')->getGroupbuyCount(array('state' => 10));
            // 积分订单
            $statistics['points_order'] = Model('pointorder')->getPointOrderCount(array('point_orderstate' => 20));
            //待审核账单
            $condition = array();
            $condition['ob_state'] = BILL_STATE_STORE_COFIRM;
            $statistics['check_billno'] = $model_bill->getOrderBillCount($condition);
            $statistics['check_vr_billno'] = $model_vr_bill->getOrderBillCount($condition);
            //待支付账单
            $condition = array();
            $condition['ob_state'] = BILL_STATE_SYSTEM_CHECK;
            $statistics['pay_billno'] = $model_bill->getOrderBillCount($condition);
            $statistics['pay_vr_billno'] = $model_vr_bill->getOrderBillCount($condition);
            // 平台客服
            $statistics['mall_consult'] = Model('mall_consult')->getMallConsultCount(array('is_reply' => 0));
            // 服务站
            $statistics['delivery_point'] = Model('delivery_point')->getDeliveryPointWaitVerifyCount(array());
            /**
             * 资讯
             */
            if (C('cms_isuse')) {
                // 文章审核
                $statistics['cms_article_verify'] = Model('cms_article')->getCmsArticleCount(array('article_state' => 2));
                // 画报审核
                $statistics['cms_picture_verify'] = Model('cms_picture')->getCmsPictureCount(array('picture_state' => 2));
            }
            /**
             * 圈子
             */
            if (C('circle_isuse')) {
                $statistics['circle_verify'] = Model('circle')->getCircleUnverifiedCount();
            }
        }
        return $statistics;
    }
}
