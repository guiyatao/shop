<?php
/**
 * 供应商账户管理
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class supplier_accountModel extends Model {
    /**
     * 获取当前用户所对应的供应商信息
     *
     * @param array $condition 查询条件
     * @return array 二维数组
     */
    public function getSupplier($condition = array()){
//        $param  = array();
//        $param['table'] = 'scm_user';
//        $param['where'] = ' user_id=9 ';
//        return $this->select1($param,'');

        $field = 'scm_supplier.id,scm_supplier.supp_id,supp_ch_name,area_province,area_city,area_district,enterprise_nature,business_licences,tax_registration,mail_address,supp_address,supp_contacter,supp_tel,supp_mobile,supp_tax,zip_code,comments,supp_bank,supp_cardno';
        $on = 'admin.admin_id = scm_user.admin_id,scm_user.supp_clie_id = scm_supplier.supp_id';
        $result =  $this->table('admin,scm_user,scm_supplier')->field($field)->join('left,left')->on($on)->where($condition)->find();
        return $result;
    }

    /**
     * 更新供应商的详细信息
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updateSupplier( $update_supplier){
        $update_supplier_result = $this->update1('scm_supplier', $update_supplier, " id=".$update_supplier['id']);
        return  $update_supplier_result;

    }

}