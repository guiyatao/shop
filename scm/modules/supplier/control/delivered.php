<?php
/**
已完成的订单
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class deliveredControl extends SCMControl
{
    const EXPORT_SIZE = 1000;
    protected $supp_info;

    public function __construct()
    {
        parent::__construct();
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
    }

    public function indexOp()
    {
        $this->ordersOp();
    }

    /**
     * 供应商商品展示列表
     */
    public function ordersOp()
    {
        Tpl::showpage('delivered.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_supplier_client = SCMModel('supplier_client');
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        $condition['supp_id'] = $supplier['supp_id'];
        //获取已经完成的订单
        $condition['order_status'] = array('neq',0);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,out_date,order_status,out_flag,in_flag,refund_flag';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','order_pay','order_date','order_status');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $order_list = $model_supplier_client->getOrderList($condition, $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_supplier_client->shownowpage();
        $temp_list = $model_supplier_client->gettotalnumon($condition,'order_no','scm_client_order,scm_client','order_no','scm_client_order.clie_id = scm_client.clie_id');
        $data['total_num'] = count($temp_list);
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($order_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=delivered&op=detail&order_id=" . $value['id'] . "'><i class='fa fa-pencil-square-o'></i>查看详情</a>";
            $param['number'] = $index;
            $param['order_no'] = $value['order_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = $value['clie_ch_name'];
            $param['order_pay'] = $value['order_pay'];
            $param['total_amount'] = $value['total_amount'];
            $param['order_date'] = $value['order_date'];
            if($value['order_status'] == 1)
                $param['order_status'] = "已完成";
            else if($value['order_status'] == 2)
                $param['order_status'] = "半单";
            else if($value['order_status'] == 3)
                $param['order_status'] = "订单已取消";
            else if($value['order_status'] == 4)
                $param['order_status'] = "退货成功";
            else if($value['order_status'] == 5)
                $param['order_status'] = "退货失败";
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();

    }

    /**
     * 查看每个订单详情
     */
    public function detailOp(){
        $model_supplier_client = SCMModel('supplier_client');
        $condition['order_id'] = $_GET['order_id'];
        $order = '';
        $field = 'scm_order_goods.id,order_id,order_no,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,order_num,set_num,actual_amount,goods_unit,goods_spec,min_set_num,scm_order_goods.supp_id,scm_order_goods.clie_id,produce_company,produce_area,order_date,scm_order_goods.production_date,scm_order_goods.valid_remind,scm_order_goods.shelf_life';
        $goods_list = $model_supplier_client->getGoodsList($condition, $field, null, $order);
        $order_info = $model_supplier_client->getOrderInfo(array('id'=>$_GET['order_id']),'id,supp_id,clie_id,order_no,order_date,out_date,total_amount,order_pay,prepare_flag,refund_flag,in_date,pay_start_time,order_status,gift_flag,comments' );
        $client_info = $model_supplier_client->getClientInfo(array('clie_id'=>$order_info['clie_id']));
        $supplier = $this->supp_info;
        Tpl::output('client_info',$client_info);
        Tpl::output('supplier_info',$supplier);
        Tpl::output('order_info',$order_info);
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('delivered.detail_backup');
    }

    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_supplier_client = SCMModel('supplier_client');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['scm_client_order.id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        $condition['supp_id'] = $supplier['supp_id'];
        //获取已经完成的订单
        $condition['order_status'] = array('neq',0);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = 'order_date DESC';
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,out_date,order_status,out_flag,in_flag,refund_flag,gift_flag,scm_client_order.comments';
        if (!is_numeric($_GET['curpage'])){
            $temp_list= $model_supplier_client->gettotalnum($condition);  //全部数据数量
            $count = count($temp_list);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=delivered&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }
        $order_list = $model_supplier_client->getOrderList($condition, $field, null, $order,$limit);
        $this->createExcel($order_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($order_list) {
        $data = array();
        foreach ($order_list as $value) {
            $param = array();
            $param['order_no'] = $value['order_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['order_pay'] = $value['order_pay'];
            $param['total_amount'] = $value['total_amount'];
            $param['order_date'] =  $value['order_date'];
            if($value['order_status'] == 1)
                $param['order_status'] = iconv('utf-8','gb2312', "已完成");
            else if($value['order_status'] == 2)
                $param['order_status'] = iconv('utf-8','gb2312', "半单");
            else if($value['order_status'] == 3)
                $param['order_status'] = iconv('utf-8','gb2312', "订单已取消");
            else if($value['order_status'] == 4)
                $param['order_status'] = iconv('utf-8','gb2312', "退货成功");
            else if($value['order_status'] == 5)
                $param['order_status'] = iconv('utf-8','gb2312', "退货失败");
            if($value['gift_flag'] == 1)
                $param['gift_flag'] = iconv('utf-8','gb2312', "有赠品");
            else
                $param['gift_flag'] = iconv('utf-8','gb2312', "无赠品");
            $param['comments'] =  iconv('utf-8','gb2312',$value['comments']);
            $data[$value['order_no']] = $param;
        }
        $header = array(
            "order_no" => iconv('utf-8','gb2312',"订单号"),
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "order_pay" => iconv('utf-8','gb2312', "实付款(元)"),
            "total_amount" => iconv('utf-8','gb2312', "总额(元)"),
            "order_date" => iconv('utf-8','gb2312', "订货日期"),
            "order_status" => iconv('utf-8','gb2312', "订单状态"),
            "gift_flag" => iconv('utf-8','gb2312', "有无赠品"),
            "comments" => iconv('utf-8','gb2312', "备注"),
        );
        \Shopnc\Lib::exporter()->output('order_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 生成Excel文件
     * @param $order_list
     */
    private function createExcel($order_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'实付款(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'总额(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订货日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'订单状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有无赠品');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'备注');
        //data
        foreach ((array)$order_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['order_no']);
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['clie_ch_name']);
            $tmp[] = array('data'=>$v['order_pay']);
            $tmp[] = array('data'=>$v['total_amount']);
            $tmp[] = array('data'=>$v['order_date']);
            $tmp[] = array('data'=>order::getClientOrderStatusByID($v['order_status']));
            $tmp[] = array('data'=>order::getGiftStatusByID($v['gift_flag']));
            $tmp[] = array('data'=>$v['comments']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /*
    * csv导出
    */
    public function export_detail_csvOp(){
        $model_supplier_client = SCMModel('supplier_client');
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['scm_order_goods.id'] = array('in', $id_array);
        }
        $condition['order_id'] = $_GET['order_id'];
        $order = '';
        $field = 'scm_order_goods.id,order_id,order_no,clie_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,order_num,set_num,actual_amount,goods_unit,goods_spec,min_set_num,scm_order_goods.clie_id,scm_order_goods.supp_id,produce_company,produce_area,order_date';
        $goods_list = $model_supplier_client->getGoodsList($condition, $field, null, $order);
        $this->createDetailCsv($goods_list);
    }

    /**
     * 生成csv文件
     */
    private function createDetailCsv($goods_list) {
        $data = array();
        foreach ($goods_list as $value) {
            $param = array();
            $param['order_no'] = $value['order_no'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = iconv('utf-8','gb2312',  $value['goods_nm']);
            $param['goods_price'] = $value['goods_price'];
            $param['goods_discount'] = $value['goods_discount'];
            $param['goods_discount_price'] = $value['goods_discount_price'];
            $param['goods_unit'] = iconv('utf-8','gb2312',  $value['goods_unit']);
            $param['min_set_num'] = $value['min_set_num'];
            $param['order_num'] = $value['order_num'];
            $param['set_num'] = $value['set_num'];
            $param['actual_amount'] = $value['actual_amount'];
            $param['goods_spec'] =iconv('utf-8','gb2312',  $value['goods_spec']);
            $param['produce_company'] = iconv('utf-8','gb2312',  $value['produce_company']);
            $param['produce_area'] = iconv('utf-8','gb2312',  $value['produce_area']);
            $data[$value['id']] = $param;
        }
        $header = array(
            "order_no" => iconv('utf-8','gb2312',"订单号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "goods_barcode" =>  iconv('utf-8','gb2312', "商品条码"),
            "goods_nm" =>   iconv('utf-8','gb2312', "商品名称"),
            "goods_price" =>   iconv('utf-8','gb2312', "原价(元)"),
            "goods_discount" =>   iconv('utf-8','gb2312', "折扣"),
            "goods_discount_price" =>   iconv('utf-8','gb2312', "折扣价(元)"),
            "goods_unit" =>   iconv('utf-8','gb2312', "单位"),
            "min_set_num" =>   iconv('utf-8','gb2312', "最小配量"),
            "order_num" =>   iconv('utf-8','gb2312', "订货数量"),
            "set_num" =>   iconv('utf-8','gb2312', "发货数量"),
            "actual_amount" =>   iconv('utf-8','gb2312', "实际金额(元)"),
            "goods_spec" =>   iconv('utf-8','gb2312', "规格"),
            "produce_company" =>   iconv('utf-8','gb2312', "生产厂家"),
            "produce_area" =>   iconv('utf-8','gb2312', "产地"),
        );
        \Shopnc\Lib::exporter()->output('goods_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
}