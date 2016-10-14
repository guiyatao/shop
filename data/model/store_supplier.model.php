<?php
/**
 * 供货商模型
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class store_supplierModel extends Model{

    public function __construct(){
        parent::__construct('store_supplier');
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getStoreSupplierList($condition, $page = '', $order = '', $field = '*') {
        return $this->field($field)->where($condition)->page($page)->order($order)->select();
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getStoreSupplierInfo($condition) {
        return $this->where($condition)->find();
    }

    /*
     * 增加
     * @param array $data
     * @return bool
     */
    public function addStoreSupplier($data){
        return $this->insert($data);
    }

    public function editStoreSupplier($data,$condition) {
        return $this->where($condition)->update($data);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function delStoreSupplier($condition){
        return $this->where($condition)->delete();
    }

}
