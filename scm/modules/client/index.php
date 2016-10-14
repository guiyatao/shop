<?php
/**
 * 商城板块初始化文件
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */

define('BASE_PATH',str_replace('\\','/',dirname(dirname(dirname(__FILE__)))));
define('MODULES_BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
require __DIR__ . '/../../../shopnc.php';
require __DIR__ . '/../../framework/function/function.php';

define('APP_SITE_URL', SCM_SITE_URL.'/modules/system');
define('TPL_NAME',TPL_ADMIN_NAME);
define('SHOP_RESOURCE_SITE_URL',SHOP_SITE_URL.DS.'resource');
define('ADMIN_TEMPLATES_URL',SCM_SITE_URL.'/templates/'.TPL_NAME);
define('ADMIN_RESOURCE_URL',SCM_SITE_URL.'/resource');
define('SHOP_TEMPLATES_URL',SHOP_SITE_URL.'/templates/'.TPL_NAME);
define('BASE_TPL_PATH',MODULES_BASE_PATH.'/templates/'.TPL_NAME);
define('MODULE_NAME', 'client');

Shopnc\Core::runApplication(MODULES_BASE_PATH);
