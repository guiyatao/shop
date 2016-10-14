<?php
/**
 * 账号同步
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
class accountControl extends SCMControl{
    protected $supp_info;
    public function __construct(){
        parent::__construct();
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
    }

    public function indexOp() {
        return $this->showsuppliersetting();
    }

    /**
     * 账户维护
     */
    public function showsuppliersetting(){

        $model_setting = SCMModel('supplier_account');

        if (chksubmit()){
            //update operation
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["supp_ch_name"], "require"=>"true", "message"=>"中文名称不能为空。"),
//                array("input"=>$_POST["supp_ch_name"], "require"=>"true", "validator"=> "chinese", "message"=>"必须为中文名称。"),
//                array("input"=>$_POST["supp_tel"], "require"=>"true", "validator"=>"phone", "message"=>"电话号码必须合法。" ),
//                array("input"=>$_POST['supp_mobile'], "require"=>"true", "validator"=>"mobile", "message"=>"手机号码必须合法。" ),
            );

            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update_supplier = array();
                $update_supplier['id'] = $_POST['id'];
                $update_supplier['supp_ch_name'] = trim($_POST['supp_ch_name']);
                $update_supplier['area_province'] = $_POST['area_province'];
                $update_supplier['area_city'] = $_POST['area_city'];
                $update_supplier['area_district'] = $_POST['area_district'];
                $update_supplier['enterprise_nature'] = $_POST['enterprise_nature'];
                $update_supplier['business_licences'] = $_POST['business_licences'];
                $update_supplier['tax_registration'] = $_POST['tax_registration'];
                $update_supplier['mail_address'] = $_POST['mail_address'];
                $update_supplier['supp_address'] = $_POST['supp_address'];
                $update_supplier['supp_contacter'] = $_POST['supp_contacter'];
                $update_supplier['supp_tel'] = $_POST['supp_tel'];
                $update_supplier['supp_mobile'] = $_POST['supp_mobile'];
                $update_supplier['supp_tax'] = $_POST['supp_tax'];
                $update_supplier['zip_code'] = $_POST['zip_code'];
                $update_supplier['comments'] = $_POST['comments'];

                $result = $model_setting->updateSupplier($update_supplier);
                if ($result === true){
//                     $this->log(L('nc_edit,sinaSettings'),1);
                    $this->log('修改供应商['.trim($_POST['supp_ch_name']).']信息',null);
                    showMessage(Language::get('nc_common_save_succ'));
                }else {
//                     $this->log(L('nc_edit,sinaSettings'),0);
                    showMessage(Language::get('nc_common_save_fail'));
                }
            }
        }
        if(count($this->supp_info)> 0) {
            Tpl::output("id",$this->supp_info['id']);
            Tpl::output("supp_id", $this->supp_info['supp_id']);
            Tpl::output("supp_ch_name", $this->supp_info['supp_ch_name']);
            Tpl::output("area_province",$this->supp_info['area_province']);
            Tpl::output("area_city",$this->supp_info['area_city']);
            Tpl::output("area_district",$this->supp_info['area_district']);
            Tpl::output("enterprise_nature",$this->supp_info['enterprise_nature']);
            Tpl::output("business_licences",$this->supp_info['business_licences']);
            Tpl::output("tax_registration",$this->supp_info['tax_registration']);
            Tpl::output("mail_address",$this->supp_info['mail_address']);
            Tpl::output("supp_address",$this->supp_info['supp_address']);
            Tpl::output("supp_contacter",$this->supp_info['supp_contacter']);
            Tpl::output("supp_tel",$this->supp_info['supp_tel']);
            Tpl::output("supp_mobile",$this->supp_info['supp_mobile']);
            Tpl::output("supp_tax",$this->supp_info['supp_tax']);
            Tpl::output("zip_code",$this->supp_info['zip_code']);
            Tpl::output("comments",$this->supp_info['comments']);
            Tpl::output("supp_bank",$this->supp_info['supp_bank']);
            Tpl::output("supp_cardno",$this->supp_info['supp_cardno']);
        }
        $enterprise_nature_items = array('国有企业','集体所有制企业','联营企业','三资企业','私营企业','其他企业');
        Tpl::output("enterprise_nature_items",$enterprise_nature_items);
        Tpl::showpage('account.setting');
    }
}
