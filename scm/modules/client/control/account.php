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

    protected $user_info;
    public function __construct(){
        parent::__construct();
        Language::read('setting');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }

    public function indexOp() {
        return $this->showClientAccount();
    }

    /**
     * 账户维护
     */
    public function showClientAccount(){

        $model_setting = SCMModel('scm_client_account');
        
        if (chksubmit()){
            $update_array = array();
            $update_array['clie_id'] = $this->user_info['supp_clie_id'];
            $update_array['clie_id'] = $_POST['clie_id'];
            $update_array['clie_ch_name'] = $_POST['clie_ch_name'];
            $update_array['area_province'] = $_POST['area_province'];
            $update_array['area_city'] = $_POST['area_city'];
            $update_array['area_district'] = $_POST['area_district'];
            $update_array['clie_address'] = $_POST['clie_address'];
            $update_array['clie_longitude'] = $_POST['clie_longitude'];
            $update_array['clie_latitude'] = $_POST['clie_latitude'];
            $update_array['clie_bank'] = $_POST['clie_bank'];
            $update_array['clie_cardno'] = $_POST['clie_cardno'];
            $update_array['clie_contacter'] = $_POST['clie_contacter'];
            $update_array['clie_tel'] = $_POST['clie_tel'];
            $update_array['clie_mobile'] = $_POST['clie_mobile'];
            $update_array['clie_tax'] = $_POST['clie_tax'];
            $update_array['comments'] = $_POST['comments'];
            $result = $model_setting->updateAccountInfo($update_array);
        }
        $account_id = $this->user_info['supp_clie_id'];
        $result = $model_setting->getAccountInfo(array('clie_id'=>$account_id));

        Tpl::output("list_setting", $result);
        Tpl::showpage('account.setting');
    }
}
