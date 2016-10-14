<?php
/**
 * 统计报表-商品分析
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2015 共铸商城  Inc. (http://www.gongzhuying.com)
 * @license    http://www.gongzhuying.com
 * @link       http://www.gongzhuying.com
 * @since      File available since Release v1.1
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class purchase_analyseControl extends SCMControl
{
    protected $user_info;

    public function __construct(){
        parent::__construct();
        //Language::read('trade');
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
        import('function.statistics');
        import('function.datehelper');
        $model = Model('stat');
        //存储参数
        $this->search_arr = $_REQUEST;
        //处理搜索时间
        //if (in_array($this->search_arr['op'],array('index','hotgoods_amount'))){
        $this->search_arr = $model->dealwithSearchTime($this->search_arr);
        //获得系统年份
        $year_arr = getSystemYearArr();
        //获得系统月份
        $month_arr = getSystemMonthArr();
        //获得本月的周时间段
        $week_arr = getMonthWeekArr($this->search_arr['week']['current_year'], $this->search_arr['week']['current_month']);
        Tpl::output('year_arr', $year_arr);
        Tpl::output('month_arr', $month_arr);
        Tpl::output('week_arr', $week_arr);
        //}
        Tpl::output('search_arr', $this->search_arr);
    }

    /**
     * 批发统计
     */
    public function indexOp(){
        import('function.statistics');

        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'week';
        }
        $search_type = $this->search_arr['search_type'];
        //计算昨天和今天时间
        if($this->search_arr['search_type'] == 'day'){
            $stime = $this->search_arr['day']['search_time'] - 86400;//昨天0点
            $etime = $this->search_arr['day']['search_time'] + 86400 - 1;//今天24点
            $curr_stime = $this->search_arr['day']['search_time'];//今天0点
        } elseif ($this->search_arr['search_type'] == 'week'){
            $current_weekarr = explode('|', $this->search_arr['week']['current_week']);
            $stime = strtotime($current_weekarr[0])-86400*7;
            $etime = strtotime($current_weekarr[1])+86400-1;
            $curr_stime = strtotime($current_weekarr[0]);//本周0点
        } elseif ($this->search_arr['search_type'] == 'month'){
            $stime = strtotime($this->search_arr['month']['current_year'].'-'.$this->search_arr['month']['current_month']."-01 -1 month");
            $etime = getMonthLastDay($this->search_arr['month']['current_year'],$this->search_arr['month']['current_month'])+86400-1;
            $curr_stime = strtotime($this->search_arr['month']['current_year'].'-'.$this->search_arr['month']['current_month']."-01");;//本月0点
        }
        $where = array();
        if(trim($_GET['order_type']) != ''){
            $where['order_status'] = trim($_GET['order_type']);
        }
        //时间戳转换为时间
        $date_stime = date("Y-m-d H:i:s",$stime);
        $date_etime = date("Y-m-d H:i:s",$etime);
        $date_curr_stime = date("Y-m-d H:i:s",$curr_stime);
        $where['order_date'] = array('between',array("'".$date_stime."'","'".$date_etime."'"));
        $where['clie_id'] = $this->user_info['supp_clie_id'];
        $where['order_status'] = array('neq',0);
        //走势图
        $field = ' COUNT(*) as ordernum,SUM(total_amount) as orderamount ';
        $stat_arr = array();
        if($this->search_arr['search_type'] == 'day'){
            //构造横轴数据
            for($i=0; $i<24; $i++){
                //统计图数据
                $curr_arr['orderamount'][$i] = 0;//今天
                $up_arr['orderamount'][$i] = 0;//昨天
                $curr_arr['ordernum'][$i] = 0;//今天
                $up_arr['ordernum'][$i] = 0;//昨天
                //统计表数据
                $currlist_arr[$i]['timetext'] = $i;
                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
                //横轴
                $stat_arr['orderamount']['xAxis']['categories'][] = "$i";
                $stat_arr['ordernum']['xAxis']['categories'][] = "$i";
            }
            $today_day = @date('d', $etime);//今天日期
            $yesterday_day = @date('d', $stime);//昨天日期

            $field .= ' ,DAY(order_date) as dayval,HOUR(order_date) as hourval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'dayval,hourval';
            } else {
                $_group = 'DAY(order_date),HOUR(order_date)';
            }
            $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_client_order as scm_client_order WHERE order_status = 1 AND scm_client_order.clie_id = '".$this->user_info['supp_clie_id']."' AND (order_date BETWEEN '".$date_stime."' AND '".$date_etime."') GROUP BY ".$_group;
            $orderlist = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
            if(count($orderlist) > 0) {
                foreach ((array)$orderlist as $k => $v) {
                    if ($today_day == $v['dayval']) {
                        $curr_arr['ordernum'][$v['hourval']] = intval($v['ordernum']);
                        $curr_arr['orderamount'][$v['hourval']] = floatval($v['orderamount']);
                        $currlist_arr[$v['hourval']]['val'] = $v[$search_type];
                    }
                    if ($yesterday_day == $v['dayval']) {
                        $up_arr['ordernum'][$v['hourval']] = intval($v['ordernum']);
                        $up_arr['orderamount'][$v['hourval']] = floatval($v['orderamount']);
                        $uplist_arr[$v['hourval']]['val'] = $v[$search_type];
                    }
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '昨天';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '今天';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '昨天';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '今天';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);

        }
        if($this->search_arr['search_type'] == 'week'){
            $up_week = @date('W', $stime);//上周
            $curr_week = @date('W', $etime);//本周
            //构造横轴数据
            for($i=1; $i<=7; $i++){
                $tmp_weekarr = getSystemWeekArr();
                //统计图数据
                $up_arr['ordernum'][$i] = 0;
                $curr_arr['ordernum'][$i] = 0;

                $up_arr['orderamount'][$i] = 0;
                $curr_arr['orderamount'][$i] = 0;

                //横轴
                $stat_arr['ordernum']['xAxis']['categories'][] = $tmp_weekarr[$i];
                $stat_arr['orderamount']['xAxis']['categories'][] = $tmp_weekarr[$i];

                //统计表数据
                $uplist_arr[$i]['timetext'] = $tmp_weekarr[$i];
                $currlist_arr[$i]['timetext'] = $tmp_weekarr[$i];
                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
                unset($tmp_weekarr);
            }
            $field .= ',WEEKOFYEAR(order_date) as weekval,WEEKDAY(order_date)+1 as dayofweekval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'weekval,dayofweekval';
            } else {
                $_group = 'WEEKOFYEAR(order_date),WEEKDAY(order_date)+1';
            }

            $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_client_order as scm_client_order WHERE order_status = 1 AND scm_client_order.clie_id = '".$this->user_info['supp_clie_id']."' AND (order_date BETWEEN '".$date_stime."' AND '".$date_etime."') GROUP BY ".$_group;

            $orderlist = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
            if(count($orderlist) > 0) {
                foreach ((array)$orderlist as $k => $v) {
                    if ($up_week == $v['weekval']) {
                        $up_arr['ordernum'][$v['dayofweekval']] = intval($v['ordernum']);
                        $up_arr['orderamount'][$v['dayofweekval']] = floatval($v['orderamount']);
                        $uplist_arr[$v['dayofweekval']]['val'] = intval($v[$search_type]);
                    }
                    if ($curr_week == $v['weekval']) {
                        $curr_arr['ordernum'][$v['dayofweekval']] = intval($v['ordernum']);
                        $curr_arr['orderamount'][$v['dayofweekval']] = floatval($v['orderamount']);
                        $currlist_arr[$v['dayofweekval']]['val'] = intval($v[$search_type]);
                    }
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '上周';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '本周';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '上周';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '本周';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);
        }
        if($this->search_arr['search_type'] == 'month'){
            $up_month = date('m',$stime);
            $curr_month = date('m',$etime);
            //计算横轴的最大量（由于每个月的天数不同）
            $up_dayofmonth = date('t',$stime);
            $curr_dayofmonth = date('t',$etime);
            $x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;
            //构造横轴数据
            for($i=1; $i<=$x_max; $i++){
                //统计图数据
                $up_arr['ordernum'][$i] = 0;
                $curr_arr['ordernum'][$i] = 0;
                $up_arr['orderamount'][$i] = 0;
                $curr_arr['orderamount'][$i] = 0;
                //横轴
                $stat_arr['ordernum']['xAxis']['categories'][] = $i;
                $stat_arr['orderamount']['xAxis']['categories'][] = $i;
                //统计表数据
                $currlist_arr[$i]['timetext'] = $i;
                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
            }
            $field .= ',MONTH(order_date) as monthval,day(order_date) as dayval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'monthval,dayval';
            } else {
                $_group = 'MONTH(order_date),DAY(order_date)';
            }
            $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_client_order as scm_client_order WHERE order_status = 1 AND scm_client_order.clie_id = '".$this->user_info['supp_clie_id']."' AND (order_date BETWEEN '".$date_stime."' AND '".$date_etime."') GROUP BY ".$_group;
            $orderlist = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
            if(count($orderlist) > 0){
                foreach($orderlist as $k=>$v){
                    if ($up_month == $v['monthval']){
                        $up_arr['ordernum'][$v['dayval']] = intval($v['ordernum']);
                        $up_arr['orderamount'][$v['dayval']] = floatval($v['orderamount']);
                        $uplist_arr[$v['dayval']]['val'] = intval($v[$search_type]);
                    }
                    if ($curr_month == $v['monthval']){
                        $curr_arr['ordernum'][$v['dayval']] = intval($v['ordernum']);
                        $curr_arr['orderamount'][$v['dayval']] = floatval($v['orderamount']);
                        $currlist_arr[$v['dayval']]['val'] = intval($v[$search_type]);
                    }
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '上月';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '本月';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '上月';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '本月';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);
        }
        $stat_arr['ordernum']['title'] = '批发订单数量统计';
        $stat_arr['ordernum']['yAxis'] = '批发订单量';
        $stat_arr['orderamount']['title'] = '批发订单金额统计';
        $stat_arr['orderamount']['yAxis'] = '批发订单金额(元)';
        $stat_json['ordernum'] = getStatData_LineLabels($stat_arr['ordernum']);
        $stat_json['orderamount'] = getStatData_LineLabels($stat_arr['orderamount']);
        Tpl::output('stat_json',$stat_json);
        Tpl::output('stattype',$search_type);
        //总数统计

        $sql = "SELECT COUNT(*) as ordernum,SUM(total_amount) as orderamount FROM ".C('tablepre')."scm_client_order as scm_client_order WHERE order_status != 0 AND scm_client_order.clie_id = '".$this->user_info['supp_clie_id']."' AND (order_date BETWEEN '".$date_curr_stime."' AND '".$date_etime."')";

        $statcount_arr = SCMModel('scm_client_stock')->getWarnGoodsList($sql);

        $statcount_arr['ordernum'] = ($t = intval($statcount_arr[0]['ordernum'])) > 0?$t:0;
        $statcount_arr['orderamount'] = ncPriceFormat(($t = floatval($statcount_arr[0]['orderamount'])) > 0?$t:0);
        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('search_arr', $this->search_arr);
        Tpl::showpage('stat.purchase_analyse.index');
    }

    /**
     * 查询每月的周数组
     */
    public function getweekofmonthOp(){
        import('function.datehelper');
        $year = $_GET['y'];
        $month = $_GET['m'];
        $week_arr = getMonthWeekArr($year, $month);
        echo json_encode($week_arr);
        die;
    }

}