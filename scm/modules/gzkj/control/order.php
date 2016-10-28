<?php
/**
 * 批发订单结算
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

class orderControl extends SCMControl
{
    const PAY_TO_SUPPLIER = 2;
    const PAY_TO_CLIENT = 3;
    public function __construct()
    {
        parent::__construct();
    }
    private $links = array(
        array('url' => 'act=order&op=index', 'text' => '供应商结算'),
        array('url' => 'act=order&op=show_flow', 'text' => '终端店结算'),
    );

    public function indexOp()
    {
        return $this->showOp();
    }

    /**
     * 显示
     */
    public function showOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::showpage('order.index');
    }

    /**
     * 显示
     */
    public function show_flowOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_flow'));
        Tpl::showpage('order_flow.index');
    }

    public function get_xmlOp()
    {
        $order = SCMModel('gzkj_settlement');
        if($_GET['type']==1){
            $where=array();
            $where['clie_id']= array('neq','');
            $orders=$order->where($where)->page($_POST['rp'])->select();
        }else{
            $where=array();
            $where['scm_settlement.supp_id']= array('neq','');
            $orders= $order->where($where)->page($_POST['rp'])->select();
        }
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                        $list = array();
                        $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['settlement_id'] . "')\">查看订单</a></li>";
                if($info['flag']==20||$info['flag']==30){
                    $list['operation'] .= "<a class=\"btn \" href='javascript:void(0)' ><i class=\"fa fa-ban\" ></i>结算</a></li>";
                }else{
                    $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"settlement('" . $info['settlement_id'] . "')\">结算</a></li>";
                }
                        if($_GET['type']==1){
                            $list['clie_id'] = $info['clie_id'];
                            $list['clie_ch_name'] =  SCMModel('gzkj_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
                            $list['cash_flow'] = '共铸商城->终端店';
                        }else{
                            $list['supp_id'] = $info['supp_id'];
                            $list['supp_ch_name'] = SCMModel('gzkj_supplier')->getfby_supp_id($info['supp_id'],'supp_ch_name');
                            $list['cash_flow'] = '共铸商城->供应商';
                        }

                        $list['order_pay'] = $info['amount'];
                        if($info['flag']==0||$info['flag']==2||$info['flag']==3){
                            $list['pay_flag'] = '未结算';
                        }elseif($info['flag']==20||$info['flag']==30){
                            $list['pay_flag'] = '已结算';
                        }
                        $list['time']=substr($info['settlement_date'],5,5);
                $img = UPLOAD_SITE_URL."/scm/settlement/".$info['photo'];
                $list['photo'] =  <<<EOB
            <a   href="{$img}" class="pic-thumb-tip" class="nyroModal"  onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
            <i class='fa fa-picture-o'></i></a>
EOB;
                $data['list'][$info['settlement_id']] = $list;
                }
            }
        echo Tpl::flexigridXML($data);
        exit();

    }

    public function get_flow_xmlOp()
    {
        $order = SCMModel('gzkj_settlement');
        if($_GET['type']==1){
            $where=array();
            $where['clie_id']= array('neq','');
            $orders=$order->where($where)->page($_POST['rp'])->select();
        }else{
            $where=array();
            $where['scm_settlement.supp_id']= array('neq','');
            $orders= $order->where($where)->page($_POST['rp'])->select();
        }
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        if(!empty($orders)) {
            foreach ($orders as $k => $info) {
                $list = array();
                $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku1('" . $info['settlement_id'] . "')\">查看订单</a></li>";
                if($info['flag']==20||$info['flag']==30){
                    $list['operation'] .= "<a class=\"btn \" href='javascript:void(0)' ><i class=\"fa fa-ban\" ></i>结算</a></li>";
                }else{
                    $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"settlement('" . $info['settlement_id'] . "')\">结算</a></li>";
                }
                if($_GET['type']==1){
                    $list['clie_id'] = $info['clie_id'];
                    $list['clie_ch_name'] =  SCMModel('gzkj_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
                    $list['cash_flow'] = '共铸商城->终端店';
                }else{
                    $list['supp_id'] = $info['supp_id'];
                    $list['supp_ch_name'] = SCMModel('gzkj_supplier')->getfby_supp_id($info['supp_id'],'supp_ch_name');
                    $list['cash_flow'] = '共铸商城->供应商';
                }

                $list['order_pay'] = $info['amount'];
                if($info['flag']==0||$info['flag']==2||$info['flag']==3){
                    $list['pay_flag'] = '未结算';
                }elseif($info['flag']==20||$info['flag']==30){
                    $list['pay_flag'] = '已结算';
                }
                $list['time']=substr($info['settlement_date'],5,5);
                $img = UPLOAD_SITE_URL."/scm/settlement/".$info['photo'];
                $list['photo'] =  <<<EOB
            <a   href="{$img}" class="pic-thumb-tip" class="nyroModal"  onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
            <i class='fa fa-picture-o'></i></a>
EOB;
                $data['list'][$info['settlement_id']] = $list;
            }
        }
        echo Tpl::flexigridXML($data);
        exit();

    }
    public function show_goodsOp()
    {

        $order = SCMModel('gzkj_client_order');
        $condition = array();
        $condition['scm_client_order.id'] = $_GET['id'];
        $list = $order->getGoodJoinList($condition);
        Tpl::output('goods_list', $list);
        Tpl::showpage('order.goods_list', 'null_layout');
    }

    public function show_ordersOp()
    {

        Tpl::output('settlement_id', $_GET['settlement_id']);
        Tpl::showpage('order.orders_list');
    }

    public function show_flow_ordersOp()
    {

        Tpl::output('settlement_id', $_GET['settlement_id']);
        Tpl::showpage('order_flow.orders_list');
    }
    public function get_order_xmlOp()
    {
        $order = SCMModel('gzkj_client_order');
        $orders=$order->where(array('settlement_id'=>$_GET['settlement_id']))->page($_POST['rp'])->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {
            $list = array();
            $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('" . $info['id'] . "')\">查看商品</a></li>";
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $list['clie_ch_name'] =  SCMModel('gzkj_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = SCMModel('gzkj_supplier')->getfby_supp_id($info['supp_id'],'supp_ch_name');
            $list['order_pay'] = $info['order_pay'];
            $list['pay_start_time'] = $info['pay_start_time'];
            $date=SCMModel('gzkj_settlement')->getfby_settlement_id($_GET['settlement_id'],'settlement_date');
            $list['time']=substr($date,5,5);
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    public function get_order_flow_xmlOp()
    {
        $order = SCMModel('gzkj_client_order');
        $orders=$order->where(array('settlement_id'=>$_GET['settlement_id']))->page($_POST['rp'])->select();
        $data = array();
        $data['now_page'] = $order->shownowpage();
        $data['total_num'] = $order->gettotalnum();
        foreach ($orders as $k => $info) {
            $list = array();
            $list['operation'] .= "<a class=\"btn blue\" href='javascript:void(0)' onclick=\"fg_sku('" . $info['id'] . "')\">查看商品</a></li>";
            $list['clie_id'] = $info['clie_id'];
            $list['order_no'] = $info['order_no'];
            $list['clie_ch_name'] =  SCMModel('gzkj_client')->getfby_clie_id($info['clie_id'],'clie_ch_name');
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = SCMModel('gzkj_supplier')->getfby_supp_id($info['supp_id'],'supp_ch_name');
            $list['order_pay'] = $info['order_pay'];
            $list['pay_start_time'] = $info['pay_start_time'];
            $date=SCMModel('gzkj_settlement')->getfby_settlement_id($_GET['settlement_id'],'settlement_date');
            $list['time']=substr($date,5,5);
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    public function settlementOp()
    {

        $model_settlement=SCMModel('gzkj_settlement');
        if (chksubmit()) {
            $result = $model_settlement->where(array("settlement_id"=>$_GET['settlement_id']))->find();
            $temp_img = $result['photo'];
            $data = array();
            $data['user_name']=$this->getAdminInfo()['name'];
            $data['change_date']=date("Y-m-d H:i:s");
            $data['comments'] = trim($_POST['comments']);
            $data['flag'] = 20;
            if($_FILES['photo']['name']) {
                @unlink(BASE_UPLOAD_PATH.DS.'scm/settlement'.DS.$temp_img);
                $upload = new UploadFile();
                $upload->set('default_dir', 'scm/settlement');
                $result = $upload->upfile('photo');
                if (!$result) {
                    showMessage($upload->error);
                }
                $data['photo'] = $upload->file_name;
                $settlement_id=$_GET['settlement_id'];
                $result=$model_settlement->where(array('settlement_id'=>$settlement_id))->update($data,$settlement_id);

                if($_POST['type']){
                    $re=$this->changeState($_GET['settlement_id'],self::PAY_TO_SUPPLIER);
                }else{
                    $re=$this->changeState($_GET['settlement_id'],self::PAY_TO_CLIENT);
                }
                if ($result&&$re){
                    $url = array(
                        array(
                            'url'=>'index.php?act=order&op=index',
                            'msg'=>"返回结算列表",
                        )
                    );
                    $this->log('结算[ID:'.intval($_GET['$settlement_id']).']',1);
                    showMessage("结算成功",$url);
                }else {
                    //添加失败则删除刚刚上传的图片,节省空间资源
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/settlement'.DS.$upload->file_name);
                    showMessage("结算失败");
                }
            }
        }else{
            $settlement = $model_settlement->where(array("settlement_id"=>$_GET['settlement_id']))->find();
            Tpl::output('settlement', $settlement);
        }
        Tpl::showpage('order.settlement1');
    }

    private function changeState($settlement_id,$state){
        $client_order=SCMModel('gzkj_client_order');
        $result=$client_order->where(array('settlement_id'=>$settlement_id))->update(array('pay_flag'=>$state));
        return $result;
    }
}