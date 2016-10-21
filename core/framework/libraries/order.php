<?php
/**
 * 订单相关通用函数
 * 为静态使用
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @author     ShopNC Team
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

final class Order
{
    private static $shop_status = array(0=>"已取消",10=>"未付款",20=>"待发货",30=>"已发货",40=>"交易完成",50=>"已接单",60=>"已弃单");
    //订单状态(未完成0/已完成1/半单2/取消单3/退货单4)
    private static $client_status = array(0=>"未完成", 1=>"已完成", 2=>"半单", 3=>"取消", 4=>"退货");

    private static $prepare_status = array(0=>"未备货",1=>"备货完成");

    private static $gift_status = array(0=>"无赠品",1=>"有赠品");

    private static $out_status = array(0=>"未发货",1=>"已发货");

    private static $refund_status = array(0=>"未申请退货",1=>"已申请退货");

    private static $in_status = array(0=>"未入库",1=>"已入库");

    private static $activity_status = array(0=>"失效", 1=>"审核通过", 2=>"未审核", 3=>"审核未通过");
    /**
     * 获取订单中文状态
     * 0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;
     *
     * @param
     *            $id
     * @return 中文状态
     */
    public static function getShopOrderStatusByID($id)
    {
        return self::$shop_status[$id];
    }
    
    public static function getClientOrderStatusByID($id) {
        return self::$client_status[$id];
    }

    /**
     * 获取供应商备货状态
     *
     * @param
     * $id
     * @return 供应商备货状态
     */
    public static function getPrepareStatusByID($id)
    {
        return self::$prepare_status[$id];
    }

    /**
     * 获取订单有无赠品
     *
     * @param
     * $id
     * @return 有无赠品
     */
    public static function getGiftStatusByID($id)
    {
        return self::$gift_status[$id];
    }

    /**
     * 获取供应商发货状态
     * @param $id
     * @return 供应商发货状态
     */
    public static function getOutStatusByID($id){
        return self::$out_status[$id];
    }

    /**
     * 获取订单有无退货请求
     * @param $id
     * @return 有无退货请求
     */
    public static function getRefundStatusByID($id){
        return self::$refund_status[$id];
    }

    /**
     * 获取终端店入库状态
     * @param $id
     * @return 终端店入库状态
     */
    public static function getInStockStatusByID($id){
        return self::$in_status[$id];
    }

    /**
     * 获取获取供应商活动状态
     * @param $id
     * @return 供应商活动状态
     */
    public static function getActivityStatusByID($id){
        return self::$activity_status[$id];
    }

}
