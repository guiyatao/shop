<?php
/**
 * 供应商的资金表
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class capitalControl extends SCMControl
{
    const EXPORT_SIZE = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        return $this->capitalOp();
    }

    /**
     * 显示当前供应商的所有资金列表
     */
    public function capitalOp()
    {
        Tpl::showpage('capital.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_capita = SCMModel('supplier_capital');
        $model_supplier = SCMModel('supplier_account');
        //当前供应商
        $adminInfo = $this->getAdminInfo();
        $result = $model_supplier->getSupplier(array("admin.admin_id" => $adminInfo['id']));
        $condition['supp_id'] = trim($result['supp_id']);

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'capital_id,supp_id,supp_bank,supp_cardno,supp_capital';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('capital_id','supp_bank');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $capital_list = $model_capita->getCapitalList($condition, $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_capita->shownowpage();
        $data['total_num'] = $model_capita->gettotalnum();
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($capital_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=capital&op=capital_edit&capital_id=" . $value['capital_id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>"."<a class='btn red' href='javascript:void(0);' onclick='fg_del(".$value['capital_id'].")'><i class='fa fa fa-trash-o'></i>删除</a>";
            $param['number'] = $index;
            $param['capital_id'] = $value['capital_id'];
            $param['supp_bank'] = $value['supp_bank'];
            $param['supp_cardno'] = $value['supp_cardno'];
            $param['supp_capital'] = $value['supp_capital'];
            $data['list'][$value['capital_id']] = $param;
        }

        echo Tpl::flexigridXML($data);exit();

    }

    /**
     * 增加资金表界面
     */
    public function capital_addOp(){
        $model_capital = SCMModel('supplier_capital');
        $model_supplier = SCMModel('supplier_account');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["supp_bank"], "require"=>"true", "message"=>"开户行的名称不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $capital = array();
                $capital['supp_bank'] = trim($_POST['supp_bank']);
                $capital['supp_cardno'] = trim($_POST['supp_cardno']);
                $capital['supp_capital'] = trim($_POST['supp_capital']);
                //获取当前供应商的详细信息
                $adminInfo = $this->getAdminInfo();
                $condition = array(
                    "admin.admin_id" => $adminInfo['id'],
                );
                $supp_result = $model_supplier->getSupplier($condition);
                $capital['supp_id'] = trim($supp_result['supp_id']);
                $result = $model_capital->addCapital($capital);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=capital&op=capital',
                            'msg'=>"返回资金列表",
                        ),
                        array(
                            'url'=>'index.php?act=capital&op=capital_add',
                            'msg'=>"继续新增资金",
                        ),
                    );
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']添加银行卡['.$capital['supp_cardno'].']',1);
                    showMessage("添加资金表成功",$url);
                }else {
                    showMessage("添加资金表失败");
                }
            }
        }
        Tpl::showpage('capital.add');
    }

    public function capital_editOp(){
        $model_capital = SCMModel('supplier_capital');
        $model_supplier = SCMModel('supplier_account');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["supp_bank"], "require"=>"true", "message"=>"开户行的名称不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $capital = array();
                $capital['capital_id'] = $_POST['capital_id'];
                $capital['supp_bank'] = trim($_POST['supp_bank']);
                $capital['supp_cardno'] = trim($_POST['supp_cardno']);
                $capital['supp_capital'] = trim($_POST['supp_capital']);
                //获取当前供应商的详细信息
                $adminInfo = $this->getAdminInfo();
                $condition = array(
                    "admin.admin_id" => $adminInfo['id'],
                );
                $supp_result = $model_supplier->getSupplier($condition);
                $capital['supp_id'] = trim($supp_result['supp_id']);
                $result = $model_capital->updateCapital($capital);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=capital&op=capital',
                            'msg'=>"返回资金列表",
                        )
                    );
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']修改银行卡['.$capital['supp_cardno'].']',1);
                    showMessage("修改资金表成功",$url);
                }else {
                    showMessage("修改资金表失败");
                }
            }
        }
        $condition['capital_id'] = intval($_GET['capital_id']);
        $capital = $model_capital->getCapitalInfo($condition);
        Tpl::output('capital',$capital);
        Tpl::showpage('capital.edit');
    }

    /**
     * 删除资金信息
     */
    public function capital_delOp(){
        $model_capital = SCMModel('supplier_capital');
        if ($_GET['id'] != '') {
            $ids = explode(',', $_GET['id']);
            if($model_capital->delCapitalByIdString($ids)){
                $this->log('删除银行卡[ID:'.$_GET['id'] .']',1);
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            }
            else
                exit(json_encode(array('state'=>true,'msg'=>'删除失败')));
        }
    }

    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_capital = SCMModel('supplier_capital');
        switch ($_GET['branch']){
            /**
             * 验证银行卡账号是否重复
             */
            case 'check_card_no':
                $condition['supp_cardno'] = $_GET['supp_cardno'];
                $condition['capital_id'] = array('neq',intval($_GET['capital_id']));
                $list = $model_capital->getCapitalInfo($condition);
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
        $model_capital = SCMModel('supplier_capital');
        $model_supplier = SCMModel('supplier_account');

        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['capital_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        //当前供应商
        $adminInfo = $this->getAdminInfo();
        $result = $model_supplier->getSupplier(array("admin.admin_id" => $adminInfo['id']));
        $condition['supp_id'] = trim($result['supp_id']);
        $order = '';
        $field = 'capital_id,supp_id,supp_bank,supp_cardno,supp_capital';
        $sortparam = array('capital_id','supp_bank');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        if (!is_numeric($_GET['curpage'])){
            $count = $model_capital->getCapitalCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=capital&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $capital_list = $model_capital->getCapitalList($condition, $field, null, $order, $limit);

        $this->createCsv($capital_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($capital_list) {

        $data = array();
        foreach ($capital_list as $value) {
            $param = array();
            $param['capital_id'] = $value['capital_id'];
            $param['supp_bank'] = iconv('utf-8','gb2312', $value['supp_bank']);
            $param['supp_cardno'] = $value['supp_cardno'];
            $param['supp_capital'] = $value['supp_capital'];
            $data[$value['capital_id']] = $param;
        }
        $header = array(
            "capital_id" => iconv('utf-8','gb2312',"资金表ID"),
            "supp_bank" => iconv('utf-8','gb2312', "开户行"),
            "supp_cardno" => iconv('utf-8','gb2312', "卡号"),
            "supp_capital" => iconv('utf-8','gb2312', "资金(元)"),
        );
        \Shopnc\Lib::exporter()->output('capital_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
}