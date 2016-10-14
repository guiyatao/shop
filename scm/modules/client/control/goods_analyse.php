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
class goods_analyseControl extends SCMControl
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
     * 下单商品数
     */
    public function indexOp(){
        import('function.statistics');
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        //获得搜索的开始时间和结束时间
        $searchtime_arr = Model('stat')->getStarttimeAndEndtime($this->search_arr);
        $topnum = 30;
        //构造横轴数据
        for($i=1; $i<=$topnum; $i++){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>'','y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = "$i";
        }
        $statlist = array();
        $where = array(
            'scm_online_order.clie_id'=>$this->user_info['supp_clie_id'],
            'finnshed_time'=>array('between',array($searchtime_arr[0],$searchtime_arr[1])),
        );
        $statlist['goodsnum'] = SCMModel('scm_online_order') ->getOrderGoodsListOn($where, 'goods_barcode,sum(goods_num) as goodsnum ,goods_name', $topnum,null,'goodsnum DESC','goods_barcode');
//        $sql = "select goods_barcode,sum(goods_num) as goodsnum,goods_name from ".C('tablepre')."scm_online_order as scm_online_order left join
//            ".C('tablepre')."scm_online_order_goods as scm_online_order_goods
//            on scm_online_order.order_id = scm_online_order_goods.order_id
//            where scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' and finnshed_time BETWEEN ".$searchtime_arr[0]." and ".$searchtime_arr[1]." GROUP BY goods_barcode ORDER BY goodsnum DESC limit ".$topnum;
//        $statlist['goodsnum'] = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        if(!empty($statlist['goodsnum']) && is_array($statlist['goodsnum']) ){
            foreach ((array)$statlist['goodsnum'] as $k=>$v){
                $goods = Model('goods')->getGoodsList(array('goods_barcode'=> $v['goods_barcode'] ));
                $statlist['goodsnum'][$k]['goods_id'] = $goods[0]['goods_id'];
                $stat_arr['series'][0]['data'][$k] = array('name'=>strval($v['goods_name']),'y'=>intval($v['goodsnum']));
            }
        }

        $stat_arr['series'][0]['name'] = '下单商品数';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '热卖商品TOP'.$topnum;
        $stat_arr['yAxis'] = '下单商品数';
        //print_r($stat_arr);die;
        $stat_json['goodsnum'] = getStatData_Column2D($stat_arr);
        unset($stat_arr);
        //查询下单金额top
        //构造横轴数据
        for($i=1; $i<=$topnum; $i++){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>'','y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = "$i";
        }
//        $sql = "select goods_barcode,sum(goods_num*goods_price ) as orderamount ,goods_name from ".C('tablepre')."scm_online_order as scm_online_order left join
//            ".C('tablepre')."scm_online_order_goods as scm_online_order_goods
//            on scm_online_order.order_id = scm_online_order_goods.order_id
//            where scm_online_order.clie_id = '".$this->user_info['supp_clie_id']."' and finnshed_time BETWEEN ".$searchtime_arr[0]." and ".$searchtime_arr[1]." GROUP BY goods_barcode ORDER BY orderamount DESC limit ".$topnum;
//        $statlist['orderamount'] = SCMModel('scm_client_stock')->getWarnGoodsList($sql);
        $statlist['orderamount'] = SCMModel('scm_online_order') ->getOrderGoodsListOn($where, 'goods_barcode,sum(goods_num*goods_price ) as orderamount ,goods_name', $topnum,null,'orderamount DESC','goods_barcode');
        foreach ((array)$statlist['orderamount'] as $k=>$v){
            $goods = Model('goods')->getGoodsList(array('goods_barcode'=> $v['goods_barcode'] ));
            $statlist['orderamount'][$k]['goods_id'] = $goods[0]['goods_id'];
            $stat_arr['series'][0]['data'][$k] = array('name'=>strval($v['goods_name']),'y'=>floatval($v['orderamount']));
        }
        $stat_arr['series'][0]['name'] = '下单金额';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '热卖商品TOP'.$topnum;
        $stat_arr['yAxis'] = '下单金额(元)';
        //print_r($stat_arr);die;
        $stat_json['orderamount'] = getStatData_Column2D($stat_arr);
        //print_r( $stat_json);die;
        Tpl::output('stat_json',$stat_json);
        Tpl::output('statlist',$statlist);
        Tpl::showpage('stat.hotgoods.index');
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