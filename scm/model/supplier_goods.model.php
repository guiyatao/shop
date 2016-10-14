<?php
/**
 * 供应商商品管理
 */

defined('InShopNC') or exit('Access Invalid!');

class supplier_goodsModel extends Model {

    /**
     * 增加供应商的商品
     * @param $goods
     * @return bool
     */
    public function addGoods($goods) {
        if(empty($goods)) {
            return false;
        }
        try {
            $this->beginTransaction();
            $insert_id  = $this->table('scm_supp_stock')->insert($goods);
            if (!$insert_id) {
                throw new Exception();
            }
            $this->commit();
            return $insert_id;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    /**
     * 获取供应商商品信息
     * @param array $condition 查询条件, array $field 筛选列
     * @return array 数组格式返回查询结果
     */
    public function getGoodsInfo($condition , $field = '*'){
        return $this->table('scm_supp_stock')->field($field)->where($condition)->find();
    }

    /**
     * 分页获取供应商商品列表
     * @param array $condition  筛选条件
     * @param string $field 筛选的字段
     * @param number $page  分页条件
     * @param string $order  排序
     */
    public function getGoodsList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = ''){
        return $this->table('scm_supp_stock')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 获取商品数量
     * @param $condition
     * @return mixed
     */
    public function getGoodsCount($condition){
        return $this->table('scm_supp_stock')->where($condition)->count();
    }
    /**
     * 更新供应商的单个商品
     *
     * @param array $update_goods
     * @return bool
     */
    public function updateGoods($goods){
        $update_id = $this->table('scm_supp_stock')->where(array('id'=>$goods["id"]))->update($goods);
        if($update_id)
            return true;
        else
            return false;
    }

    /**
     * 批量删除商品
     * @param $ids id数组
     */
    public function delGoodsByIdString($ids){
        if(empty($ids)){
            return false;
        }
        $delete_goods = $this->table('scm_supp_stock')->where(array('id' => array('in', $ids)))->delete();
        if($delete_goods)
            return true;
        else
            return false;
    }
}