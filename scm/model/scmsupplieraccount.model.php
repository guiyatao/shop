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
class scmsupplieraccountModel extends Model {
    /**
     * 活动列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getSupplier(){
        $param  = array();
        $param['table'] = 'scm_user';
        $param['where'] = ' user_id=1 ';
        return $this->select1($param,'');
    }

    /**
     * 更新活动
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updateSupplier($data,$id){
        return $this->update1('scm_user',$data," user_id='$id' ");
    }

}
