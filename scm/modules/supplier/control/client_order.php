<?php
/**
 供应商接受终端店的订单
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class client_orderControl extends SCMControl
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
        Tpl::showpage('orders.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_supplier_client = SCMModel('supplier_client');
        //获取当前管理员
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        $condition['supp_id'] = $supplier['supp_id'];
        //默认只获取没有完成的订单
        $condition['order_status'] = 0;
        //只获取未发货的订单
        $condition['out_flag'] = 0;
        //只获取已经付款的订单
        $condition['pay_flag'] = 1;

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        //print_r($condition);die;
        $order = '';
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,order_status,out_flag,prepare_flag ';
       // $field = 'id,order_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,order_num,set_num,goods_unit,goods_spec,order_pay,order_date,valid_date,order_flag,out_flag,in_flag,pay_flag,order_status,comments';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','order_pay','order_date');
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
        //print_r($order_list);
        foreach ($order_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=client_order&op=detail&order_id=" . $value['id'] . "'><i class='fa fa-pencil-square-o'></i>查看详情</a>";
            $param['number'] = $index;
            $param['order_no'] = $value['order_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = $value['clie_ch_name'];
            $param['order_pay'] = $value['order_pay'];
            $param['total_amount'] = $value['total_amount'];
            $param['order_date'] = $value['order_date'];
            $param['order_status'] = "未完成";
            if($value['prepare_flag'] == 0)
                $param['prepare_flag'] = "未备货";
            else if($value['prepare_flag'] == 1)
                $param['prepare_flag'] = "备货完成";
            $param['out_flag'] = "未发货";
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();

    }

    /**
     * 查看每个订单详情，发货
     */
    public function detailOp(){
        $model_supplier_client = SCMModel('supplier_client');
        $model_supplier_goods = SCMModel('supplier_goods');
        if (chksubmit()) {
            if( intval($_POST['prepare_flag']) == 0){  //备货完成
                $total_amount = 0;
                foreach($_POST['ids'] as $k => $v){
                    $condition = array();
                    $condition['id'] = intval($v);
                    $field = 'goods_barcode,goods_price,goods_discount,min_set_num,order_num';
                    $result = $model_supplier_client->getGoodsInfo($condition,$field);
                    if(!empty($result)){
//                        $set_num = intval($_POST['set_num'][$k]);
//                        if($set_num < $result['min_set_num'])
//                            $set_num = $result['min_set_num'];
                        $set_num = intval($result['order_num']);
                        $condition['set_num'] = $set_num;
                        $condition['actual_amount'] = $result['goods_price']*$result['goods_discount']*$set_num;
                        $condition['production_date'] = $_POST['production_date'][$k];
                        $condition['valid_remind'] = $_POST['valid_remind'][$k];
                        $condition['shelf_life'] = $_POST['shelf_life'][$k];

                        $total_amount = $total_amount + $condition['actual_amount'];
                        $model_supplier_client->updateGoods($condition);
                    }
                }

                $model_supplier_client->updateOrder(array('id' => $_POST['order_id'],'prepare_flag'=>1,'gift_flag'=>$_POST['gift_flag'],'comments'=>trim($_POST['comments'])));
                $url = array(
                    array(
                        'url'=>'index.php?act=client_order&op=index',
                        'msg'=>"返回未发货列表",
                    )
                );
                $this->log('订单ID['.$_POST['order_id'].']备货完成',1);
                showMessage("备货完成",$url);
            }else{    //发货
                $total_amount = 0;
                foreach($_POST['ids'] as $k => $v){
                    $condition = array();
                    $condition['id'] = intval($v);
                    $field = 'goods_barcode,goods_price,goods_discount,min_set_num,order_num';
                    $result = $model_supplier_client->getGoodsInfo($condition,$field);
                    if(!empty($result)){
//                        $set_num = intval($_POST['set_num'][$k]);
//                        if($set_num < $result['min_set_num'])
//                            $set_num = $result['min_set_num'];
                        $set_num = intval($result['order_num']);
                        $condition['set_num'] = $set_num;
                        $condition['actual_amount'] = $result['goods_price']*$result['goods_discount']*$set_num;
                        $condition['production_date'] = $_POST['production_date'][$k];
                        $condition['valid_remind'] = $_POST['valid_remind'][$k];
                        $condition['shelf_life'] = $_POST['shelf_life'][$k];

                        $total_amount = $total_amount + $condition['actual_amount'];
                        $model_supplier_client->updateGoods($condition);
                    }
                }
                $model_supplier_client->updateOrder(array('id' => $_POST['order_id'],'out_flag'=>1,'total_amount'=>$total_amount,'out_date'=> date("Y-m-d h:i:s"),'gift_flag'=>$_POST['gift_flag'],'comments'=>trim($_POST['comments'])));
                $url = array(
                    array(
                        'url'=>'index.php?act=client_order&op=index',
                        'msg'=>"返回未发货列表",
                    )
                );
                $this->log('订单ID['.$_POST['order_id'].']已发货',1);
                showMessage("发货成功",$url);
            }
        }

        $condition['order_id'] = $_GET['order_id'];
        $order = '';
        $field = 'scm_order_goods.id,order_id,order_no,scm_order_goods.clie_id,clie_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,order_num,set_num,actual_amount,goods_unit,goods_spec,min_set_num,scm_order_goods.clie_id,scm_order_goods.supp_id,produce_company,produce_area,order_date,scm_order_goods.production_date,scm_order_goods.valid_remind,scm_order_goods.shelf_life';
        $goods_list = $model_supplier_client->getGoodsList($condition, $field, null, $order);
        $total_amount = 0;
        $order_info = $model_supplier_client->getOrderInfo(array('id'=>$_GET['order_id']),'id,order_no,order_date,total_amount,order_pay,prepare_flag,gift_flag,comments' );
        foreach($goods_list as $k => $v){
            $total_amount = $total_amount + $v['actual_amount'];
            if($order_info['prepare_flag'] != 1){  //还未备货前用的是供应商默认的生产日期
                $goodsInfo = $model_supplier_goods->getGoodsInfo(array('goods_barcode' => $v['goods_barcode'],'supp_id'=>$this->supp_info['supp_id']), 'production_date,valid_remind,shelf_life');
                $goods_list[$k]['production_date'] = $goodsInfo['production_date'];   //发货前默认生产日期
                $goods_list[$k]['valid_remind'] = $goodsInfo['valid_remind'];    //发货前默认有效期提醒天数
                $goods_list[$k]['shelf_life'] = $goodsInfo['shelf_life'];      //发货前默认的保质期
            }
            $clie_id = $v['clie_id'];
        }
        $client_info = $model_supplier_client->getClientInfo(array('clie_id'=>$clie_id));
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        Tpl::output('client_info',$client_info);
        Tpl::output('supplier_info',$supplier);
        Tpl::output('total_amount',$total_amount);
        Tpl::output('goods_list',$goods_list);
        Tpl::output('order_info',$order_info);
        //Tpl::showpage('orders.detail');
        Tpl::showpage('orders.detail_backup');
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
        $order = '';

        //获取当前管理员
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        $condition['supp_id'] = $supplier['supp_id'];
        //默认只获取没有完成的订单
        $condition['order_status'] = 0;
        //只获取未发货的订单
        $condition['out_flag'] = 0;
        //只获取已经付款的订单
        $condition['pay_flag'] = 1;
        $field = 'scm_client_order.id,order_no,scm_client_order.clie_id,scm_client.clie_ch_name,supp_id,order_pay,total_amount,order_date,order_status,prepare_flag,out_flag,gift_flag,scm_client_order.comments';
        $order = 'order_date DESC';
        if (!is_numeric($_GET['curpage'])){
            $count = $model_supplier_client->getOrderCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=client_order&op=index');
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
            $param['order_status'] = iconv('utf-8','gb2312', "未完成");
            if($value['prepare_flag'] == 0)
                $param['prepare_flag'] = iconv('utf-8','gb2312', "未备货");
            else if($value['prepare_flag'] == 1)
                $param['prepare_flag'] = iconv('utf-8','gb2312', "备货完成");
            $param['out_flag'] = iconv('utf-8','gb2312', "未发货");
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
            "prepare_flag" => iconv('utf-8','gb2312', "备货状态"),
            "out_flag" => iconv('utf-8','gb2312', "发货状态"),
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'备货状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'发货状态');
        //data
        foreach ((array)$order_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['order_no']);
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['clie_ch_name']);
            $tmp[] = array('data'=>$v['order_pay']);
            $tmp[] = array('data'=>$v['total_amount']);
            $tmp[] = array('data'=>$v['order_date']);
            $tmp[] = array('data'=>'未完成');
            if($v['prepare_flag'] == 0)
                $prepare_flag = "未备货";
            else if($v['prepare_flag'] == 1)
                $prepare_flag = "备货完成";
            $tmp[] = array('data'=>$prepare_flag);
            $tmp[] = array('data'=>'未发货');
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
        $condition = array();
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
            $param['order_date'] = $value['order_date'];
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
            "set_num" =>  iconv('utf-8','gb2312', "发货数量"),
            "actual_amount" => iconv('utf-8','gb2312', "实际金额(元)"),
            "goods_spec" =>   iconv('utf-8','gb2312', "规格"),
            "produce_company" =>   iconv('utf-8','gb2312', "生产厂家"),
            "produce_area" =>   iconv('utf-8','gb2312', "产地"),
            "order_date" =>   iconv('utf-8','gb2312', "订货日期"),
        );
        \Shopnc\Lib::exporter()->output('order_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
}