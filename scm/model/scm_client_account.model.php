<?php
/**
 * 账户维护
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
class scm_client_accountModel extends Model {
    /**
     * 取当前终端店账户信息
     *
     * @param unknown_type $condition
     * @param array $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return unknown
     */
    public function getAccountInfo($condition = array(), $extend = array(), $fields = '*', $order = '',$group = '') {
        $account_info = $this->table('scm_client')->field($fields)->where($condition)->group($group)->order($order)->find();
        if (empty($account_info)) {
            return array();
        }
        return $account_info;
    }

    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function updateAccountInfo($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $where = " clie_id = '". $param['clie_id'] ."'";
            $result = $this->update1('scm_client',$tmp,$where);
            return $result;
        }else {
            return false;
        }
    }

}
