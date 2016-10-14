<?php
/**
 * 商品分类管理
 */
defined('InShopNC') or exit('Access Invalid!');
class supplier_categoryModel extends Model {

    /**
     *
     *添加商品分类
     * @param   array $category 业务员信息
     * @return  array 数组格式的返回结果
     */
    public function addCategory($category) {
        if(empty($category)) {
            return false;
        }
        try {
            $this->beginTransaction();
            $insert_id  = $this->table('scm_supp_category')->insert($category);
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
     * 获取分类信息
     * @param array $condition 查询条件, array $field 筛选列
     * @return array 数组格式返回查询结果
     */
    public function getCategoryInfo($condition , $field = '*'){
        return $this->table('scm_supp_category')->field($field)->where($condition)->find();
    }

    /**
     * 按条件获取分类列表
     * @param $condition 查询条件, array $field 筛选列
     * @return array 数组格式返回查询结果
     */
    public function getCategoryList($condition = array(), $field = '*',$order = 'sort asc'){
        return $this->table('scm_supp_category')->field($field)->where($condition)->order($order)->select();
    }

    /**
     * 获取分类的数量
     * @param $condition
     */
    public function getCategoryCount($condition){
        return $this->table('scm_supp_category')->where($condition)->count();
    }
    /**
     * 更新商品分类的详细信息
     *
     * @param array $update_category
     * @param int $id
     * @return bool
     */
    public function updateCategory($update_category){
        $update_id = $this->table('scm_supp_category')->where(array('cate_id'=>$update_category["cate_id"]))->update($update_category);
        if($update_id)
            return true;
        else
            return false;
    }
}