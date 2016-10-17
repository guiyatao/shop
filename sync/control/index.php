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
        
//         $codes = '6953392554351|904';
//         $client_id= 'LHHS0000101';
                
        if(empty($client_id)) {
            $errret = array();
            $errret['error'] = 1;
            $errret['msg'] = '终端店ID不正确.';
            output_data($errret);
        }
        
        if(empty($codes)) {
            $errret = array();
            $errret['error'] = 1;
            $errret['msg'] = 'Barcode格式不正确.';
            output_data($errret);
        }
        
        $basegoodsqty = array();
        try {
            $codearr = explode(",",$codes);
            foreach($codearr as $code) {
                $c = explode("|", $code);
                $basegoodsqty[$c[0]] = $c[1];
            }
        } catch (Exception $e) {
            $errret = array();
            $errret['error'] = 1;
            $errret['msg'] = 'Barcode格式不正确.';
            output_data($errret);
        }
        
        $model_scm_stock = SCMModel("scm_client_stock");
        $this->pos_client_id = $client_id;
        
        $condition['clie_id'] = array('eq', $client_id);
        $condition['goods_barcode'] = array("in", implode(",", array_keys($basegoodsqty)));

        //获取当前库存 ---不直接取
        $goodsinfo = $model_scm_stock->getStockList($condition);
        if(count($goodsinfo)==0) {
            output_data(array('changelist'=>array()));
        }

//         $basegoodsqty = array();
        $min_sync_time = NULL;
        foreach($goodsinfo as $goods) {
//             $basegoodsqty[$goods['goods_barcode']] = array(
//                 "goods_barcode" => $goods['goods_barcode'],
//                 "goods_stock" => $goods['goods_stock'],
//                 "sync_time" => $goods['sync_time']
//             );
            if ($min_sync_time == null) $min_sync_time = $goods['sync_time'];
            if($min_sync_time > $goods['sync_time']) $min_sync_time = $goods['sync_time'];
        }

        //获取同步之后发生的入库数量
        if($min_sync_time==null) $min_sync_time = date("Y-m-d H:i:s", strtotime("2016-01-01"));
        $condition["in_stock_date"] = array("gt", $min_sync_time);
        $model_scm_in_stock = SCMModel("scm_instock_info");
        $instockgoodsinfo = array();
        $instocklist = $model_scm_in_stock->getInStockList($condition, '', "clie_id, goods_barcode, set_num, unit_num" );
//         $instockgoodsinfo = $model_scm_stock->getClientGoodInstockInfo($condition);
        foreach($instocklist as $in) {
            $unit_num = 1;
            if($in['unit_num'] && $in['unit_num'] > 1) {
                $unit_num = $in['unit_num'];
            }
            $instockgoodsinfo[$in['goods_barcode']] += $in['set_num'] * $unit_num;
        }
        
//         获取同步之后发生的出库数量
        $min_sync_time_stamp = strtotime($min_sync_time);
        $outstock=array();
        $sql = "
                SELECT g.goods_barcode, sum(og.goods_num) as outstock FROM `gzkj_scm_online_order` soo
                join gzkj_orders o on soo.order_id=o.order_id
                join gzkj_order_goods og on og.order_id=o.order_id
                join gzkj_goods g on g.goods_id=og.goods_id
                where soo.clie_id='$client_id' and o.order_state=40 and o.refund_state < 2 and g.goods_barcode in(" . implode(",", array_keys($basegoodsqty)) . ") and o.finnshed_time > ". $min_sync_time_stamp ." GROUP BY g.goods_barcode"
            ;
        $result = Model() -> query($sql);
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $outstock[$tmp['goods_barcode']] = $tmp['outstock'];
        }

        //获取退货发生的入库数量
        $refundstock = array();
        $sql = "
                SELECT g.goods_barcode, sum(rr.goods_num) as refundstock FROM `gzkj_scm_online_order` soo
                join gzkj_orders o on soo.order_id=o.order_id
                join gzkj_order_goods og on og.order_id=o.order_id
                join gzkj_goods g on g.goods_id=og.goods_id
                join gzkj_refund_return rr on rr.order_id=o.order_id and rr.goods_id=g.goods_id
                where soo.clie_id='$client_id' and o.order_state=40 and o.refund_state < 2 and g.goods_barcode in(".implode(",", array_keys($basegoodsqty)).") and o.finnshed_time > $min_sync_time_stamp
                group by g.goods_barcode
            ";
        $result = Model() -> query($sql);
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $refundstock[$tmp['goods_barcode']] = $tmp['refundstock'];
        }

        foreach($basegoodsqty as $k=>$v) {
            $basegoodsqty[$k] += $instockgoodsinfo[$k];
            $basegoodsqty[$k] -= $outstock[$k];
            $basegoodsqty[$k] += $refundstock[$k];
        }
        
        $ret = array();
        foreach($basegoodsqty as $k=>$v) {
            $ret[] = "$k|$v";
            $updatedata = array(
                "goods_barcode" => $k,
                "clie_id" => $client_id,
                "goods_stock" => $v,
                "sync_time" => date("Y-m-d H:i:s")
            );
            $model_scm_stock->editStockInfo($updatedata);
        }
        $retcode = implode(",", $ret);
//         $retcode = "6953392554351|914,6953245711849|118";

        output_data(array('changelist'=>$retcode));

    }

}
