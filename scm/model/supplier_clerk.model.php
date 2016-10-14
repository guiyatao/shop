<?php
/*
 * 供应商员工管理
 */
defined('InShopNC') or exit('Access Invalid!');

class supplier_clerkModel extends Model {
    /*
     * 按条件获取业务员的详细信息
     */
    public function getClerkInfo($condition, $field = '*', $master = false){
        $on = 'admin.admin_id = scm_user.admin_id';
        return $this->table('admin,scm_user')->field($field)->join('left')->on($on)->where($condition)->master($master)->find();
    }

    /**
     * 注册业务员
     *
     * @param   array $param 业务员信息
     * @return  array 数组格式的返回结果
     */
    public function addClerk($admin,$user) {
        if(empty($admin) || empty($user)) {
            return false;
        }
        try {
            $this->beginTransaction();
            $insert_id  = $this->table('admin')->insert($admin);
            $user['admin_id'] = $insert_id;
            $this->table('scm_user')->insert($user);
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

    /*
     * 修改业务员信息
     * @param   array $param 业务员信息
     * @return  array 数组格式的返回结果
     */
    public function editClerk($admin,$user){
        if(empty($admin) || empty($user)) {
            return false;
        }
        $update_admin_id = $this->table('admin')->where(array('admin_id'=>$admin["admin_id"]))->update($admin);
        $update_user_id = $this->table('scm_user')->where(array('user_id'=>$user["user_id"]))->update($user);
        if($update_user_id && $update_admin_id)
            return true;
        else
            return false;
    }

    /*
     * 分页获取业务员列表
     * @param array $condition  筛选条件
     * @param string $field 筛选的字段
     * @param number $page  分页条件
     * @param string $order  排序
     */
    public function getClerkList($condition = array(), $field = '*', $page = null, $order = 'user_id desc', $limit = ''){
        $on = 'admin.admin_id = scm_user.admin_id';
        $condition['user_type'] = 3;
        return $this->table('admin,scm_user')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /*
     * 获取业务员数量
     */
    public function getClerkCount($condition){
        $on = 'scm_user.supp_clie_id = scm_supplier.supp_id';
        $condition['user_type'] = 3;
        return $this->table('scm_user,scm_supplier')->join('left')->on($on)->where($condition)->count();
    }
    /*
     * 获取业务员头像
     */
    public function getClerkAvatar($avatar){
        if(file_exists(BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR.'/'.$avatar) && !empty($avatar))
            return UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.'/'.$avatar;
        else
            return UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait');
    }
    /*
     * 批量删除业务员
     * @param array $ids
     * @return boolean
     */
    public function delClerkByIdString($ids){
        if(empty($ids)){
            return false;
        }
        $admin_list = $this->getClerkList(array('user_id' => array('in', $ids)),'scm_user.admin_id');
        $admin_id_list = array();
        foreach($admin_list as $k => $v){
            $admin_id_list[$k] = $v['admin_id'];
        }
        $delete_user = $this->table('scm_user')->where(array('user_id' => array('in', $ids)))->delete();
        $delete_admin = $this->table('admin')->where(array('admin_id' => array('in', $admin_id_list)))->delete();
        if($delete_user && $delete_admin)
            return true;
        else
            return false;
    }

}