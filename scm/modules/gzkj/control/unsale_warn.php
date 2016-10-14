<?php
/**
 * 滞销预警
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class unsale_warnControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        return $this->unsale_warnOp();
    }

    /**
     * 显示当前供应商的所有资金列表
     */
    public function unsale_warnOp()
    {
        Tpl::showpage('unsale_warn.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp()
    {
        if (strlen($q = trim($_REQUEST['query'])) > 0) {
            switch ($_REQUEST['qtype']) {
                case 'clie_id':
                    $clie_id = $q;
                    break;
            }
        }
        $page = new Page();
        $page->setEachNum($_POST['rp']) ;
        $page->setStyle('admin') ;
        $data = array();
        $page_num = $_POST['rp'];
        $data['now_page'] = $page->get('now_page');
        $index = ($data['now_page'] - 1) * $page_num;
        $good_list = $this->unsale_warn_sql($clie_id,array('index'=> $index, 'page_num'=> $page_num ));
        $data['total_num'] = count($this->unsale_warn_sql());
        foreach ($good_list as $stock_id => $goods_info) {
            $list = array();
            $index++;
            $list['number']=$index;
            $list['clie_id'] = $goods_info['clie_id'];
            $list['clie_ch_name'] = $goods_info['clie_ch_name'];
            $list['clie_contacter'] = $goods_info['clie_contacter'];
            $list['clie_mobile'] = $goods_info['clie_mobile'];
            $list['clie_tel'] = $goods_info['clie_tel'];
            $list['goods_barcode'] = $goods_info['goods_barcode'];
            $list['goods_nm'] = $goods_info['goods_nm'];
            $list['goods_unit'] = $goods_info['goods_unit'];
            $list['goods_spec'] = $goods_info['goods_spec'];
            $list['goods_stock'] = $goods_info['goods_stock'];
            $list['goods_uper_stock'] = $goods_info['goods_uper_stock'];
            $list['supp_ch_name'] = $goods_info['supp_ch_name'];
            $list['supp_contacter'] = $goods_info['supp_contacter'];
            $list['supp_tel'] = $goods_info['supp_tel'];
            $list['supp_mobile'] = $goods_info['supp_mobile'];
            $list['last_time'] = $goods_info['last_time'];
            $data['list'][$goods_info['id']] = $list;
        }

        echo Tpl::flexigridXML($data);exit();
    }



    public function dealOp()
    {

        //创建活动内容对象
        $activity    = SCMModel('gzkj_activity');
        $data=array();
        $data['act_id']=$_GET['act_id'];
        $data['activity_status']=$_GET['state'];

        if($activity->updateActivity($data)){
            Tpl::showpage('activity.index');

        } else {
            $this->jsonOutput('操作失败');
        }
    }


    /**
     * 提取字符串中所有的数字
     * @param string $str
     * @return string
     */
    private function findNum($str=''){
        $str=trim($str);
        if(empty($str)){return '';}
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(is_numeric($str[$i])){
                $result.=$str[$i];
            }
        }
        return $result;
    }

    /*
     * 修改活动界面
     */
    public function activity_editOp(){
        $model_activity = SCMModel('supplier_activity');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["act_name"], "require"=>"true", "message"=>"活动名称不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            } else {
                $condition['act_id'] = intval($_GET['act_id']);
                $result = $model_activity->getActivityInfo($condition);
                $temp_img = $result['act_banner'];

                $activity = array();
                $activity['act_id'] = $_POST['act_id'];
                $activity['act_name'] = trim($_POST['act_name']);
                $activity['act_info'] = trim($_POST['act_info']);
                $activity['set_type'] = trim($_POST['set_type']);
                if($_FILES['act_banner']['name']) {
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/activity'.DS.$temp_img);
                    $upload = new UploadFile();
                    $upload->set('default_dir', 'scm/activity');
                    $result = $upload->upfile('act_banner');
                    if (!$result) {
                        showMessage($upload->error);
                    }
                    $activity['act_banner'] = $upload->file_name;
                }
//                $activity['status'] = trim($_POST['status']);
                $result = $model_activity->updateActivity($activity);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=activity&op=activity',
                            'msg'=>"返回活动列表",
                        )
                    );
                    //$this->log(L('nc_add,member_index_name').'[ '.$_POST['member_name'].']',1);
                    showMessage("修改活动成功",$url);
                }else {
                    //添加失败则删除刚刚上传的图片,节省空间资源
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/activity'.DS.$upload->file_name);
                    showMessage("修改活动失败");
                }
            }
        }
        $condition['act_id'] = intval($_GET['act_id']);
        $activity_array = $model_activity->getActivityInfo($condition);
        Tpl::output('activity_array',$activity_array);
        Tpl::showpage('activity.edit');
    }

    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_activity = SCMModel('supplier_activity');
        $model_supplier = SCMModel('supplier_account');
        //当前供应商
        $adminInfo = $this->getAdminInfo();
        $result = $model_supplier->getSupplier(array("admin.admin_id" => $adminInfo['id']));
        $condition['supp_id'] = trim($result['supp_id']);

        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'act_id,act_name,supp_id,act_info,set_type,act_banner,status';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('act_id','act_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        if (!is_numeric($_GET['curpage'])){
            $count = $model_activity->getActivityCount($condition);
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
        $activity_list = $model_activity->getActivityList($condition, $field,  null, $order, $limit);

        $this->createCsv($activity_list);
    }

    /**
     * 发送活动
     */
    public function activity_sentOp(){
        $model_activity = SCMModel('supplier_activity');
        $flag = true;
        $user_list = array('oO0LrwbzPYfDddOx23_aW6PSTQ1s','oO0LrwS6f2V5Dw6WfMphuthUxW-M');
        foreach($user_list as $k => $v){
            if($this->activity_sent($_GET['act_id'],$v) == false){
                $flag = false;
            }
        }

        if($flag){
            $model_activity->updateActivity(array('act_id' => $_GET['act_id'], 'status' => 1));
            $url = array(
                array(
                    'url'=>'index.php?act=activity&op=activity',
                    'msg'=>"返回活动列表",
                )
            );
            showMessage("发送活动成功",$url);
        }

    }


    private  function unsale_warn_sql($clie_id=null,$condition = array()){
        $pre=C('tablepre');

        if($clie_id){
            $sql="SELECT
	d.id,
	d.clie_id,
	b.clie_ch_name,
	b.clie_contacter,
	b.clie_mobile,
	b.clie_tel,
	d.goods_barcode,
	d.goods_nm,
	d.goods_unit,
	d.goods_spec,
	d.goods_stock,
	d.goods_uper_stock,
	c.supp_ch_name,
	c.supp_contacter,
	c.supp_tel,
	c.supp_mobile,
	max(i.in_stock_date) AS last_time
FROM
	".$pre."scm_client_stock AS d
JOIN ".$pre."scm_client AS b ON b.clie_id = d.clie_id
JOIN ".$pre."scm_supplier AS c ON c.supp_id = d.supp_id
LEFT JOIN ".$pre."scm_instock_info AS i ON d.clie_id = i.clie_id AND d.supp_id = i.supp_id AND d.goods_barcode = i.goods_barcode
where
d.clie_id='".$clie_id."'
AND
d.goods_barcode NOT IN (
	SELECT
		goods_barcode
	FROM
		".$pre."scm_instock_info
	WHERE
		in_stock_date > DATE_SUB(NOW(), INTERVAL d.drug_remind DAY)
)";
        }else{
            $sql="SELECT
	d.id,
	d.clie_id,
	b.clie_ch_name,
	b.clie_contacter,
	b.clie_mobile,
	b.clie_tel,
	d.goods_barcode,
	d.goods_nm,
	d.goods_unit,
	d.goods_spec,
	d.goods_stock,
	d.goods_uper_stock,
	c.supp_ch_name,
	c.supp_contacter,
	c.supp_tel,
	c.supp_mobile,
	max(i.in_stock_date) AS last_time
FROM
	".$pre."scm_client_stock AS d
JOIN ".$pre."scm_client AS b ON b.clie_id = d.clie_id
JOIN ".$pre."scm_supplier AS c ON c.supp_id = d.supp_id
LEFT JOIN ".$pre."scm_instock_info AS i ON d.clie_id = i.clie_id
AND d.supp_id = i.supp_id
AND d.goods_barcode = i.goods_barcode
where d.goods_barcode NOT IN (
	SELECT
		goods_barcode
	FROM
		".$pre."scm_instock_info
	WHERE
		in_stock_date > DATE_SUB(NOW(), INTERVAL d.drug_remind DAY)
)
 ";

        }

$sql.="GROUP BY d.goods_barcode ";
        if(isset($condition['ids'])  && $condition['ids'] != ''){
            $sql.= "AND d.id in (".$condition['ids'].") ";
        }
        if(isset($condition['index']) ){
            $sql.= " limit ".$condition['index'].",".$condition['page_num'];
        }
        $order_goods = SCMModel('gzkj_order_goods');
        $good_list = $order_goods->execute_sql($sql);
        return $good_list;
    }

    public function export_unsale_warnOp()
    {

        $good_list = $this->unsale_warn_sql('',array('ids' => $_GET['id']));
        $this->createExcel($good_list);
    }

    private function createExcel($good_list)
    {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店编号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店联系人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '终端店电话');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品条码');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '单位');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '规格');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '库存');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '库存上限');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商联系人');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商电话');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '供应商手机');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '最后一次进货时间');

        //data
        foreach ((array)$good_list as $k => $goods_info) {
            $tmp = array();
            $tmp[] = array('data' => $goods_info['clie_id']);
            $tmp[] = array('data' => $goods_info['clie_ch_name']);
            $tmp[] = array('data' => $goods_info['clie_contacter']);
            $tmp[] = array('data' => $goods_info['clie_mobile']);
            $tmp[] = array('data' => $goods_info['clie_tel']);
            $tmp[] = array('data' => $goods_info['goods_barcode']);
            $tmp[] = array('data' => $goods_info['goods_nm']);
            $tmp[] = array('data' => $goods_info['goods_unit']);
            $tmp[] = array('data' => $goods_info['goods_spec']);
            $tmp[] = array('data' => $goods_info['goods_stock']);
            $tmp[] = array('data' => $goods_info['goods_uper_stock']);
            $tmp[] = array('data' => $goods_info['supp_ch_name']);
            $tmp[] = array('data' => $goods_info['supp_contacter']);
            $tmp[] = array('data' => $goods_info['supp_tel']);
            $tmp[] = array('data' => $goods_info['supp_mobile']);
            $tmp[] = array('data' => $goods_info['last_time']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'), CHARSET));
        $excel_obj->generateXML('unsale_warn-' . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}