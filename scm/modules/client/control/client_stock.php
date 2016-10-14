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
class client_stockControl extends SCMControl{
    protected $user_info;
    public function __construct(){
        parent::__construct();
        Language::read('trade');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);

    }

    public function indexOp() {
        $this->stockoutOrderOp();
    }

    /**
     * 生成订单号
     *
     */
    public function createOrderNo($clie_id) {
        $order_no = substr($clie_id, 0, 4) . time();
        return $order_no;
    }

    /**
     * 缺货列表
     */
    public function stockoutOrderOp() {
        $clie_id = $this->user_info['supp_clie_id'];
        $model_stock = SCMModel('scm_client_stock');
        if (chksubmit()){
            $goods_array = $_POST;
            unset($goods_array['form_submit']); 
            $order_no = $this->createOrderNo($clie_id);
            $state = $model_stock->createOrderToSupp($goods_array, $order_no, $clie_id);
            if ($state) {
                $this->log('新增店铺帮助，编号'.$state);
                showMessage(Language::get('nc_common_save_succ'),'index.php?act=client_stock&op=index');
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $field = 'scm_client_stock.clie_id, scm_client_stock.goods_barcode, scm_client_stock.goods_nm,
            scm_client_stock.goods_price, scm_client_stock.goods_discount, (scm_client_stock.goods_price * scm_client_stock.goods_discount) goods_discount_price,
            scm_client_stock.goods_unit, scm_client_stock.goods_spec, scm_client_stock.goods_stock,
            scm_client_stock.goods_low_stock, scm_client_stock.supp_id, scm_supp_stock.min_set_num';
        $list = $model_stock->getStockoutList(array('clie_id'=>$clie_id), $field);
        $new_goods_list = $model_stock->getNewGoodsList($this->user_info['supp_clie_id']);
        $list = array_merge($list,$new_goods_list);
        Tpl::output('list',$list);
        Tpl::showpage('stockout.list');
    }

    /**
     * 输出XML数据
     */
    public function get_xmlOp() {
        $model_help = SCMModel('scm_client_stock');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('supp_id');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $stockout_list = $model_help->getStockoutList($condition, $_POST['rp'], '*', $order);

        $data = array();
        $data['now_page'] = $model_help->shownowpage();
        $data['total_num'] = $model_help->gettotalnum();
        foreach ($stockout_list as $value) {
            $param = array();
            $param['operation'] = "<a class='btn red' href=\"javascript:void(0);\" onclick=\"fg_del('".$value['supp_id']."')\"><i class='fa fa-trash-o'></i>删除</a><a class='btn blue' href='index.php?act=supp_manage&op=edit_supp&supp_id=".$value['supp_id']."' class='url'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            $param['clie_id'] = $value['clie_id'];
            $param['supp_id'] = $value['supp_id'];
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = $value['goods_nm'];
            $param['goods_price'] = ncPriceFormat($value['goods_price']);
            $param['goods_discount'] = $value['goods_discount'];
            $param['goods_unit'] = $value['goods_unit'];
            $param['goods_spec'] = $value['goods_spec'];
            $param['goods_rate'] = $value['goods_rate'];
            $param['goods_stock'] = $value['goods_stock'];
            $param['goods_low_stock'] = $value['goods_low_stock'];
            $param['goods_uper_stock'] = $value['goods_uper_stock'];
            $param['goods_prod_date'] = $value['goods_prod_date'];
            $param['new_product_flag'] = $value['new_product_flag'];
            $data['list'][] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 新增商品
     *
     */
    public function add_goodsOp() {
        $model_help = SCMModel('scm_client_stock');
        if (chksubmit()) {
            $goods_array = $_POST;
            unset($goods_array['form_submit']); 
            $state = $model_help->addNewGoodsToClientStock($goods_array);
            if ($state) {
                showMessage(Language::get('nc_common_save_succ'),'index.php?act=client_stock&op=index');
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $goods_list = $model_help->getNewGoodsList($this->user_info['supp_clie_id']);
        Tpl::output('goods_list',$goods_list);
        // $condition = array();
        // $condition['item_id'] = '0';
        // $pic_list = $model_help->getHelpPicList($condition);
        // Tpl::output('pic_list',$pic_list);
        Tpl::showpage('new_goods.add');
    }

    /**
     * 编辑帮助
     *
     */
    public function edit_suppOp() {
        $model_supp = SCMModel('scm_client_supp');
        $condition = array();
        $supp_id = intval($_GET['supp_id']);
        $condition['supp_id'] = $supp_id;
        $supp_info = $model_supp->getSuppInfo($condition);
        Tpl::output('supp_info',$supp_info[0]);
        if (chksubmit()) {
//            $help_array = array();
//            $help_array['help_title'] = $_POST['help_title'];
//            $help_array['help_url'] = $_POST['help_url'];
//            $help_array['help_info'] = $_POST['content'];
//            $help_array['help_sort'] = intval($_POST['help_sort']);
//            $help_array['type_id'] = intval($_POST['type_id']);
//            $help_array['update_time'] = time();
//            $state = $model_help->editHelp($condition, $help_array);
//            if ($state) {
//                $this->log('编辑店铺帮助，编号'.$help_id);
//                showMessage(Language::get('nc_common_save_succ'),'index.php?act=help_store&op=help_store');
//            } else {
//                showMessage(Language::get('nc_common_save_fail'));
//            }
        }
//        $type_list = $model_help->getStoreHelpTypeList();
//        Tpl::output('type_list',$type_list);
//        $condition = array();
//        $condition['item_id'] = $help_id;
//        $pic_list = $model_help->getHelpPicList($condition);
//        Tpl::output('pic_list',$pic_list);
        Tpl::showpage('supp.edit');
    }

    /**
     * 删除帮助
     *
     */
    public function del_helpOp() {
        $id = intval($_GET['id']);
        if ($id > 0) {
            $model_help = Model('help');
            $condition = array();
            $condition['help_id'] = $id;
            $state = $model_help->delHelp($condition,array($id));
            $this->log('删除店铺帮助，ID'.$id);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        } else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }
}
