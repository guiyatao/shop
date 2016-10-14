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
class scm_supp_stockModel extends Model {

    /**
     * 用户列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getList($condition,$page=''){
        $param  = array();
        $param['table'] = 'scm_user';
        return $this->select1($param,$page);
    }
    /**
     * 添加用户
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return $this->insert1('scm_user',$input);
    }
    /**
     * 更新用户
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return $this->update1('activity',$input," activity_id='$id' ");
    }
    /**
     * 删除用户
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return $this->delete1('activity','activity_id in('.$id.')');
    }
    /**
     * 根据id查询一条活动
     *
     * @param int $id 活动id
     * @return array 一维数组
     */
    public function getOneById($id){
        return $this->getRow1(array('table'=>'activity','field'=>'activity_id','value'=>$id));
    }

}
