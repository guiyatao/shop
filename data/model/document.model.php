<?php
/**
 * 系统文章
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class documentModel extends Model {
    /**
     * 查询所有系统文章
     */
    public function getList(){
        $param  = array(
            'table' => 'document'
        );
        return $this->select1($param);
    }
    /**
     * 根据编号查询一条
     *
     * @param unknown_type $id
     */
    public function getOneById($id){
        $param  = array(
            'table' => 'document',
            'field' => 'doc_id',
            'value' => $id
        );
        return $this->getRow1($param);
    }
    /**
     * 根据标识码查询一条
     *
     * @param unknown_type $id
     */
    public function getOneByCode($code){
        $param  = array(
            'table' => 'document',
            'field' => 'doc_code',
            'value' => $code
        );
        return $this->getRow1($param);
    }
    /**
     * 更新
     *
     * @param unknown_type $param
     */
    public function updates($param){
        return $this->update1('document',$param,"doc_id='{$param['doc_id']}'");
    }
}
