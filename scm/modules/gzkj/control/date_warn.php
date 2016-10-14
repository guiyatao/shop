<?php
/**
 * 有效期预警
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class date_warnControl extends SCMControl
{
    const EXPORT_SIZE = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        return $this->date_warnOp();
    }

    /**
     *
     */
    public function date_warnOp()
    {
        Tpl::showpage('date_warn.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp()
    {
        if (strlen($q = trim($_REQUEST['query'])) > 0) {
            switch ($_REQUEST['qtype']) {
                case 'clie_id':
                    $clie_id = $q;
                    break;
            }
        }
        $page = new Page();
        $page->setEachNum($_POST['rp']) ;
        $page->setStyle('admin') ;
        $data = array();
        $page_num = $_POST['rp'];
        $data['now_page'] = $page->get('now_page');
        $index = ($data['now_page'] - 1) * $page_num;
        $instock_list = $this->date_warn_sql($clie_id,array('index'=> $index, 'page_num'=> $page_num ));
        $data['total_num'] = count($this->date_warn_sql());

        foreach ($instock_list as $stock_id => $stock_info) {
            $list = array();
            $index++;
            $list['number'] =$index;
            $list['clie_id'] = $stock_info['clie_id'];
            $list['clie_ch_name'] = $stock_info['clie_ch_name'];
            $list['clie_contacter'] = $stock_info['clie_contacter'];
            $list['clie_mobile'] = $stock_info['clie_mobile'];
            $list['clie_tel'] = $stock_info['clie_tel'];
            $list['goods_barcode'] = $stock_info['goods_barcode'];
            $list['goods_nm'] = $stock_info['goods_nm'];
            $list['goods_unit'] = $stock_info['goods_unit'];
            $list['goods_spec'] = $stock_info['goods_spec'];
            $list['production_date'] = $stock_info['production_date'];
            $list['shelf_life'] = $stock_info['expire_date'];
            $list['valid_remind'] = $stock_info['valid_remind'];
            $list['supp_ch_name'] = $stock_info['supp_ch_name'];
            $list['supp_contacter'] = $stock_info['supp_contacter'];
            $list['supp_tel'] = $stock_info['supp_tel'];
            $list['supp_mobile'] = $stock_info['supp_mobile'];
            $data['list'][$stock_info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
    }
    /**
     * 提取字符串中所有的数字
     * @param string $str
     * @return string
     */
    private function findNum($str=''){
        $str=trim($str);
        if(empty($str)){return '';}
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(is_numeric($str[$i])){
                $result.=$str[$i];
            }
        }
        return $result;
    }


    private function date_warn_sql($clie_id='',$condition = array()){
        $pre=C('tablepre');

        if($clie_id){
            $sql ="SELECT
    a.id,
	a.clie_id,b.clie_ch_name,b.clie_contacter,b.clie_mobile,b.clie_tel,a.goods_barcode,
	a.goods_nm,a.goods_unit,a.goods_spec,a.production_date,a.shelf_life,a.waring_flag,
	c.supp_ch_name,c.supp_contacter,c.supp_tel,c.supp_mobile,d.valid_remind,
CASE
WHEN a.shelf_life LIKE '%年'  THEN DATE_ADD( a.production_date, INTERVAL (a.shelf_life * 360) DAY )
WHEN a.shelf_life LIKE '%月'  THEN DATE_ADD( a.production_date, INTERVAL (a.shelf_life * 30) DAY )
WHEN a.shelf_life LIKE '%天'  THEN DATE_ADD( a.production_date, INTERVAL a.shelf_life DAY )
END AS expire_date
FROM
	".$pre."scm_instock_info AS a,
	".$pre."scm_client AS b,
	".$pre."scm_supplier AS c,
	".$pre."scm_client_stock AS d
WHERE
a.waring_flag=1
AND
    a.goods_barcode=d.goods_barcode
AND
    a.clie_id='".$clie_id."'
AND
	a.clie_id = b.clie_id
AND
    a.supp_id = c.supp_id
AND
(
	(
		a.shelf_life LIKE '%月'
		AND datediff(
			DATE_ADD(
				a.production_date,
				INTERVAL (a.shelf_life * 30) DAY
			),
			NOW()
		) <= d.valid_remind
	)
	OR (
		a.shelf_life LIKE '%天'
		AND datediff(
			DATE_ADD(
				a.production_date,
				INTERVAL a.shelf_life DAY
			),
			NOW()
		) <= d.valid_remind
	)
)
 ";

        }else{
            $sql ="
SELECT
    a.id,
	a.clie_id,b.clie_ch_name,b.clie_contacter,b.clie_mobile,b.clie_tel,a.goods_barcode,
	a.goods_nm,a.goods_unit,a.goods_spec,a.production_date,a.shelf_life,a.waring_flag,
	c.supp_ch_name,c.supp_contacter,c.supp_tel,c.supp_mobile,d.valid_remind,
	CASE
WHEN a.shelf_life LIKE '%年'  THEN DATE_ADD( a.production_date, INTERVAL (a.shelf_life * 360) DAY )
WHEN a.shelf_life LIKE '%月'  THEN DATE_ADD( a.production_date, INTERVAL (a.shelf_life * 30) DAY )
WHEN a.shelf_life LIKE '%天'  THEN DATE_ADD( a.production_date, INTERVAL a.shelf_life DAY )
END AS expire_date
FROM
	".$pre."scm_instock_info AS a,
	".$pre."scm_client AS b,
	".$pre."scm_supplier AS c,
	".$pre."scm_client_stock AS d
WHERE
a.waring_flag=1
AND
    a.goods_barcode=d.goods_barcode
AND
	a.clie_id = b.clie_id
AND a.supp_id = c.supp_id
AND (
	(
		a.shelf_life LIKE '%月'
		AND datediff(
			DATE_ADD(
				a.production_date,
				INTERVAL (a.shelf_life * 30) DAY
			),
			NOW()
		) <= d.valid_remind
	)
	OR (
		a.shelf_life LIKE '%天'
		AND datediff(
			DATE_ADD(
				a.production_date,
				INTERVAL a.shelf_life DAY
			),
			NOW()
		) <= d.valid_remind
	)
)   ";

        }
        if(isset($condition['ids'])  && $condition['ids'] != ''){
            $sql.= "AND a.id in (".$condition['ids'].") ";
        }
        $sql.="GROUP BY a.id";
        if(isset($condition['index']) ){
            $sql.= " limit ".$condition['index'].",".$condition['page_num'];
        }
        $model_instock = SCMModel('gzkj_instock_info');
        $instock_list = $model_instock->execute_sql($sql);
        return$instock_list;
    }
    public function export_date_warnOp()
    {

        $instock_list = $this->date_warn_sql('',array('ids' => $_GET['id']));
        $this->createExcel($instock_list);
    }

    private function createExcel($instock_list)
    {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店编号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店联系人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店电话');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品条码');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '单位');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '规格');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品生产日期');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品有效期');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品有效期预警天数');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商联系人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商电话');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商手机');

        //data
        foreach ((array)$instock_list as $k => $stock_info) {
            $tmp = array();
            $tmp[] = array('data' => $stock_info['clie_id']);
            $tmp[] = array('data' => $stock_info['clie_ch_name']);
            $tmp[] = array('data' => $stock_info['clie_contacter']);
            $tmp[] = array('data' => $stock_info['clie_mobile']);
            $tmp[] = array('data' => $stock_info['clie_tel']);
            $tmp[] = array('data' => $stock_info['goods_barcode']);
            $tmp[] = array('data' => $stock_info['goods_nm']);
            $tmp[] = array('data' => $stock_info['goods_unit']);
            $tmp[] = array('data' => $stock_info['goods_spec']);
            $tmp[] = array('data' => $stock_info['production_date']);
            $tmp[] = array('data' => $stock_info['expire_date']);
            $tmp[] = array('data' => $stock_info['valid_remind']);
            $tmp[] = array('data' => $stock_info['supp_ch_name']);
            $tmp[] = array('data' => $stock_info['supp_contacter']);
            $tmp[] = array('data' => $stock_info['supp_tel']);
            $tmp[] = array('data' => $stock_info['supp_mobile']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'), CHARSET));
        $excel_obj->generateXML('date_warn-' . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }
}