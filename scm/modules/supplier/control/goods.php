<?php
/**
 * 供应商商品管理
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class goodsControl extends SCMControl
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
        $this->goodsOp();
    }

    /**
     * 供应商商品展示列表
     */
    public function goodsOp(){
        Tpl::showpage('goods.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_goods = SCMModel('supplier_goods');
        $supplier = $this->supp_info;
        //当前供应商的所有商品分类
        $condition['supp_id'] = $supplier['supp_id'];
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'id,goods_nm,goods_barcode,goods_price,goods_discount,goods_rate,goods_unit,min_set_num,unit_num,goods_spec,produce_company,produce_area,production_date,valid_remind,shelf_life,status';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('id','goods_nm','goods_price');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $goods_list = $model_goods->getGoodsList($condition, $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_goods->shownowpage();
        $data['total_num'] = $model_goods->gettotalnum();
        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($goods_list as $value) {
            $param = array();
            $index++;
            $param['operation'] = "<a class='btn blue' href='index.php?act=goods&op=goods_edit&goods_id=" . $value['id'] . "'><i class='fa fa-pencil-square-o'></i>编辑</a>"."<a class='btn red' href='javascript:void(0);' onclick='fg_del(".$value['id'].")'><i class='fa fa fa-trash-o'></i>删除</a>";
            $param['number'] = $index;
            $param['goods_nm'] = $value['goods_nm'];
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_price'] = $value['goods_price'];
            $param['goods_discount'] = $value['goods_discount'];
            $param['goods_unit'] = $value['goods_unit'];
            $param['min_set_num'] = $value['min_set_num'];
            $param['unit_num'] = $value['unit_num'];
            $param['goods_spec'] = $value['goods_spec'];
            $param['production_date'] = $value['production_date'];
            $param['valid_remind'] = $value['valid_remind'];
            $param['shelf_life'] = $value['shelf_life'];
            if($value['status'] == 3)
                $param['status'] = "审核未通过";
            else if($value['status'] == 2)
                $param['status'] = "未审核";
            else if($value['status'] == 1)
                $param['status'] = "已审核";
            else if($value['status'] == 0)
                $param['status'] = "失效";
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }


    /**
     * 供应商增加商品
     */
    public function goods_addOp(){
        $model_goods = SCMModel('supplier_goods');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["goods_name"], "require"=>"true", "message"=>"商品名不能为空"),

            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $goods = array();
                $goods['goods_nm'] = trim($_POST['goods_name']);
                $goods['goods_barcode'] = trim($_POST['goods_barcode']);
                $goods['goods_price'] = trim($_POST['goods_price']);
                if(trim($_POST['goods_discount']) == '' ){
                    $goods['goods_discount'] = 1;
                }else{
                    $goods['goods_discount'] = trim($_POST['goods_discount']);
                }

                $goods['goods_rate'] = trim($_POST['goods_tax_rate']);
                $supp_result = $this->supp_info;
                $goods['supp_id'] = trim($supp_result['supp_id']);
                $goods['goods_unit'] = $_POST['stock_unit'];
                $goods['min_set_num'] = trim($_POST['min_supp_num']);
                $goods['unit_num'] = trim($_POST['unit_num']);
                $goods['goods_spec'] = trim($_POST['specification']);
                $goods['produce_company'] = $_POST['manufacturer'];
                $goods['produce_area'] = $_POST['origin'];
                $goods['production_date'] = $_POST['production_date'];
                $goods['valid_remind'] = $_POST['valid_remind'];
                $goods['shelf_life'] = trim($_POST['shelf_life']).$_POST['shelf_life_unit'] ;

                $result = $model_goods->addGoods($goods);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=goods&op=goods',
                            'msg'=>"返回商品列表",
                        ),
                        array(
                            'url'=>'index.php?act=goods&op=goods_add',
                            'msg'=>"继续新增商品",
                        ),
                    );
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']添加商品['.trim($_POST['goods_name']).']',1);
                    showMessage("添加商品成功",$url);
                }else {
                    showMessage("添加商品失败");
                }
            }
        }

        Tpl::showpage('goods.add');

    }

    /**
     * 供应商修改商品
     */
    public function goods_editOp(){
        $model_goods = SCMModel('supplier_goods');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["goods_name"], "require"=>"true", "message"=>"商品名不能为空"),

            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $goods = array();
                $goods['id'] = trim($_POST['goods_id']);
                $goods['goods_nm'] = trim($_POST['goods_name']);
                $goods['goods_barcode'] = trim($_POST['goods_barcode']);
                $goods['goods_price'] = trim($_POST['goods_price']);
                if(trim($_POST['goods_discount']) == '' ){
                    $goods['goods_discount'] = 1;
                }else{
                    $goods['goods_discount'] = trim($_POST['goods_discount']);
                }
                $goods['goods_rate'] = trim($_POST['goods_tax_rate']);
                $supp_result = $this->supp_info;
                $goods['supp_id'] = trim($supp_result['supp_id']);
                $goods['goods_unit'] = $_POST['stock_unit'];
                $goods['min_set_num'] = trim($_POST['min_supp_num']);
                $goods['unit_num'] = trim($_POST['unit_num']);
                $goods['goods_spec'] = trim($_POST['specification']);
                $goods['produce_company'] = $_POST['manufacturer'];
                $goods['produce_area'] = $_POST['origin'];
                $goods['production_date'] = $_POST['production_date'];
                $goods['valid_remind'] = $_POST['valid_remind'];
                $goods['shelf_life'] = trim($_POST['shelf_life']).$_POST['shelf_life_unit'] ;
                $result = $model_goods->updateGoods($goods);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=goods&op=goods',
                            'msg'=>"返回商品列表",
                        ),
                        array(
                            'url'=>'index.php?act=goods&op=goods_edit&goods_id='.$_POST['goods_id'],
                            'msg'=>"继续修改该商品",
                        ),
                    );
                    $this->log('为供应商[ '.trim($supp_result['supp_ch_name']).']修改商品['.trim($_POST['goods_name']).']',1);
                    showMessage("修改商品成功",$url);
                }else {
                    showMessage("修改商品失败");
                }
            }
        }

        $goods = $model_goods->getGoodsInfo(array('id'=>$_GET['goods_id']));
        Tpl::output('goods',$goods);
        $number = $this->findNum($goods['shelf_life']);
        Tpl::output('shelf_life',$number);
        $shelf_life_unit = str_replace($number,'',$goods['shelf_life']);
        Tpl::output('shelf_life_unit',$shelf_life_unit);
        Tpl::showpage('goods.edit');
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

    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_goods = SCMModel('supplier_goods');
        switch ($_GET['branch']){
            /**
             * 验证商品名称是否重复
             *
             */
            case 'check_goods_name':
                $condition['goods_nm']   = $_GET['goods_name'];
                $condition['id'] = array('neq',intval($_GET['goods_id']));
                $condition['supp_id'] = trim($this->supp_info['supp_id']);
                $goods = $model_goods->getGoodsInfo($condition);
                if (empty($goods)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
            /**
             * 验证当前供应商商品的条形码是否重复,不同供应商的商品条码不能重复
             */
            case 'check_goods_barcode':
                $condition['goods_barcode']   = $_GET['goods_barcode'];
                $condition['id'] = array('neq',intval($_GET['goods_id']));
                $condition['supp_id'] = trim($this->supp_info['supp_id']);
                $goods = $model_goods->getGoodsInfo($condition);
                if (empty($goods)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
            /**
             * 根据商品编号获取商品信息
             */
            case 'get_goods_by_id':
                $condition['id'] = $_POST['goods_id'];
                $goods = $model_goods->getGoodsInfo($condition);
                echo $goods;exit;
                break;
        }
    }

    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_goods = SCMModel('supplier_goods');
        $supplier = $this->supp_info;
        $condition['supp_id'] = $supplier['supp_id'];
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }

        $order = '';
        $field = 'id,goods_nm,goods_barcode,goods_price,goods_discount,goods_rate,goods_unit,min_set_num,unit_num,goods_spec,produce_company,produce_area,valid_remind,shelf_life,status';
        $sortparam = array('id','goods_nm');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        if (!is_numeric($_GET['curpage'])){
            $count = $model_goods->getGoodsCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=goods&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $goods_list = $model_goods->getGoodsList($condition, $field, null, $order, $limit);
        $this->createExcel($goods_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($goods_list) {
        $data = array();
        foreach ($goods_list as $value) {
            $param = array();
            $param['goods_id'] = $value['id'];
            $param['goods_name'] = iconv('utf-8','gb2312', $value['goods_nm']);
            $param['goods_price'] = $value['goods_price'];
            $param['goods_discount'] = $value['goods_discount'];
            $param['stock_unit'] = iconv('utf-8','gb2312', $value['goods_unit']);
            $param['min_supp_num'] = iconv('utf-8','gb2312', $value['min_set_num']);
            $param['unit_num'] = $value['unit_num'];
            $param['specification'] = iconv('utf-8','gb2312', $value['goods_spec']);
            $param['valid_remind'] = iconv('utf-8','gb2312', $value['valid_remind']);
            $param['shelf_life'] = iconv('utf-8','gb2312', $value['shelf_life']);
            if($value['status'] == 3)
                $param['status'] = iconv('utf-8','gb2312', "审核未通过");
            else if($value['status'] == 2)
                $param['status'] = iconv('utf-8','gb2312', "未审核");
            else if($value['status'] == 1)
                $param['status'] = iconv('utf-8','gb2312', "已审核");
            else if($value['status'] == 0)
                $param['status'] = iconv('utf-8','gb2312', "失效");
            $data[$value['id']] = $param;
        }
        $header = array(
            "goods_id" => iconv('utf-8','gb2312',"商品ID"),
            "goods_name" => iconv('utf-8','gb2312', "商品名称"),
            "goods_price" => iconv('utf-8','gb2312', "商品单价(元)"),
            "goods_discount" => iconv('utf-8','gb2312', "折扣"),
            "stock_unit" => iconv('utf-8','gb2312', "库存单位"),
            "min_supp_num" => iconv('utf-8','gb2312', "最小配量"),
            "unit_num" => iconv('utf-8','gb2312', "单位数量"),
            "specification" => iconv('utf-8','gb2312', "规格"),
            "valid_remind" => iconv('utf-8','gb2312', "有效期提醒天数"),
            "shelf_life" => iconv('utf-8','gb2312', "保质期"),
            "status" => iconv('utf-8','gb2312', "状态"),
        );
        \Shopnc\Lib::exporter()->output('goods_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 创建Excel文件
     * @param $goods_list
     */
    private function createExcel($goods_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品ID');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品单价(元)');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'折扣');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'最小配量');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'单位数量');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'保质期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'状态');
        //data
        foreach ((array)$goods_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['id']);
            $tmp[] = array('data'=>$v['goods_nm']);
            $tmp[] = array('data'=>$v['goods_price']);
            $tmp[] = array('data'=>$v['goods_discount']);
            $tmp[] = array('data'=>$v['goods_unit']);
            $tmp[] = array('data'=>$v['min_set_num']);
            $tmp[] = array('data'=>$v['unit_num']);
            $tmp[] = array('data'=>$v['goods_spec']);
            $tmp[] = array('data'=>$v['valid_remind']);
            $tmp[] = array('data'=>$v['shelf_life']);
            if($v['status'] == 3)
                $status = "审核未通过";
            else if($v['status'] == 2)
                $status = "未审核";
            else if($v['status'] == 1)
                $status = "已审核";
            else if($v['status'] == 0)
                $status = "失效";
            $tmp[] = array('data'=>$status);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('goods-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 删除商品
     */
    public function goods_delOp(){
        $model_goods = SCMModel('supplier_goods');
        if ($_GET['id'] != '') {
            $ids = explode(',', $_GET['id']);
            if($model_goods->delGoodsByIdString($ids)){
                $this->log('删除供应商商品[ID:'.$_GET['id'] .']',1);
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            }
            else
                exit(json_encode(array('state'=>true,'msg'=>'删除失败')));
        }
    }

}