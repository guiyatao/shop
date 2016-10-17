<?php
/**
 * 缺货订单
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
class client_purchaseControl extends SCMControl{

    protected $user_info;

    private $links = array(
        array('url'=>'act=client_purchase&op=allGoodsList','lang'=>'client_order_manage'),
        array('url'=>'act=client_purchase&op=stockoutList','lang'=>'stockout_index_brand'),
        array('url'=>'act=client_purchase&op=cartList','lang'=>'client_cart'),
    );
    public function __construct(){
        parent::__construct();
        Language::read('trade');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp() {
        $this->stockoutListOp();
    }

    public function stockoutListOp() {
        Tpl::output('top_link',$this->sublink($this->links,'stockoutList'));
        Tpl::output('clie_id',$this->user_info['supp_clie_id']);
        Tpl::showpage('client_purchase.index');
    }

    /**
     * 缺货列表
     */
    public function get_stockout_xmlOp(){
        $page = intval($_POST['rp']);
        if ($page < 1) {
            $page = 15;
        }
        $clie_id = $this->user_info['supp_clie_id'];
        $model = SCMModel('scm_client_stock');
        $field = 'scm_client_stock.clie_id, scm_client_stock.goods_barcode, scm_client_stock.goods_nm,
            scm_client_stock.goods_price, scm_client_stock.goods_discount, (scm_client_stock.goods_price * scm_client_stock.goods_discount) goods_discount_price,
            scm_client_stock.goods_unit, scm_client_stock.goods_spec, scm_client_stock.goods_stock,
            scm_client_stock.goods_low_stock, scm_client_stock.supp_id';
        $list = $model->getStockoutList(array('clie_id'=>$clie_id), $page, $field);
        if (!empty($list) && is_array($list)){
            $fields_array = array('goods_barcode','goods_nm','goods_discount_price','goods_unit','goods_spec','supp_id','min_set_num','goods_stock');
            foreach ($list as $k => $v){
                $out_array = getFlexigridArray(array(),$fields_array,$v,array('goods_discount_price'));
                //$out_array['admin_login_time'] = $v['admin_login_time'] ? date('Y-m-d H:i:s',$v['admin_login_time']) : $lang['admin_index_login_null'];
                $temp_goods = SCMModel('supplier_goods')->getGoodsInfo(array('goods_barcode'=> $v['goods_barcode'],'supp_id'=>$v['supp_id'] ));
                if ($temp_goods['status'] != 1) {
                    $operation = "<a id={$v['goods_barcode']} name={$v['supp_id']} class='btn' style='background-color: #EEEEEE;' disabled='disabled' href='javascript:void(0);'  onclick=\"fg_add_cart(this, '" . $v['goods_barcode'] . "')\"><i class='fa fa-list-alt'></i>添加到购物车</a>";
                    $out_array['goods_num'] = '<input disabled="disabled" id="num_'.$v['goods_barcode'].'"style="width:50px" type="number" min="0" name=num_'.$v['goods_barcode'].'" title="订购数量" class="editable" />';
                    $out_array['status'] = "已失效&nbsp;&nbsp;";
                    $goods_list = SCMModel('supplier_goods')->getGoodsList(array('goods_barcode'=> $v['goods_barcode'],'status'=>1));
                    if(count($goods_list) > 0){
                        $select = '<select name="new_supp" onchange="change_supp(this)"><option value="0">重新选择供应商</option>';
                        foreach($goods_list as $k => $v){
                            $select.= '<option value='.$v['id'].'>'.$v['supp_id'].'</option>';
                        }
                        $select.='</select>';
                        $out_array['status'] .= $select;
                    }
                } else {
                    $operation = "<a id={$v['goods_barcode']} name={$v['supp_id']} class='btn blue' href='javascript:void(0);' onclick=\"fg_add_cart(this,'" . $v['goods_barcode'] . "')\"><i class='fa fa-list-alt'></i>添加到购物车</a>";
                    $out_array['goods_num'] = '<input id="num_'.$v['goods_barcode'].'"style="width:50px" type="number" min="0" name=num_'.$v['goods_barcode'].'" title="订购数量" class="editable" />';
                    $out_array['status'] = "正常";
                }
                $out_array['goods_discount_price'] = $temp_goods['goods_price'] * $temp_goods['goods_discount'];
                $out_array['goods_unit'] = $temp_goods['goods_unit'];
                $out_array['goods_spec'] = $temp_goods['goods_spec'];
                $out_array['min_set_num'] = $temp_goods['min_set_num'];
                $out_array['operation'] = $operation;
                $out_list[$v['goods_barcode']] = $out_array;
            }
        }

        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        $data['list'] = $out_list;
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_goods = SCMModel('supplier_goods');
        switch ($_GET['branch']){
            /**
             * 根据商品编号获取商品信息
             */
            case 'get_goods_by_id':
                $condition['id'] = $_POST['goods_id'];
                $goods = $model_goods->getGoodsInfo($condition);
                echo json_encode($goods);exit;
                break;
        }
    }

    /**
     * 所有商品列表
     */
    public function allGoodsListOp() {
        Tpl::output('top_link',$this->sublink($this->links,'allGoodsList'));
        Tpl::output('clie_id',$this->user_info['supp_clie_id']);
        Tpl::showpage('client_purchase.all');
    }

    public function get_all_goods_xmlOp(){
        $page = intval($_POST['rp']);
        if ($page < 1) {
            $page = 15;
        }
        $model = SCMModel('scm_client_stock');
        $field = 'id,goods_barcode, goods_nm,
            goods_price, goods_discount, (goods_price * goods_discount) goods_discount_price,
            goods_unit, goods_spec,unit_num, produce_company, produce_area, supp_id, min_set_num, status';
        $condition = array();
        $condition['status'] = 1;
        $this->_get_condition($condition);
        $list = $model->getAllGoodsList($condition, $page, $field);
//        $src = UPLOAD_SITE_URL_HTTPS.DS.ATTACH_GOODS.DS.'1'.DS.$pic_name;
        $src = "/data/upload/shop/store/goods/1/1_05211427960233743_240.jpg";
        $out_list = array();
        if (!empty($list) && is_array($list)){
            $fields_array = array('goods_barcode','goods_nm','goods_discount_price','goods_unit','unit_num','goods_spec','supp_id','produce_company', 'produce_area','min_set_num','goods_online_exist', 'is_new_good');
            foreach ($list as $k => $v){
                $out_array = getFlexigridArray(array(),$fields_array,$v,array('goods_discount_price'));
                //$out_array['admin_login_time'] = $v['admin_login_time'] ? date('Y-m-d H:i:s',$v['admin_login_time']) : $lang['admin_index_login_null'];
                $out_array['goods_num'] = '<input id="num_'.$v['id'].'"style="width:50px" type="number" min="0" name="num_'.$v['id'].'" goods_barcode='.$v['goods_barcode'].' title="订购数量" class="editable" />';
                $operation = "<a id={$v['goods_barcode']} name={$v['supp_id']} class='btn blue' href='javascript:void(0);' onclick=\"fg_add_cart('" . $v['id'] . "','" . $v['goods_barcode'] . "','" . $v['supp_id'] ."')\"><i class='fa fa-list-alt'></i>添加到购物车</a>";
                $out_array['operation'] = $operation;
                //$out_array['goods_nm'] = "<a href='javascript:void(0);' onmouseover=\"toolTip('<img src=" . $src .">')\" onmouseout=\"toolTip()\">{$v['goods_nm']}</a>";
                $out_array['goods_nm'] = $v['goods_nm'];
                $out_list[$v['id']] = $out_array;
            }
        }

        $data = array();
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        $data['list'] = $out_list;
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 处理搜索条件
     */
    private function _get_condition(& $condition) {
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('goods_barcode','goods_nm','supp_id'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
    }

    /**
     * 生成订单号
     *
     */
    public function createOrderNo($clie_id, $supp_id, $time) {
        $refund_no = substr($clie_id, 0, 4) . substr($supp_id, -4) . $time;
        return $refund_no;
    }

    /**
     * 购物车列表
     */
    public function cartListOp() {
        Tpl::output('top_link',$this->sublink($this->links,'cartList'));
        $clie_id = $this->user_info['supp_clie_id'];
        $model_cart = SCMModel('scm_cart');
        if (chksubmit()){
            if( !isset($_POST['goods_barcode'])){
                showMessage(Language::get('nc_common_save_fail'));
            }
            $goods_array = array();
            foreach($_POST['goods_barcode'] as $k => $v){
                $goods_array[$v] = $_POST['goods_num'][$k];
            }
            unset($goods_array['form_submit']);
            $time = date('ymdHis', time());
            $condition = array();
            $condition['goods_barcode'] = array('in', array_keys($goods_array));
            $tmp = $model_cart->getCartSuppList($condition);
            $supp_list = array_flip(array_flip($this->get_array_column($model_cart->getCartSuppList($condition), 'supp_id')));
            $model = SCMModel('scm_client_stock');
            $state = false;
            $paysn1 = "";
            $totalpay = 0;
            foreach ($supp_list as $supp_id) {
                $order_no = $this->createOrderNo($clie_id, $supp_id, $time);
                if($paysn1=="") {
                    $paysn1 = Logic('buy_1')->makePaySn(substr($order_no,4,4));
                }
                $condition['supp_id'] = $supp_id;
                $temp = array();  //临时数组储存一个供应商内的商品
                foreach($_POST['supp_id'] as $k => $v){
                    if($supp_id == $v){
                        $temp[] = $_POST['goods_barcode'][$k];
                    }
                }
                $condition['goods_barcode']= array('in', $temp);
                $supp_goods = $this->getSuppOrderGoodsInfo($model, $condition);
                $order_pay = 0;
                foreach ($supp_goods as $index => $good) {
                    $pay = $good['goods_discount_price'] * $goods_array[$good['goods_barcode']];
                    $order_pay += $pay;
                    $supp_goods[$index]['actual_amount'] = $pay;
                    $supp_goods[$index]['clie_id'] = $clie_id;
                    $supp_goods[$index]['set_num'] = $supp_goods[$index]['order_num'] = $goods_array[$good['goods_barcode']];
                    $supp_goods[$index]['unit_num'] = $good['unit_num'];
                }
                $totalpay += $order_pay;
                $order_id = $model->createOrderToSupp($order_no, $clie_id, $supp_id, $order_pay, $paysn1);
                $state = $model->createOrderGoods($supp_goods, $order_id);
            }

           if ($state) {
              //删除购物车中相关商品
                $del_condition = array();
                $del_condition['goods_barcode'] = array('in', array_keys($goods_array));
                $del_condition['clie_id'] = $clie_id;
                $model_cart->delGood($del_condition);
                $this->alipayForCart($paysn1, $totalpay);
                //showMessage(Language::get('nc_common_save_succ'),'index.php?act=client_purchase&op=cartList');
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $field = 'cart_id, clie_id, goods_barcode, goods_name,
            goods_price, goods_num, (goods_price * goods_num) goods_unit_price, supp_id'; 
        $list = $model_cart->getCartGoodsList(array('clie_id'=>$clie_id), $field);
        Tpl::output('list',$list);
        Tpl::showpage('cart.list');
    }

    private function alipayForCart($paysn1, $totalpay) {
        $order_info = array();
        
        $logic_payment = Logic('payment');
        
        $result = $logic_payment->getPaymentInfo('alipay');
        
        if(!$result['state']) {
            showMessage($result['msg'], "/index.php", 'html', 'error');
        }
        $payment_info = $result['data'];
        
        $order_info['order_type'] = "scm_client_order";
        $order_info['subject'] = "实物订单".$paysn1;
        $order_info['pay_sn'] = $paysn1;
//         $order_info['api_pay_amount'] = "0.01";
        $order_info['api_pay_amount'] = $totalpay;
        
        $payment_api = new alipay($payment_info, $order_info);
        
//         @header("Location: ".$payment_api->get_payurl());
        
        exit('<script>top.location.href="'.$payment_api->get_payurl().'"</script>');

    }
    public function get_array_column($input, $columnKey, $indexKey = NULL){
        $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
        $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
        $result = array();
     
        foreach ((array)$input AS $key => $row)
        { 
          if ($columnKeyIsNumber)
          {
            $tmp = array_slice($row, $columnKey, 1);
            $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
          }
          else
          {
            $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
          }
          if ( ! $indexKeyIsNull)
          {
            if ($indexKeyIsNumber)
            {
              $key = array_slice($row, $indexKey, 1);
              $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
              $key = is_null($key) ? 0 : $key;
            }
            else
            {
              $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
            }
          }
     
          $result[$key] = $tmp;
        }
        return $result;
    }

    /**
     * 获取每个供应商子订单商品信息
     *
     */
    public function getSuppOrderGoodsInfo($model, $condition) {
        $field = 'supp_id, goods_barcode, goods_nm, goods_price, goods_discount, (goods_price * goods_discount) goods_discount_price,
            goods_unit, goods_spec,unit_num, min_set_num, produce_company, produce_area';
        $result = $model->getSuppGoodsInfo($condition, $field);
        return $result ? $result : null;
    }

    /**
     * 新增商品
     *
     */
    public function add_cartOp() {
        $model = SCMModel('scm_cart');
        $data = array();
        $data['clie_id'] = $this->user_info['supp_clie_id'];
        $data['supp_id'] = $_GET['supp_id'];
        $data['goods_barcode'] = $_GET['goods_id'];
        $data['goods_num'] = $_GET['goods_num'];
        $state = $model->addCart($data, 'db', $data['goods_num']);
        if ($state) {
            showDialog(L('nc_common_op_succ'), '', 'succ');
        } else {
            showMessage(Language::get('nc_common_save_fail'));
        }
    }

    // public function batch_cartOp() {
    //     $model_order = SCMModel('scm_cart');
    //     if (chksubmit()) {
    //         $update = array();
    //         $update['order_status'] = 3;

    //         $where = array();
    //         $where['order_no'] = $_POST['order_no'];

    //         $model_order->editOrder($update, $where);
    //         showDialog(L('nc_common_op_succ'), '', 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
    //     }
    //     $common_info = $model_order->getOrderGoodsInfoByID($_GET['order_no']);
    //     Tpl::output('common_info', $common_info);
    //     Tpl::output('order_no', $_GET['order_no']);
    //     Tpl::showpage('add_cart.close_remark', 'null_layout');
    // }

    /**
     * 批量新增商品
     *
     */
    public function batch_cartOp() {
         $model = SCMModel('scm_cart');
         $data = $_POST['goods_arr'];
         $state = $model->addCart($data, 'ob');
         echo $state;
    }

    /**
     * 删除购物车商品
     *
     */
    public function del_cartOp() {
        $cart_id = $_GET['cart_id'];
        if ($cart_id) {
            $model = SCMModel('scm_cart');
            $condition = array();
            $condition['cart_id'] = $cart_id;
            $state = $model->delGood($condition);
            showDialog(L('nc_common_op_succ'));
        } else {
            showMessage(Language::get('nc_common_save_fail'));
        }
    }
}
