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
class scm_instock_infoModel extends Model {
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
            $order_good['order_num'], $order_good['actual_amount'], $order_good['min_set_num'],$order_good['unpacking_num'],$order_good['create_date']);
        $result = $this->table('scm_instock_info')->insert($order_good);
        return $result ? $result : null;
    }

    /**
     * 取得进货库存列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getInStockList($condition, $pagesize = '', $field = '*', $order = 'supp_id desc', $limit = '', $extend = array(), $master = false){
        $list = $this->table('scm_instock_info')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
        if (empty($list)) return array();
        $order_list = array();
        if (empty($order_list)) $order_list = $list;
        return $order_list;
    }

    /**
     * 根据条件
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getValidityList($condition, $page=''){
        $param  = array();
        $param['table'] = 'scm_supplier,scm_instock_info';
        $param['join_type'] = 'RIGHT JOIN';
        $param['join_on'] = array('scm_instock_info.supp_id = scm_supplier.supp_id');
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'];
        return $this->select1($param, $page);
    }

    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return string
     */
    private function getCondition($condition){
        $conditionStr   = '';
        if($condition['month'] != ''){
            $conditionStr   .= $condition['month'];
        }
        if($condition['day'] != ''){
            $conditionStr   .= " or {$condition['day']} ";
        }
        if($condition['waring_flag'] != ''){
            $conditionStr   .= " and waring_flag=".$condition['waring_flag'];
        }
        //活动删除in
        if(isset($condition['activity_id_in'])){
            if ($condition['activity_id_in'] == ''){
                $conditionStr   .= " and activity_id in('')";
            }else{
                $conditionStr   .= " and activity_id in({$condition['activity_id_in']}) ";
            }
        }

        //当前时间大于结束时间（过期）
        if ($condition['activity_enddate_greater'] != ''){
            $conditionStr   .= " and activity.activity_end_date < '{$condition['activity_enddate_greater']}'";
        }
        //可删除的活动记录
        if ($condition['activity_enddate_greater_or'] != ''){
            $conditionStr   .= " or activity.activity_end_date < '{$condition['activity_enddate_greater_or']}'";
        }

        return $conditionStr;
    }
    /*
     * 修改入库信息表
     */
    public function editInstockInfo($data){
        $update_id = $this->table('scm_instock_info')->where(array('id'=>$data["id"]))->update($data);
        if($update_id)
            return true;
        else
            return false;
    }
}
