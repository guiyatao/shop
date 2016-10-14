<?php
/**
 * 供应商的资金管理
 */

defined('InShopNC') or exit('Access Invalid!');

class supplier_capitalModel extends Model {

    /**
     * 注册新的银行卡
     * @param $capital
     * @return bool
     */
    public function addCapital($capital) {
        if(empty($capital)) {
            return false;
        }
        try {
            $this->beginTransaction();
            $insert_id  = $this->table('scm_supp_capital')->insert($capital);
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
     * 获取银行卡详细信息
     * @param $condition
     * @param string $field
     */
    public function getCapitalInfo($condition , $field = '*'){
        return $this->table('scm_supp_capital')->field($field)->where($condition)->find();
    }

    /**
     * 更新资金表信息
     * @param $condition
     * @return bool
     */
    public function updateCapital($condition){
        $update_id = $this->table('scm_supp_capital')->where(array('capital_id'=>$condition["capital_id"]))->update($condition);
        if($update_id)
            return true;
        else
            return false;
    }
    /*
    * 批量注销银行卡
    * @param array $ids
    * @return boolean
    */
    public function delCapitalByIdString($ids){
        if(empty($ids)){
            return false;
        }
        $delete_capital = $this->table('scm_supp_capital')->where(array('capital_id' => array('in', $ids)))->delete();
        if($delete_capital)
            return true;
        else
            return false;
    }

    /**
     * 分页获取银行卡列表
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getCapitalList($condition = array(), $field = '*', $page = null, $order = 'capital_id desc', $limit = ''){
        return $this->table('scm_supp_capital')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 按条件获取银行卡数量
     * @param array $condition
     * @return mixed
     */
    public function getCapitalCount($condition = array()){
        return $this->table('scm_supp_capital')->where($condition)->count();
    }

}