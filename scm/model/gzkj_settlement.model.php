<?php
/**
 * 结算管理
 */
defined('InShopNC') or exit('Access Invalid!');

class gzkj_settlementModel extends Model
{

    public function __construct()
    {
        parent::__construct('scm_settlement');
    }

    /**
     * 根据批发订单生成结算清单
     * 
     * @return bool
     */
    public function generate()
    {
        $model_scm_client_order = SCMModel("scm_client_order");
        $maxcaltime = $this->getMaxCalTime();
		$nextSettlementDate = $this->calSettlementDate(date("Y-m-d"));

        // 供应商结算，orderstatus=1，正常结算给供应商，flag设置为2（20表示结算完毕）
        $this->_generateSupplier($model_scm_client_order,$nextSettlementDate,$maxcaltime);
		$this->_generateClient($model_scm_client_order,$nextSettlementDate,$maxcaltime);
        
        // 零售店退款结算，orderstatus=3or4，退钱给零售店，flag设置为3（30表示结算完毕）
		/*
        $this->_generate($model_scm_client_order, array(
            3,
            4
        ), 3, $maxcaltime);
		*/
    }

    /**
     *
     * @param
     *            model_scm_client_order
     * @param
     *            where
     */
    private function _generateSupplier($model_scm_client_order, $nextSettlementDate,$maxcaltime)
    {
		$sql="
			SELECT sco.id,sco.supp_id,sco.clie_id,sco.order_pay,sco.settlement_id,sco.pay_start_time,ss.settlement_id as settlement_id1,ss.settlement_date FROM `gzkj_scm_client_order` sco
			left join gzkj_scm_settlement ss on sco.supp_id=ss.supp_id and ss.settlement_date='$nextSettlementDate'
			where sco.order_status=1 and (sco.settlement_id is null or sco.settlement_id=0) and sco.pay_start_time > 0 and sco.pay_start_time < '$maxcaltime'		
		";

		$result=$model_scm_client_order->query($sql);
		$orderData = array();
        while ($list = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $orderData[$list['supp_id']][$list['id']] = array(
                "id" => $list['id'],
                "clie_id" => $list['clie_id'],
                "supp_id" => $list['supp_id'],
                "order_pay" => $list['order_pay'],
                "pay_start_time" => $list['pay_start_time'],
				"settlement_id1" => $list['settlement_id1']
            );			
        }
        
        $settlement = array();
        $where = array();
        foreach ($orderData as $supp_id => $data) {
            $amount = 0;
			$updateamount = 0;
			$existSettlementid="";
			$settlementid="";
            foreach ($data as $id => $val) {
				if($val['settlement_id1'] && $val['settlement_id1']>0) {
					$existSettlementid=$val['settlement_id1'];
					$updateamount += $val['order_pay'];
				} else {
					$amount += $val['order_pay'];
				}
            }
			if($amount>0) {
				$settlement["supp_id"] = $supp_id;
				$settlement["amount"] = $amount;
				$settlement['create_date'] = date("Y-m-d H:i:s");
				$settlement['settlement_date'] = $nextSettlementDate;
				$settlement['flag'] = 2;

				$settlementid = $this->addSettlement($settlement);
			} else if($updateamount > 0) {
				$sql = "update gzkj_scm_settlement set amount=amount+" . $updateamount . " where settlement_id=".$existSettlementid;
				$model_scm_client_order->query($sql);
				$settlementid=$existSettlementid;
			}

			if($settlementid && $settlementid > 0) {
				$updateOrder = array();
				$updateOrder['settlement_id'] = $settlementid;
				$where["id"] = array(
					"in",
					implode(",", array_keys($data))
				);
				$model_scm_client_order->editOrder($updateOrder, $where);
			}
        }		
    }

