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

class statistics_saleControl extends SCMControl {
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

    }
    public function indexOp(){
        return $this->hotgoodsOp();
    }
    public function hotgoodsOp(){

        $order = SCMModel('gzkj_client_order');
        $where = array();
        $where['scm_client_order.supp_id']=$this->supp_info['supp_id'];
        $where['scm_client_order.order_status']=array('in','1,2');
        $field = ' COUNT( DISTINCT scm_client_order.clie_id) as num,scm_client.area_district as area,SUM(scm_client_order.total_amount) as orderamount ';
        $statlist['orderamount'] = $order->getCashAndCount($where, $field,'scm_client.area_district');

        //查询销量top
        //构造横轴数据
        for($i=0; $i<=9; $i++){
            $arr=['西岗区','中山区', '沙河口区','甘井子区', '旅顺口区', '金州区','普兰店区', '瓦房店市', '庄河市','长海县'];
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>'','y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = $arr[$i];
        }

        foreach ((array)$statlist['orderamount'] as $k=>$v){
            foreach($arr as $kk => $vv){
                if($v['area'] == $vv){
                    $stat_arr['series'][0]['data'][$kk] = array('name'=>$v['area'],'y'=>floatval($v['orderamount']));
                }
            }
        }

        $stat_arr['series'][0]['name'] = '订单金额';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '运营分析';
        $stat_arr['yAxis'] = '订单金额';
//        $stat_arr['xAxis']['categories'] =['中山区', '西岗区', '沙河口区','甘井子区', '旅顺口区', '金州区','普兰店区', '瓦房店市', '庄河市','长海县'];
        $stat_json['orderamount'] =getStatData_Column2D($stat_arr);
        unset($stat_arr);

        //查询销量top
        //构造横轴数据
        for($i=0; $i<=9; $i++){
            $arr=['西岗区','中山区', '沙河口区','甘井子区', '旅顺口区', '金州区','普兰店区', '瓦房店市', '庄河市','长海县'];
            //数据
            $stat_arr['series'][0]['data'][] = array('name'=>'','y'=>0);
            //横轴
            $stat_arr['xAxis']['categories'][] = $arr[$i];
        }

        foreach ((array)$statlist['orderamount'] as $k=>$v){
            foreach($arr as $kk => $vv){
                if($v['area'] == $vv){
                    $stat_arr['series'][0]['data'][$kk] = array('name'=>$v['area'],'y'=>floatval($v['num']));
                }
            }
        }
        $stat_arr['series'][0]['name'] = '合作商家个数';
        $stat_arr['legend']['enabled'] = false;
        //得到统计图数据
        $stat_arr['title'] = '运营分析';
        $stat_arr['yAxis'] = '合作商家个数';
//        $stat_arr['xAxis']['categories'] =['中山区', '西岗区', '沙河口区','甘井子区', '旅顺口区', '金州区','普兰店区', '瓦房店市', '庄河市','长海县'];
        $stat_json['goodsnum'] =getStatData_Column2D($stat_arr);

        Tpl::output('stat_json',$stat_json);
        Tpl::output('statlist',$statlist);
        Tpl::showpage('stat.sale.hotgoods');
    }
}
