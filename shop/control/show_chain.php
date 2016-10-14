<?php
/**
 * 会员店铺
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');

class show_chainControl extends BaseChainControl {
    public function __construct(){
        parent::__construct();
    }
    /**
     * 展示门店
     */
    public function indexOp() {
        $chain_id = intval($_GET['chain_id']);
        $chain_info = Model('chain')->getChainInfo(array('chain_id' => $chain_id));
        Tpl::output('chain_info', $chain_info);
        Tpl::showpage('show_chain');
    }
}
