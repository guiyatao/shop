<?php
/**
 * cms首页
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
class indexControl extends syncHomeControl{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function indexOp() {
        $codes = $_POST['codes'];
        $client_id= $_POST['clientid'];
        $codearr = explode(",",$codes);
//         echo $client_id;
        $model_scm_stock = SCMModel("scm_client_stock");
        foreach($codearr as $code) {
            $c = explode("|", $code);
            $barcode = $c[0];
            $qty = $c[1];
//             echo $barcode.','.$qty;
        }
        
        $retcode = "6953392554351|914,6953245711849|118";
        output_data(array('changelist'=>$retcode));

    }

}
