<?php
/**
 * 滞销预警
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
class unsalable_warnControl extends SCMControl{
    protected $user_info;
    public function __construct(){
        parent::__construct();
        Language::read('setting');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp() {
        return $this->showUnsalableGoods();
    }

    /**
     * 显示滞销的库存商品
     */
    public function showUnsalableGoods(){
        Tpl::showpage('unsalable_warn.index');
    }

    public function get_xmlOp(){
        $page = new Page();
        $page->setEachNum($_POST['rp']) ;
        $page->setStyle('admin') ;
        $data = array();
        $page_num = $_POST['rp'];
        $data['now_page'] = $page->get('now_page');
        $index = ($data['now_page'] - 1) * $page_num;
        $data['total_num'] = count($this->unsalable_warn_sql());
        $stock_list = $this->unsalable_warn_sql(array('index'=> $index, 'page_num'=> $page_num ));
        if( count($stock_list) > 0 ){
            foreach ($stock_list as $stock_id => $stock_info) {
                $list = array();
                $list['clie_id'] = $stock_info['clie_id'];
                $list['goods_barcode'] = $stock_info['goods_barcode'];
                $list['goods_nm'] = $stock_info['goods_nm'];
                $list['goods_unit'] = $stock_info['goods_unit'];
                $list['goods_spec'] = $stock_info['goods_spec'];
                $list['goods_stock'] = $stock_info['goods_stock'];
                $list['drug_remind'] = $stock_info['drug_remind'];
                $list['supp_ch_name'] = $stock_info['supp_ch_name'];
                $list['supp_contacter'] = $stock_info['supp_contacter'];
                $list['supp_tel'] = $stock_info['supp_tel'];
                $list['supp_mobile'] = $stock_info['supp_mobile'];
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
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
    }

    /**
     * 当前终端店的滞销期预警
     * @param array $condition
     */
    private function unsalable_warn_sql($condition=array()){
        $sql ="SELECT
                    scs.id,
                    scs.clie_id,
                    sc.clie_ch_name,
                    scs.goods_barcode,
                    scs.goods_nm,
                    scs.goods_unit,
                    scs.goods_stock,
                    scs.goods_uper_stock,
                    ss.supp_ch_name,
                    ss.supp_contacter,
                    ss.supp_tel,
                    ss.supp_mobile,
                    scs.goods_spec,
                    scs.drug_remind,
                    max(sii.in_stock_date) as last_time
                FROM
                    ".C('tablepre')."scm_client_stock AS scs
                LEFT JOIN ".C('tablepre')."scm_client AS sc ON sc.clie_id = scs.clie_id
                LEFT JOIN ".C('tablepre')."scm_supplier AS ss ON scs.supp_id = ss.supp_id
                LEFT JOIN ".C('tablepre')."scm_instock_info sii ON scs.clie_id=sii.clie_id and scs.supp_id=sii.supp_id and scs.goods_barcode=sii.goods_barcode
                WHERE
                    scs.clie_id = '{$this->user_info['supp_clie_id']}'
                AND scs.goods_barcode NOT IN (
                    SELECT
                        goods_barcode
                    FROM
                        ".C('tablepre')."scm_instock_info
                    WHERE
                        clie_id = '{$this->user_info['supp_clie_id']}'
                    AND in_stock_date > DATE_SUB(NOW(), INTERVAL scs.drug_remind DAY)
                ) ";
            if(isset($condition['ids']) && $condition['ids'] != ''){
                $sql.= " AND scs.id in (".$condition['ids'].") ";
            }
            $sql.=" GROUP BY scs.goods_barcode";
        if(isset($condition['index']) ){
            $sql.= " limit ".$condition['index'].",".$condition['page_num'];
        }
        $stock_list =  SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        return $stock_list;

    }

    public function export_unsalable_warnOp(){
        $stock_list = $this->unsalable_warn_sql(array('ids'=> $_GET['id']));
        $this->createExcel($stock_list);
    }

    private function createExcel($stock_list){
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品库存');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'滞销提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商联系人');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商电话');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商手机');

        //data
        foreach ((array)$stock_list as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['goods_barcode']);
            $tmp[] = array('data'=>$order_info['goods_nm']);
            $tmp[] = array('data'=>$order_info['goods_unit']);
            $tmp[] = array('data'=>$order_info['goods_spec']);
            $tmp[] = array('data'=>$order_info['goods_stock']);
            $tmp[] = array('data'=>$order_info['drug_remind']);
            $tmp[] = array('data'=>$order_info['supp_ch_name']);
            $tmp[] = array('data'=>$order_info['supp_contacter']);
            $tmp[] = array('data'=>$order_info['supp_tel']);
            $tmp[] = array('data'=>$order_info['supp_mobile']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }


}
