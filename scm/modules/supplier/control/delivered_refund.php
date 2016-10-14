<?php
/**
供应商接受终端店已入库的退货单
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class delivered_refundControl extends SCMControl
{
    const EXPORT_SIZE = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->refundOp();
    }

    /**
     * 供应商已入库退货单展示列表
     */
    public function refundOp()
    {
        Tpl::showpage('delivered_refund.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp()
    {
        $model_supplier = SCMModel('supplier_account');
        $model_supplier_client = SCMModel('supplier_client');
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //获取当前管理员所在的供应商
        $supplier = $model_supplier->getSupplier(array('admin.admin_id'=>$adminInfo['id']));
        $condition['supp_id'] = $supplier['supp_id'];

        //获取所有合作终端店的退货单(退货单中只要有一个需要退货则整个退货单都需要显示)
        //获取已经申请退货的订单
        $condition['refund_flag'] = 0;   //未受理退货

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';

        $field = 'refund_no,supp_id,supp_ch_name,clie_id,clie_ch_name, sum(refund_amount) as total,refund_date';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','refund_date');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $refund_list = $model_supplier_client->getRefundListGroupBy($condition,'refund_no', $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_supplier_client->shownowpage();
        $temp_list = $model_supplier_client->gettotalnum($condition,'refund_no','scm_client_refund','refund_no');
        $data['total_num'] = count($temp_list);
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($refund_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=delivered_refund&op=detail&refund_no=" . $value['refund_no'] . "'><i class='fa fa-pencil-square-o'></i>查看详情</a>";
            $param['number'] = $index;
            $param['refund_no'] = $value['refund_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = $value['clie_ch_name'];
            $param['supp_id'] = $value['clie_id'];
            $param['supp_ch_name'] = $value['clie_ch_name'];
            $param['total'] = $value['total'];
            $param['refund_date'] = $value['refund_date'];
            $data['list'][$value['refund_no']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /*
     * 查看每个退货单详情
     */
    public function detailOp(){
        $model_supplier = SCMModel('supplier_account');
        $model_supplier_client = SCMModel('supplier_client');
        if (chksubmit()) {
            foreach($_POST['ids'] as $k => $v){
//                if($_POST['allow_refund_'.$v]) {
//                    foreach ($_POST['allow_refund_'.$v] as $kk => $vv) {
//                        if($v != null){
//                            $condition['id'] = $v;
//                            $condition['refund_flag'] = $vv;
//                            $condition['refund_date'] = date("Y-m-d h:i:s");
//                            $model_supplier_client->updateRefund($condition);
//                        }
//                    }
//                }
                $condition['id'] = $v;
                $condition['refund_flag'] = $_POST['allow_refund'];
                $condition['refund_date'] = date("Y-m-d h:i:s");
                $model_supplier_client->updateRefund($condition);
            }
            $url = array(
                array(
                    'url'=>'index.php?act=delivered_refund&op=index',
                    'msg'=>"返回已入库退货单列表",
                )
            );
            showMessage("返回已入库退货单列表",$url);
        }
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //获取当前管理员所在的供应商
        $supplier = $model_supplier->getSupplier(array('admin.admin_id'=>$adminInfo['id']));
        $condition['supp_id'] = $supplier['supp_id'];
        $condition['refund_no'] = $_GET['refund_no'];
        $condition['refund_flag'] = 0; //还未允许退货
        $order = '';
        $field = 'id,refund_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,refund_num,goods_unit,goods_spec,refund_flag,refund_amount,refund_date';
        $refund_list = $model_supplier_client->getRefundList($condition, $field, null, $order);
        Tpl::output('refund_list',$refund_list);
        Tpl::output('refund_no',$_GET['refund_no']);
        Tpl::showpage('delivered_refund.detail');
    }

    /**
     * 导出数据
     */
    public function export_csvOp(){
        $model_supplier = SCMModel('supplier_account');
        $model_supplier_client = SCMModel('supplier_client');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['order_no'] = array('in', $id_array);
        }
        $order = '';
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //获取当前管理员所在的供应商
        $supplier = $model_supplier->getSupplier(array('admin.admin_id'=>$adminInfo['id']));
        $condition['supp_id'] = $supplier['supp_id'];
        //获取所有合作终端店的退货单(退货单中只要有一个需要退货则整个退货单都需要显示)
        //获取已经申请退货的订单
        $condition['refund_flag'] = 0;   //未受理退货
        $field = 'refund_no,supp_id,supp_ch_name,clie_id,clie_ch_name, sum(refund_amount) as total,refund_date';

        if (!is_numeric($_GET['curpage'])){
            $temp_list = $model_supplier_client->gettotalnum($condition,'refund_no','scm_client_refund','refund_no');
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
                Tpl::output('murl','index.php?act=delivering_refund&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }
        $refund_list = $model_supplier_client->getRefundListGroupBy($condition,'refund_no', $field, null, $order,$limit);
        $this->createCsv($refund_list);
    }

    /**
     * 生成csv文件
     */
    private function createCsv($refund_list) {
        $data = array();
        foreach ($refund_list as $value) {
            $param = array();
            $param['refund_no'] = $value['refund_no'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['supp_id'] = $value['clie_id'];
            $param['supp_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['total'] = $value['total'];
            $param['refund_date'] =  $value['refund_date'];
            $data[$value['refund_no']] = $param;
        }
        $header = array(
            "refund_no" => iconv('utf-8','gb2312',"退单号"),
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "supp_id" => iconv('utf-8','gb2312', "供应商编号"),
            "supp_ch_name" => iconv('utf-8','gb2312', "供应商名称"),
            "total" => iconv('utf-8','gb2312', "总额(元)"),
            "refund_date" => iconv('utf-8','gb2312', "退货日期"),
        );
        \Shopnc\Lib::exporter()->output('order_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /*
  * csv导出
  */
    public function export_detail_csvOp(){
        $model_supplier = SCMModel('supplier_account');
        $model_supplier_client = SCMModel('supplier_client');
        $condition = array();
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['id'] = array('in', $id_array);
        }
        $condition['refund_no'] = $_GET['refund_no'];
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //获取当前管理员所在的供应商
        $supplier = $model_supplier->getSupplier(array('admin.admin_id'=>$adminInfo['id']));
        $condition['supp_id'] = $supplier['supp_id'];
        $condition['refund_no'] = $_GET['refund_no'];
        //默认只获取没有完成的订单
        $condition['refund_flag'] = 0;
        $order = '';
        $field = 'id,refund_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_discount_price,refund_num,goods_unit,goods_spec,refund_flag,refund_amount,refund_date';
        $refund_list = $model_supplier_client->getRefundList($condition, $field, null, $order);
        $this->createDetailCsv($refund_list);
    }

    /**
     * 生成csv文件
     */
    private function createDetailCsv($refund_list) {
        $data = array();
        foreach ($refund_list as $value) {
            $param = array();
            $param['refund_no'] = $value['refund_no'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = iconv('utf-8','gb2312',  $value['goods_nm']);
            $param['goods_price'] = $value['goods_price'];
            $param['goods_discount'] = $value['goods_discount'];
            $param['goods_discount_price'] = $value['goods_discount_price'];
            $param['goods_unit'] = iconv('utf-8','gb2312',  $value['goods_unit']);
            $param['refund_num'] = $value['refund_num'];
            $param['goods_discount_price'] = $value['goods_discount_price'];
            $param['goods_spec'] =iconv('utf-8','gb2312',  $value['goods_spec']);
            $param['supp_ch_name'] = iconv('utf-8','gb2312',  $value['supp_ch_name']);
            $param['refund_date'] = $value['refund_date'];
            $data[$value['id']] = $param;
        }
        $header = array(
            "refund_no" => iconv('utf-8','gb2312',"退货单号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "goods_barcode" =>  iconv('utf-8','gb2312', "商品条码"),
            "goods_nm" =>   iconv('utf-8','gb2312', "商品名称"),
            "goods_price" =>   iconv('utf-8','gb2312', "原价(元)"),
            "goods_discount" =>   iconv('utf-8','gb2312', "折扣"),
            "goods_discount_price" =>   iconv('utf-8','gb2312', "折扣价(元)"),
            "goods_unit" =>   iconv('utf-8','gb2312', "单位"),
            "refund_num" =>   iconv('utf-8','gb2312', "退货数量"),
            "goods_discount_price" =>   iconv('utf-8','gb2312', "退款金额(元)"),
            "goods_spec" =>   iconv('utf-8','gb2312', "规格"),
            "supp_ch_name" =>   iconv('utf-8','gb2312', "供应商名"),
            "refund_date" =>  iconv('utf-8','gb2312', "订货日期"),

        );
        \Shopnc\Lib::exporter()->output('order_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
}