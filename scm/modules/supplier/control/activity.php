<?php
/**
 * 供应商的资金表
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class activityControl extends SCMControl
{
    const EXPORT_SIZE = 1000;
    protected $supp_info;

    public function __construct()
    {
        parent::__construct();
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
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
        $model_activity = SCMModel('supplier_activity');
        $result = $this->supp_info;
        $condition['supp_id'] = trim($result['supp_id']);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        //高级查询
        if(isset($_GET['act_status'])){
            if($_GET['act_status'] == 1){   //未开始
                $condition[] = array('exp','start_date > NOW() OR activity_status = 3');
            }else if($_GET['act_status'] == 2){ //进行中
                $condition[] = array('exp','start_date < NOW() AND NOW() < end_date AND activity_status != 3');
            }else if($_GET['act_status'] == 3){  //已结束
                $condition[] = array('exp','end_date < NOW() AND activity_status != 3');
            }
            if($_GET['start_date'] != ""){  //开始时间小于活动开始时间
                $condition['start_date'] = array('elt',$_GET['start_date']);
            }
            if($_GET['end_date'] != ""){   //结束时间大于活动结束时间
                $condition['end_date'] = array('egt',$_GET['end_date']);
            }

        }

        $order = '';
        $field = "act_id,act_name,supp_id,act_info,act_banner,activity_status,start_date,end_date,
                CASE WHEN end_date < NOW() AND activity_status != 3 THEN '已结束'
                WHEN start_date > NOW() AND activity_status != 3  THEN '未开始'
                WHEN start_date < NOW() < end_date AND activity_status != 3  THEN '进行中'
                WHEN activity_status = 3 THEN '未开始'
                END AS act_status";
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('act_id','act_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $activity_list = $model_activity->getActivityList($condition, $field, $page_num, $order);

        $data = array();
        $data['now_page'] = $model_activity->shownowpage();
        $data['total_num'] = $model_activity->gettotalnum();
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($activity_list as $value) {
            $param = array();
            $index++;
            $o = "<a class='btn red' href='javascript:void(0);' onclick='fg_del(".$value['act_id'].")'><i class='fa fa fa-trash-o'></i>删除</a>";
//            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';
//            $o .= '<li><a href="index.php?act=activity&op=activity_sent&act_id=' .
//                $value['act_id'] .
//                '">发送</a></li>';
//            $o .= '<li><a href="index.php?act=activity&op=activity_edit&act_id=' .
//                $value['act_id'] .
//                '">编辑</a></li>';
//            $o .= '</ul></span>';
            $o .= "<a class='btn blue' href='index.php?act=activity&op=activity_edit&act_id=".$value['act_id']."'><i class='fa fa fa-trash-o'></i>编辑</a>";

            $param['operation'] =$o ;
            $param['number'] = $index;
            $param['act_id'] = $value['act_id'];
            $param['act_name'] = $value['act_name'];
//            if($value['set_type'] == 'V')
//                $param['set_type'] = "微信";
//            else if($value['set_type'] == 'D')
//                $param['set_type'] = "手机";
            $img = UPLOAD_SITE_URL."/scm/activity/".$value['act_banner'];
            $param['act_banner'] =  <<<EOB
            <a href="javascript:;" class="pic-thumb-tip" onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
            <i class='fa fa-picture-o'></i></a>
EOB;
//            $param['state'] = $value['status'] == 1
//                ? '<span class="yes"><i class="fa fa-check-circle"></i>已发送</span>'
//                : '<span class="no"><i class="fa fa-ban"></i>未发送</span>';
            if( $value['activity_status'] == 0 )
                $param['state']  = "失效";
            else if($value['activity_status'] == 1)
                $param['state'] = "审核通过";
            else if($value['activity_status'] == 2)
                $param['state'] = "未审核";
            else if($value['activity_status'] == 3)
                $param['state'] = "审核未通过";
            $param['start_date'] = $value['start_date'];
            $param['end_date'] = $value['end_date'];
            $param['act_status'] = $value['act_status'];
            $data['list'][$value['act_id']] = $param;
        }

        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 增加活动界面
     */
    public function activity_addOp(){
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
            if ($error != ''){
                showMessage($error);
            }else {
                $activity = array();
                $activity['act_name'] = trim($_POST['act_name']);
                $activity['act_info'] = trim($_POST['act_info']);
                $activity['set_type'] = trim($_POST['set_type']);
                $activity['start_date'] = trim($_POST['start_date']);
                $activity['end_date'] = trim($_POST['end_date']);
                if($_FILES['act_banner']['name']) {
                    $upload = new UploadFile();
                    $upload->set('default_dir', 'scm/activity');
                    $result = $upload->upfile('act_banner');
                    if (!$result) {
                        showMessage($upload->error);
                    }
                    $activity['act_banner'] = $upload->file_name;
                }

                $supp_result = $this->supp_info;
                $activity['supp_id'] = trim($supp_result['supp_id']);
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
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']增加活动['.trim($_POST['act_name']).']',1);
                    showMessage("添加活动成功",$url);
                }else {
                    //添加失败则删除刚刚上传的图片,节省空间资源
                    @unlink(BASE_UPLOAD_PATH.DS.'scm/activity'.DS.$upload->file_name);
                    showMessage("添加活动失败");
                }
            }
        }
