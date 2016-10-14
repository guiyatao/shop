<?php
/**
 * SCM 拆单
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城 Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class shoponlineorderModel extends Model
{

    public function addToScmOrder($shop_order_id, $reorder=false) {
        
        $retClients = array();
        $model_order= Model('order');
        $model_goods= Model('goods');
        $model_scm_client = SCMModel('scm_client');
        
        $order_info_common = $model_order->getOrderCommonInfo(array('order_id'=>$shop_order_id));
        $order_info = $model_order->getOrderInfo(array('order_id'=>$shop_order_id));

        $pickup_type=$order_info_common['pickup_type'];
        $receive_address = unserialize($order_info_common['reciver_info']);
        $receive_address = $receive_address['address'];

        //自提，也分配到店了
        if($pickup_type==1) {
            $scm_client_id = $order_info_common['pickup_store'];
            $scmorderdata = array();
            
            $scmorderdata['order_id']  = $order_info['order_id'];
            $scmorderdata['clie_id']  = $order_info_common['pickup_store'];
            $scmorderdata['clie_address']  = $order_info[''];
            $scmorderdata['order_sn']  = $order_info['order_sn'];
            $scmorderdata['store_name']  = $order_info['store_name'];
            $scmorderdata['buyer_name']  = $order_info['buyer_name'];
            $scmorderdata['buyer_phone']  = $order_info['buyer_phone'];
            $scmorderdata['buyer_address']  = $receive_address;
            $scmorderdata['pickup_mode']  = 0;
            $scmorderdata['add_time']  = $order_info['add_time'];
            $scmorderdata['payment_code']  = $order_info['payment_code'];
            $scmorderdata['payment_time']  = $order_info['payment_time'];
            $scmorderdata['finnshed_time']  = $order_info['finnshed_time'];
            $scmorderdata['order_amount']  = $order_info['order_amount'];
            $scmorderdata['order_state']  = $order_info['order_state'];
            $scmorderdata['buyer_comment']  = $order_info_common['order_message'];
            $this->table('scm_online_order')->insert($scmorderdata);
            
            $goodslist = $model_order->getOrderGoodsList(array('order_id'=>$shop_order_id));
            $goodsids = array();
            foreach($goodslist as $g) {
                $goodsids[] = $g['goods_id'];
            }
            
            $barcodes = array();
            $goodslistforbarcode = $this->table("goods")->field("goods_id, goods_barcode")->where("goods_id in (" . implode(",", $goodsids).")")->select();
            foreach($goodslistforbarcode as $k=>$v) {
                $barcodes[$v['goods_id']] = $v['goods_barcode'];
            }
            
            foreach($goodslist as $g) {
                $scmordergoodsdata = array();
                $scmordergoodsdata['order_id']  = $shop_order_id;
                $scmordergoodsdata['clie_id']  = $order_info_common['pickup_store'];
                $scmordergoodsdata['goods_name']  = $g['goods_name'];
                $scmordergoodsdata['goods_price']  = $g['goods_price'];
                $scmordergoodsdata['goods_num']  = $g['goods_num'];
                $scmordergoodsdata['goods_barcode']  = $barcodes[$g['goods_id']];
                $this->table('scm_online_order_goods')->insert($scmordergoodsdata);
            }
            $client_info = $model_scm_client->getClientInfo(array('clie_id'=>$order_info_common['pickup_store']));
            $retClients[] = array('clie_id'=>$client_info['clie_ch_name'], 'wx_openid'=>$client_info['wechat_id'],'msg'=>array('orderid'=>$order_info['order_sn'],'name'=>$order_info['buyer_name'],'phone'=>$order_info['buyer_phone'],'address'=>$receive_address, 'time'=>date("Y-m-d H:i:s", $order_info['add_time'])) );

        } else {
            //需要重新分配商品到各个店铺

            $geo = $this->getGeoByAddress($receive_address);
            $goodslist = $model_order->getOrderGoodsList(array('order_id'=>$shop_order_id));
            $goodsids = array();
            foreach($goodslist as $g) {
                $goodsids[] = $g['goods_id'];
            }
            $shops = $this->assignGoodsToStores($shop_order_id, implode(",", $goodsids), $geo);
            foreach($shops as $s) {
                $barcodes = array();
                $clien_id = "";
                $scmorderdata=array();
                for($i = 0; $i < count($s); $i++) {
                    if($i == 0) {
                        //第一条时插入order信息
                        $clien_id = $s[$i]['clie_id'];
                        $scmorderdata['order_id']  = $order_info['order_id'];
                        $scmorderdata['clie_id']  = $clien_id;
                        $scmorderdata['clie_address']  = $order_info[''];
                        $scmorderdata['order_sn']  = $order_info['order_sn'];
                        $scmorderdata['store_name']  = $order_info['store_name'];
                        $scmorderdata['buyer_name']  = $order_info['buyer_name'];
                        $scmorderdata['buyer_phone']  = $order_info['buyer_phone'];
                        $scmorderdata['buyer_address']  = $receive_address;
                        $scmorderdata['pickup_mode']  = 1;
                        $scmorderdata['add_time']  = $order_info['add_time'];
                        $scmorderdata['payment_code']  = $order_info['payment_code'];
                        $scmorderdata['payment_time']  = $order_info['payment_time'];
                        $scmorderdata['finnshed_time']  = $order_info['finnshed_time'];
                        $scmorderdata['order_state']  = $order_info['order_state'];
                        $scmorderdata['buyer_comment']  = $order_info_common['order_message'];                    
                    }
                    $barcodes[] = $s[$i]['goods_barcode'];
                }
                $goodslist = $model_order->getOrderGoodsList(array('order_id'=>$shop_order_id));
                
                $goodslistforbarcode = $this->table("goods")->field("goods_id, goods_barcode")->where("goods_barcode in (" . implode(",", $barcodes).")")->select();
                foreach($goodslistforbarcode as $k=>$v) {
                    $barcodes[$v['goods_id']] = $v['goods_barcode'];
                }
                
                $order_amount = 0;
                foreach($goodslist as $g) {
                    if(isset($barcodes[$g['goods_id']])) {
                        $scmordergoodsdata['order_id']  = $shop_order_id;
                        $scmordergoodsdata['clie_id']  = $clien_id;
                        $scmordergoodsdata['goods_name']  = $g['goods_name'];
                        $scmordergoodsdata['goods_price']  = $g['goods_price'];
                        $order_amount += ($g['goods_price'] * $g['goods_num']);
                        $scmordergoodsdata['goods_num']  = $g['goods_num'];
                        $scmordergoodsdata['goods_barcode']  = $barcodes[$g['goods_id']];
                        $this->table('scm_online_order_goods')->insert($scmordergoodsdata);
                    }
                }
                
                $scmorderdata['order_amount']  = $order_amount;
                $this->table('scm_online_order')->insert($scmorderdata);
                $client_info = $model_scm_client->getClientInfo(array('clie_id'=>$clien_id));
                $retClients[] = array('clie_id'=>$client_info['clie_ch_name'], 'wx_openid'=>$client_info['wechat_id'],'msg'=>array('orderid'=>$order_info['order_sn'],'name'=>$order_info['buyer_name'],'phone'=>$order_info['buyer_phone'],'address'=>$receive_address, 'time'=>date("Y-m-d H:i:s", $order_info['add_time'])));
            }
        }
        return $retClients;
    }
    
    /**
     * 活动列表
     *
     * @param array $condition
     *            查询条件
     * @param obj $page
     *            分页对象
     * @return array 二维数组
     */
    public function getGeoByAddress($address)
    {
        $url = "http://api.map.baidu.com/geocoder/v2/?ak=8ekeZOxobazKilGsbEemRRTm&output=json&address=" . urlencode($address);

        $ret = file_get_contents($url);
        $geo = new stdClass();
        if ($ret) {
            $json = json_decode($ret);
            if ($json) {
                if (isset($json->result)) {
                    if (isset($json->result->location)) {
                        if (isset($json->result->location->lng)) {
                            $geo->lng = $json->result->location->lng;
                            $geo->lat = $json->result->location->lat;
                            return $geo;
                        }
                    }
                }
            }
        }
        return $geo;
    }
    
    public function getStoreByGoodsid($goodsids, $geo, $distance = 1)
    {
        $ret = array();
        if(empty($goodsids)) {
            return $ret;
        }
        $tmparr = explode(",",$goodsids);
        $expgoodsids = array();
        if(is_array($tmparr)) {
            foreach($tmparr as $v) {
                $tmparr2 = explode("|", $v);
                if(count($tmparr2) == 2) {
                    $expgoodsids[$tmparr2[0]] = $tmparr2[1];
                } else {
                    if(count($tmparr2) == 1) {
                        $expgoodsids[$tmparr2[0]] = 1;
                    }
                }
            }
        }
        
        $goodsidsstr = implode(",",array_keys($expgoodsids));
        $aroundGeo = $this->getLatlngRange($geo->lat, $geo->lng, $distance);
        $barcodes = $this->getBarcodesByIds($goodsidsstr);
        
        if($barcodes && count($barcodes) > 0) {
            $ret = $this->_getStore($barcodes, $aroundGeo);
        }
        return $ret;
    }

    public function getLatlngRange($lat, $lng, $distance = 1)
    {
        $half = 6371;
        if ($distance == 0) {
            $distance = 1;
        }
        
        $dlng = 2 * asin(sin($distance / (2 * $half)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance / $half;
        $dlat = rad2deg($dlat);
        
        $maxlat = round($lat + $dlat, 6);
        $minlat = round($lat - $dlat, 6);
        if ($maxlat < $minlat) {
            $tmp = $maxlat;
            $maxlat = $minlat;
            $minlat = $tmp;
        }
        
        $maxlng = round($lng + $dlng, 6);
        $minlng = round($lng - $dlng, 6);
        if ($maxlng < $minlng) {
            $tmp = $maxlng;
            $maxlng = $minlng;
            $minlng = $tmp;
        }
        
        $ret = new stdClass();
        $ret->latmin = $minlat;
        $ret->latmax = $maxlat;
        $ret->lngmin = $minlng;
        $ret->lngmax = $maxlng;
        return $ret;
    }

    private function _getStore($barcodes, $georange)
    {
        $param = array(
            'table' => 'scm_client',
            'field' => '*'
        );
        
        $condition = " is_close=0 ";
        if (isset($georange)) {
            // 获取附近的零售店
            $condition .= " and 
                clie_longitude > $georange->lngmin and clie_longitude < $georange->lngmax and clie_latitude > $georange->latmin and clie_latitude < $georange->latmax 
            ";
//             $param['where'] = $condition;
        }

        $param['where'] = $condition;

        $aroundStores = $this->select1($param);
        
        $sids = array();
        $addressinfo = array();
        $phone = array();
        if ($aroundStores && count($aroundStores) > 0) {
            foreach ($aroundStores as $s) {
                $sids["'" . $s['clie_id'] . "'"] = $s['clie_ch_name'];
                $addressinfo["'" . $s['clie_id'] . "'"] = $s['area_province'].$s['area_city'].$s['area_district'].$s['clie_address'];
                $phone["'" . $s['clie_id'] . "'"] = array("tel"=>$s['clie_tel'], "mobile"=>$s['clie_mobile']);
            }
        }
        
        // can not find the stores then return empty
        if (count($sids) == 0) {
            return array();
        }
        
        $where_barcode = "";

        // check store stock
        $param = array(
            'table' => 'scm_client_stock',
            'field' => ' clie_id, count(distinct goods_barcode) as cnt ',
            'where' => " goods_barcode in (" . implode(",", $barcodes) . ") and clie_id in (" . implode(",", array_keys($sids)) . ")  and goods_stock > 0 ",
            'group' => ' clie_id ',
            'order' => ' cnt desc ',
            'limit' => ' 0, 5'
        );
       
        $result = $this->select1($param);

        // check store stock
        $paramtotalcnt = array(
            'table' => 'scm_client_stock',
            'field' => ' count(distinct goods_barcode) as cnt ',
            'where' => " goods_barcode in (" . implode(",", $barcodes) . ") and clie_id in (" . implode(",", array_keys($sids)) . ")  and goods_stock > 0 ",
            'limit' => ' 0, 5'
        );
        $resulttotalcnt = $this->select1($paramtotalcnt);
        
        $ret = array();
        if($result && count($result) > 0) {
            foreach ($result as $s) {
                if ($s['cnt'] == count($barcodes)) {
                    $ret[$s['clie_id']]['name'] = $sids["'" . $s['clie_id'] . "'"];
                    $ret[$s['clie_id']]['address'] = $addressinfo["'" . $s['clie_id'] . "'"];
                    $ret[$s['clie_id']]['phone'] = $phone["'" . $s['clie_id'] . "'"];
                }
            }
        }
        if($resulttotalcnt && count($resulttotalcnt) > 0) {
            $ret1['totalcnt'] = $resulttotalcnt[0]['cnt'];
        } else {
            $ret1['totalcnt'] = 0;
        }
        $ret1['client'] = $ret;
        return $ret1;
    }

    private function getBarcodesByIds($goodsids)
    {

        $param = array(
            'table' => 'goods',
            'field' => ' goods_barcode as barcode ',
            'where' => " goods_id in ($goodsids) "
        );

        $result = $this->select1($param);
        $ret = array();
        if($result && count($result) > 0) {
            foreach ($result as $b) {
                $ret[$b['barcode']] = "'" . $b['barcode'] . "'";
            }
        }
        return $ret;
    }
    
    // assign goods to stores
    public function assignGoodsToStores($shop_order_id, $goodsids, $geo)
    {
        $distince = 1;

        $barcodes = $this->getBarcodesByIds($goodsids);
        $totalcnt = count($barcodes);
        $ret = array();
        if ($totalcnt > 0) {
            $aroundGeo = $this->getLatlngRange($geo->lat, $geo->lng, $distince);
            $precnt = 0;
            //
            while ($totalcnt != $precnt) {
                $precnt = $totalcnt;
                
                if (count($barcodes)==0) break;
                $stores = $this->_assignStore($shop_order_id, $barcodes, $aroundGeo);
                if (count($stores) == 0)
                    break;
                $ret[] = $stores;
                
                foreach ($stores as $k => $v) {
                    unset($barcodes[$v['goods_barcode']]);
                }
                $totalcnt = count($barcodes);
            }
            return $ret;
        }
        
        return array();
    }

    private function _assignStore($shop_order_id, $barcodes, $georange)
    {
        //todo 拆单的时候需要根据状态60的order，找到对应的goodsid，目前还没处理。
        $existorderparam = array(
            'table' => 'scm_online_order',
            'field' => 'clie_id',
            'where' => 'order_id='.$shop_order_id
        );

        $existorderresult = $this->select1($existorderparam);
        $existclieids = array();
        if($existorderresult && count($existorderresult) > 0) {
            foreach ($existorderresult as $s) {
                $existclieids[] = "'".$s['clie_id']."'";
            }
        }
        
        $param = array(
            'table' => 'scm_client',
            'field' => '*'
        );
        $condition = " is_close=0 ";
        // 优先分给附近的零售店
        if (isset($georange)) {
            // 获取附近的零售店
            $condition .= " and 
                clie_longitude > $georange->lngmin and clie_longitude < $georange->lngmax and clie_latitude > $georange->latmin and clie_latitude < $georange->latmax
            ";
            if(count($existclieids) > 0) {
                $condition .= " and clie_id not in (" . implode(",",$existclieids).") ";
            }
//             $param['where'] = $condition;
        }
        
        $param['where'] = $condition;
        
        $aroundStores = $this->select1($param);
        
        $sids = array();
        if ($aroundStores && count($aroundStores) > 0) {
            foreach ($aroundStores as $s) {
                $sids["'" . $s['clie_id'] . "'"] = $s['clie_ch_name'];
            }
        }
        
        // can not find the stores then return empty
        if (count($sids) == 0) {
            return array();
        }
        
        // check store stock
        $param = array(
            'table' => 'scm_client_stock',
            'field' => ' clie_id, count(goods_barcode) as cnt ',
            'where' => " goods_barcode in (" . implode(",", $barcodes) . ") and clie_id in (" . implode(",", array_keys($sids)) . ") and goods_stock > 0 ",
            'group' => ' clie_id ',
            'order' => ' cnt desc ',
            'limit' => ' 0, 1'
        );
        
        $result = $this->select1($param);
        $ret = array();
        if ($result) {
            foreach ($result as $s) {
                $clientid = $s['clie_id'];
                break;
            }
            $param2 = array(
                'table' => 'scm_client_stock',
                'field' => ' clie_id, goods_barcode ',
                'where' => " goods_barcode in (" . implode(",", $barcodes) . ") and clie_id ='" . $clientid . "' and goods_stock > 0 "
            );
            $result2 = $this->select1($param2);
            if ($result2) {
                foreach ($result2 as $s) {
                    $ret[] = array(
                        'clie_id' => $s['clie_id'],
                        'goods_barcode' => $s['goods_barcode']
                    );
                }
            }
        }
        return $ret;
    }
}
