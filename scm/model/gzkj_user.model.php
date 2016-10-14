<?php
/**
 * 活动
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
class gzkj_userModel extends Model {

    public function __construct()
    {
        parent::__construct('scm_user');
    }

    /**
     * 用户查询
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getList($condition, $page = '')
    {
        $param = array();
        $param['table'] = 'scm_user';
        $param['where'] = $this->getCondition($condition);
        $param['field'] = 'scm_user.user_id,scm_user.user_ch_name';
        $param['order'] = $condition['order'] ? $condition['order'] : 'user_id';
        return $this->select1($param, $page);
    }

    /**
     * 添加用户
     *
     * @param array $input
     * @return bool
     */
    public function add($input)
    {
        return $this->insert1('scm_user', $input);
    }

    /**
     * 更新用户
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input, $id)
    {
        return $this->update1('scm_user', $input, " user_id='$id' ");
    }

    /**
     * 删除用户
     *
     * @param string $id
     * @return bool
     */
    public function del($id)
    {
        return $this->delete1('scm_user', 'user_id in(' . $id . ')');
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
        if ($condition['user_id'] != '') {
            $conditionStr .= " and scm_user.user_id='{$condition['user_id']}' ";
        }
        if ($condition['user_ch_name'] != '') {
            $conditionStr .= " and scm_user.user_ch_name='{$condition['user_ch_name']}' ";
        }
        return $conditionStr;
    }

}
