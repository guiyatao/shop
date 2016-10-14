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
class gzkj_supp_stockModel extends Model {

    public function __construct()
    {
        parent::__construct('scm_supp_stock');
    }

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



    public function getStockAndSupp($condition = array(), $field = '*', $page = null, $order = 'scm_supp_stock.id desc')
    {
        return $this->table('scm_supp_stock,scm_supplier')->join('inner')->on('scm_supp_stock.supp_id=scm_supplier.supp_id')->where($condition)->field($field)->page($page)->order($order)->select();
    }

    /**
     * 更新
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return $this->update1('scm_supp_stock',$input," id='$id' ");
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
    /**
     * 获取个数
     */
    public function getSuppStockCount($condition){
        return $this->table('scm_supp_stock')->where($condition)->count();
    }
    public function getSupp_stockAndSuppCount($condition = array(), $field = '*', $page = null, $order = 'act_id desc')
    {
        return $this->table('scm_supp_stock,scm_supplier')->join('inner')->on('scm_supp_stock.supp_id=scm_supplier.supp_id')->where($condition)->count();
    }

    public function getActivityAndSuppCount($condition = array(), $field = '*', $page = null, $order = 'act_id desc')
    {
        return $this->table('scm_activity,scm_supplier')->join('inner')->on('scm_activity.supp_id=scm_supplier.supp_id')->where($condition)->count();
    }
    /**
     * 获取个数
     */
    public function getActivityCount($condition){
        return $this->table('scm_activity')->where($condition)->count();
    }

    public function get_pending_matters(){
        $flag = false;
        $condition['status'] = 2;
        $condition['is_close'] = 0;

        if($this->getSupp_stockAndSuppCount($condition)){

            $flag = true;
        }
        $condition = array();
        $condition['activity_status'] = 2;
        $condition['is_close'] = 0;
        if($this->getActivityAndSuppCount($condition)){
            $flag = true;
        }

        return $flag;
    }
}
