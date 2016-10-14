<?php
/**
 * 终端店库存
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
class gzkj_instock_infoModel extends Model {
    /**
     * 取得库存列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getStockList($condition, $pagesize = '', $field = '*', $order = 'supp_id desc', $limit = '', $extend = array(), $master = false){
        $list = $this->table('scm_client_stock')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
        if (empty($list)) return array();
        $order_list = array();
        if (empty($order_list)) $order_list = $list;
        return $order_list;
    }

    /**
     * 增加入库信息到入库信息表
     *
     * @param
     * @return int
     */
    public function addInstockInfo($order_good, $in_date) {
        $order_good['in_stock_date'] = $in_date;
        unset($order_good['id'], $order_good['goods_price'], $order_good['goods_discount'], $order_good['goods_discount_price'], $order_good['produce_company'], $order_good['produce_area'],
            $order_good['order_num'], $order_good['actual_amount'], $order_good['min_set_num']);
        $result = $this->table('scm_instock_info')->insert($order_good);
        return $result ? $result : null;
    }


    public function execute_sql($sql){
        if (empty($sql)) {
            return null;
        }
        $result = $this->query($sql);
        if ($result === false) return array();
        $goods_list = array();
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $goods_list[] = $tmp;
        }
        return !empty($goods_list) ? $goods_list : null;
    }



}
