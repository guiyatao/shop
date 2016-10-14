<?php
/**
 * 缺货订单
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
class client_accountControl extends SCMControl{
    protected $user_info;
    public function __construct(){
        parent::__construct();
        Language::read('setting');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);

    }

    public function indexOp() {
        $this->showClientAccount();
    }

    /**
     * 账户信息
     */
    public function showClientAccount() {
        $clie_id = $this->user_info['supp_clie_id'];
        $model_client = SCMModel('scm_client');
        if (chksubmit()){
            $update = $_POST;
            $where = array();
            $where['clie_id'] = $clie_id;
            unset($update['form_submit']);
            $state = $model_client->editClientInfo($update, $where);
            if ($state) {
                showMessage(Language::get('nc_common_save_succ'),'index.php?act=client_account&op=index');
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $list = $model_client->getClientInfo(array('clie_id'=>$clie_id));
        Tpl::output('list_setting',$list);
        Tpl::showpage('account.setting');
    }

    /**
     * 新增商品
     *
     */
    public function add_goodsOp() {
        $model_help = SCMModel('scm_client_stock');
        if (chksubmit()) {
            $goods_array = $_POST;
            unset($goods_array['form_submit']); 
            $state = $model_help->addNewGoodsToClientStock($goods_array);
            if ($state) {
                showMessage(Language::get('nc_common_save_succ'),'index.php?act=client_stock&op=index');
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $goods_list = $model_help->getNewGoodsList($this->user_info['supp_clie_id']);
        Tpl::output('goods_list',$goods_list);
        Tpl::showpage('new_goods.add');
    }
}
