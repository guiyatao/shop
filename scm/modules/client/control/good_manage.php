<?php
/**
 * 近效期预警
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
class good_manageControl extends SCMControl{
    const EXPORT_SIZE = 1000;

    public function __construct(){
        parent::__construct();
        Language::read('goods');
    }

    public function indexOp() {
        return $this->showAllGoods();
    }

    /**
     * 显示库存商品近效期
     */
    public function showAllGoods(){
        Tpl::showpage('good_manage.index');
    }

    public function get_xmlOp(){
        $model_order = SCMModel('scm_client_stock');
        $condition  = array();
        $this->_get_condition($condition);
        $sort_fields = array('clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_unit','goods_stock','goods_low_stock');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $stock = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $stock_list = $model_order->getStockList($condition,$_POST['rp'],'*',$stock);
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = $model_order->gettotalnum();
        foreach ($stock_list as $stock_id => $stock_info) {
            $list = array();
            $operation = '';
            $goods = Model('goods')->getGoodsList(array('goods_barcode'=> $stock_info['goods_barcode'] ));
            if(count($goods)> 0 ) {  //商城有该商品上架
                $operation .= "<span class='btn'><em><i class='fa fa-cog'></i>设置 <i class='arrow'></i></em><ul>";
                $operation .= "<li><a href='javascript:void(0)' onclick=\"fg_sku('" . $goods[0]['goods_commonid'] . "')\">查看商品SKU</a></li>";
                $operation .= "</ul>";
                $operation = "<a href='javascript:void(0)' class='btn blue' onclick=\"fg_sku('" . $goods[0]['goods_commonid'] . "')\" >查看商品SKU</a>";
            }else{
                $operation = "<a href='javascript:void(0)' class='btn' style='background-color:yellow'>商城无此商品</a>";
            }
            $operation.="<a href='javascript:void(0)' class='btn blue' onclick=\"goods_edit('" . $stock_info['id'] . "')\"  >修改</a>";
            $list['operation'] = $operation;
            $list['clie_id'] = $stock_info['clie_id'];
            $list['supp_id'] = $stock_info['supp_id'];
            $list['goods_barcode'] = $stock_info['goods_barcode'];
            $list['goods_nm'] = $stock_info['goods_nm'];
            $list['goods_price'] = ncPriceFormat($stock_info['goods_price']);
            $list['goods_discount'] = $stock_info['goods_discount'];
            $list['goods_unit'] = $stock_info['goods_unit'];
            $list['goods_stock'] = $stock_info['goods_stock'];
            $list['goods_low_stock'] = $stock_info['goods_low_stock'];
            $list['production_date'] = $stock_info['production_date'];
            $list['valid_remind'] = $stock_info['valid_remind'];
            $list['shelf_life'] = $stock_info['shelf_life'];
            $list['drug_remind'] = $stock_info['drug_remind'];
            $supp_goods = SCMModel('supplier_goods')->getGoodsInfo(array('goods_barcode'=>$stock_info['goods_barcode'],'supp_id'=>$stock_info['supp_id']) );
            if($supp_goods['status'] == 0){
                $list['status'] = "已失效";
            }else if($supp_goods['status'] == 1){
                $list['status'] = "正常";
            }else if($supp_goods['status'] == 2){
                $list['status'] = "未审核";
            } else if($supp_goods['status'] == 3){
                $list['status'] = "审核未通过";
            }
            $data['list'][$stock_info['id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * ajax获取商品列表
     */
    public function get_goods_sku_listOp() {
        $commonid = $_GET['commonid'];
        if ($commonid <= 0) {
            showDialog('参数错误', '', '', 'CUR_DIALOG.close();');
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodsCommonInfoByID($commonid, 'spec_name');
        if (empty($goodscommon_list)) {
            showDialog('参数错误', '', '', 'CUR_DIALOG.close();');
        }
        $spec_name = array_values((array)unserialize($goodscommon_list['spec_name']));
        $goods_list = $model_goods->getGoodsList(array('goods_commonid' => $commonid), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage,goods_image');
        if (empty($goods_list)) {
            showDialog('参数错误', '', '', 'CUR_DIALOG.close();');
        }

        foreach ($goods_list as $key => $val) {
            $goods_spec = array_values((array)unserialize($val['goods_spec']));
            $spec_array = array();
            foreach ($goods_spec as $k => $v) {
                $spec_array[] = '<div class="goods_spec">' . $spec_name[$k] . L('nc_colon') . '<em title="' . $v . '">' . $v .'</em>' . '</div>';
            }
            $goods_list[$key]['goods_image'] = thumb($val, '60');
            $goods_list[$key]['goods_spec'] = implode('', $spec_array);
            $goods_list[$key]['url'] = urlShop('goods', 'index', array('goods_id' => $val['goods_id']));
        }

//         /**
//          * 转码
//          */
//         if (strtoupper(CHARSET) == 'GBK') {
//             Language::getUTF8($goods_list);
//         }
//         echo json_encode($goods_list);
        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('goods.sku_list', 'null_layout');
    }

    /**
     * 编辑商品
     */
    public function goods_editOp(){
        $model = SCMModel('scm_client_stock');
        if (chksubmit()) {
            $result = $model-> editGoods(array(
                'id'=> $_POST['id'],
                'goods_low_stock' => $_POST['goods_low_stock'],
                'valid_remind' => $_POST['valid_remind'],
                'drug_remind'=> $_POST['drug_remind'],
            ));
            showDialog(L('nc_common_op_succ'), urlSCMClient('good_manage', 'index'), 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
        }
        $goods_info = $model-> getGoodsInfoById($_GET['id']);
        if($goods_info){
            Tpl::output('goods_info', $goods_info);
            Tpl::showpage('goods_edit.close_remark', 'null_layout');
        }
    }

    /**
     * 处理搜索条件
     */
    private function _get_condition(& $condition) {
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('supp_id','goods_barcode','goods_nm'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
        $user = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
        $user_id = $user['id'];
        $model_scmuser = SCMModel('scm_user');
        $client = $model_scmuser->getUserInfo($user_id);
        $clie_id = $client["supp_clie_id"];
        $condition['clie_id'] = $clie_id;
    }

    /**
     * 导出数据
     */
    public function export_csvOp(){
        $model_order = SCMModel('scm_client_stock');
        $condition  = array();
        $this->_get_condition($condition);
        $sort_fields = array('clie_id','supp_id','goods_barcode','goods_nm','goods_price','goods_discount','goods_unit','goods_stock','goods_low_stock');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }else{
            $order = 'supp_id desc';
        }
        if (preg_match('/^[\d,]+$/', $_GET['id'])) {
            $_GET['id'] = explode(',',trim( $_GET['id'],','));
            $condition['id'] = array('in',$_GET['id']);
        }
        if (!is_numeric($_GET['curpage'])) {   //没有分页默认只取1000行
            $limit =  '0,'. self::EXPORT_SIZE;
        } else {
            $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 . ',' . $limit2;
        }
        $stock_list = $model_order->getStockList($condition,null,'*',$order,$limit);
        $this->createExcel($stock_list);
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品条码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品原价');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品折扣');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品库存');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存下限');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'保质期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'滞销期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品状态');
        //data
        foreach ((array)$data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['supp_id']);
            $tmp[] = array('data'=>$v['goods_barcode']);
            $tmp[] = array('data'=>$v['goods_nm']);
            $tmp[] = array('data'=>$v['goods_price']);
            $tmp[] = array('data'=>$v['goods_discount']);
            $tmp[] = array('data'=>$v['goods_unit']);
            $tmp[] = array('data'=>$v['goods_stock']);
            $tmp[] = array('data'=>$v['goods_low_stock']);
            $tmp[] = array('data'=>$v['production_date']);
            $tmp[] = array('data'=>$v['valid_remind']);
            $tmp[] = array('data'=>$v['shelf_life']);
            $tmp[] = array('data'=>$v['drug_remind']);
            $supp_goods = SCMModel('supplier_goods')->getGoodsInfo(array('goods_barcode'=>$v['goods_barcode'],'supp_id'=>$v['supp_id']) );
            if($supp_goods['status'] == 0){
                $tmp[] = array('data'=>"已失效");
            }else if($supp_goods['status'] == 1){
                $tmp[] = array('data'=>"正常");
            }else if($supp_goods['status'] == 2){
                $tmp[] = array('data'=>"未审核");
            } else if($supp_goods['status'] == 3){
                $tmp[] = array('data'=>"审核未通过");
            }
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }
}
