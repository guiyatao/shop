<?php
/**
 * 终端店管理
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
class clientControl extends SCMControl{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp() {
        return $this->showOp();
    }

    /**
     * 显示
     */
    public function showOp(){
        Tpl::showpage('client.index');
    }

    public function get_xmlOp(){
        $client=SCMModel('gzkj_client');
        $clients=$client->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $client->shownowpage();
        $data['total_num'] = $client->gettotalnum();
        foreach ($clients as $k => $info) {
            $list = array();
            $o = "<a class='btn red' href='javascript:void(0);' onclick='fg_del(".$value['act_id'].")'><i class='fa fa fa-trash-o'></i>删除</a>";
            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
            $o .= '<li><a href="index.php?act=activity&op=deal&state=1&act_id=' .
                $value['act_id'] .
                '">通过</a></li>';
            $o .= '<li><a href="index.php?act=activity&op=deal&state=2&act_id=' .
                $value['act_id'] .
                '">拒绝</a></li>';
            $o .= '<li><a href="index.php?act=activity&op=activity_edit&act_id=' .
                $value['act_id'] .
                '">编辑</a></li>';
            $o .= '</ul></span>';

            $list['operation']=$o;
            $list['id'] = $info['id'];
            $list['clie_id'] = $info['clie_id'];
            $list['clie_ch_name'] = $info['clie_ch_name'];
            $list['area_city'] = $info['area_city'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);exit();

    }
    /**
     * 删除终端店
     */
    public function client_delOp(){
        if (!empty($_GET['id'])){

            Model()->table('scm_client')->where(array('id'=>intval($_GET['id'])))->delete();
//            $this->log(L('nc_delete,limit_admin').'[ID:'.intval($_GET['admin_id']).']',1);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }

    /**
     * 编辑终端店
     */
    public function client_editOp(){
        if (chksubmit()){

            $clie_id = intval($_GET['id']);
            $data['clie_ch_name'] = $_POST['clie_ch_name'];
            $data['clie_address'] = $_POST['clie_address'];
            $data['clie_mobile'] = $_POST['clie_mobile'];
            //查询管理员信息
            $client_model = SCMModel('gzkj_client');
            $result = $client_model->updates($data,$clie_id);
            if ($result){
                showMessage(Language::get('admin_edit_success'),'index.php?act=client&op=index');
            }else{
                showMessage(Language::get('admin_edit_fail'),'index.php?act=client&op=index');
            }
        }else{
//            查询用户信息
            $client_model = SCMModel('gzkj_client');
//            $clientinfo = $client_model->where(array('id'=>intval($_GET['id'])))->find();
            $clientinfo = $client_model->getby_id(intval($_GET['id']));
            if (!is_array($clientinfo) || count($clientinfo)<=0){
                showMessage(Language::get('admin_edit_admin_error'),'index.php?act=client&op=index');
            }
            Tpl::output('clientinfo',$clientinfo);
            Tpl::showpage('client.edit');
        }
    }
    /**
     * 添加终端店
     */
    public function client_addOp(){
        if (chksubmit()){
            $limit_str = '';
            $model_client = SCMModel('gzkj_client');
            $param['clie_ch_name'] = $_POST['clie_ch_name'];
            $param['clie_address'] = $_POST['clie_address'];
            $param['clie_mobile'] = $_POST['clie_mobile'];
            $rs = $model_client->add($param);
            if ($rs){
//                $this->log(L('nc_add,limit_admin').'['.$_POST['admin_name'].']',1);
                showMessage(L('nc_common_save_succ'),'index.php?act=client&op=index');
            }else {
                showMessage(L('nc_common_save_fail'));
            }
        }

        Tpl::showpage('client.add');

    }
}
