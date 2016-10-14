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
class validity_warnControl extends SCMControl{
    protected $user_info;

    public function __construct(){
        parent::__construct();
        Language::read('setting');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp() {
        return $this->showStockValidity();
    }

    /**
     * 显示库存商品近效期
     */
    public function showStockValidity(){
        Tpl::showpage('validity_warn.index');
    }

    public function get_xmlOp(){
        $page = new Page();
        $page->setEachNum($_POST['rp']) ;
        $page->setStyle('admin') ;
        $data = array();
        $page_num = $_POST['rp'];
        $data['now_page'] = $page->get('now_page');
        $index = ($data['now_page'] - 1) * $page_num;
        $stock_list = $this->validation_warn_sql(array('index'=> $index, 'page_num'=> $page_num ));
        $data['total_num'] = count($this->validation_warn_sql());
        if(count($stock_list) > 0){
            foreach ($stock_list as $stock_id => $stock_info) {
                $list = array();
                $list['operation'] = "<a class='btn blue' href='index.php?act=validity_warn&op=cancelWarning&id={$stock_info['id']}'>取消预警</a>";
                $list['clie_id'] = $stock_info['clie_id'];
                $list['supp_id'] = $stock_info['supp_id'];
                $list['supp_ch_name'] = $stock_info['supp_ch_name'];
                $list['goods_barcode'] = $stock_info['goods_barcode'];
                $list['goods_nm'] = $stock_info['goods_nm'];
                $list['goods_unit'] = $stock_info['goods_unit'];
                $list['goods_spec'] = $stock_info['goods_spec'];
                $list['set_num'] = $stock_info['set_num'];
                $list['in_stock_date'] = $stock_info['in_stock_date'];
                $list['production_date'] = $stock_info['production_date'];
                $list['shelf_life'] = $stock_info['shelf_life'];
                $list['valid_remind'] = $stock_info['valid_remind'];
                $list['supp_contacter'] = $stock_info['supp_contacter'];
                $list['supp_tel'] = $stock_info['supp_tel'];
                $list['supp_mobile'] = $stock_info['supp_mobile'];
                $data['list'][$stock_info['id']] = $list;
            }
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * 当前终端店的有效期预警
     * return  goods_list
     */
    private function validation_warn_sql($condition=array()){
        $sql = "SELECT sii.id,sii.clie_id,ss.supp_id, ss.supp_ch_name, sii.goods_barcode,sii.goods_nm,sii.goods_unit, sii.goods_spec,sii.set_num,sii.in_stock_date, sii.production_date,scs.valid_remind,scs.shelf_life,sii.order_id,ss.supp_contacter,ss.supp_tel,ss.supp_mobile,
			    CASE WHEN scs.shelf_life LIKE '%年'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 360) DAY )
				WHEN scs.shelf_life LIKE '%月'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 30) DAY )
				WHEN scs.shelf_life LIKE '%天'  THEN DATE_ADD( scs.production_date, INTERVAL scs.shelf_life DAY )
				END AS expire_date
				FROM ".C('tablepre')."scm_instock_info AS sii
				LEFT JOIN ".C('tablepre')."scm_client_stock AS scs ON sii.goods_barcode = scs.goods_barcode
				LEFT JOIN ".C('tablepre')."scm_supplier AS ss ON sii.supp_id = ss.supp_id
				WHERE
				sii.clie_id = '{$this->user_info['supp_clie_id']}'
				AND sii.waring_flag = 1 ";
        if(isset($condition['ids'])  && $condition['ids'] != ''){
            $sql.= "AND sii.id in (".$condition['ids'].") ";
        }
        $sql.= "AND (
						(
								sii.shelf_life LIKE '%年'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL (sii.shelf_life * 360) DAY
										),
										NOW()
								) <= scs.valid_remind
						)
						OR(
								sii.shelf_life LIKE '%月'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL (sii.shelf_life * 30) DAY
										),
										NOW()
								) <= scs.valid_remind
						)
						OR (
								sii.shelf_life LIKE '%天'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL sii.shelf_life DAY
										),
										NOW()
								) <= scs.valid_remind
						)
				)
				GROUP BY sii.id
              ORDER BY sii.in_stock_date DESC ";
        if(isset($condition['index']) ){
            $sql.= " limit ".$condition['index'].",".$condition['page_num'];
        }
        $stock_list =  SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        return $stock_list;
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
     * 取消预警
     */
    public function cancelWarningOp(){
        SCMModel('scm_instock_info')->editInstockInfo(array('id'=>$_GET['id'],'waring_flag'=>0));
        @header('Location: index.php?act=validity_warn&op=index');exit;
    }

    /**
     * 导出
     */
    public function export_validity_warnOp(){
        $stock_list = $this->validation_warn_sql(array('ids'=> $_GET['id']));
        $this->createValidityWarnExcel($stock_list);
    }

    private function createValidityWarnExcel($stock_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店ID');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商ID');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品条码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'进货数量');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'入库时间');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品生产日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品有效期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商联系人');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商电话');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商手机');

        //data
        foreach ((array)$stock_list as $k=>$order_info){
            $tmp = array();
            $tmp[] = array('data'=>$order_info['clie_id']);
            $tmp[] = array('data'=>$order_info['supp_id']);
            $tmp[] = array('data'=>$order_info['supp_ch_name']);
            $tmp[] = array('data'=>$order_info['goods_barcode']);
            $tmp[] = array('data'=>$order_info['goods_nm']);
            $tmp[] = array('data'=>$order_info['goods_unit']);
            $tmp[] = array('data'=>$order_info['goods_spec']);
            $tmp[] = array('data'=>$order_info['set_num']);
            $tmp[] = array('data'=>$order_info['in_stock_date']);
            $tmp[] = array('data'=>$order_info['production_date']);
            $tmp[] = array('data'=>$order_info['shelf_life']);
            $tmp[] = array('data'=>$order_info['valid_remind']);
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
