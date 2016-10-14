<?php
/**
 * 验证码
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');

class seccodeControl{

    public function __construct(){
    }
    /**
     * 产生验证码
     *
     */
    public function makecodeOp(){
        $refererhost = parse_url($_SERVER['HTTP_REFERER']);
        $refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';

        $seccode = makeSeccode($_GET['nchash']);

        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        @header("Pragma: no-cache");
        
        @header("Content-type:image/png");

        echo \Shopnc\Lib::imager()->createCaptcha($seccode, 90, 26);
    }

    /**
     * AJAX验证
     *
     */
    public function checkOp(){
        if (checkSeccode($_GET['nchash'],$_GET['captcha'])){
            exit('true');
        }else{
            exit('false');
        }
    }
}
