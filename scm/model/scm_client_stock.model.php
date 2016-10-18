<?php
/**
 * 终端店库存
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
class scm_client_stockModel extends Model {

    public function getAllClients($condition = array(), $field = 'clie_id, wechat_id, clie_ch_name'){
        $list = $this->table('scm_client')->field($field)->where($condition)->select();
        return $list;
    }

    /**
     * 取得库存列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getStockList($condition, $pagesize = '', $field = '*', $order = 'id desc', $limit = '', $extend = array(), $master = false){
        //$on = 'scm_client_stock.goods_barcode = scm_supp_stock.goods_barcode';
        $list = $this->table('scm_client_stock')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
        if (empty($list)) return array();
        $order_list = array();
        if (empty($order_list)) $order_list = $list;
        return $order_list;
    }

    /**
     * 取得缺货库存列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getStockoutList($condition, $pagesize = '', $field = '*', $order = 'scm_client_stock.supp_id desc', $master = false){
        $condition['scm_client_stock.goods_stock'] = array('lt', array('exp', 'scm_client_stock.goods_low_stock'));
        //$on = 'scm_client_stock.goods_barcode = scm_supp_stock.goods_barcode';
        //$list = $this->table('scm_client_stock,scm_supp_stock')->field($field)->join('inner')->on($on)->where($condition)->page($pagesize)->order($order)->master($master)->select();
        $list = $this->table('scm_client_stock')->field($field)->where($condition)->page($pagesize)->order($order)->master($master)->select();
        if (empty($list)) return array();
        $stockout_list = array();
        if (empty($stockout_list)) $stockout_list = $list;
        return $stockout_list;
    }

    /**
     * 取得商品列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getAllGoodsList($condition, $pagesize = '', $field = '*', $order = 'goods_barcode desc', $master = false){
        // $supp_list = $this->table('scm_supp_client')->field(array('supp_id'))->where(array('clie_id'=>$clie_id))->select();
        // if(!function_exists('array_column')) {
        //     $condition['supp_id'] = array('in', $this->get_array_column($supp_list, 'supp_id'));
        // } else {
        //     $condition['supp_id'] = array('in', array_column($supp_list, 'supp_id'));
        // }   
        $list = $this->table('scm_supp_stock')->field($field)->where($condition)->page($pagesize)->order($order)->master($master)->select();
        if (empty($list)) return array();
        $model_goods = Model('goods');
        foreach ($list as $k => $v) {
            $list[$k]['is_new_good'] = $this->table('scm_client_stock')->where(array('goods_barcode' => $v['goods_barcode']))->count() == 0 ? '新商品' : '已有商品';
            $list[$k]['goods_online_exist'] = $model_goods->where(array('goods_id' => $v['goods_barcode']))->count() == 0 ? '无' : '有';
        }
        return $list;
    }

    public function get_array_column($input, $columnKey, $indexKey = NULL){
        $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
        $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
        $result = array();
     
        foreach ((array)$input AS $key => $row)
        { 
          if ($columnKeyIsNumber)
          {
            $tmp = array_slice($row, $columnKey, 1);
            $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
          }
          else
          {
            $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
          }
          if ( ! $indexKeyIsNull)
          {
            if ($indexKeyIsNumber)
            {
              $key = array_slice($row, $indexKey, 1);
              $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
              $key = is_null($key) ? 0 : $key;
            }
            else
            {
              $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
            }
          }
     
          $result[$key] = $tmp;
        }
        return $result;
    }

    /**
     * 取得当前终端店商品库存列表中是否已有商品
     * @param
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getGoodExist($goods_barcode,$clie_id){
        return $this->table('scm_client_stock')->where(array('goods_barcode' => $goods_barcode,'clie_id' => $clie_id))->count() == 0 ? false : true;
    }

    /**
     * 取得终端店所有预警信息
     * @param
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getWarnInfo($clie_id){
        $condition = array();
        $condition['clie_id'] = $clie_id;
        $stockout_list = $this->getStockoutList($condition);
        $stockout_count = count($stockout_list);
        $sql = "SELECT sii.id,sii.clie_id,ss.supp_id, ss.supp_ch_name, sii.goods_barcode,sii.goods_nm,sii.goods_unit, sii.goods_spec,sii.set_num,sii.production_date,scs.valid_remind,scs.shelf_life,sii.order_id,
			  CASE WHEN scs.shelf_life LIKE '%年'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 360) DAY )
				WHEN scs.shelf_life LIKE '%月'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 30) DAY )
				WHEN scs.shelf_life LIKE '%天'  THEN DATE_ADD( scs.production_date, INTERVAL scs.shelf_life DAY )
				END AS expire_date
				FROM ".C('tablepre')."scm_instock_info AS sii
				LEFT JOIN ".C('tablepre')."scm_client_stock AS scs ON sii.goods_barcode = scs.goods_barcode
				LEFT JOIN ".C('tablepre')."scm_supplier AS ss ON sii.supp_id = ss.supp_id
				WHERE
				sii.clie_id = '{$clie_id}'
				AND sii.waring_flag = 1
				AND (
						(
								sii.shelf_life LIKE '%年'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL (sii.shelf_life * 360) DAY
										),
										NOW()
								) <= scs.valid_remind
						)
						OR(
								sii.shelf_life LIKE '%月'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL (sii.shelf_life * 30) DAY
										),
										NOW()
								) <= scs.valid_remind
						)
						OR (
								sii.shelf_life LIKE '%天'
								AND datediff(
										DATE_ADD(
												sii.production_date,
												INTERVAL sii.shelf_life DAY
										),
										NOW()
								) <= scs.valid_remind
						)
				)
				GROUP BY sii.id
                 ORDER BY sii.in_stock_date DESC ";
        $date_warn_list = $this->getWarnGoodsList($sql);
        $date_warn_count = count($date_warn_list);

        $sql = "SELECT
                    scs.id
                FROM
                    ".C('tablepre')."scm_client_stock AS scs
                LEFT JOIN ".C('tablepre')."scm_client AS sc ON sc.clie_id = scs.clie_id
                LEFT JOIN ".C('tablepre')."scm_supplier AS ss ON scs.supp_id = ss.supp_id
                LEFT JOIN ".C('tablepre')."scm_instock_info sii ON scs.clie_id=sii.clie_id and scs.supp_id=sii.supp_id and scs.goods_barcode=sii.goods_barcode
                WHERE
                    scs.clie_id = '{$clie_id}'
                AND scs.goods_barcode NOT IN
                (
                    SELECT
                                goods_barcode
                            FROM
                                ".C('tablepre')."scm_instock_info
                            WHERE
                                clie_id = '{$clie_id}'
                AND in_stock_date > DATE_SUB(NOW(), INTERVAL scs.drug_remind DAY)
                 )  GROUP BY scs.goods_barcode";
        $unsalable_list = $this->getWarnGoodsList($sql);
        $unsalable_count = count($unsalable_list);
        
        $result = array(
                        'stockout_count' => $stockout_count,
                        'date_warn_count' => $date_warn_count,
                        'unsalable_count' => $unsalable_count,
                        );
        return $result;
    }

    /**
     * 取得商品库存列表中达到预警的商品(所有)
     * @param
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getWarnGoodsList($sql){
        if (empty($sql)) {
              return null;
        }  
        $result = $this->query($sql);
        if ($result === false) return array();
        $goods_list = array();
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $goods_list[] = $tmp;
        }
        return !empty($goods_list) ? $goods_list : null;
    }

    /**
     * 取得供应商新商品库存列表(所有)
     * @param
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getNewGoodsList($clie_id){
        $sql = "select goods_barcode, goods_nm, goods_price, goods_discount, (goods_price*goods_discount) goods_discount_price,
            goods_unit, goods_spec, supp_id, min_set_num
            from ".C('tablepre')."scm_supp_stock where goods_barcode not in (select goods_barcode from ".C('tablepre')."scm_client_stock)
                        and supp_id in (select supp_id from ".C('tablepre')."scm_supp_client where clie_id = '$clie_id')";
        $result = $this->query($sql);
        if ($result === false) return array();
        $model_goods = Model('goods');
        $goods_list = array();
        while ($tmp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $tmp['goods_online_exist'] = $model_goods->where(array('goods_id' => $tmp['goods_barcode']))->count();
            $goods_list[] = $tmp;
        }
        return !empty($goods_list) ? $goods_list : null;
    }

    /**
     * 增加新商品到库存表
     *
     * @param
     * @return int
     */
    public function addNewGoodsToClientStock($order_good) {
        $new_goods = array(
            'clie_id' => $order_good['clie_id'],
            'supp_id' => $order_good['supp_id'],
            'goods_barcode' => $order_good['goods_barcode'],
            'goods_nm' => $order_good['goods_nm'],
            'goods_stock' => $order_good['set_num'] * $order_good['unit_num'],
            'goods_low_stock' => 10,
            'drug_remind'=>30,
            'production_date' => $order_good['production_date'],
            'valid_remind'=>$order_good['valid_remind'],
            'shelf_life' => $order_good['shelf_life'],
        );
        $result = $this->table('scm_client_stock')->insert($new_goods);
        return $result ? $result : null;
    }

    public function addNewGoodsStock($goods){
        $result = $this->table('scm_client_stock')->insert($goods);
        return $result ? $result : null;
    }

    /**
     * 批量删除商品
     * @param $ids
     * @return bool
     */
    public function delGoodsByIdString($ids){
        if(empty($ids)){
            return false;
        }
        $delete_goods = $this->table('scm_client_stock')->where(array('id' => array('in', $ids)))->delete();
        if($delete_goods)
            return true;
        else
            return false;
    }

    /**
     * 获得供应商单件商品信息
     *
     * @param
     * @return int
     */
    public function getGoodInfo($goods_barcode, $fields = '*') {
        $result = $this->table('scm_supp_stock')->field($fields)->where(array('goods_barcode' => $goods_barcode))->find();
        return $result;
    }

    /**
     * 获取终端店单件商品信息
     */
    public function getGoodsInfoById($id,$fields = '*'){
        $result = $this->table('scm_client_stock')->field($fields)->where(array('id' => $id))->find();
        return $result;
    }

    /**
     * 编辑商品其余信息
     */
    public function editGoods($condition){
        $result = $this->table('scm_client_stock')->where(array('id' => $condition['id']))->update($condition);
        return $result;
    }

    /**
     * 获得供应商商品信息
     *
     * @param
     * @return int
     */
    public function getSuppGoodsInfo($condition, $fields = '*') {
        $result = $this->table('scm_supp_stock')->field($fields)->where($condition)->select();
        return $result;
    }

    /**
     * 更改库存信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editStock($data, $operator, $clie_id) {
        $stock_num = $data['set_num'] * $data['unit_num'];
        $sql = 'UPDATE gzkj_scm_client_stock scm_client_stock SET goods_stock=goods_stock' . $operator .$stock_num . ' where ( goods_barcode = ' . $data['goods_barcode']
                . ' AND clie_id="'. $clie_id .'")';
        $result = $this->query($sql);
        return $result;
    }

    /**
     * 更改库存表其他信息
     * @param $condition
     */
    public function editStockInfo($condition){
        $update_id = $this->table('scm_client_stock')->where(array('goods_barcode'=>$condition["goods_barcode"], 'clie_id'=>$condition['clie_id'] ))->update($condition);
        if($update_id)
            return true;
        else
            return false;
    }

    /**
     * 生成批发订单
     *
     * @param
     * @return int
     */
    public function createOrderToSupp($order_no, $clie_id, $supp_id, $order_pay, $paysn1) {
        $order_info = array();
        $order_time = date("Y-m-d H:i:s",time());
        $order_info['order_no'] = $order_no;
        $order_info['clie_id'] = $clie_id;
        $order_info['supp_id'] = $supp_id;
        $order_info['total_amount'] = $order_info['order_pay'] = $order_pay;
        $order_info['order_date'] = $order_time;
        $order_info['pay_sn'] = Logic('buy_1')->makePaySn(substr($order_no,4,4));
        $order_info['pay_sn1'] = $paysn1;
         if (!empty($order_info)) {
             $result = $this->table('scm_client_order')->insert($order_info);
         }
        return $result ? $result : null;
    }

    /**
     * 生成订单商品表
     *
     * @param
     * @return int
     */
    public function createOrderGoods($goods_array, $order_id) {
        $refund_info = array();
        foreach ($goods_array as $index => $good) {
            if ($good['order_num']) {
                $goods_array[$index]['order_id'] = $order_id;
            }
        }
        if (!empty($goods_array)) {
            $result = $this->table('scm_order_goods')->insertAll($goods_array);
        }
        return $result ? $result : null;
    }

    /**
     * 生成退货单
     *
     * @param
     * @return int
     */
    public function createRefundToSupp($goods_array, $refund_no, $clie_id) {
        $refund_info = array();
         foreach ($goods_array as $good => $num) {
             if ($num) {
                 if ($tmp = $this->getClientGoodInfo($good)) {
                     $tmp['refund_no'] = $refund_no;
                     $tmp['refund_num'] = $num;
                     $tmp['refund_amount'] = $tmp['goods_discount_price'] = $tmp['goods_price'] * $tmp['goods_discount'] * $num;
                     $tmp['refund_flag'] = 0;
                     unset($tmp['goods_stock']);
                     unset($tmp['goods_low_stock']);
                     unset($tmp['goods_uper_stock']);
                     unset($tmp['id']);
                     unset($tmp['new_product_flag']);
                     unset($tmp['goods_rate']);
                     $refund_info[] = $tmp;
                 }
             }
         }
          if (!empty($refund_info)) {
              $result = $this->table('scm_client_refund')->insertAll($refund_info);
          }
        return $result ? $result : null;
    }

    /**
     * 获得终端店单件商品库存信息
     *
     * @param
     * @return int
     */
    public function getClientGoodInfo($condition, $fields = '*') {
        $result = $this->table('scm_client_stock')->field($fields)->where($condition)->find();
        return $result;
    }

    /**
     * 取得缺货库存列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getStockoutWarnList($condition, $pagesize = '', $field = '*', $order = 'scm_client_stock.supp_id desc', $master = false){
        $condition['scm_client_stock.goods_stock'] = array('lt', array('exp', 'scm_client_stock.goods_low_stock'));
        $on = 'scm_client_stock.supp_id = scm_supplier.supp_id';
        $list = $this->table('scm_client_stock,scm_supplier')->field($field)->join('inner')->on($on)->where($condition)->page($pagesize)->order($order)->master($master)->select();
        if (empty($list)) return array();
        $stockout_list = array();
        if (empty($stockout_list)) $stockout_list = $list;
        return $stockout_list;
    }

    /**
     * 根据条件
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getUnsalableList($condition, $page=''){
        $param  = array();
        $param['table'] = 'scm_client_stock, scm_supplier';
        $param['join_type'] = 'LEFT JOIN';
        $param['join_on'] = array('scm_client_stock.supp_id = scm_supplier.supp_id');
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'];
        return $this->select1($param, $page);
    }

    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return string
     */
    private function getCondition($condition){
        $conditionStr   = '';
        if($condition['stock'] != ''){
            $conditionStr   .= $condition['stock'];
        }
        if($condition['instock'] != ''){
            $conditionStr   .= " not in {$condition['instock']} ";
        }
        //活动删除in
        if(isset($condition['activity_id_in'])){
            if ($condition['activity_id_in'] == ''){
                $conditionStr   .= " and activity_id in('')";
            }else{
                $conditionStr   .= " and activity_id in({$condition['activity_id_in']}) ";
            }
        }

        //当前时间大于结束时间（过期）
        if ($condition['activity_enddate_greater'] != ''){
            $conditionStr   .= " and activity.activity_end_date < '{$condition['activity_enddate_greater']}'";
        }
        //可删除的活动记录
        if ($condition['activity_enddate_greater_or'] != ''){
            $conditionStr   .= " or activity.activity_end_date < '{$condition['activity_enddate_greater_or']}'";
        }

        return $conditionStr;
    }
}
