<?php
/**
 * 统计概述
 *
 * @copyright  Copyright (c) 2007-2015 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
use Shopnc\Tpl;


defined('InShopNC') or exit('Access Invalid!');

class statistics_goodsControl extends SCMControl {
    private $search_arr;//处理后的参数
    private $gc_arr;//分类数组
    private $choose_gcid;//选择的分类ID

    public function __construct(){
        parent::__construct();
        Language::read('member_store_statistics');
        import('function.statistics');
        import('function.datehelper');
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
        $model = Model('stat');
        //存储参数
        $this->search_arr = $_REQUEST;
        //处理搜索时间
//        if (in_array($this->search_arr['op'],array('price','hotgoods'))){
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
//        }
        Tpl::output('search_arr', $this->search_arr);

        /**
         * 处理商品分类
         */
        $this->choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
        $gccache_arr = Model('goods_class')->getGoodsclassCache($this->choose_gcid,3);
        $this->gc_arr = $gccache_arr['showclass'];
        Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
        Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));
    }
    public function indexOp(){
      return $this->hotgoodsOp();
    }
    public function hotgoodsOp(){
        $topnum = 30;
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        $model = Model('stat');
        //获得搜索的开始时间和结束时间
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        $searchtime_arr[0]=date('Y-m-d H:i:s', $searchtime_arr[0]);
        $searchtime_arr[1]=date('Y-m-d H:i:s', $searchtime_arr[1]);
        $where = array();
        $where['scm_client_order.supp_id']=$this->supp_info['supp_id'];
        $where['scm_client_order.order_status']=array('in','1,2');
        $where['scm_client_order.order_date'] = array('between',$searchtime_arr);

        //查询销量top
        //构造横轴数据
        for($i=1; $i<=$topnum; $i++){
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>'','y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = "$i";
        }
        $field = ' scm_order_goods.goods_barcode,SUM(scm_order_goods.set_num) as goodsnum ,min(scm_order_goods.goods_nm) as goods_name';
        $orderby = 'goodsnum desc,scm_client_order.id';
        $statlist = array();
        $order = SCMModel('gzkj_client_order');

        $statlist['goodsnum'] = $order->getGoodJoinList($where, $field,0,'scm_order_goods.goods_barcode',$orderby,$topnum);
        foreach ((array)$statlist['goodsnum'] as $k=>$v){
            $stat_arr['series'][0]['data'][$k] = array('name'=>strval($v['goods_name']),'y'=>intval($v['goodsnum']));
        }
        $stat_arr['series'][0]['name'] = '下单商品数';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '热卖商品TOP'.$topnum;
        $stat_arr['yAxis'] = '下单商品数';

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
        $field = ' scm_order_goods.goods_barcode,min(scm_order_goods.goods_nm) as goods_name,SUM(actual_amount) as orderamount ';
        $orderby = 'orderamount desc,scm_client_order.id';
        $statlist['orderamount'] = $order->getGoodJoinList($where, $field,0,'scm_order_goods.goods_barcode',$orderby,$topnum);
        foreach ((array)$statlist['orderamount'] as $k=>$v){
            $stat_arr['series'][0]['data'][$k] = array('name'=>strval($v['goods_name']),'y'=>floatval($v['orderamount']));
        }
        $stat_arr['series'][0]['name'] = '下单金额';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '热卖商品TOP'.$topnum;
        $stat_arr['yAxis'] = '下单金额';
        $stat_json['orderamount'] = getStatData_Column2D($stat_arr);
        Tpl::output('stat_json',$stat_json);
        Tpl::output('statlist',$statlist);
        self::profile_menu('hotgoods');
        Tpl::showpage('stat.goods.hotgoods');
    }
    /**
     * 商品列表
     */
    public function goodslistOp(){
        $order = SCMModel('gzkj_client_order');
        //统计的日期0点
        $stat_time = strtotime(date('Y-m-d',time())) - 86400;
        /*
         * 近30天
         */
        $stime =date('Y-m-d H:i:s', $stat_time - (86400*29) );//30天前
        $etime =date('Y-m-d H:i:s', $stat_time + 86400 - 1 );//昨天23:59
        //查询订单商品表下单商品数
        $where = array();
        $where['scm_client_order.supp_id']=$this->supp_info['supp_id'];
        $where['scm_client_order.order_status']=array('in','1,2');
        $where['scm_client_order.order_date'] = array('between',array($stime,$etime));
        if(trim($_GET['search_gname'])){
            $where['scm_order_goods.goods_nm'] = array('like',"%".trim($_GET['search_gname'])."%");
        }
        //查询总条数

        $count_arr = $order->getGoodJoinList($where, 'count(DISTINCT scm_order_goods.goods_barcode) as countnum');
        $countnum = intval($count_arr[0]['countnum']);

        $field = 'scm_order_goods.goods_barcode,min(scm_order_goods.id) as goods_id,min(scm_order_goods.goods_nm) as goods_name,min(scm_order_goods.goods_price) as goods_price,SUM(scm_order_goods.set_num) as ordergoodsnum,SUM(actual_amount) as ordergamount ';
        //排序
        $orderby_arr = array('ordergoodsnum asc','ordergoodsnum desc','ordergamount asc','ordergamount desc');
        if (!in_array(trim($this->search_arr['orderby']),$orderby_arr)){
            $this->search_arr['orderby'] = 'ordergoodsnum desc';
        }
        $orderby = trim($this->search_arr['orderby']).',goods_id';

        $stat_ordergoods  = $order->getGoodJoinList($where, $field,array(5,$countnum),'scm_order_goods.goods_barcode', $this->search_arr['orderby'],0);
        Tpl::output('goodslist',$stat_ordergoods);
        Tpl::output('show_page',$order->showpage());
        Tpl::output('orderby',$this->search_arr['orderby']);
        self::profile_menu('goodslist');
        Tpl::showpage('stat.goods.goodslist');
    }

    /**
     * 商品详细
     */
    public function goodsinfoOp(){
        $templatesname = 'stat.goods.goodsinfo';
        $goods_id = $_GET['gid'];
        if ($goods_id <= 0){
            Tpl::output('stat_msg','参数错误');
            Tpl::showpage($templatesname,'null_layout');
        }

       $goods_info= Model()->table('scm_order_goods')->where(array('goods_barcode'=>$goods_id))->group('goods_barcode')->select();
        $order = SCMModel('gzkj_client_order');
        //统计的日期0点
        $stat_time = strtotime(date('Y-m-d',time())) - 86400;
        /*
         * 近30天
         */
        $stime = $stat_time - (86400*29);//30天前
        $etime = $stat_time + 86400 - 1;//昨天23:59


        $stat_arr = array();
        for($i=$stime; $i<$etime; $i+=86400){
            //当前数据的时间
            $timetext = date('n',$i).'-'.date('j',$i);
            //统计图数据
            $stat_list['ordergoodsnum'][$timetext] = 0;
            $stat_list['ordergamount'][$timetext] = 0;
            $stat_list['ordernum'][$timetext] = 0;
            //横轴
            $stat_arr['ordergoodsnum']['xAxis']['categories'][] = $timetext;
            $stat_arr['ordergamount']['xAxis']['categories'][] = $timetext;
            $stat_arr['ordernum']['xAxis']['categories'][] = $timetext;
        }
        //查询订单商品表下单商品数
        $where = array();
        $stime =date('Y-m-d H:i:s', $stat_time - (86400*29) );//30天前
        $etime =date('Y-m-d H:i:s', $stat_time + 86400 - 1 );//昨天23:59
        $where['scm_client_order.supp_id']=$this->supp_info['supp_id'];
        $where['scm_client_order.order_status']=array('in','1,2');
        $where['scm_order_goods.goods_barcode'] = $goods_id;
        $where['scm_client_order.order_date'] = array('between',array($stime,$etime));

        $field = 'scm_order_goods.goods_barcode,min(scm_order_goods.id) as goods_id,min(scm_order_goods.goods_nm) as goods_name,COUNT(DISTINCT scm_order_goods.order_id) as ordernum,min(scm_order_goods.goods_price) as goods_price,SUM(scm_order_goods.set_num) as ordergoodsnum,SUM(actual_amount) as ordergamount, MONTH(scm_client_order.order_date) as monthval,DAY(scm_client_order.order_date) as dayval ';
        if (C('dbdriver') == 'mysql') {
            $_group = 'monthval,dayval';
        } else {
            $_group = 'MONTH(scm_client_order.order_date),DAY(scm_client_order.order_date)';
        }
        $stat_ordergoods = $order->getGoodJoinList($where, $field,0,$_group, '',0);
        $stat_count = array();
        if($stat_ordergoods){
            foreach($stat_ordergoods as $k => $v){
                $stat_list['ordergoodsnum'][$v['monthval'].'-'.$v['dayval']] = intval($v['ordergoodsnum']);
                $stat_list['ordergamount'][$v['monthval'].'-'.$v['dayval']] = floatval($v['ordergamount']);
                $stat_list['ordernum'][$v['monthval'].'-'.$v['dayval']] = intval($v['ordernum']);

                $stat_count['ordergoodsnum'] = intval($stat_count['ordergoodsnum']) + $v['ordergoodsnum'];
                $stat_count['ordergamount'] = floatval($stat_count['ordergamount']) + floatval($v['ordergamount']);
                $stat_count['ordernum'] = intval($stat_count['ordernum']) + $v['ordernum'];
            }
        }

        $stat_count['ordergamount'] = ncPriceFormat($stat_count['ordergamount']);

        $stat_arr['ordergoodsnum']['legend']['enabled'] = false;
        $stat_arr['ordergoodsnum']['series'][0]['name'] = '下单商品数';
        $stat_arr['ordergoodsnum']['series'][0]['data'] = array_values($stat_list['ordergoodsnum']);
        $stat_arr['ordergoodsnum']['title'] = '最近30天下单商品数走势';
        $stat_arr['ordergoodsnum']['yAxis'] = '下单商品数';
        $stat_json['ordergoodsnum'] = getStatData_LineLabels($stat_arr['ordergoodsnum']);

        $stat_arr['ordergamount']['legend']['enabled'] = false;
        $stat_arr['ordergamount']['series'][0]['name'] = '下单金额';
        $stat_arr['ordergamount']['series'][0]['data'] = array_values($stat_list['ordergamount']);
        $stat_arr['ordergamount']['title'] = '最近30天下单金额走势';
        $stat_arr['ordergamount']['yAxis'] = '下单金额';
        $stat_json['ordergamount'] = getStatData_LineLabels($stat_arr['ordergamount']);

        $stat_arr['ordernum']['legend']['enabled'] = false;
        $stat_arr['ordernum']['series'][0]['name'] = '下单量';
        $stat_arr['ordernum']['series'][0]['data'] = array_values($stat_list['ordernum']);
        $stat_arr['ordernum']['title'] = '最近30天下单量走势';
        $stat_arr['ordernum']['yAxis'] = '下单量';
        $stat_json['ordernum'] = getStatData_LineLabels($stat_arr['ordernum']);
        Tpl::output('stat_json',$stat_json);
        Tpl::output('stat_count',$stat_count);
        Tpl::output('goods_info',$goods_info[0]);
        Tpl::showpage($templatesname,'null_layout');
    }




    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key='') {
        $menu_array = array(
            1=>array('menu_key'=>'goodslist','menu_name'=>'商品详情',   'menu_url'=>'index.php?act=statistics_goods&op=goodslist'),
            3=>array('menu_key'=>'hotgoods','menu_name'=>'热卖商品',    'menu_url'=>'index.php?act=statistics_goods&op=hotgoods'),
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
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
