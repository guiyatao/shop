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
class scm_cartModel extends Model {

    public function __construct() {
       parent::__construct('scm_cart');
    }

    /**
     * 检查购物车内商品是否存在
     *
     * @param
     */
    public function checkCart($condition = array()) {
        return  $this->table('scm_cart')->where($condition)->find();
    }

    /**
     * 将商品添加到购物车中
     *
     * @param array $data   商品数据信息
     * @param string $save_type 保存类型，可选值 db,cookie
     * @param int $quantity 购物数量
     */
    public function addCart($data = array(), $save_type = 'db', $quantity = null) {
        $method = '_addCart'.ucfirst($save_type);
        $insert = $this->$method($data,$quantity);
        return $insert;
    }

    /**
     * 添加数据库购物车
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    private function _addCartDb($goods_info = array(),$quantity) {
        //验证购物车商品是否已经存在
        $condition = array();
        $condition['goods_barcode'] = $goods_info['goods_barcode'];
        $condition['clie_id'] = $goods_info['clie_id'];
        $condition['supp_id'] = $goods_info['supp_id'];
        $check_cart = $this->checkCart($condition);
        if (!empty($check_cart)) {  //当添加同样商品时，合并商品数量，显示为一条
            $array['cart_id'] = $check_cart['cart_id'];
            $array['goods_num'] = $check_cart['goods_num'] + $quantity;
            $update = $this->table('scm_cart')->where(array('cart_id'=>$check_cart['cart_id']))->update($array);
            return $update;
        }
        $info = $this->getGoodInfo($goods_info['goods_barcode'], $goods_info['supp_id'], array('goods_nm', '(goods_price * goods_discount) goods_discount_price'));
        $array    = array();
        $array['clie_id']  = $goods_info['clie_id'];
        $array['supp_id']  = $goods_info['supp_id'];
        $array['goods_barcode']  = $goods_info['goods_barcode'];
        $array['goods_name'] = $info['goods_nm'];
        $array['goods_price'] = $info['goods_discount_price'];
        $array['goods_num']   = $quantity;
        
        return $insert = $this->table('scm_cart')->insert($array);
    }

    /**
     * 一键添加多件商品到购物车
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    private function _addCartOb($goods_info = array()) {
        //验证购物车商品是否已经存在
        $cart_goods_info = array();
        $result = true;
        $update = true;
        foreach ($goods_info as $good) {
            $array = array();
            $condition = array();
            $condition['goods_barcode'] = $good['goods_id'];
            $condition['clie_id'] = $good['clie_id'];
            $condition['supp_id'] = $good['supp_id'];
            $check_cart = $this->checkCart($condition);
            if (!empty($check_cart)) { //当添加同样商品时，合并商品数量，显示为一条
                $array['cart_id'] = $check_cart['cart_id'];
                $array['goods_num'] = $check_cart['goods_num'] + $good['goods_num'];
                $update = $this->table('scm_cart')->where(array('cart_id'=>$check_cart['cart_id']))->update($array);
                continue;
            }
            $info = $this->getGoodInfo($good['goods_id'], $good['supp_id'], array('goods_nm', '(goods_price * goods_discount) goods_discount_price'));
            $array = array();
            $array['clie_id']  = $good['clie_id'];
            $array['supp_id']  = $good['supp_id'];
            $array['goods_barcode']  = $good['goods_id'];
            $array['goods_name'] = $info['goods_nm'];
            $array['goods_price'] = $info['goods_discount_price'];
            $array['goods_num']   = $good['goods_num'];
            $cart_goods_info[] = $array;
        }
        // return $insert = $this->table('scm_cart')->insert($array);
        if (!empty($cart_goods_info)) {
            $result = $this->table('scm_cart')->insertAll($cart_goods_info);
        }
        // $condition = array();
        // $condition['goods_id'] = $goods_info['goods_id'];
        // $condition['clie_id'] = $goods_info['buyer_id'];
        // $check_cart = $this->checkCart($condition);
        // if (!empty($check_cart)) return true;
        // $info = $this->getGoodInfo($goods_info['goods_id'], $goods_info['supp_id'], array('goods_nm', '(goods_price * goods_discount) goods_discount_price'));
        // $array    = array();
        // $array['clie_id']  = $goods_info['clie_id'];
        // $array['supp_id']  = $goods_info['supp_id'];
        // $array['goods_id']  = $goods_info['goods_id'];
        // $array['goods_name'] = $info['goods_nm'];
        // $array['goods_price'] = $info['goods_discount_price'];
        // $array['goods_num']   = $quantity;
        // return $insert = $this->table('scm_cart')->insert($array);
        return ($result&&$update) ;
    }

    /**
     * 获得供应商单件商品信息
     *
     * @param
     * @return int
     */
    public function getGoodInfo($goods_barcode, $supp_id, $fields = '*') {
        $result = $this->table('scm_supp_stock')->field($fields)->where(array('goods_barcode' => $goods_barcode, 'supp_id' => $supp_id))->find();
        return $result;
    }

    /**
     * 更改库存信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editStock($data, $condition, $operator) {
        $sql = 'UPDATE `gzkj`.`gzkj_scm_client_stock` scm_client_stock SET goods_stock=goods_stock' . $operator . $data['set_num'] . ' where ( goods_barcode = ' . $condition['goods_barcode'] . ' )';
        $result = $this->query($sql);
        return $result;
    }

    /**
     * 获得终端店单件商品库存信息
     *
     * @param
     * @return int
     */
    public function getClientGoodInfo($goods_barcode, $fields = '*') {
        $result = $this->table('scm_client_stock')->field($fields)->where(array('goods_barcode' => $goods_barcode))->find();
        return $result;
    }

    /**
     * 取得购物车商品列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getCartGoodsList($condition, $field = '*', $order = 'supp_id desc', $master = false){
        $list = $this->table('scm_cart')->field($field)->where($condition)->order($order)->master($master)->select();
        if (empty($list)) return array();
        return $list;
    }

    /**
     * 取得购物车商品供应商列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getCartSuppList($condition, $field = 'supp_id', $order = 'supp_id desc', $master = false){
        $list = $this->table('scm_cart')->field($field)->where($condition)->order($order)->master($master)->select();
        if (empty($list)) return array();
        return $list;
    }

    /**
     * 删除购物车商品
     *
     * @param
     * @return bool
     */
    public function delGood($condition) {
        if (empty($condition)) {
            return false;
        } else {
            $result = $this->table('scm_cart')->where($condition)->delete();
            return $result;
        }
    }

}
