<?php
/**
 *供应商终端店业务逻辑
 *
 */

defined('InShopNC') or exit('Access Invalid!');

class supplier_clientModel extends Model{
    /**
     * 分页获取终端店提交的商品订单
     * @param array $condition  筛选条件
     * @param string $field 筛选的字段
     * @param number $page  分页条件
     * @param string $order  排序
     */
    public function getOrderList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = ''){
        $on = 'scm_client_order.clie_id = scm_client.clie_id';
        return $this->table('scm_client_order,scm_client')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 分页获取订单详情表
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     */
    public function getGoodsList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = ''){
        $on = 'scm_order_goods.order_id = scm_client_order.id';
        return $this->table('scm_order_goods,scm_client_order')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }
    /**
     * 分页获取退货单列表
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     */
    public function getRefundList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = ''){
        return $this->table('scm_client_refund')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }
    /**
     * 分组获取终端店提交商品订单
     * @param array $condition
     * @param string $group
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     */
    public function getOrderListGroupBy($condition = array(),$group = 'order_no', $field = 'order_no,clie_id,clie_ch_name,sum(goods_discount_price) ', $page = null, $order = 'id desc', $limit = ''){
        return $this->table('scm_client_order')->field($field)->where($condition)->page($page)->group($group)->order($order)->limit($limit)->select();
    }

    /**
     * 获取合作终端店列表
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     */
    public function getClientList($condition = array(),$group = 'clie_id', $field = '*', $page = null, $order = '', $limit = ''){
        $on = 'scm_client_order.clie_id = scm_client.clie_id ';
        return $this->table('scm_client_order,scm_client')->field($field)->join('left')->on($on)->where($condition)->page($page)->group($group)->order($order)->limit($limit)->select();
    }

    /**
     * 获取合作供应商数量
     * @param array $condition
     */
    public function getClientCount($condition = array(),$group = 'clie_id',$order = ''){
        $on = 'scm_client_order.clie_id = scm_client.clie_id ';
        return $this->table('scm_client_order,scm_client')->join('left')->on($on)->where($condition)->group($group)->order($order)->count();
    }

    /**
     * 获取单个终端店信息
     * @param array $condition
     */
    public function getClientInfo($condition = array()){
        return $this->table('scm_client')->where($condition)->find();
    }
    /**
     * 获取订单的数量
     * @param array $condition
     */
    public function getOrderCount($condition = array()){
        return $this->table('scm_client_order')->where($condition)->count();
    }

    /**
     * 分页时按条件获取数据数量
     * @param array $condition
     * @param string $group
     * @param string $table
     * @param string $field
     * @return mixed
     */
    public function gettotalnum($condition = array(),$group = 'order_no',$table='scm_client_order',$field = 'order_no'){
        return $this->table($table)->field($field)->where($condition)->group($group)->select();
    }

    /**
     * 分页时按条件获取数据数量(多表连接)
     * @param array $condition
     * @param string $group
     * @param string $table
     * @param string $field
     * @param string $on
     * @return mixed
     */
    public function gettotalnumon($condition = array(),$group = 'order_no',$table='scm_client_order',$field = 'order_no',$on=''){
        return $this->table($table)->field($field)->join('left')->on($on)->where($condition)->group($group)->select();
    }
    /**
     * 获取单个订单
     * @param array $condition 查询条件, array $field 筛选列
     * @return array 数组格式返回查询结果
     */
    public function getOrderInfo($condition , $field = '*'){
        return $this->table('scm_client_order')->field($field)->where($condition)->find();
    }

    /**
     * 获取单个订单详情
     * @param $condition
     * @param string $field
     */
    public function getGoodsInfo($condition , $field = '*'){
        return $this->table('scm_order_goods')->field($field)->where($condition)->find();
    }

    /**
     * 更新订单表信息
     * @param $order
     * @return bool
     */
    public function updateOrder($order){
        $update_id = $this->table('scm_client_order')->where(array('id'=>$order["id"]))->update($order);
        if($update_id)
            return true;
        else
            return false;
    }

    /**
     * 更新订单详情
     * @param $goods
     */
    public function updateGoods($goods){
        $update_id = $this->table('scm_order_goods')->where(array('id'=>$goods["id"]))->update($goods);
        if($update_id)
            return true;
        else
            return false;
    }
    /**
     * 更新退货单信息
     * @param $refund
     * @return bool
     */
    public function updateRefund($refund){
        $update_id = $this->table('scm_client_refund')->where(array('id'=>$refund["id"]))->update($refund);
        if($update_id)
            return true;
        else
            return false;
    }

    /**
     * 分组获取终端店提交的入库退货单
     * @param array $condition
     * @param string $group
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     */
    public function getRefundListGroupBy($condition = array(),$group = 'refund_no', $field = 'order_no,clie_id,clie_ch_name,sum(goods_discount_price) ', $page = null, $order = 'id desc', $limit = ''){
        return $this->table('scm_client_refund')->field($field)->where($condition)->page($page)->group($group)->order($order)->limit($limit)->select();
    }

    /**
     * 合作终端店库存表
     * @param array $condition
     * @param string $field
     * @param null $page
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getClientStockList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = ''){
        $on = 'scm_client_stock.supp_id = scm_supplier.supp_id,scm_client.clie_id =scm_client_stock.clie_id ';
        return $this->table('scm_client_stock,scm_supplier,scm_client')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 有效期预警
     */
    public function execute_sql($sql){
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
     * 查看当前合作终端店是否有预警
     */
    public function get_warn($supp_id){
        $condition = array();
        $condition['supp_id'] = $supp_id;
        $field = 'scm_client_order.clie_id,scm_client_order.clie_ch_name,scm_client.clie_tel,clie_mobile,clie_contacter,clie_address';
        $order = '';
        $client_list = $this->getClientList($condition,'clie_id', $field, null, $order);
        $warn = array(
            'validity_warn' => 0,
            'unavailable_warn' => 0,
            'unsalable_warn' => 0,
        );
        $i = $j =$k =0;
        if(count($client_list) > 0){
            foreach($client_list as $k => $value) {
                //近效期预警
                $sql = "SELECT sii.id,sii.clie_id,sii.goods_barcode,sii.goods_nm,sii.goods_unit, sii.goods_spec,sii.set_num,sii.production_date,scs.valid_remind,scs.shelf_life,sii.order_id,
                CASE WHEN scs.shelf_life LIKE '%年'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 360) DAY )
                WHEN scs.shelf_life LIKE '%月'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 30) DAY )
                WHEN scs.shelf_life LIKE '%天'  THEN DATE_ADD( scs.production_date, INTERVAL scs.shelf_life DAY )
                END AS expire_date
                FROM ".C('tablepre')."scm_instock_info AS sii
                LEFT JOIN ".C('tablepre')."scm_client_stock AS scs ON sii.goods_barcode = scs.goods_barcode,
                (
                SELECT goods_barcode,MIN(production_date)AS riqi FROM ".C('tablepre')."scm_instock_info
                WHERE waring_flag = 1
                AND clie_id = '" . $value['clie_id'] . "'
                AND supp_id =  '" . $supp_id . "'
                GROUP BY goods_barcode
                ) AS c
                WHERE sii.goods_barcode = c.goods_barcode
                AND sii.production_date = c.riqi
                AND sii.clie_id = '" . $value['clie_id'] . "'
                AND sii.supp_id =  '" . $supp_id . "'
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
                GROUP BY sii.production_date
                ORDER BY sii.production_date";
                $temp_list = $this->execute_sql($sql);
                if (count($temp_list) > 0) {
                    $i = $i +1;
                    $warn['validity_warn'] = $i;
                    $warn['warn'] = true;
                }
                //缺货预警
                $condition = array();
                $condition['scm_client_stock.clie_id'] = $value['clie_id'];
                $condition['scm_client_stock.supp_id'] = $supp_id;
                $condition[] = array('exp', 'goods_stock < goods_low_stock');
                $order = '';
                $field = 'scm_client_stock.id';
                $temp_stock_list = $this->getClientStockList($condition, $field, null, $order);
                if (count($temp_stock_list) > 0) {
                    $j = $j + 1;
                    $warn['unavailable_warn'] = $j;
                    $warn['warn'] = true;
                }
                //滞销预警
                $sql = "
                SELECT
                	scs.id
                FROM
                	".C('tablepre')."scm_client_stock scs
                LEFT JOIN ".C('tablepre')."scm_client AS sc ON sc.clie_id = scs.clie_id
                LEFT JOIN ".C('tablepre')."scm_instock_info sii on scs.clie_id=sii.clie_id and scs.supp_id=sii.supp_id and scs.goods_barcode=sii.goods_barcode
                WHERE
                	scs.clie_id = '".$value['clie_id']."'
                AND scs.supp_id='".$supp_id."'
                AND scs.goods_barcode NOT IN (
                	SELECT
                		goods_barcode
                	FROM
                		".C('tablepre')."scm_instock_info
                	WHERE
                		clie_id = '".$value['clie_id']."'
                	AND in_stock_date > DATE_SUB(NOW(), INTERVAL scs.drug_remind DAY)
                )
                GROUP BY scs.goods_barcode";
                $temp_list = $this->execute_sql($sql);
                if (count($temp_list) > 0) {
                    $k = $k +1;
                    $warn['unsalable_warn'] = $k;
                    $warn['warn'] = true;
                }

            }
        }
        return $warn;
    }

    /**
     * 是否有预警和处理事项
     * @param $supp_id
     */
    public function get_pending_matters($supp_id){
        $flag = false;
        $condition['supp_id'] = $supp_id;
        $condition['order_status'] = 0;
        $condition['out_flag'] = 0;
        $temp_list = $this->gettotalnum($condition);
        if(count($temp_list) > 0){
            $flag = true;
        }
        $condition = array();
        $condition['supp_id'] = $supp_id;
        $condition['order_status'] = 0;
        $condition['out_flag'] = 1;
        $condition['refund_flag'] = 1;
        $condition['in_flag'] = 0;
        $temp_list = $this->gettotalnum($condition);
        if(count($temp_list) > 0){
            $flag = true;
        }
        $warn = $this->get_warn($supp_id);
        if($warn['warn']){
            $flag = true;
        }
        return $flag;
    }
}