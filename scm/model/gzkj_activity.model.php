<?php
/**
 * 供应商的资金管理
 */

defined('InShopNC') or exit('Access Invalid!');

class gzkj_activityModel extends Model {
    public function __construct()
    {
        parent::__construct('scm_activity');
    }

    /**
     * 新建活动
     * @param $activity
     * @return bool
     */
    public function addActivity($activity) {
        if(empty($activity)) {
            return false;
        }
        try {
            $this->beginTransaction();
            $insert_id  = $this->table('scm_activity')->insert($activity);
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
     * 获取活动详情
     * @param $condition
     * @param string $field
     */
    public function getActivityInfo($condition , $field = '*'){
        return $this->table('scm_activity')->field($field)->where($condition)->find();
    }

    /**
     * 更新活动信息
     * @param $condition
     * @return bool
     */
    public function updateActivity($condition){
        $update_id = $this->table('scm_activity')->where(array('act_id'=>$condition["act_id"]))->update($condition);
        if($update_id)
            return true;
        else
            return false;
    }
    /*
    * 批量删除活动
    * @param array $ids
    * @return boolean
    */
    public function delActivityByIdString($ids){
        if(empty($ids)){
            return false;
        }
        $activity_list = $this->table('scm_activity')->where(array('act_id' => array('in', $ids)))->select();
        foreach($activity_list as $k => $v){
            @unlink(BASE_UPLOAD_PATH.DS.'scm/activity'.DS.$v['act_banner']);
        }
        $delete_id = $this->table('scm_activity')->where(array('act_id' => array('in', $ids)))->delete();
        if($delete_id)
            return true;
        else
            return false;
    }

    /**
     * 分页获取活动信息
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getActivityList($condition = array(), $field = '*', $page = null, $order = 'act_id desc', $limit = ''){
        return $this->table('scm_activity')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 获取个数
     */
    public function getActivityCount($condition){
        return $this->table('scm_activity')->where($condition)->count();
    }
    public function getActivityAndSuppCount($condition = array(), $field = '*', $page = null, $order = 'act_id desc')
    {
        return $this->table('scm_activity,scm_supplier')->join('inner')->on('scm_activity.supp_id=scm_supplier.supp_id')->where($condition)->field($field)->page($page)->order($order)->select();
    }
    public function getActivityAndSupp($condition = array(), $field = '*', $page = null, $order = 'act_id desc')
    {
        return $this->table('scm_activity,scm_supplier')->join('inner')->on('scm_activity.supp_id=scm_supplier.supp_id')->where($condition)->field($field)->page($page)->order($order)->select();
    }


}