    private function _generateClient($model_scm_client_order, $nextSettlementDate,$maxcaltime)
    {
		$sql="
			SELECT sco.id,sco.supp_id,sco.clie_id,sco.order_pay,sco.settlement_id,sco.pay_start_time,ss.settlement_id as settlement_id1,ss.settlement_date FROM `gzkj_scm_client_order` sco
			left join gzkj_scm_settlement ss on sco.clie_id=ss.clie_id and ss.settlement_date='$nextSettlementDate'
			where sco.order_status in(3,4) and (sco.settlement_id is null or sco.settlement_id=0) and sco.pay_start_time > 0 and sco.pay_start_time < '$maxcaltime'		
		";

		$result=$model_scm_client_order->query($sql);
		$orderData = array();
        while ($list = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $orderData[$list['clie_id']][$list['id']] = array(
                "id" => $list['id'],
                "clie_id" => $list['clie_id'],
                "supp_id" => $list['supp_id'],
                "order_pay" => $list['order_pay'],
                "pay_start_time" => $list['pay_start_time'],
				"settlement_id1" => $list['settlement_id1']
            );			
        }
        
        $settlement = array();
        $where = array();
        foreach ($orderData as $clie_id => $data) {
            $amount = 0;
			$updateamount = 0;
			$existSettlementid="";
			$settlementid="";
            foreach ($data as $id => $val) {
				if($val['settlement_id1'] && $val['settlement_id1']>0) {
					$existSettlementid=$val['settlement_id1'];
					$updateamount += $val['order_pay'];
				} else {
					$amount += $val['order_pay'];
				}
            }
			if($amount>0) {
				$settlement["clie_id"] = $clie_id;
				$settlement["amount"] = $amount;
				$settlement['create_date'] = date("Y-m-d H:i:s");
				$settlement['settlement_date'] = $nextSettlementDate;
				$settlement['flag'] = 3;

				$settlementid = $this->addSettlement($settlement);
			} else if($updateamount > 0) {
				$sql = "update gzkj_scm_settlement set amount=amount+" . $updateamount . " where settlement_id=".$existSettlementid;
				$model_scm_client_order->query($sql);
				$settlementid=$existSettlementid;
			}

			if($settlementid && $settlementid > 0) {
				$updateOrder = array();
				$updateOrder['settlement_id'] = $settlementid;
				$where["id"] = array(
					"in",
					implode(",", array_keys($data))
				);
				$model_scm_client_order->editOrder($updateOrder, $where);
			}
        }		
    }
	
	//根据pay start day来获取下一个结算周期
    public function calSettlementDate($date) {
        $year = date("Y", strtotime($date));
        $month = date("m", strtotime($date));
        $day = date("d", strtotime($date));
        echo $year.'-'.$month.'-'.$day;
        if($day >=1 && $day <= 9) {
            return $year.'-'.$month.'-16';
        }
        if($day >=10 && $day <= 18) {
            return $year.'-'.$month.'-25';
        }
        if($month == 12) {
            $year++;
            $month=1;
            $day = 7;
            return $year.'-'.$month.'-'.$day;
        }
        return $year.'-'.(++$month).'-7';
        
    }
    //根据系统当天去计算pay start date的最大统计日期
	//如果当天小于等于7，那么统计上个月19号到月末的数据，也就是小于这个月1号凌晨
	//如果小于等于16，统计9号（包括）之前的数据，也就是10号凌晨
	//如果小于等于25，统计18号（包括）之前的数据，也就是19号凌晨
	//其他表示是下个月的统计周期，也就是下个月7号
	private function getMaxCalTime() {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        if($day <=7 ){
			return $year.'-'.$month.'-1';
		}
        if($day <= 16) {
            return $year.'-'.$month.'-10';
        }
        if($day <= 25) {
            return $year.'-'.$month.'-19';
        }
        return $year.'-'.(++$month).'-1';
	}
    public function addSettlement($data)
    {
        return $this->table('scm_settlement')->insert($data);
    }

	public function getSettlementInfo($condition = array(), $field = '*', $page = null, $order = '')
	{
		return $this->table('scm_settlement,scm_supplier,scm_client,scm_client_order')->join('left join')->on('scm_settlement.supp_id=scm_supplier.supp_id,scm_settlement.clie_id=scm_client.clie_id,scm_settlement.settlement_id=scm_client_order.settlement_id')->where($condition)->field($field)->page($page)->order($order)->select();
	}
}