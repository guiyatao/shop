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
class supp_stockControl extends SCMControl{
    const EXPORT_SIZE = 1000;
    public function __construct(){
        parent::__construct();
    }
    public function indexOp() {
        return $this->showOp();
    }
    /**
     * 账户维护
     */
    public function showOp(){
        Tpl::showpage('supp_stock.index');
    }
    public function get_xmlOp(){
        $supp_stock=SCMModel('gzkj_supp_stock');
        $condition=array();
        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['status']))) {
                $condition['scm_supp_stock.status'] = (int) $q;
            }
        } else{
            if ($_POST['query'] != '') {
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }
        }
        $field='scm_supp_stock.id,scm_supp_stock.supp_id,scm_supplier.supp_ch_name,scm_supp_stock.goods_nm,scm_supp_stock.goods_barcode,scm_supp_stock.goods_price,scm_supp_stock.goods_discount,scm_supp_stock.goods_unit,scm_supp_stock.min_set_num,scm_supp_stock.goods_spec,scm_supp_stock.production_date,scm_supp_stock.valid_remind,scm_supp_stock.shelf_life,scm_supp_stock.status';
        $supp_stocks=$supp_stock->getStockAndSupp($condition,$field,$_POST['rp']);
        $data = array();
        $data['now_page'] = $supp_stock->shownowpage();
        $data['total_num'] = $supp_stock->gettotalnum();
        foreach ($supp_stocks as $k => $info) {
            $list = array();
            $model = SCMModel('gzkj_supplier');
            $is_close=$model->getfby_supp_id($info['supp_id'],'is_close');
            if($is_close){
                if($info['status']==0||$info['status']==2||$info['status']==3){
                    $o = '<span class="no"><em><i class="fa fa-ban" ></i>设置&nbsp;&nbsp;&nbsp;<i class="arrow"></i></em>';
                    $o .= '</span>';
                }
            }else{
                if($info['status']==2){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=supp_stock&op=deal&state=1&id=' .
                        $info['id'] .
                        '">通过</a></li>';
                    $o .= '<li><a href="index.php?act=supp_stock&op=deal&state=3&id=' .
                        $info['id'] .
                        '">拒绝</a></li>';
                    $o .= '</ul></span>';
                }
                if($info['status']==1){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=supp_stock&op=deal&state=3&id=' .
                        $info['id'] .
                        '">拒绝</a></li>';
                    $o .= '</ul></span>';
                }
                if($info['status']==3){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=supp_stock&op=deal&state=1&id=' .
                        $info['id'] .
                        '">通过</a></li>';
                    $o .= '</ul></span>';
                }
                if($info['status']==0){
                    $o = '<span class="no"><em style="height: 20px; padding: 1px 6px;"><i class="fa fa-ban" ></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="#"></a></li>';
                    $o .= '</ul></span>';
                }
            }
            $list['operation'] =$o ;
            if($info['status']==2){
                $list['status'] = '未审核';
            }elseif($info['status']==1){
                $list['status']='通过';
            }elseif($info['status']==3){
                $list['status']='拒绝';
            }elseif($info['status']==0){
                $list['status']='失效';
            }
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['id'] = $info['id'];
            $list['goods_nm'] = $info['goods_nm'];
            $list['goods_barcode'] = $info['goods_barcode'];
            $list['goods_price'] = $info['goods_price'];
            $list['goods_discount'] = $info['goods_discount'];
            $list['goods_unit'] = $info['goods_unit'];
            $list['min_set_num'] = $info['min_set_num'];
            $list['goods_spec'] = $info['goods_spec'];
            $list['production_date'] = $info['production_date'];
            $list['valid_remind'] = $info['valid_remind'];
            $list['shelf_life'] = $info['shelf_life'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);exit();

    }

    public function dealOp()
    {
//
        $supp_stock    = SCMModel('gzkj_supp_stock');
        $data=array();
        $data['status']=$_GET['state'];
        if($supp_stock-> updates($data,$_GET['id'])){
            Tpl::showpage('supp_stock.index');

        } else {
            $this->jsonOutput('操作失败');
        }
    }
    /**
     * 删除供应商
     */
    public function supplier_delOp(){
        if (!empty($_GET['supp_id'])){

            Model()->table('scm_supplier')->where(array('supp_id'=>intval($_GET['supp_id'])))->delete();
//            $this->log(L('nc_delete,limit_admin').'[ID:'.intval($_GET['admin_id']).']',1);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }

    /**
     * 添加供应商
     */
    public function supplier_addOp(){
        if (chksubmit()){
            $limit_str = '';
            $model_supp = SCMModel('gzkj_supplier');
            $param['supp_ch_name'] = $_POST['supp_ch_name'];
            $param['supp_address'] = $_POST['supp_address'];
            $param['supp_mobile'] = $_POST['supp_mobile'];
            $rs = $model_supp->add($param);
            if ($rs){
                showMessage(L('nc_common_save_succ'),'index.php?act=supplier&op=index');
            }else {
                showMessage(L('nc_common_save_fail'));
            }
        }

        Tpl::showpage('supplier.add');

    }

    /**
     * 编辑供应商
     */
    public function supplier_editOp(){
        if (chksubmit()){

            $supp_id = intval($_GET['supp_id']);
            $data['supp_ch_name'] = $_POST['supp_ch_name'];
            $data['supp_address'] = $_POST['supp_address'];
            $data['supp_mobile'] = $_POST['supp_mobile'];
            $client_model = SCMModel('gzkj_supplier');
            $result = $client_model->updates($data,$supp_id);

            if ($result){
                showMessage(Language::get('admin_edit_success'),'index.php?act=supplier&op=index');
            }else{
                showMessage(Language::get('admin_edit_fail'),'index.php?act=supplier&op=index');
            }
        }else{
//
            $supp_model = SCMModel('scm_supplier');
            $suppinfo = $supp_model->getby_id(intval($_GET['id']));
            if (!is_array($suppinfo) || count($suppinfo)<=0){
                showMessage(Language::get('admin_edit_admin_error'),'index.php?act=supplier&op=index');
            }
            Tpl::output('suppinfo',$suppinfo);
            Tpl::showpage('supplier.edit');
        }
    }



    public function export_step1Op(){

        $supp_stock=SCMModel('gzkj_supp_stock');
        $condition=array();

        if (preg_match('/^[\d,]+$/', $_GET['id'])) {
            $_GET['id'] = explode(',',trim($_GET['id'],','));
            $condition['scm_supp_stock.id'] = array('in',$_GET['id']);
        }

        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['status']))) {
                $condition['scm_supp_stock.status'] = (int) $q;
            }
        } else{
            if ($_POST['query'] != '') {
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }
        }
        $field='scm_supp_stock.id,scm_supp_stock.supp_id,scm_supplier.supp_ch_name,scm_supp_stock.goods_nm,scm_supp_stock.goods_barcode,scm_supp_stock.goods_price,scm_supp_stock.goods_discount,scm_supp_stock.goods_unit,scm_supp_stock.min_set_num,scm_supp_stock.goods_spec,scm_supp_stock.production_date,scm_supp_stock.valid_remind,scm_supp_stock.shelf_life,scm_supp_stock.status';

        if (!is_numeric($_GET['curpage'])){
            $count = $supp_stock->getSuppStockCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=supp_stock&op=index');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $supp_stocks=$supp_stock->getStockAndSupp($condition,$field,$_POST['rp']);
                $this->createExcel($supp_stocks);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $supp_stocks=$supp_stock->getStockAndSupp($condition,$field,$_POST['rp']);

            $this->createExcel($supp_stocks);
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
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商编码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'供应商名字');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品ID');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品编码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品单价(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'折扣');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'最小配量');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'保质期');
        //data
        foreach ((array)$data as $k=>$info){
            $list = array();

            if($info['status']==2){
                $list['status'] = '未审核';
            }elseif($info['status']==1){
                $list['status']='通过';
            }elseif($info['status']==3){
                $list['status']='拒绝';
            }elseif($info['status']==0){
                $list['status']='失效';
            }
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['id'] = $info['id'];
            $list['goods_nm'] = $info['goods_nm'];
            $list['goods_barcode'] = $info['goods_barcode'];
            $list['goods_price'] = $info['goods_price'];
            $list['goods_discount'] = $info['goods_discount'];
            $list['goods_unit'] = $info['goods_unit'];
            $list['min_set_num'] = $info['min_set_num'];
            $list['goods_spec'] = $info['goods_spec'];
            $list['production_date'] = $info['production_date'];
            $list['valid_remind'] = $info['valid_remind'];
            $list['shelf_life'] = $info['shelf_life'];

            $tmp = array();
            $tmp[] = array('data'=>$list['status']);
            $tmp[] = array('data'=>$list['supp_id']);
            $tmp[] = array('data'=>$list['supp_ch_name']);
            $tmp[] = array('data'=>$list['id']);
            $tmp[] = array('data'=>$list['goods_nm']);
            $tmp[] = array('data'=>$list['goods_barcode']);
            $tmp[] = array('data'=>$list['goods_price']);
            $tmp[] = array('data'=>$list['goods_discount']);
            $tmp[] = array('data'=>$list['goods_unit']);
            $tmp[] = array('data'=>$list['min_set_num']);
            $tmp[] = array('data'=>$list['goods_spec']);
            $tmp[] = array('data'=>$list['production_date']);
            $tmp[] = array('data'=>$list['valid_remind']);
            $tmp[] = array('data'=>$list['shelf_life']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('order-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }
}
