<?php
/**
 * 控制台
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class aboutusControl extends SCMControl{
    public function __construct(){
        parent::__construct();
        Language::read('dashboard');
    }

    public function indexOp() {
        $this->aboutusOp();
    }

    /**
     * 关于我们
     */
    public function aboutusOp(){
        $version = C('version');
        $v_date = substr($version,0,4).".".substr($version,4,2);
        $s_date = substr(C('setup_date'),0,10);
        Tpl::output('v_date',$v_date);
        Tpl::output('s_date',$s_date);
        Tpl::showpage('aboutus', 'null_layout');
    }

}
