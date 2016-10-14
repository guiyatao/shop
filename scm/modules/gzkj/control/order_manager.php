<?php
/**
 * 批发订单管理
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

class order_managerControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }
    public function indexOp()
    {
        return $this->showOp();
    }

    /**
     * 显示
     */
    public function showOp()
    {
//        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('order_manager.index');
    }


    public function get_xmlOp()
    {
        $order = SCMModel('gzkj_client_order');
        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['order_status']))) {
                $condition['order_status'] = (int) $q;
            }
        } else{
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'clie_id':
                        $condition['clie_id'] = $q;
                        break;
                    case 'order_no':
                        $condition['order_no'] = $q;
                        break;
                    case'supp_id':
                        $condition['supp_id'] = $q;
                        break;
                }
            }
        }
        $orders = $order->where($condition)->page($_POST['rp'])->order('order_date desc')->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        $index = ($data['now_page'] - 1) * $_POST['rp'];
            foreach ($orders as $k => $info) {
                $list = array();
                $index++;
                $list['operation'] = "<a class='btn blue' href='index.php?act=delivered&op=detail&order_id=" . $info['id'] . "&supp_id=".$info['supp_id']."'><i class='fa fa-pencil-square-o'></i>查看详情</a>";
                $list['number'] = $index;
                $list['order_no'] = $info['order_no'];
                $list['clie_id'] = $info['clie_id'];
                $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'], 'clie_ch_name');
                $list['supp_id'] = $info['supp_id'];
                $list['supp_ch_name'] = SCMModel('scm_supplier')->getfby_supp_id($info['supp_id'], 'supp_ch_name');
                $list['order_no'] = $info['order_no'];
                $list['total_amount'] = $info['total_amount'];
                if($info['order_status']==0){
                    if($info['out_flag']){
                        $list['order_status'] = '已发货';
                    }else{
                        $list['order_status'] = '未发货';
                    }

                }elseif($info['order_status']==1){
                    $list['order_status'] = '已完成';
                }elseif($info['order_status']==3){
                    $list['order_status'] = '取消单';
                }elseif($info['order_status']==4){
                    $list['order_status'] = '退货单';
                }
                $list['order_date'] = $info['order_date'];

                $data['list'][$info['id']] = $list;
                }
        echo Tpl::flexigridXML($data);
        exit();
    }
}
