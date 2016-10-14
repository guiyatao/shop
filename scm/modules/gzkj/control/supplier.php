<?php
/**
 * 供应商管理
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
class supplierControl extends SCMControl{

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
        Tpl::showpage('supplier.index');
    }

    public function get_xmlOp(){
        $supplier=SCMModel('gzkj_supplier');
        $suppliers=$supplier->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $supplier->shownowpage();
        $data['total_num'] = $supplier->gettotalnum();
        foreach ($suppliers as $k => $info) {
            $list = array();
            $list['operation'].= '<a class="btn red" href="javascript:fg_operation_del('.$info['id'].');"><i class="fa fa-trash-o"></i>删除</a>';
            $list['operation'].= '<a class="btn blue" href="index.php?act=supplier&op=supplier_edit&id='.$info['id'].'"><i class="fa fa-pencil-square-o"></i>'.L('nc_edit').'</a>';
            $list['supp_id'] = $info['supp_id'];
            $list['supp_en_name'] = $info['supp_en_name'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['supp_area'] = $info['supp_area'];
            $data['list'][$info['supp_id']] = $list;
        }
        echo Tpl::flexigridXML($data);exit();

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
//                $this->log(L('nc_add,limit_admin').'['.$_POST['admin_name'].']',1);
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
}
