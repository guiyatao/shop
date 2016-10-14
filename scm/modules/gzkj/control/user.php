<?php
/**
 * 用户管理
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

class userControl extends SCMControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('setting');
    }

    private $links = array(
        array('url' => 'act=user&op=show_client', 'text' => '终端店'),
        array('url' => 'act=user&op=show_supp', 'text' => '供应商'),
    );

    public function indexOp()
    {
        return $this->show_clientOp();
    }


    public function show_clientOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_client'));
        Tpl::showpage('user_client.index');
    }

    public function show_suppOp()
    {
        Tpl::output('top_link', $this->sublink($this->links, 'show_supp'));
        Tpl::showpage('user_supp.index');
    }

    public function get_xmlOp()
    {

        $user = SCMModel('gzkj_user');
        $users = $user->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $user->shownowpage();
        $data['total_num'] = $user->gettotalnum();
        foreach ($users as $k => $info) {
            $list = array();
            if ($info['user_degree'] == 0) {
                continue;
            } elseif ($info['user_degree'] == '1') {
                $list['operation'] .= '<a class="btn red" href="javascript:fg_operation_del(' . $info['user_id'] . ');"><i class="fa fa-trash-o"></i>删除</a>';
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_close&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>关闭</a>';
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_open&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i> 开启 </a>';
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_edit&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>' . L('nc_edit') . '</a>';
                $list['user_id'] = $info['user_id'];
                $list['user_name'] = $info['user_name'];
                $list['user_degree'] = '经理';
                $list['supp_clie_id'] = $info['supp_clie_id'];
                if ($info['user_type'] == '2') {
                    $list['user_type'] = '终端店';
                } elseif ($info['user_type'] == '3') {
                    $list['user_type'] = '供应商';
                }
                $data['list'][$info['user_id']] = $list;
            }
        }
        echo Tpl::flexigridXML($data);
        exit();

    }


    public function get_xml_clientOp()
    {

        $user = SCMModel('gzkj_user');
        $users = $user->where(array('user_type' => 2,'user_degree'=>1))->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $user->shownowpage();
        $data['total_num'] = $user->gettotalnum();
        foreach ($users as $k => $info) {
            $list = array();
//                $list['operation'] .= '<a class="btn red" href="javascript:fg_operation_del(' . $info['user_id'] . ');"><i class="fa fa-trash-o"></i>删除</a>';
//                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_open&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i> 开启 </a>';
//                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_close&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>关闭</a>';
            $info['is_close'] = SCMModel('scm_client')->getfby_clie_id($info['supp_clie_id'], 'is_close');
                if ($info['is_close'] == 1){
                    $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_open&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i> 开启 </a>';
                }
                if ($info['is_close'] == 0){
                    $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_close&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>关闭</a>';
                }
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=clie_edit&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>' . L('nc_edit') . '</a>';
                $list['user_id'] = $info['user_id'];
                $list['user_name'] = $info['user_name'];
                $list['user_degree'] = '经理';
                $list['supp_clie_id'] = $info['supp_clie_id'];
                if ($info['user_type'] == '2') {
                    $list['user_type'] = '终端店';
                } elseif ($info['user_type'] == '3') {
                    $list['user_type'] = '供应商';
                }
                $info['is_close'] = SCMModel('scm_client')->getfby_clie_id($info['supp_clie_id'], 'is_close');
                if ($info['is_close'] == 1) {
                    $list['is_close'] = '关闭';
                } elseif ($info['is_close'] == 0) {
                    $list['is_close'] = '开启';
                }
                $data['list'][$info['user_id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    public function get_xml_suppOp()
    {

        $user = SCMModel('gzkj_user');
        $users = $user->where(array('user_type' => 3,'user_degree'=>1))->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $user->shownowpage();
        $data['total_num'] = $user->gettotalnum();
        foreach ($users as $k => $info) {
            $list = array();

//                $list['operation'] .= '<a class="btn red" href="javascript:fg_operation_del(' . $info['user_id'] . ');"><i class="fa fa-trash-o"></i>删除</a>';
            $info['is_close'] = SCMModel('scm_supplier')->getfby_supp_id($info['supp_clie_id'], 'is_close');

            if ($info['is_close'] == 1) {
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_open&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i> 开启 </a>';
            }
            if ($info['is_close'] == 0) {
                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=user_close&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>关闭</a>';
            }

                $list['operation'] .= '<a class="btn blue" href="index.php?act=user&op=supp_edit&user_id=' . $info['user_id'] . '"><i class="fa fa-pencil-square-o"></i>' . L('nc_edit') . '</a>';
                $list['user_id'] = $info['user_id'];
                $list['user_name'] = $info['user_name'];
                $list['user_degree'] = '经理';
                $list['supp_clie_id'] = $info['supp_clie_id'];
                if ($info['user_type'] == '2') {
                    $list['user_type'] = '终端店';
                } elseif ($info['user_type'] == '3') {
                    $list['user_type'] = '供应商';
                }

                $info['is_close'] = SCMModel('scm_supplier')->getfby_supp_id($info['supp_clie_id'], 'is_close');
                if ($info['is_close'] == 1) {
                    $list['is_close'] = '关闭';
                } elseif ($info['is_close'] == 0) {
                    $list['is_close'] = '开启';
                }
                $data['list'][$info['user_id']] = $list;

        }
        echo Tpl::flexigridXML($data);
        exit();

    }


    public function ajaxOp()
    {
        switch ($_GET['branch']) {
            //用户名验证
            case 'check_admin_name':
                $model_user = SCMModel('gzkj_user');
                $condition['user_name'] = $_GET['user_name'];
                $condition['user_id'] = array('neq', intval($_GET['user_id']));
                $list = $model_user->where($condition)->find();
                if (!empty($list)) {
                    exit('false');
                } else {
                    exit('true');
                }
                break;
        }
    }

    /**
     * 删除用户
     */
    public function user_delOp()
    {
        if (!empty($_GET['user_id'])) {
            $model_user = SCMModel('gzkj_user');
            $model_supp = SCMModel('gzkj_supplier');
            $model_client = SCMModel('gzkj_client');
            $user = $model_user->where(array('user_id' => intval($_GET['user_id'])))->find();
            if ($user['user_degree'] == 1) {//如果是经理就删掉整个账户
                if ($user['user_type'] == 3) {
                    $model_supp->where(array('supp_id' => $user["supp_clie_id"]))->delete();
                }
                if ($user['user_type'] == 2) {
                    $model_client->where(array('clie_id' => $user["supp_clie_id"]))->delete();
                }
            }
            Model()->table('admin')->where(array('admin_id' => $user['admin_id']))->delete();
            $model_user->where(array('user_id' => intval($_GET['user_id'])))->delete();
            exit(json_encode(array('state' => true, 'msg' => '删除成功')));
        } else {
            exit(json_encode(array('state' => false, 'msg' => '删除失败')));
        }
    }


    public function  user_closeOp()
    {
        $pwd = $this->generate_password();
        if (!empty($_GET['user_id'])) {
            $model_user = SCMModel('gzkj_user');
            $model_supp = SCMModel('gzkj_supplier');

            $supp_stock = SCMModel('gzkj_supp_stock');

            $model_client = SCMModel('gzkj_client');
            $user = $model_user->where(array('user_id' => intval($_GET['user_id'])))->find();
            $data1['admin_id'] = $user['admin_id'];
            $data1['admin_password'] = md5($pwd);
            $admin_model = Model('admin');
            $result1 = $admin_model->updateAdmin($data1);
            if ($user['user_type'] == 3) {

                $result2 = $supp_stock->where(array('supp_id' => $user["supp_clie_id"],'status'=>1))->update(array('status' => 0));
                $result3 = $model_supp->where(array('supp_id' => $user["supp_clie_id"]))->update(array('is_close' => 1));
                $model_activity = SCMModel('gzkj_activity');
                $result5 = $model_activity->where(array('supp_id' => $user["supp_clie_id"],'activity_status'=>1))->update(array('activity_status' => 0));

                if ($result1 && $result2 && $result3 && $result5) {
                    $this->scmLog('关闭供应商成功',1);
                    showMessage('关闭成功', 'index.php?act=user&op=show_supp');
                } else {
                    $this->scmLog('关闭终端店失败',1);
                    showMessage('关闭失败', 'index.php?act=user&op=show_supp');
                }
            }
            if ($user['user_type'] == 2) {

                $result4 = $model_client->where(array('clie_id' => $user["supp_clie_id"]))->update(array('is_close' => 1));
                if ($result1 && $result4) {
                    $this->scmLog('关闭终端店成功',1);
                    showMessage('关闭成功', 'index.php?act=user&op=show_client');
                } else {
                    $this->scmLog('关闭终端店失败',1);
                    showMessage('关闭失败', 'index.php?act=user&op=show_client');
                }
            }


        }


    }

    public function user_openOp()
    {
        $pwd = '123456';
        if (!empty($_GET['user_id'])) {
            $model_user = SCMModel('gzkj_user');
            $model_supp = SCMModel('gzkj_supplier');

            $supp_stock = SCMModel('gzkj_supp_stock');

            $model_client = SCMModel('gzkj_client');
            $user = $model_user->where(array('user_id' => intval($_GET['user_id'])))->find();

            $data1['admin_id'] = $user['admin_id'];
            $data1['admin_password'] = md5($pwd);
            $admin_model = Model('admin');
            $result1 = $admin_model->updateAdmin($data1);
            if ($user['user_type'] == 3) {
                $result2 = $supp_stock->where(array('supp_id' => $user["supp_clie_id"],'status'=>0))->update(array('status' => 1));
                $result3 = $model_supp->where(array('supp_id' => $user["supp_clie_id"]))->update(array('is_close' => 0));

                $model_activity = SCMModel('gzkj_activity');
                $result5 = $model_activity->where(array('supp_id' => $user["supp_clie_id"],'activity_status'=>0))->update(array('activity_status' => 1));

                if ($result1 && $result2 && $result3 && $result5) {
                    $this->scmLog('开启供应商成功',1);
                    showMessage('开启成功', 'index.php?act=user&op=show_supp');
                } else {
                    $this->scmLog('开启供应商失败',1);
                    showMessage('开启失败', 'index.php?act=user&op=show_supp');
                }
            }
            if ($user['user_type'] == 2) {

                $result4 = $model_client->where(array('clie_id' => $user["supp_clie_id"]))->update(array('is_close' => 0));
                if ($result1 && $result4) {
                    $this->scmLog('开启终端店成功',1);
                    showMessage('开启成功', 'index.php?act=user&op=show_client');
                } else {
                    $this->scmLog('开启终端店失败',1);
                    showMessage('开启失败', 'index.php?act=user&op=show_client');
                }
            }

        }
    }

    /**
     * 编辑终端店
     */
    public function user_editOp()
    {
        if (chksubmit()) {
            $user_id = intval($_GET['user_id']);
            $data['user_name'] = $_POST['user_name'];
            $model_user = SCMModel('gzkj_user');
            $result = $model_user->updates($data, $user_id);
            $userinfo = $model_user->getby_user_id(intval($_GET['user_id']));
            $data1['admin_id'] = $userinfo['admin_id'];
            $data1['admin_name'] = $_POST['user_name'];
            //没有更改密码
            if ($_POST['user_password'] != '') {
                $data1['admin_password'] = md5($_POST['user_password']);
            }
            $admin_model = Model('admin');
            $result1 = $admin_model->updateAdmin($data1);
            if ($result && $result1) {
                showMessage(Language::get('admin_edit_success'), 'index.php?act=user&op=index');
            } else {
                showMessage(Language::get('admin_edit_fail'), 'index.php?act=user&op=index');
            }
        } else {
//            查询用户信息
            $user_model = SCMModel('gzkj_user');
            $userinfo = $user_model->getby_user_id(intval($_GET['user_id']));
            switch ($userinfo['user_type']) {
                case '3':
                    $userinfo['user_type'] = '供应商';
                    break;
                case '2':
                    $userinfo['user_type'] = '终端店';
                    break;
            }
            if (!is_array($userinfo) || count($userinfo) <= 0) {
                showMessage(Language::get('admin_edit_admin_error'), 'index.php?act=user&op=index');
            }
            $userinfo['admin_password'] = Model()->table('admin')->getfby_admin_id($userinfo["admin_id"], 'admin_password');
            Tpl::output('userinfo', $userinfo);
            Tpl::showpage('user.edit');
        }
    }

    public function supp_editOp()
    {
        if (chksubmit()) {
            $user_id = intval($_GET['user_id']);
            $data['user_name'] = $_POST['user_name'];
            $model_user = SCMModel('gzkj_user');
            $result = $model_user->updates($data, $user_id);
            $userinfo = $model_user->getby_user_id(intval($_GET['user_id']));
            $data1['admin_id'] = $userinfo['admin_id'];
            $data1['admin_name'] = $_POST['user_name'];
            //没有更改密码
            if ($_POST['user_password'] != '') {
                $data1['admin_password'] = md5($_POST['user_password']);
            }
            $admin_model = Model('admin');
            $result1 = $admin_model->updateAdmin($data1);

            $model_supp = SCMModel('gzkj_supplier');
            $data = $_POST;
            unset($data['form_submit']);
            unset($data['user_type']);
            unset($data['user_name']);
            unset($data['user_password']);
            unset($data['user_rpassword']);
            unset($data['supp_clie_id']);
//            var_dump($data);die();

            $result2 = $model_supp->updates($data, $userinfo['supp_clie_id']);


            if ($result && $result1 && $result2) {
                $this->scmLog('编辑供应商成功',1);
                showMessage('保存成功', 'index.php?act=user&op=show_supp');
            } else {
                $this->scmLog('编辑供应商成功',1);
                showMessage(Language::get('admin_edit_fail'), 'index.php?act=user&op=index');
            }
        } else {
//            查询用户信息
            $user_model = SCMModel('gzkj_user');
            $userinfo = $user_model->getby_user_id(intval($_GET['user_id']));
            $supp_model = SCMModel('gzkj_supplier');
            $suppinfo = $supp_model->getby_supp_id($userinfo['supp_clie_id']);
            switch ($userinfo['user_type']) {
                case '3':
                    $userinfo['user_type'] = '供应商';
                    break;
                case '2':
                    $userinfo['user_type'] = '终端店';
                    break;
            }
            if (!is_array($userinfo) || count($userinfo) <= 0) {
                showMessage(Language::get('admin_edit_admin_error'), 'index.php?act=user&op=index');
            }
            $userinfo['admin_password'] = Model()->table('admin')->getfby_admin_id($userinfo["admin_id"], 'admin_password');
            Tpl::output('suppinfo', $suppinfo);
            Tpl::output('userinfo', $userinfo);
            Tpl::showpage('user_supp.edit');
        }
    }

    public function clie_editOp()
    {
        if (chksubmit()) {
            $user_id = intval($_GET['user_id']);
            $data['user_name'] = $_POST['user_name'];
            $model_user = SCMModel('gzkj_user');
            $result = $model_user->updates($data, $user_id);
            $userinfo = $model_user->getby_user_id(intval($_GET['user_id']));
            $data1['admin_id'] = $userinfo['admin_id'];
            $data1['admin_name'] = $_POST['user_name'];
            //没有更改密码
            if ($_POST['user_password'] != '') {
                $data1['admin_password'] = md5($_POST['user_password']);
            }
            $admin_model = Model('admin');
            $result1 = $admin_model->updateAdmin($data1);

            $model_clie = SCMModel('gzkj_client');
            $data = $_POST;
            unset($data['form_submit']);
            unset($data['user_type']);
            unset($data['user_name']);
            unset($data['user_password']);
            unset($data['user_rpassword']);
            unset($data['supp_clie_id']);

            $result2 = $model_clie->updates($data, $userinfo['supp_clie_id']);


            if ($result && $result1 && $result2) {
                $this->scmLog('编辑终端店成功',1);
                showMessage('编辑成功', 'index.php?act=user&op=index');
            } else {
                $this->scmLog('编辑终端店失败',1);
                showMessage('编辑失败', 'index.php?act=user&op=index');
            }
        } else {
//            查询用户信息
            $user_model = SCMModel('gzkj_user');
            $userinfo = $user_model->getby_user_id(intval($_GET['user_id']));
            $clie_model = SCMModel('gzkj_client');
            $clieinfo = $clie_model->getby_clie_id($userinfo['supp_clie_id']);
            switch ($userinfo['user_type']) {
                case '3':
                    $userinfo['user_type'] = '供应商';
                    break;
                case '2':
                    $userinfo['user_type'] = '终端店';
                    break;
            }
            if (!is_array($userinfo) || count($userinfo) <= 0) {
                showMessage(Language::get('admin_edit_admin_error'), 'index.php?act=user&op=index');
            }
            $userinfo['admin_password'] = Model()->table('admin')->getfby_admin_id($userinfo["admin_id"], 'admin_password');
            Tpl::output('clieinfo', $clieinfo);
            Tpl::output('userinfo', $userinfo);
            Tpl::showpage('user_clie.edit');
        }
    }

    /**
     * 添加终端店
     */
    public function user_addOp()
    {
        if (chksubmit()) {
            $model_user = SCMModel('gzkj_user');
            $model_admin = Model('admin');
            $model_supp = SCMModel('gzkj_supplier');
            $model_client = SCMModel('gzkj_client');

            $param1['admin_name'] = $_POST['user_name'];
            $param1['admin_password'] = md5($_POST['user_password']);
            if ($_POST['user_type'] == '2') {
                $param1['admin_gid'] = 4;//终端店组
            } elseif ($_POST['user_type'] == '3') {
                $param1['admin_gid'] = 3;//供应商组
            }
            $admin_id = $model_admin->addAdmin($param1);
            $param['admin_id'] = $admin_id;
            $param['user_type'] = $_POST['user_type'];
            $param['user_name'] = $_POST['user_name'];
            $param['user_degree'] = 1;//1表示经理0表示业务员
            $param['supp_clie_id'] = $_POST['supp_clie_id'];
            $rs = $model_user->add($param);
            $param2['clie_id'] = $_POST['supp_clie_id'];
            $param3['supp_id'] = $_POST['supp_clie_id'];
            if ($_POST['user_type'] == '2') {//2表示供应商
                $rs2 = $model_client->add($param2);
            } elseif ($_POST['user_type'] == '3') {//
                $rs3 = $model_supp->add($param3);
            }
            if ($rs) {

                showMessage(L('nc_common_save_succ'), 'index.php?act=user&op=index');
            } else {

                showMessage(L('nc_common_save_fail'));
            }
        };
        Tpl::showpage('user.add');
    }

    public function clie_addOp()
    {
        if (chksubmit()) {
            $model_user = SCMModel('gzkj_user');
            $model_admin = Model('admin');
            $model_client = SCMModel('gzkj_client');

            $data = $_POST;
            unset($data['form_submit']);
            unset($data['user_type']);
            unset($data['user_name']);
            unset($data['user_password']);
            unset($data['user_rpassword']);
            unset($data['supp_clie_id']);
            $data['clie_id'] = $_POST['supp_clie_id'];
            $rs1 = $model_client->add($data);

            $param1['admin_name'] = $_POST['user_name'];
            $param1['admin_password'] = md5($_POST['user_password']);
            if ($_POST['user_type'] == '2') {
                $param1['admin_gid'] = 4;//终端店组
            } elseif ($_POST['user_type'] == '3') {
                $param1['admin_gid'] = 3;//供应商组
            }
            $admin_id = $model_admin->addAdmin($param1);
            $param['admin_id'] = $admin_id;
            $param['user_type'] = $_POST['user_type'];
            $param['user_name'] = $_POST['user_name'];
            $param['user_degree'] = 1;//1表示经理0表示业务员
            $param['supp_clie_id'] = $_POST['supp_clie_id'];
            $rs = $model_user->add($param);


            if ($rs && $rs1) {
                $this->scmLog('添加终端店成功',1);
                showMessage(L('nc_common_save_succ'), 'index.php?act=user&op=index');
            } else {
                $this->scmLog('添加终端店失败',1);
                showMessage(L('nc_common_save_fail'));
            }
        };
        Tpl::showpage('user_clie.add');
    }

    public function supp_addOp()
    {
        if (chksubmit()) {
            $model_user = SCMModel('gzkj_user');
            $model_admin = Model('admin');
            $model_supp = SCMModel('gzkj_supplier');

            $data = $_POST;
            unset($data['form_submit']);
            unset($data['user_type']);
            unset($data['user_name']);
            unset($data['user_password']);
            unset($data['user_rpassword']);
            unset($data['supp_clie_id']);
            $data['supp_id'] = $_POST['supp_clie_id'];
            $model_supp->add($data);


            $param1['admin_name'] = $_POST['user_name'];
            $param1['admin_password'] = md5($_POST['user_password']);
            if ($_POST['user_type'] == '2') {
                $param1['admin_gid'] = 4;//终端店组
            } elseif ($_POST['user_type'] == '3') {
                $param1['admin_gid'] = 3;//供应商组
            }
            $admin_id = $model_admin->addAdmin($param1);
            $param['admin_id'] = $admin_id;
            $param['user_type'] = $_POST['user_type'];
            $param['user_name'] = $_POST['user_name'];
            $param['user_degree'] = 1;//1表示经理0表示业务员
            $param['supp_clie_id'] = $_POST['supp_clie_id'];
            $rs = $model_user->add($param);


            if ($rs) {
                $this->scmLog('添加终端店成功',1);
                showMessage(L('nc_common_save_succ'), 'index.php?act=user&op=index');
            } else {
                $this->scmLog('添加终端店失败',1);
                showMessage(L('nc_common_save_fail'));
            }
        };
        Tpl::showpage('user_supp.add');
    }

    private function generate_password($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
//        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
