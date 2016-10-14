<?php
/**
 * 终端店
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

class scm_clientModel extends Model
{
    public function __construct()
    {
        parent::__construct('scm_client');
    }

    /**
     * 终端店查询
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getList($condition = '', $page = '')
    {

        $param = array();
        $param['table'] = 'scm_client';
        $param['where'] = $this->getCondition($condition);
        $param['field'] = 'scm_client.clie_id,scm_client.clie_ch_name';
        $param['order'] = $condition['order'] ? $condition['order'] : 'clie_id';
        return $this->select1($param, $page);
    }

    /**
     * 查询各终端店内所有商品实时库存情况
     *
     * @param array $condition 查询条件数组
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getStockJoinList($condition='', $page = '')
    {
        $param = array();
        $param['table'] = 'scm_client,scm_client_stock';
        $param['join_type'] = 'inner join';
        $param['field'] = 'scm_client.clie_id,scm_client.clie_ch_name,scm_client_stock.goods_stock';
        $param['join_on'] = array('scm_client.clie_id=scm_client_stock.clie_id');
        $param['order'] = $condition['order'] ? $condition['order'] : 'clie_id';
        return $this->select1($param, $page);
    }
    /**
     * 终端店向供应商批发订购的所有订单列表，点击可查看订单详情
     *
     * @param array $condition 查询条件数组
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getOrderJoinList($condition='', $page = '')
    {
        $param = array();
        $param['table'] = 'scm_client,scm_client_order';
        $param['join_type'] = 'inner join';
        $param['field'] = 'scm_client.clie_id,scm_client.clie_ch_name,scm_client_stock.goods_stock';
        $param['join_on'] = array('scm_client.clie_id=scm_client_order.clie_id');
        $param['order'] = $condition['order'] ? $condition['order'] : 'clie_id';
        return $this->select1($param, $page);
    }
    /**
     * 添加终端店
     *
     * @param array $input
     * @return bool
     */
    public function add($input)
    {
        return $this->insert1('scm_client', $input);
    }

    /**
     * 更新终端店
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input, $id)
    {
        return $this->update1('scm_client', $input, " clie_id='$id' ");
    }

    /**
     * 删除终端店
     *
     * @param string $id
     * @return bool
     */
    public function del($id)
    {
        return $this->delete1('scm_client', 'clie_id in(' . $id . ')');
    }


    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return string
     */
    private function getCondition($condition)
    {
        $conditionStr = '';
        if ($condition['clie_id'] != '') {
            $conditionStr .= " and scm_client.clie_id='{$condition['clie_id']}' ";
        }
        if ($condition['clie_ch_name'] != '') {
            $conditionStr .= " and scm_client.clie_ch_name='{$condition['clie_ch_name']}' ";
        }
        return $conditionStr;
    }

    /**
     * 取当前终端店账户信息
     *
     * @param unknown_type $condition
     * @param array $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return unknown
     */
    public function getClientInfo($condition = array(), $extend = array(), $fields = '*', $order = '',$group = '') {
        $account_info = $this->table('scm_client')->field($fields)->where($condition)->group($group)->order($order)->find();
        if (empty($account_info)) {
            return array();
        }
        return $account_info;
    }

    /**
     * 更改终端店账户信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editClientInfo($data,$condition) {
        return $this->table('scm_client')->where($condition)->update($data);
    }
}
