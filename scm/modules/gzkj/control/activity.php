<?php
/**
 * 活动管理
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class activityControl extends SCMControl
{
    const EXPORT_SIZE = 1000;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        return $this->activityOp();
    }

    /**
     * 显示当前供应商的所有资金列表
     */
    public function activityOp()
    {
        Tpl::showpage('activity.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp()
    {
        $model_activity = SCMModel('gzkj_activity');
        if ($_REQUEST['advanced']) {
            if (strlen($q = trim((string) $_REQUEST['activity_status']))) {
                $condition['activity_status'] = (int) $q;
            }
        } else{
            if ($_POST['query'] != '') {
                if($_POST['qtype']=='supp_id'){
                    $_POST['qtype']='scm_supp_stock.supp_id';
                }
                $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
            }
        }


        $order = '';
        $field = 'act_id,act_name,scm_activity.supp_id,scm_supplier.supp_ch_name,act_info,act_banner,activity_status,start_date,end_date';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('act_id','act_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $activity_list = $model_activity->getActivityAndSupp($condition, $field, $page_num, $order);

        $data = array();
        $data['now_page'] = $model_activity->shownowpage();
        $data['total_num'] = $model_activity->gettotalnum();
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($activity_list as $value) {
            $param = array();
            $index++;
            $model = SCMModel('gzkj_supplier');
            $is_close=$model->getfby_supp_id($value['supp_id'],'is_close');
            if($is_close){
                if($value['activity_status']==0||$value['activity_status']==2||$value['activity_status']==3){
                    $o = '<span class="no"><em><i class="fa fa-ban"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '</ul></span>';
                }
            }else{
                if($value['activity_status']==2){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=activity&op=deal&state=1&act_id=' .
                        $value['act_id'] .
                        '">通过</a></li>';
                    $o .= '<li><a href="index.php?act=activity&op=deal&state=3&act_id=' .
                        $value['act_id'] .
                        '">拒绝</a></li>';
                    $o .= '</ul></span>';
                }
                if($value['activity_status']==1){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=activity&op=deal&state=3&act_id=' .
                        $value['act_id'] .
                        '">拒绝</a></li>';
                    $o .= '</ul></span>';
                }
                if($value['activity_status']==3){
                    $o = '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '<li><a href="index.php?act=activity&op=deal&state=1&act_id=' .
                        $value['act_id'] .
                        '">通过</a></li>';
                    $o .= '</ul></span>';
                }
                if($value['activity_status']==0){
                    $o = '<span class="no"><em style="width: 66px;height: 24px "><i class="fa fa-ban"></i>设置<i class="arrow"></i></em><ul>';
                    $o .= '</ul></span>';
                }

            }



            $param['operation'] =$o ;
            if($value['activity_status']==2){
                $param['activity_status']='未审核';
            }elseif($value['activity_status']==1){
                $param['activity_status']='通过';
            }elseif($value['activity_status']==3){
                $param['activity_status']='拒绝';
            }elseif($value['activity_status']==0){
                $param['activity_status']='失效';
            }
            $param['number'] = $index;
            $param['act_id'] = $value['act_id'];
            $param['supp_id'] = $value['supp_id'];
            $param['supp_ch_name'] = $value['supp_ch_name'];

            $param['act_name'] = $value['act_name'];
            $img = UPLOAD_SITE_URL."/scm/activity/".$value['act_banner'];
            $param['act_banner'] =  <<<EOB
            <a href="javascript:;" class="pic-thumb-tip" onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
            <i class='fa fa-picture-o'></i></a>
EOB;

            $param['start_date']=$value['start_date'];
            $param['end_date']=$value['end_date'];
            $data['list'][$value['act_id']] = $param;
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
     * 增加活动界面
     */
    public function activity_addOp(){
        $model_activity = SCMModel('supplier_activity');
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
                array("input"=>$_POST["act_name"], "require"=>"true", "message"=>"活动名称不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $activity = array();
                $activity['act_name'] = trim($_POST['act_name']);
                $activity['act_info'] = trim($_POST['act_info']);
                $activity['set_type'] = trim($_POST['set_type']);
                if($_FILES['act_banner']['name']) {
                    $upload = new UploadFile();
                    $upload->set('default_dir', 'scm/activity');
                    $result = $upload->upfile('act_banner');
                    if (!$result) {
                        showMessage($upload->error);
                    }
                    $activity['act_banner'] = $upload->file_name;
                }
//                $activity['status'] = trim($_POST['status']);
                //获取当前供应商的详细信息
                $adminInfo = $this->getAdminInfo();
                $condition = array(
                    "admin.admin_id" => $adminInfo['id'],
                );
                $result = $model_supplier->getSupplier($condition);
                $activity['supp_id'] = trim($result['supp_id']);
                $result = $model_activity->addActivity($activity);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=activity&op=activity',
                            'msg'=>"返回活动列表",
                        ),
                        array(
                            'url'=>'index.php?act=activity&op=activity_add',
                            'msg'=>"继续新增活动",
                        ),
                    );
                    //$this->log(L('nc_add,member_index_name').'[ '.$_POST['member_name'].']',1);
                    showMessage("添加活动成功",$url);
                }else {
                    //添加失败则删除刚刚上传的图片,节省空间资源
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/activity'.DS.$upload->file_name);
                    showMessage("添加活动失败");
                }
            }
        }
        if($_GET['clie_id'] != null) {
            $model_client = SCMModel('supplier_client');
            $condition['scm_client_stock.clie_id'] = trim($_GET['clie_id']);
            //缺货商品数量
            $condition[] = array('exp', 'goods_stock <= goods_low_stock ');
            $temp_list = $model_client->gettotalnumon($condition,'scm_client_stock.id','scm_client_stock,scm_supplier','scm_client_stock.id','scm_client_stock.supp_id = scm_supplier.supp_id');
            $low_number = count($temp_list);
            //滞销商品数量
            $sql = "select * from `gzkj`.`gzkj_scm_client_stock` where clie_id = '".trim($_GET['clie_id'])."' and goods_barcode not in
                (select goods_barcode from `gzkj`.`gzkj_scm_client_order` as a LEFT JOIN `gzkj`.`gzkj_scm_order_goods` as b on a.id = b.order_id where a.clie_id = '".trim($_GET['clie_id'])."' and order_date  > DATE_SUB(NOW(),INTERVAL 30 DAY) and goods_barcode
                is not null GROUP BY goods_barcode)";
            $temp_goods_list = $model_client->execute_sql($sql);
            $uper_number = count($temp_goods_list);
            //近效期商品数量
            $sql = "SELECT a.id,a.clie_id,a.goods_barcode,a.goods_nm,a.goods_unit, a.goods_spec,a.set_num,a.production_date,b.valid_remind,b.shelf_life
                FROM `gzkj`.`gzkj_scm_instock_info` AS a LEFT JOIN `gzkj`.`gzkj_scm_client_stock` AS b ON a.goods_barcode = b.goods_barcode,
                (
                SELECT goods_barcode,MIN(production_date)AS riqi FROM `gzkj`.`gzkj_scm_instock_info`
                WHERE waring_flag is NULL
                GROUP BY goods_barcode
                ) AS c
                WHERE a.goods_barcode = c.goods_barcode AND a.production_date = c.riqi
                AND a.clie_id = '".trim($_GET['clie_id'])."'";
            $temp_goods_list = $model_client->execute_sql($sql);
            $goods_list = array();
            foreach($temp_goods_list as $k => $v){
                $date =  floor((strtotime($v['production_date'] )-strtotime(date('y-m-d h:i:s',time())))/86400);
                if (strpos($v['shelf_life'], '天') !== false) {
                    //如果包含‘天’
                    $date = $date+ intval($this->findNum($v['shelf_life']));
                }else if(strpos($v['shelf_life'], '月') !== false){
                    //如果包含‘月’
                    $date = $date+ intval($this->findNum($v['shelf_life'])) * 30;
                }
                if($date <= $v['valid_remind']){
                    $goods_list[] = $v;
                }
            }
            $validity_warn_count = count($goods_list);
            $act_name = "预警";
            $act_info = "尊敬的店主:\r\n"."您店中有".$low_number."种商品缺货，".$validity_warn_count."种商品即将过期，".$uper_number."种商品滞销，请登入商城系统查看详细信息并及时处理";
            Tpl::output('act_name',$act_name);
            Tpl::output('act_info',$act_info);
        }

        Tpl::showpage('activity.add');
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
     * 生成csv文件
     */
    private function createCsv($activity_list) {
        $data = array();
        foreach ($activity_list as $value) {
            $param = array();
            $param['act_id'] = $value['act_id'];
            $param['act_name'] = iconv('utf-8','gb2312', $value['act_name']);
            $param['act_info'] = iconv('utf-8','gb2312', $value['act_info']);
            if($value['set_type'] == 'V')
                $param['act_info'] = iconv('utf-8','gb2312', '微信');
            else if($value['set_type'] == 'D')
                $param['act_info'] = iconv('utf-8','gb2312', '短信');
            if($value['status'] == 0)
                $param['status'] = iconv('utf-8','gb2312', '未发送');
            else if($value['status'] == 1)
                $param['status'] = iconv('utf-8','gb2312', '已发送');
            $data[$value['act_id']] = $param;
        }
        $header = array(
            "act_id" => iconv('utf-8','gb2312',"活动ID"),
            "act_name" => iconv('utf-8','gb2312', "活动名称"),
            "act_info" => iconv('utf-8','gb2312', "活动详情"),
            "status" => iconv('utf-8','gb2312', "发送状态"),
        );
        \Shopnc\Lib::exporter()->output('activity_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /*
    * 删除活动
    */
    public function activity_delOp(){
        $model_activity = SCMModel('supplier_activity');
        if ($_GET['id'] != '') {
            $ids = explode(',', $_GET['id']);
            if($model_activity->delActivityByIdString($ids))
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            else
                exit(json_encode(array('state'=>true,'msg'=>'删除失败')));
        }
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

    private function activity_sent($act_id,$touser_id) {
        $appid = "wxf09a0ccc7ca85114";
        $appsecret = "af580f6f85636e556c9b48331b4982d6";
        $access_token = $this->_get_wechat_access_token($appid, $appsecret);
        $alert_time = date("Y-m-d H:i:s",time());

        $model_activity = SCMModel('supplier_activity');
        $activity_array = $model_activity->getActivityInfo(array('act_id' => $act_id));
        //$touser_id从user表中获取openid
        if ($access_token && $activity_array != null ) {
            // foreach ($stockout_list as $value) {
            if($activity_array['act_name'] == "预警"){
                $info = array(
                    'touser' => $touser_id,
                    "template_id" => "n-sEN36y9Ejl5ipzo984j0Zo5TngLkToW3rbyqfRMQQ",
                    "data" => array(
                        "info" => array(
                            "value" => $activity_array['act_info'],
                            "color" => "#173177"
                        ),
                        "time" => array(
                            "value" => $alert_time,
                            "color" => "#173177"
                        ),
                    )
                );
            }else {
                $info = array(
                    'touser' => $touser_id,
                    "template_id" => "vaGjjxbUaJw24XhkTubdLz-yRPu_EaOyZqwI7-jQ_VY",
                    "data" => array(
                        "first" => array(
                            "value" => "尊敬的店主",
                            "color" => "#173177"
                        ),
                        "title" => array(
                            "value" => $activity_array['act_name'],
                            "color" => "#173177"
                        ),
                        "content" => array(
                            "value" => $activity_array['act_info'],
                            "color" => "#173177"
                        ),
                        "time" => array(
                            "value" => $alert_time,
                            "color" => "#173177"
                        ),
                        "remark" => array(
                            "value" => '欢迎订购。',
                            "color" => "#173177"
                        )
                    )
                );
            }
            $jsdata=json_encode($info);
            //print_r($jsdata);die;
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
            $data = http_postdata($url, $jsdata);

            $temp = json_decode($data);
            if ($temp->errcode == 0){
                return true;
            }else {
                print_r($data);die;
               // showMessage("发送活动失败".$data);
            }

        } else {
            print_r('error get access_token');die;
        }

    }

    /**
     * 获取微信access_token
     */
    private function _get_wechat_access_token($appid, $appsecret) {
        // 尝试读取缓存的access_token
        $access_token = rkcache('wechat_access_token');
        if($access_token) {
            $access_token = unserialize($access_token);
            // 如果access_token未过期直接返回缓存的access_token
            if($access_token['time'] > TIMESTAMP) {
                return $access_token['token'];
            }
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $url = sprintf($url, $appid, $appsecret);
        $re = http_get($url);
        $result = json_decode($re, true);
        if($result['errcode']) {
            return '';
        }

        // 缓存获取的access_token
        $access_token = array();
        $access_token['token'] = $result['access_token'];
        $access_token['time'] = TIMESTAMP + $result['expires_in'];
        wkcache('wechat_access_token', serialize($access_token));

        return $result['access_token'];
    }

}