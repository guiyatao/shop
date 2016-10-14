<?php
/**
 * 业务员管理
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
class clerkControl extends SCMControl
{
    const EXPORT_SIZE = 1000;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->clerkOp();
    }

    /*
     * 业务员管理
     */
    public function clerkOp(){
        Tpl::showpage('clerk.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_clerk = SCMModel('supplier_clerk');
        $model_supplier = SCMModel('supplier_account');
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //除当前管理员下所有的业务员
        $condition = array(
            "admin.admin_id" => array('neq', $adminInfo['id'])
        );
        //当前供应商下的业务员
        $adminInfo = $this->getAdminInfo();
        $result = $model_supplier->getSupplier(array("admin.admin_id" => $adminInfo['id']));
        $condition['supp_clie_id'] = trim($result['supp_id']);

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'admin_name,admin_avatar,user_id,user_type,user_name,user_degree';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('user_id','admin_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $clerk_list = $model_clerk->getClerkList($condition, $field, $page_num, $order);

        $data = array();
        $data['now_page'] = $model_clerk->shownowpage();
        $data['total_num'] = $model_clerk->gettotalnum();
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($clerk_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=clerk&op=clerk_edit&user_id=" . $value['user_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>"."<a class='btn red' href='javascript:void(0);' onclick='fg_del(".$value['user_id'].")'><i class='fa fa fa-trash-o'></i>删除</a>";
            $param['number'] = $index;
            $param['user_id'] = $value['user_id'];
            $param['admin_name'] = "<img src=".$model_clerk->getClerkAvatar($value['admin_avatar'])." class='user-avatar' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".$model_clerk->getClerkAvatar($value['admin_avatar']).">\")'>".$value['admin_name'];
            $data['list'][$value['user_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /*
     * 增加业务员
     */
    public function clerk_addOp(){
        $model_supplier = SCMModel('supplier_account');
        $model_clerk = SCMModel('supplier_clerk');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["clerk_en_name"], "require"=>"true", "message"=>"业务员登录名不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $admin = array();
                $admin['admin_name'] = trim($_POST['clerk_en_name']);
                $admin['admin_password'] = md5(trim($_POST['clerk_passwd']));
                $admin['admin_avatar'] =  trim($_POST['avatar']);
                $user = array();
                $user['user_name'] = trim($_POST['clerk_en_name']);
                $user['user_type'] = 3;
                //获取当前供应商的详细信息
                $adminInfo = $this->getAdminInfo();
                $condition = array(
                    "admin.admin_id" => $adminInfo['id'],
                );
                $supp_result = $model_supplier->getSupplier($condition);
                $user['supp_clie_id'] = trim($supp_result['supp_id']);
                $result = $model_clerk->addClerk($admin,$user);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=clerk&op=clerk',
                            'msg'=>"返回业务员列表",
                        ),
                        array(
                            'url'=>'index.php?act=clerk&op=clerk_add',
                            'msg'=>"继续新增业务员",
                        ),
                    );
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']添加业务员['.trim($_POST['clerk_en_name']).']',1);
                    showMessage("添加业务员成功",$url);
                }else {
                    showMessage("添加业务员失败");
                }
            }
        }
        Tpl::showpage('clerk.add');
    }
    /*
     * 修改业务员详细信息
     */
    public function clerk_editOp(){
        $model_clerk = SCMModel('supplier_clerk');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["clerk_en_name"], "require"=>"true", "message"=>"业务员登录名不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            } else {
                $admin = array();
                $admin['admin_id'] = trim($_POST['admin_id']);
                $admin['admin_name'] = trim($_POST['clerk_en_name']);
                if(trim($_POST['clerk_passwd']) != '')
                    $admin['admin_password'] = md5(trim($_POST['clerk_passwd']));
                if(trim($_POST['avatar']) != '')
                    $admin['admin_avatar'] =  trim($_POST['avatar']);
                $user = array();
                $user['user_id'] = trim($_POST['user_id']);
                $user['user_name'] = trim($_POST['clerk_en_name']);

                $result = $model_clerk->editClerk($admin,$user);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=clerk&op=clerk',
                            'msg'=>"返回业务员列表",
                        )
                    );
                    $this->log('修改业务员['.trim($_POST['clerk_en_name']).']',1);
                    showMessage("修改业务员成功",$url);
                }else {
                    showMessage("修改业务员失败");
                }
            }
        }
        $condition['user_id'] = intval($_GET['user_id']);
        $clerk_array = $model_clerk->getClerkInfo($condition);
        Tpl::output('clerk_array',$clerk_array);
        Tpl::showpage('clerk.edit');
    }

    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_clerk = SCMModel('supplier_clerk');
        switch ($_GET['branch']){
            /**
             * 验证业务员名称是否重复
             */
            case 'check_clerk_name':
                $condition['admin_name']   = $_GET['clerk_en_name'];
                $condition['user_id'] = array('neq',intval($_GET['clerk_id']));
                $list = $model_clerk->getClerkInfo($condition);
                if (empty($list)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
        }
    }
    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_supplier = SCMModel('supplier_account');
        $model_clerk = SCMModel('supplier_clerk');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['user_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        //获取当前管理员
        $adminInfo = $this->getAdminInfo();
        //除当前管理员下所有的业务员
        $condition['admin.admin_id'] = array('neq', $adminInfo['id']);
        //当前供应商下的业务员
        $adminInfo = $this->getAdminInfo();
        $result = $model_supplier->getSupplier(array("admin.admin_id" => $adminInfo['id']));
        $condition['supp_clie_id'] = trim($result['supp_id']);
        $order = '';
        $field = 'admin_name,admin_avatar,user_id,user_type,user_name,user_degree';
        $sortparam = array('user_id','admin_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        if (!is_numeric($_GET['curpage'])){
            $count = $model_clerk->getClerkCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=clerk&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $clerk_list = $model_clerk->getClerkList($condition, $field, null, $order, $limit);

        $this->createCsv($clerk_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($clerk_list) {
        $model_clerk = SCMModel('supplier_clerk');
        $data = array();
        foreach ($clerk_list as $value) {
            $param = array();
            $param['user_id'] = $value['user_id'];
            $param['admin_name'] = iconv('utf-8','gb2312', $value['admin_name']);
            $data[$value['user_id']] = $param;
        }
        $header = array(
            "user_id" => iconv('utf-8','gb2312',"业务员ID"),
            "admin_name" => iconv('utf-8','gb2312', "业务员登录名"),

        );
        \Shopnc\Lib::exporter()->output('clerk_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
    /*
     * 删除业务员
     */
    public function clerk_delOp(){
        $model_clerk = SCMModel('supplier_clerk');
        if ($_GET['id'] != '') {
            $ids = explode(',', $_GET['id']);
            if($model_clerk->delClerkByIdString($ids)){
                $this->log('删除业务员[ID:'.$_GET['id'] .']',1);
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            }
            else
                exit(json_encode(array('state'=>true,'msg'=>'删除失败')));
        }
    }
}