<?php
/**
 * 库存预警
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
class stockout_warnControl extends SCMControl{

    const EXPORT_SIZE = 1000;
    protected $user_info;
    public function __construct(){
        parent::__construct();
        Language::read('setting');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp() {
        return $this->showStockoutGoods();
    }

    /**
     * 显示库存不足商品
     */
    public function showStockoutGoods(){
        Tpl::showpage('stockout_warn.index');
    }

    public function get_xmlOp(){
        $model_order = SCMModel('scm_client_stock');
        $condition  = array();
        $condition['clie_id'] = $this->user_info['supp_clie_id'];
        $this->_get_condition($condition);

        $field = "scm_client_stock.*,scm_supplier.supp_ch_name,scm_supplier.supp_contacter, scm_supplier.supp_tel, scm_supplier.supp_mobile";
        $stock_list = $model_order->getStockoutWarnList($condition, $_POST['rp'],$field);
        $data = array();
        $data['now_page'] = $model_order->shownowpage();
        $data['total_num'] = $model_order->gettotalnum();
        if(count($stock_list) > 0){
            foreach ($stock_list as $stock_id => $stock_info) {
                $list = array();
                $list['clie_id'] = $stock_info['clie_id'];
                $list['goods_barcode'] = $stock_info['goods_barcode'];
                $list['goods_nm'] = $stock_info['goods_nm'];
                $list['goods_price'] = ncPriceFormat($stock_info['goods_price']);
                $list['goods_discount'] = $stock_info['goods_discount'];
                $list['goods_unit'] = $stock_info['goods_unit'];
                $list['goods_spec'] = $stock_info['goods_spec'];
                $list['goods_stock'] = $stock_info['goods_stock'];
                $list['goods_low_stock'] = $stock_info['goods_low_stock'];
                $list['supp_ch_name'] = $stock_info['supp_ch_name'];
                $list['supp_contacter'] = $stock_info['supp_contacter'];
                $list['supp_tel'] = $stock_info['supp_tel'];
                $list['supp_mobile'] = $stock_info['supp_mobile'];
                if ($stock_info['goods_stock'] <= ($stock_info['goods_low_stock'] * 0.7) &&
                    $stock_info['goods_stock'] >= ($stock_info['goods_low_stock'] * 0.3)) {
                    $list['warn_status'] = '<a class="btn" style="background-color: yellow"> &nbsp;&nbsp;&nbsp;&nbsp;</a>';
                } else if ($stock_info['goods_stock'] <= ($stock_info['goods_low_stock'] * 0.3)) {
                    $list['warn_status'] = '<a class="btn" style="background-color: red"> &nbsp;&nbsp;&nbsp;&nbsp;</a>';
                } else {
                    $list['warn_status'] = '<a class="btn" style="background-color: #8ac43f"> &nbsp;&nbsp;&nbsp;&nbsp;</a>';;
                }
                $data['list'][$stock_info['id']] = $list;
            }
        }

        exit(Tpl::flexigridXML($data));
    }

    /**
     * 处理搜索条件
     */
    private function _get_condition(& $condition) {
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('supp_id','goods_barcode','goods_nm'))) {
            $condition['scm_client_stock.'.$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
    }

    /**
     * 导出文件
     */
    public function export_stockout_warnOp(){
        $model_order = SCMModel('scm_client_stock');
        $condition  = array();
        $condition['clie_id'] = $this->user_info['supp_clie_id'];
        $this->_get_condition($condition);
        $field = "scm_client_stock.*,scm_supplier.supp_ch_name,scm_supplier.supp_contacter, scm_supplier.supp_tel, scm_supplier.supp_mobile";
        if (preg_match('/^[\d,]+$/', $_GET['id'])) {
            $_GET['id'] = explode(',',trim($_GET['id'],','));
            $condition['scm_client_stock.id'] = array('in',$_GET['id']);
        }
        $order = "supp_id DESC";
        if (!is_numeric($_GET['curpage'])){
            $count = count($model_order->getStockoutWarnList($condition, null,$field));
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
                $data = $model_order->getStockoutWarnList($condition, null,$field);
                $this->createExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_order->getStockoutWarnList($condition, null,$field);
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品条码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品原价');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品折扣');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品库存');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存下限');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商联系人');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商电话');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商手机');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品预警状态');

        //data
        foreach ((array)$data as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['goods_barcode']);
            $tmp[] = array('data'=>$order_info['goods_nm']);
            $tmp[] = array('data'=>$order_info['goods_price']);
            $tmp[] = array('data'=>$order_info['goods_discount']);
            $tmp[] = array('data'=>$order_info['goods_unit']);
            $tmp[] = array('data'=>$order_info['goods_spec']);
            $tmp[] = array('data'=>$order_info['goods_stock']);
            $tmp[] = array('data'=>$order_info['goods_low_stock']);
            $tmp[] = array('data'=>$order_info['supp_ch_name']);
            $tmp[] = array('data'=>$order_info['supp_contacter']);
            $tmp[] = array('data'=>$order_info['supp_tel']);
            $tmp[] = array('data'=>$order_info['supp_mobile']);
            if ($order_info['goods_stock'] <= ($order_info['goods_low_stock'] * 0.7) &&
                $order_info['goods_stock'] >= ($order_info['goods_low_stock'] * 0.3)) {
                $tmp[] = array('data'=>"黄色预警");
            } else if ($order_info['goods_stock'] <= ($order_info['goods_low_stock'] * 0.3)) {
                $tmp[] = array('data'=>"红色预警");
            } else {
                $tmp[] = array('data'=>"缺货");
            }
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }



}
