<?php
/**
 * scm日志
 */

defined('InShopNC') or exit('Access Invalid!');

class gzkj_scm_logModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加短信记录
     *
     * @param
     * @return int
     */
    public function addScm_log($log_array) {
        $log_id = $this->table('scm_log')->insert($log_array);
        return $log_id;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getScm_logInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('scm_log')->where($condition)->order('log_id desc')->find();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getScm_logList($condition = array(), $page = '', $limit = '', $order = 'log_id desc') {
        $result = $this->table('scm_log')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }

    /**
     * 取得记录数量
     *
     * @param
     * @return int
     */
    public function getScm_logCount($condition) {
        return $this->table('scm_log')->where($condition)->count();
    }

}