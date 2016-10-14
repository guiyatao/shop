<?php
/**
 * 统计报表-销售统计
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
class sale_analyseControl extends SCMControl
{
    protected $user_info;

    private $links = array(
        array('url' => 'act=sale_analyse&op=index', 'text' => '销售统计'),
        array('url' => 'act=sale_analyse&op=area', 'text' => '区域分布'),
//        array('url' => 'act=sale_analyse&op=buying', 'text' => '购买分析'),
    );

    public function __construct()
    {
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
     * 下单金额和下单数量
     */
    public function indexOp(){
        import('function.statistics');
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
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
            $where['order_state'] = trim($_GET['order_type']);
        }
        $where['add_time'] = array('between',array($stime,$etime));
        $where['clie_id'] = $this->user_info['supp_clie_id'];
        //走势图
        $field = ' COUNT(*) as ordernum,SUM(order_amount) as orderamount ';
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

            $field .= ' ,DAY(FROM_UNIXTIME(add_time)) as dayval,HOUR(FROM_UNIXTIME(add_time)) as hourval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'dayval,hourval';
            } else {
                $_group = 'DAY(FROM_UNIXTIME(add_time)),HOUR(FROM_UNIXTIME(add_time))';
            }
            if(trim($_GET['order_type']) != ''){
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE order_state = ".trim($_GET['order_type'])." AND scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }else{
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }

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
            $field .= ',WEEKOFYEAR(FROM_UNIXTIME(add_time)) as weekval,WEEKDAY(FROM_UNIXTIME(add_time))+1 as dayofweekval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'weekval,dayofweekval';
            } else {
                $_group = 'WEEKOFYEAR(FROM_UNIXTIME(add_time)),WEEKDAY(FROM_UNIXTIME(add_time))+1';
            }
            if(trim($_GET['order_type']) != ''){
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE order_state = ".trim($_GET['order_type'])." AND scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }else{
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }

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
            $field .= ',MONTH(FROM_UNIXTIME(add_time)) as monthval,day(FROM_UNIXTIME(add_time)) as dayval ';
            if (C('dbdriver') == 'mysql') {
                $_group = 'monthval,dayval';
            } else {
                $_group = 'MONTH(FROM_UNIXTIME(add_time)),DAY(FROM_UNIXTIME(add_time))';
            }
            if(trim($_GET['order_type']) != ''){
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE order_state = ".trim($_GET['order_type'])." AND scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }else{
                $sql = "SELECT ".$field." FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$stime." AND ".$etime." GROUP BY ".$_group;
            }
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
        $stat_arr['ordernum']['title'] = '下单数量统计';
        $stat_arr['ordernum']['yAxis'] = '下单量';
        $stat_arr['orderamount']['title'] = '下单金额统计';
        $stat_arr['orderamount']['yAxis'] = '下单金额(元)';
        $stat_json['ordernum'] = getStatData_LineLabels($stat_arr['ordernum']);
        $stat_json['orderamount'] = getStatData_LineLabels($stat_arr['orderamount']);
        Tpl::output('stat_json',$stat_json);
        Tpl::output('stattype',$search_type);
        //总数统计
        if(trim($_GET['order_type']) != ''){
            $sql = "SELECT COUNT(*) as ordernum,SUM(order_amount) as orderamount FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE order_state = ".trim($_GET['order_type'])." AND  scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$curr_stime." AND ".$etime;
        }else{
            $sql = "SELECT COUNT(*) as ordernum,SUM(order_amount) as orderamount FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$curr_stime." AND ".$etime;
        }
        $statcount_arr = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        $statcount_arr['ordernum'] = ($t = intval($statcount_arr[0]['ordernum'])) > 0?$t:0;
        $statcount_arr['orderamount'] = ncPriceFormat(($t = floatval($statcount_arr[0]['orderamount'])) > 0?$t:0);
        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('searchtime',implode('|',array($curr_stime,$etime)));
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('stat.sale_analyse.index');
    }

    /**
     * 分页获取订单数据
     */
    public function salelistOp(){
        $model = SCMModel('scm_online_order');
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = intval($v);
        }
        $where = array();
        if(trim($_GET['order_type']) != ''){
            $where['order_state'] = trim($_GET['order_type']);
        }
        if(trim($_GET['curpage']) == ''){
            $_GET['curpage'] = 1;
        }
        $where['finnshed_time'] = array('between',$searchtime_arr);
        $where['clie_id'] = $this->user_info['supp_clie_id'];
        $pagenum = 20;

        if ($_GET['exporttype'] == 'excel'){
            //$order_list = $model->statByStatorder($where, '', 0, 0,'order_id desc');
            $order_list = $model->getOrderList($where,null,'order_sn,buyer_name,add_time,order_amount,order_state','id DESC');

        } else {
            //$sql = "SELECT order_sn,buyer_name,add_time,order_amount,order_state FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND add_time BETWEEN ".$searchtime_arr[0]." AND ".$searchtime_arr[1];
            $index = ($_GET['curpage'] -1 ) * $pagenum;
            $limit = $index.",".$pagenum;
            $order_list = $model->getOrderList($where,$pagenum,'order_sn,buyer_name,add_time,order_amount,order_state','id DESC', $limit);
        }

        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'序号','key'=>'seq');
        $statheader[] = array('text'=>'订单编号','key'=>'order_sn');
        $statheader[] = array('text'=>'买家','key'=>'buyer_name');
        $statheader[] = array('text'=>'下单时间','key'=>'add_time');
        $statheader[] = array('text'=>'订单总额','key'=>'order_amount');
        $statheader[] = array('text'=>'订单状态','key'=>'order_state');
        foreach ((array)$order_list as $k=>$v){
            $index++;
            $v['seq'] = $index;
            $v['add_time'] = @date('Y-m-d H:i:s',$v['add_time']);
            $v['order_state'] = Order::getShopOrderStatusByID($v['order_state']);
            $statlist[$k]= $v;
        }
        //导出Excel
        if ($this->search_arr['exporttype'] == 'excel'){
            //导出Excel
            import('libraries.excel');
            $excel_obj = new Excel();
            $excel_data = array();
            //设置样式
            $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
            //header
            foreach ($statheader as $k=>$v){
                $excel_data[0][] = array('styleid'=>'s_title','data'=>$v['text']);
            }
            //data
            foreach ($statlist as $k=>$v){
                foreach ($statheader as $h_k=>$h_v){
                    $excel_data[$k+1][] = array('data'=>$v[$h_v['key']]);
                }
            }
            $excel_data = $excel_obj->charset($excel_data,CHARSET);
            $excel_obj->addArray($excel_data);
            $excel_obj->addWorksheet($excel_obj->charset('订单记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('订单记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);
        //print_r($_SERVER['REQUEST_URI']);die;
        Tpl::output('show_page',$model->showpage(2));
        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&order_type={$this->search_arr['order_type']}&t={$this->search_arr['t']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }

    /**
     * 区域分布
     */
    public function areaOp(){
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        $model = Model('stat');
        //获得搜索的开始时间和结束时间
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        $where = array();
        $where['clie_id'] = $this->user_info['supp_clie_id'];
        $where['order_add_time'] = array('between',$searchtime_arr);
        $area = array('西岗区','中山区', '沙河口区','甘井子区', '旅顺口区', '金州区','普兰店区', '瓦房店市', '庄河市','长海县');

        //构造横轴数据
       foreach($area as $k => $v){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>$v,'y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = $v;
        }
        $sql = "SELECT buyer_name,buyer_address FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$searchtime_arr[0]." AND ".$searchtime_arr[1]." GROUP BY buyer_name";
        $memberlist = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        if(!empty($memberlist) && is_array($memberlist) ){
            foreach($memberlist as $k => $v){
                foreach($area as $kk => $vv ){
                    $tmparray = explode($vv,$v['buyer_address']);
                    if(count($tmparray)>1){
                        $stat_arr['series'][0]['data'][$kk]['y'] = $stat_arr['series'][0]['data'][$kk]['y']+1;
                    }
                }
            }
        }
        $stat_arr['series'][0]['name'] = '区域会员数量';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '下单会员数';
        $stat_arr['yAxis'] = '大连市各区域的会员数量';
        //print_r($stat_arr);die;
        $stat_json['membernum'] = getStatData_Column2D($stat_arr);
        unset($stat_arr);
        //构造横轴数据
        foreach($area as $k => $v){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>$v,'y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = $v;
        }
        $sql = "SELECT count(*) as ordernum , sum(order_amount) as orderamount,buyer_address FROM ".C('tablepre')."scm_online_order as scm_online_order WHERE scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' AND finnshed_time BETWEEN ".$searchtime_arr[0]." AND ".$searchtime_arr[1]." GROUP BY buyer_address";
        $orderlist =  SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        if(!empty($orderlist) && is_array($orderlist)){
            foreach($orderlist as $k => $v){
                foreach($area as $kk => $vv){
                    $tmparray = explode($vv,$v['buyer_address']);
                    if(count($tmparray)>1){
                        $stat_arr['series'][0]['data'][$kk]['y'] = floatval($stat_arr['series'][0]['data'][$kk]['y']) + floatval($v['orderamount']);
                    }
                }
            }
        }
        $stat_arr['series'][0]['name'] = '区域下单金额';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '下单金额';
        $stat_arr['yAxis'] = '大连市各区域的下单金额(元)';
        //print_r($stat_arr);die;
        $stat_json['orderamount'] = getStatData_Column2D($stat_arr);
        unset($stat_arr);
        //构造横轴数据
        foreach($area as $k => $v){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>$v,'y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = $v;
        }
        if(!empty($orderlist) && is_array($orderlist)){
            foreach($orderlist as $k => $v){
                foreach($area as $kk => $vv){
                    $tmparray = explode($vv,$v['buyer_address']);
                    if(count($tmparray)>1){
                        $stat_arr['series'][0]['data'][$kk]['y'] = $stat_arr['series'][0]['data'][$kk]['y'] + $v['ordernum'];
                    }
                }
            }
        }
        $stat_arr['series'][0]['name'] = '区域下单数量';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '下单数量';
        $stat_arr['yAxis'] = '大连市各区域的下单数量';
        //print_r($stat_arr);die;
        $stat_json['ordernum'] = getStatData_Column2D($stat_arr);


        Tpl::output('stat_json',$stat_json);
        Tpl::output('top_link', $this->sublink($this->links, 'area'));
        Tpl::showpage('stat.sale_analyse.area');
    }

    /**
     * 购买分析
     */
    public function buyingOp(){

        Tpl::showpage('stat.sale_analyse.buying');
    }
}