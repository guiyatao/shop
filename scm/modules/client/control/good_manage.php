<?php
/**
 * 商品管理
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

    protected $user_info;

    public function __construct(){
        parent::__construct();
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
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
     * 新增商品
     */
    public function goods_addOp(){
        $model = SCMModel('scm_client_stock');
        if (chksubmit()) {
            $goods = array();
            $goods['goods_nm'] = trim($_POST['goods_nm']);
            $goods['goods_barcode'] = trim($_POST['goods_barcode']);
            $goods['supp_id'] = trim($_POST['supp_id']);
            $goods['goods_spec'] = trim($_POST['goods_spec']);
            $goods['goods_price'] = trim($_POST['goods_price']);
            $goods['goods_unit'] = trim($_POST['goods_unit']);
            $goods['goods_stock'] = trim($_POST['goods_stock']);
            $goods['goods_stock'] = trim($_POST['goods_stock']);
            $goods['goods_low_stock'] = trim($_POST['goods_low_stock']);
            $goods['production_date'] = $_POST['production_date'];
            $goods['valid_remind'] = trim($_POST['valid_remind']);
            $goods['shelf_life'] = trim($_POST['shelf_life']).$_POST['shelf_life_unit'] ;
            $goods['drug_remind'] = trim($_POST['drug_remind']);
            $goods['clie_id'] = $this->user_info['supp_clie_id'];
            $result =$model->addNewGoodsStock($goods);
            if($result)
                showDialog(L('nc_common_op_succ'), urlSCMClient('good_manage', 'index'), 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
            else
                showDialog(L('nc_common_op_succ'), urlSCMClient('good_manage', 'index'), 'error', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
        }
        $supp_list =  SCMModel('scm_supp_client')->getAllSupplier();
        Tpl::output('supp_list',$supp_list);
        Tpl::showpage('goods_add');
    }

    /**
     * 编辑商品
     */
    public function goods_editOp(){
        $model = SCMModel('scm_client_stock');
        if (chksubmit()) {
            $result = $model-> editGoods(array(
                'id'=> $_POST['id'],
                'goods_nm' => trim($_POST['goods_nm']),
                'goods_barcode' => trim($_POST['goods_barcode']),
                'goods_spec' => trim($_POST['goods_spec']),
                'supp_id' => trim($_POST['supp_id']),
                'goods_price' => trim($_POST['goods_price']),
                'goods_unit' => trim($_POST['goods_unit']),
                'goods_stock' => trim($_POST['goods_stock']),
                'goods_stock' => trim($_POST['goods_stock']),
                'goods_low_stock' => trim($_POST['goods_low_stock']),
                'production_date' => $_POST['production_date'],
                'valid_remind' => trim($_POST['valid_remind']),
                'shelf_life' => trim($_POST['shelf_life']).$_POST['shelf_life_unit'] ,
                'drug_remind' => trim($_POST['drug_remind']),
            ));
            if($result)
                showDialog(L('nc_common_op_succ'), urlSCMClient('good_manage', 'index'), 'succ', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
            else
                showDialog('操作失败', urlSCMClient('good_manage', 'index'), 'error', '$("#flexigrid").flexReload();CUR_DIALOG.close()');
        }
        $goods_info = $model-> getGoodsInfoById($_GET['id']);
        if($goods_info){
            Tpl::output('goods_info', $goods_info);
            $supp_list =  SCMModel('scm_supp_client')->getAllSupplier();
            Tpl::output('supp_list',$supp_list);
            $number = $this->findNum($goods_info['shelf_life']);
            Tpl::output('shelf_life',$number);
            $shelf_life_unit = str_replace($number,'',$goods_info['shelf_life']);
            Tpl::output('shelf_life_unit',$shelf_life_unit);
            Tpl::showpage('goods_edit');
        }
    }

    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_goods = SCMModel('scm_client_stock');
        switch ($_GET['branch']){
            /**
             * 验证商品名称是否重复
             *
             */
            case 'check_goods_name':
                $condition['goods_nm']   = $_GET['goods_name'];
                $condition['id'] = array('neq',intval($_GET['goods_id']));
                $condition['clie_id'] = $this->user_info['supp_clie_id'];
                $goods = $model_goods->getClientGoodInfo($condition);
                if (empty($goods)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
            /**
             * 验证当前供应商商品的条形码是否重复,不同供应商的商品条码不能重复
             */
            case 'check_goods_barcode':
                $condition['goods_barcode']   = $_GET['goods_barcode'];
                $condition['id'] = array('neq',intval($_GET['goods_id']));
                $condition['clie_id'] = $this->user_info['supp_clie_id'];
                $goods = $model_goods->getClientGoodInfo($condition);
                if (empty($goods)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;

        }
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

    /**
     * 删除商品
     */
    public function goods_delOp(){
        $model_goods = SCMModel('scm_client_stock');
        if ($_GET['id'] != '') {
            if($model_goods->delGoodsByIdString($_GET['id'])){
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            }
            else
                exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
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
    public function export_step1Op(){
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
            $temp_list = $model_order->getStockList($condition,null,'*',$order);
            $count = count($temp_list);
            $array = array();
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=good_manage&op=index');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_order->getStockList($condition,null,'*',$order,self::EXPORT_SIZE);
                $this->createExcel($data);
            }
        } else {
            $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $stock_list = $model_order->getStockList($condition,null,'*',$order,"{$limit1},{$limit2}");
            $this->createExcel($stock_list);
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