//        if($_GET['clie_id'] != null) {
//            $model_client = SCMModel('supplier_client');
//            $condition['scm_client_stock.clie_id'] = trim($_GET['clie_id']);
//            //缺货商品数量
//            $condition[] = array('exp', 'goods_stock <= goods_low_stock ');
//            $temp_list = $model_client->gettotalnumon($condition,'scm_client_stock.id','scm_client_stock,scm_supplier','scm_client_stock.id','scm_client_stock.supp_id = scm_supplier.supp_id');
//            $low_number = count($temp_list);
//            //滞销商品数量
//            $sql = "select * from `gzkj`.`gzkj_scm_client_stock` where clie_id = '".trim($_GET['clie_id'])."' and goods_barcode not in
//                (select goods_barcode from `gzkj`.`gzkj_scm_client_order` as a LEFT JOIN `gzkj`.`gzkj_scm_order_goods` as b on a.id = b.order_id where a.clie_id = '".trim($_GET['clie_id'])."' and order_date  > DATE_SUB(NOW(),INTERVAL 30 DAY) and goods_barcode
//                is not null GROUP BY goods_barcode)";
//            $temp_goods_list = $model_client->execute_sql($sql);
//            $uper_number = count($temp_goods_list);
//            //近效期商品数量
//            $sql = "SELECT a.id,a.clie_id,a.goods_barcode,a.goods_nm,a.goods_unit, a.goods_spec,a.set_num,a.production_date,b.valid_remind,b.shelf_life
//                FROM `gzkj`.`gzkj_scm_instock_info` AS a LEFT JOIN `gzkj`.`gzkj_scm_client_stock` AS b ON a.goods_barcode = b.goods_barcode,
//                (
//                SELECT goods_barcode,MIN(production_date)AS riqi FROM `gzkj`.`gzkj_scm_instock_info`
//                WHERE waring_flag is NULL
//                GROUP BY goods_barcode
//                ) AS c
//                WHERE a.goods_barcode = c.goods_barcode AND a.production_date = c.riqi
//                AND a.clie_id = '".trim($_GET['clie_id'])."'";
//            $temp_goods_list = $model_client->execute_sql($sql);
//            $goods_list = array();
//            foreach($temp_goods_list as $k => $v){
//                $date =  floor((strtotime($v['production_date'] )-strtotime(date('y-m-d h:i:s',time())))/86400);
//                if (strpos($v['shelf_life'], '天') !== false) {
//                    //如果包含‘天’
//                    $date = $date+ intval($this->findNum($v['shelf_life']));
//                }else if(strpos($v['shelf_life'], '月') !== false){
//                    //如果包含‘月’
//                    $date = $date+ intval($this->findNum($v['shelf_life'])) * 30;
//                }
//                if($date <= $v['valid_remind']){
//                    $goods_list[] = $v;
//                }
//            }
//            $validity_warn_count = count($goods_list);
//            $act_name = "预警";
//            $act_info = "尊敬的店主:\r\n"."您店中有".$low_number."种商品缺货，".$validity_warn_count."种商品即将过期，".$uper_number."种商品滞销，请登入商城系统查看详细信息并及时处理";
//            Tpl::output('act_name',$act_name);
//            Tpl::output('act_info',$act_info);
//        }

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
                $activity['start_date'] = trim($_POST['start_date']);
                $activity['end_date'] = trim($_POST['end_date']);
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
                    $this->log('修改活动[ID:'.intval($_GET['act_id']).']',1);
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
        $result = $this->supp_info;
        $condition['supp_id'] = trim($result['supp_id']);
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['act_id'] = array('in', $id_array);
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = "act_id,act_name,supp_id,act_info,act_banner,activity_status,start_date,end_date";
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
        $this->createExcel($activity_list);
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
            if($value['activity_status'] == 0)
                $param['activity_status'] = iconv('utf-8','gb2312', '失效');
            else if($value['activity_status'] == 1)
                $param['activity_status'] = iconv('utf-8','gb2312', '审核通过');
            else if($value['activity_status'] == 2)
                $param['activity_status'] = iconv('utf-8','gb2312', '未审核');
            else if($value['activity_status'] == 3)
                $param['activity_status'] = iconv('utf-8','gb2312', '审核未通过');
            $param['start_date'] = $value['start_date'];
            $param['end_date'] = $value['end_date'];
            $now = strtotime(date('Y-m-d H:i:s'));

            if(strtotime($value['end_date']) < $now && $value['activity_status'] != 3)
                $param['act_status'] = iconv('utf-8','gb2312', '已结束');
            else if(strtotime($value['start_date']) > $now || $value['activity_status'] == 3)
                $param['act_status'] = iconv('utf-8','gb2312', '未开始');
            else if(strtotime($value['start_date']) < $now && $now < strtotime($value['end_date']) && $value['activity_status'] != 3)
                $param['act_status'] = iconv('utf-8','gb2312', '进行中');
            $data[$value['act_id']] = $param;
        }
        $header = array(
            "act_id" => iconv('utf-8','gb2312',"活动ID"),
            "act_name" => iconv('utf-8','gb2312', "活动名称"),
            "act_info" => iconv('utf-8','gb2312', "活动详情"),
            "activity_status" => iconv('utf-8','gb2312', "状态"),
            "start_date" => iconv('utf-8','gb2312', "开始时间"),
            "end_date" => iconv('utf-8','gb2312', "结束时间"),
            "act_status" => iconv('utf-8','gb2312', "活动状态"),
        );
        \Shopnc\Lib::exporter()->output('activity_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 生成Excel文件
     * @param $activity_list
     */
    private function createExcel($activity_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'活动ID');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'活动名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'活动详情');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'开始时间');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'结束时间');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'活动状态');

        //data
        foreach ((array)$activity_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['act_id']);
            $tmp[] = array('data'=>$v['act_name']);
            $tmp[] = array('data'=>$v['act_info']);
            $tmp[] = array('data'=>order::getActivityStatusByID($v['activity_status']));
            $tmp[] = array('data'=>$v['start_date']);
            $tmp[] = array('data'=>$v['end_date']);
            $now = strtotime(date('Y-m-d H:i:s'));
            if(strtotime($v['end_date']) < $now && $v['activity_status'] != 3)
                $tmp[] = array('data'=>'已结束');
            else if(strtotime($v['start_date']) > $now || $v['activity_status'] == 3)
                $tmp[] = array('data'=>'未开始');
            else if(strtotime($v['start_date']) < $now && $now < strtotime($v['end_date']) && $v['activity_status'] != 3)
                $tmp[] = array('data'=>'进行中');
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /*
    * 删除活动
    */
    public function activity_delOp(){
        $model_activity = SCMModel('supplier_activity');
        if ($_GET['id'] != '') {
            $ids = explode(',', $_GET['id']);
            if($model_activity->delActivityByIdString($ids)){
                $this->log('删除活动[ID:'.intval($_GET['id']).']',1);
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            }
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