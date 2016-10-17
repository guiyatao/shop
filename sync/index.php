<?php
/**
 * POS端同步
 *
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

define('APP_ID','sync');
define('IGNORE_EXCEPTION', true);
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));

require __DIR__ . '/../shopnc.php';

//框架扩展
require(BASE_PATH.'/framework/function/function.php');

Shopnc\Core::runApplication();
