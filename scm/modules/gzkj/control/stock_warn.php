<?php
/**
 * 库存预警
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class stock_warnControl extends SCMControl
{
    const EXPORT_SIZE = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        return $this->stock_warnOp();
    }

    /**
     * 显示当前供应商的所有资金列表
     */
    public function stock_warnOp()
    {
        Tpl::showpage('stock_warn.index');
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
        $page->setEachNum($_POST['rp']);
        $page->setStyle('admin');
        $data = array();
        $page_num = $_POST['rp'];

        $data['now_page'] = $page->get('now_page');
        $index = ($data['now_page'] - 1) * $page_num;
        $good_list = $this->stock_warn_sql($clie_id, array('index' => $index, 'page_num' => $page_num));
        $data['total_num'] = count($this->stock_warn_sql());
        foreach ($good_list as $stock_id => $goods_info) {
            $list = array();
            $index++;
            $list['number'] = $index;
            $list['clie_id'] = $goods_info['clie_id'];
            $list['clie_ch_name'] = $goods_info['clie_ch_name'];
            $list['clie_contacter'] = $goods_info['clie_contacter'];
            $list['clie_mobile'] = $goods_info['clie_mobile'];
            $list['clie_tel'] = $goods_info['clie_tel'];
            $list['goods_barcode'] = $goods_info['goods_barcode'];
            $list['goods_nm'] = $goods_info['goods_nm'];
            $list['goods_unit'] = $goods_info['goods_unit'];
            $list['goods_spec'] = $goods_info['goods_spec'];
            $list['goods_stock'] = $goods_info['goods_stock'];
            $list['goods_low_stock'] = $goods_info['goods_low_stock'];
            $list['supp_ch_name'] = $goods_info['supp_ch_name'];
            $list['supp_contacter'] = $goods_info['supp_contacter'];
            $list['supp_tel'] = $goods_info['supp_tel'];
            $list['supp_mobile'] = $goods_info['supp_mobile'];
            if ($goods_info['rate'] == 1) {
                $list['operation'] = '<a class="btn "  style="background-color: #8ac43f
">&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            if ($goods_info['rate'] == 2) {
                $list['operation'] = '<a class="btn yellow"  style="background-color: yellow"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            if ($goods_info['rate'] == 3) {
                $list['operation'] = '<a class="btn red"  style="background-color: red"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            $data['list'][$goods_info['id']] = $list;
        }

        echo Tpl::flexigridXML($data);
        exit();
    }

    public function export_stock_warnOp()
    {

        $good_list = $this->stock_warn_sql('',array('ids' => $_GET['id']));
        $this->createExcel($good_list);
    }

private function createExcel($stock_list)
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
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '库存');
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '库存下限');
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商名称');
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商联系人');
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商电话');
    $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商手机');

    //data
    foreach ((array)$stock_list as $k => $order_info) {
        $tmp = array();
        $tmp[] = array('data' => $order_info['clie_id']);
        $tmp[] = array('data' => $order_info['clie_ch_name']);
        $tmp[] = array('data' => $order_info['clie_contacter']);
        $tmp[] = array('data' => $order_info['clie_mobile']);
        $tmp[] = array('data' => $order_info['clie_tel']);
        $tmp[] = array('data' => $order_info['goods_barcode']);
        $tmp[] = array('data' => $order_info['goods_nm']);
        $tmp[] = array('data' => $order_info['goods_unit']);
        $tmp[] = array('data' => $order_info['goods_spec']);
        $tmp[] = array('data' => $order_info['goods_stock']);
        $tmp[] = array('data' => $order_info['goods_low_stock']);
        $tmp[] = array('data' => $order_info['supp_ch_name']);
        $tmp[] = array('data' => $order_info['supp_contacter']);
        $tmp[] = array('data' => $order_info['supp_tel']);
        $tmp[] = array('data' => $order_info['supp_mobile']);
        $excel_data[] = $tmp;
    }
    $excel_data = $excel_obj->charset($excel_data, CHARSET);
    $excel_obj->addArray($excel_data);
    $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'), CHARSET));
    $excel_obj->generateXML('stock_warn-' . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
}

private
function stock_warn_sql($clie_id = '', $condition = array())
{
    $order_goods = SCMModel('gzkj_order_goods');
    $pre = C('tablepre');
    if ($clie_id) {
        $sql = "SELECT
	d.id,
	d.clie_id,
	b.clie_ch_name,
	b.clie_contacter,
	b.clie_mobile,
	b.clie_tel,
	d.goods_barcode,
	d.goods_nm,
	d.goods_unit,
	d.goods_spec,
	d.goods_stock,
	d.goods_low_stock,
	c.supp_ch_name,
	c.supp_contacter,
	c.supp_tel,
	c.supp_mobile,
	CASE
WHEN  d.goods_stock / d.goods_low_stock > 0.7  THEN 1
WHEN d.goods_stock / d.goods_low_stock >= 0.3&& d.goods_stock / d.goods_low_stock <= 0.7  THEN 2
WHEN d.goods_stock / d.goods_low_stock <0.3  THEN 3
END AS rate
FROM
	" . $pre . "scm_client AS b,
	" . $pre . "scm_supplier AS c,
	" . $pre . "scm_client_stock AS d
WHERE
d.goods_stock / d.goods_low_stock<=1
AND
    d.clie_id='" . $clie_id . "'
AND d.clie_id = b.clie_id
AND d.supp_id = c.supp_id ";
    } else {
        $sql = "SELECT
	d.id,
	d.clie_id,
	b.clie_ch_name,
	b.clie_contacter,
	b.clie_mobile,
	b.clie_tel,
	d.goods_barcode,
	d.goods_nm,
	d.goods_unit,
	d.goods_spec,
	d.goods_stock,
	d.goods_low_stock,
	c.supp_ch_name,
	c.supp_contacter,
	c.supp_tel,
	c.supp_mobile,
	CASE
WHEN  d.goods_stock / d.goods_low_stock > 0.7  THEN 1
WHEN d.goods_stock / d.goods_low_stock >= 0.3&& d.goods_stock / d.goods_low_stock <= 0.7  THEN 2
WHEN d.goods_stock / d.goods_low_stock <0.3  THEN 3
END AS rate
FROM

	" . $pre . "scm_client AS b,
	" . $pre . "scm_supplier AS c,
	" . $pre . "scm_client_stock AS d
WHERE
d.goods_stock / d.goods_low_stock<=1
AND
 d.clie_id = b.clie_id
AND d.supp_id = c.supp_id ";
    }
    if(isset($condition['ids'])  && $condition['ids'] != ''){
        $sql.= "AND d.id in (".$condition['ids'].") ";
    }
    if (isset($condition['index'])) {
        $sql .= " limit " . $condition['index'] . "," . $condition['page_num'];
    }
    $good_list = $order_goods->execute_sql($sql);
    return $good_list;
}
}