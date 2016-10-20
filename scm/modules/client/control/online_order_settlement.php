<?php
/**
 * 商城订单结算
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
class online_order_settlementControl extends SCMControl
{
    protected $user_info;
    public function __construct()
    {
        parent::__construct();
        $this->user_info = SCMModel('scm_user')->getUserInfo($this->admin_info['id']);
    }
    private $links = array(
        array('url' => 'act=online_order_setttlement&op=index', 'text' => '商城订单结算'),
//        array('url' => 'act=online_order_settlement&op=show_payed', 'text' => '交易清单'),
    );


    public function indexOp()
    {
        return $this->showOp();
    }
    public function showOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('online_order_settlement.index');
    }

    /**
     * 显示
     */
    public function show_payedOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_payed'));
        Tpl::showpage('online_order_end.index');
    }


    public function get_xmlOp()
    {
        $online_order=SCMModel('gzkj_online_settlement');
        $where['clie_id']=$this->user_info['supp_clie_id'];
        $orders=$online_order->where($where)->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $online_order->shownowpage();
        $data['total_num'] = $online_order->gettotalnum();
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                $list = array();
                    $list['operation'] .= "<a  class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['settlement_id'] . "')\">查看订单</a></li>";
//                    $list['operation'] .= "<a  class=\"btn blue\" href='javascript:void(0)' onclick=\"online_settlement('" . $info['settlement_id'] . "')\">结算</a></li>";
                    $list['clie_id'] = $info['clie_id'];
                    $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'], 'clie_ch_name');
                    $list['order_amount'] = $info['amount'];
                    $list['cash_flow'] = '共铸商城->终端店';
                    if($info['flag']==30){
                        $list['pay_flag'] = '已结算';
                    }else{
                        $list['pay_flag'] = '未结算';
                    }

                    $list['time']=substr($info['settlement_date'],5,5);
                $img = UPLOAD_SITE_URL."/scm/online_settlement/".$info['photo'];
                $list['photo'] =  <<<EOB
            <a href="{$img}" class="pic-thumb-tip  nyroModal" onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
            <i class='fa fa-picture-o'></i></a>
EOB;
                    $data['list'][$info['settlement_id']] = $list;
            }
        }
        echo Tpl::flexigridXML($data);
        exit();

    }

    public  function show_goodsOp(){

        $goods = SCMModel('gzkj_online_order_goods');
        $condition=array();
        $condition['order_id']=$_GET['order_id'];
        $condition['clie_id']=$_GET['clie_id'];
        $list = $goods->where($condition)->select();

        Tpl::output('goods_list', $list);
        Tpl::showpage('online_order_settlement.goods_list', 'null_layout');
    }




    public function show_ordersOp()
    {
        Tpl::output('settlement_id', $_GET['settlement_id']);
        Tpl::showpage('online_order_settlement.orders_list');
    }
    public function get_order_xmlOp()
    {
        $order = SCMModel('gzkj_online_order');
        $orders=$order->where(array('settlement_id'=>$_GET['settlement_id']))->page($_POST['rp'])->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        if(!empty($orders)){
            foreach ($orders as $k => $info) {
                $list = array();
                $list['operation'] .= "<li><a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('".$info['order_id']."','".$info['clie_id']."')\">查看商品</a>";
                $list['order_sn'] = $info['order_sn'];
                $list['clie_id'] = $info['clie_id'];
                $list['clie_ch_name'] = SCMModel('scm_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
                $list['cash_flow'] = '共铸商城->终端店';
                $list['order_amount'] = $info['order_amount'];
//                if($info['pay_flag']==0){
//                    $list['pay_flag'] = '未结算';
//                }else{
//                    $list['pay_flag'] = '已结算';
//                }
                $date=SCMModel('gzkj_online_settlement')->getfby_settlement_id($_GET['settlement_id'],'settlement_date');
                $list['time']=substr($date,5,5);
                $data['list'][$info['id']] = $list;
            }
        }

        echo Tpl::flexigridXML($data);
        exit();
    }


    public function settlementOp()
    {

        $model_settlement=SCMModel('gzkj_online_settlement');
        if (chksubmit()) {
            $result = $model_settlement->where(array("settlement_id"=>$_GET['settlement_id']))->find();
            $temp_img = $result['photo'];
            $data = array();
            $data['user_name']=$this->getAdminInfo()['name'];
            $data['change_date']=date("Y-m-d H:i:s");
            $data['comments'] = trim($_POST['comments']);
            $data['flag'] = 30;
            if($_FILES['photo']['name']) {
                @unlink(BASE_UPLOAD_PATH.DS.'scm/online_settlement'.DS.$temp_img);
                $upload = new UploadFile();
                $upload->set('default_dir', 'scm/online_settlement');
                $result = $upload->upfile('photo');
                if (!$result) {
                    showMessage($upload->error);
                }
                $data['photo'] = $upload->file_name;
                $settlement_id=$_GET['settlement_id'];
                $result=$model_settlement->where(array('settlement_id'=>$settlement_id))->update($data,$settlement_id);
                $re=$this->changeState($_GET['settlement_id'],1);
                if ($result&&$re){
                    $url = array(
                        array(
                            'url'=>'index.php?act=online_order&op=index',
                            'msg'=>"返回结算列表",
                        )
                    );
                    $this->log('结算[ID:'.intval($_GET['$settlement_id']).']',1);
                    showMessage("结算成功",$url);
                }else {
                    //添加失败则删除刚刚上传的图片,节省空间资源
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/online_settlement'.DS.$upload->file_name);
                    showMessage("结算失败");
                }
            }
        }else{
            $settlement = $model_settlement->where(array("settlement_id"=>$_GET['settlement_id']))->find();
            Tpl::output('settlement', $settlement);
        }
        Tpl::showpage('online_order.settlement1');
    }

    private function changeState($settlement_id,$state){
        $online_order=SCMModel('gzkj_online_order');
        $result=$online_order->where(array('settlement_id'=>$settlement_id))->update(array('pay_flag'=>$state));
        return $result;
    }
}
