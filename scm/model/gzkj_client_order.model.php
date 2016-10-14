<?php
/**
 * 订单结算
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

class gzkj_client_orderModel extends Model
{


    public function __construct()
    {
        parent::__construct('scm_client_order');
    }

    public function getList($condition, $page = '')
    {
        $param = array();
        $param['table'] = 'activity';
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order'] : 'activity_id';
        return $this->select1($param, $page);
    }

    public function getOrderList($condition = array(), $field = '*', $group=null,$page = null, $order = 'id desc', $limit = '')
    {
        return $this->table('scm_client_order')->field($field)->where($condition)->order($order)->group($group)->page($page)->limit($limit)->select();
    }

    public function getGoodJoinList($condition, $field = '*', $page = 0, $group=null,$order = 'scm_order_goods.id desc',$limit='')
    {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('scm_client_order,scm_order_goods')->join('inner')->on('scm_client_order.id=scm_order_goods.order_id')->where($condition)->field($field)->order($order)->group($group)->page($page[0],$page[1])->limit($limit)->select();
            } else {
                return $this->table('scm_client_order,scm_order_goods')->join('inner')->on('scm_client_order.id=scm_order_goods.order_id')->where($condition)->field($field)->order($order)->group($group)->page($page)->limit($limit)->select();
            }
        } else {
            return $this->table('scm_client_order,scm_order_goods')->join('inner')->on('scm_client_order.id=scm_order_goods.order_id')->where($condition)->field($field)->order($order)->group($group)->page($page)->limit($limit)->select();
        }

    }


    public function getCashAndCount($condition, $field = '*', $group=null)
    {
        return $this->table('scm_client_order,scm_client')->join('inner')->on('scm_client_order.clie_id=scm_client.clie_id')->where($condition)->field($field)->group($group)->select();
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
