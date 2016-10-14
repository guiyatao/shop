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
class scm_client_favoriteModel extends Model {

    public function __construct() {
       parent::__construct('scm_client_favorite');
    }

    /**
     * 检查收藏商品是否存在
     *
     * @param
     */
    public function checkFavorite($condition = array()) {
        return $this->where($condition)->find();
    }

    /**
     * 添加数据库收藏商品
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    public function addFavoriteGoods($goods_info = array(),$quantity) {
        //验证购物车商品是否已经存在
        $condition = array();
        $condition['goods_id'] = $goods_info['goods_id'];
        $condition['clie_id'] = $goods_info['clie_id'];
        $check_cart = $this->checkCart($condition);
        if (!empty($check_cart)) return false;
        $info = $this->getGoodInfo($goods_info['goods_id'], $goods_info['supp_id'], array('goods_nm', '(goods_price * goods_discount) goods_discount_price'));
        $array    = array();
        $array['clie_id']  = $goods_info['clie_id'];
        $array['supp_id']  = $goods_info['supp_id'];
        $array['goods_id']  = $goods_info['goods_id'];
        $array['goods_name'] = $info['goods_nm'];
        $array['goods_price'] = $info['goods_discount_price'];
        $array['goods_num']   = $quantity;
        return $insert = $this->table('scm_cart')->insert($array);
    }

    /**
     * 一键添加多件商品到收藏
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    private function addFavoriteGoodsOb($goods_info = array()) {
        //验证购物车商品是否已经存在
        $cart_goods_info = array();
        $result = false;
        foreach ($goods_info as $good) {
            $condition = array();
            $condition['goods_id'] = $good['goods_id'];
            $condition['clie_id'] = $good['clie_id'];
            $check_cart = $this->checkCart($condition);
            if (!empty($check_cart)) continue;
            $info = $this->getGoodInfo($good['goods_id'], $good['supp_id'], array('goods_nm', '(goods_price * goods_discount) goods_discount_price'));
            $array = array();
            $array['clie_id']  = $good['clie_id'];
            $array['supp_id']  = $good['supp_id'];
            $array['goods_id']  = $good['goods_id'];
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
        return $result;
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
     * 取得收藏商品列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getFavoriteGoodsList($condition, $pagesize = '', $field = '*', $order = 'supp_id desc', $master = false){
        $list = $this->field($field)->where($condition)->page($pagesize)->order($order)->master($master)->select();
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
        $list = $this->table('scm_supp_stock')->field($field)->where($condition)->order($order)->master($master)->select();
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
