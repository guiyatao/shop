<?php
/**
 * 店铺动态自动发布
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

class store_sns_settingModel extends Model {
    public function __construct(){
        parent::__construct('store_sns_setting');
    }

    /**
     * 获取单条动态设置设置信息
     *
     * @param unknown $condition
     * @param string $field
     * @return array
     */
    public function getStoreSnsSettingInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 保存店铺动态设置
     *
     * @param unknown $insert
     * @return boolean
     */
    public function saveStoreSnsSetting($insert, $replace) {
        return $this->pk(array('sauto_storeid'))->insert($insert, $replace);
    }
}
