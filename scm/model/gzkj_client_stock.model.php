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

class gzkj_client_stockModel extends Model
{


    public function __construct()
    {
        parent::__construct('scm_client_stock');
    }

    /**
     * 更新终端店
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input, $id)
    {
        return $this->update1('scm_client', $input, " clie_id='$id' ");
    }

}
