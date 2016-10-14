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

class gzkj_online_order_goodsModel extends Model
{

    public function __construct()
    {
        parent::__construct('scm_online_order_goods');
    }



    public function execute_sql($sql)
    {
        if (empty($sql)) {
            return null;
        }
        $result = $this->query($sql);
        if ($result === false) return array();
        $goods_list = array();
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $goods_list[] = $tmp;
        }
        return !empty($goods_list) ? $goods_list : null;
    }
}
