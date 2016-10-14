<?php
/**
 * 商品分类管理
 *
 */
use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class categoryControl extends SCMControl{
    const EXPORT_SIZE = 1000;
    public function __construct()
    {
        parent::__construct();
    }

    public function indexOp()
    {
        $this->categoryOp();
    }

    /*
     * 查看商品分类
     */
    public function categoryOp(){
        $model_category = SCMModel('supplier_category');
        //父ID
        $pid = $_GET['pid']?intval($_GET['pid']):0;
        $condition = array();
        //一级分类
        if($pid == 0){
            $condition['level'] = $level =  1;
            $title = "商品分类列表(一级)";
        }else{  //二级或三级分类
            $parent = $model_category->getCategoryInfo(array('cate_id' => $pid));
            $condition['level'] = $level = $parent['level'] + 1;
            if( $parent['level'] == 1)
                $title = "分类".'"' . $parent['category_name'] . '"的下级列表(二级)';
            else if($parent['level'] == 2){
                $grand_parent = $model_category->getCategoryInfo(array('cate_id' => $parent['pid']));
                $title ="分类". '"' . $grand_parent['category_name'] . ' - ' . $parent['category_name'] . '"的下级列表(三级)';
            }
        }
        //获取当前供应商的详细信息
        $result = $this->getSupplier();

        $tmp_list = $model_category->getCategoryList(array('pid' => $pid, 'supp_id' =>$result['supp_id'] ));
        $category_list = array();
        //查看当前分类列表下的每个分类是否有子分类
        if (is_array($tmp_list)){
            foreach ($tmp_list as $k => $v){
                if(!empty($model_category->getCategoryList(array('pid' => $v['cate_id']))))
                    $v['have_child'] = 1;
                $category_list[] = $v;
            }
        }
        Tpl::output('level', $level);
        Tpl::output('title',$title);
        Tpl::output('pid',$pid);
        Tpl::output('category_list',$category_list);
        Tpl::showpage('category.index');
    }

    private function getSupplier(){
        $model_supplier = SCMModel('supplier_account');
        //获取当前供应商的详细信息
        $adminInfo = $this->getAdminInfo();
        $condition = array(
            "admin.admin_id" => $adminInfo['id'],
        );
        $result = $model_supplier->getSupplier($condition);
        return $result;
    }
    /*
     * 添加商品分类
     */
    public function category_addOp(){
        $model_category = SCMModel('supplier_category');
        /**
         * 保存
         */
        if (chksubmit()) {
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["category_name"], "require"=>"true", "message"=>"分类名不能为空"),
                array("input"=>$_POST["sort"], "require"=>"true", "message"=>"排序不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $category = array();
                $category['category_name'] = trim($_POST['category_name']);
                $category['pid'] = $_POST['pid'];
                $category['is_open'] = $_POST['is_open'];
                $category['sort'] = $_POST['sort'];
                //获取当前供应商的详细信息

                $result = $this->getSupplier();
                $category['supp_id'] = $result['supp_id'];

                //设置新增的的分类的级别
                if($_POST['pid'] == 0){
                    $category['level'] = 1;
                }else{
                    $parent = $model_category->getCategoryInfo(array('cate_id'=>$_POST['pid'] ));
                    $category['level'] = $parent['level'] + 1;
                }
                $result = $model_category->addCategory($category);
                if ($result){
                    $url = array(
                        array(
                            'url'=>'index.php?act=category&op=category&pid='.$_POST['pid'],
                            'msg'=>"返回商品分类列表",
                        ),
                        array(
                            'url'=>'index.php?act=category&op=category_add&pid='.$_POST['pid'],
                            'msg'=>"继续新增商品分类",
                        ),
                    );
                    //$this->log(L('nc_add,member_index_name').'[ '.$_POST['member_name'].']',1);
                    showMessage("添加商品分类成功",$url);
                }else {
                    showMessage("添加商品分类失败");
                }
            }
        }
        //获取当前供应商的详细信息
        $result = $this->getSupplier();
        //组合所有的分类为合法格式
        $top_list =  $model_category->getCategoryList(array('level' => 1, 'supp_id' =>$result['supp_id']));
        $parent_list = array();
        foreach($top_list as $k => $v){
            $parent_list[] = array('cate_id' => $v['cate_id'],'category_name' =>'&nbsp&nbsp;&nbsp;'. $v['category_name']);
            $second_list =  $model_category->getCategoryList(array('pid' => $v['cate_id']));
            foreach($second_list as $kk => $vv){
                $parent_list[] = array('cate_id' => $vv['cate_id'],'category_name' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$vv['category_name']);
            }
        }
        Tpl::output('parent_list', $parent_list);
        Tpl::output('pid',$_GET['pid']);
        Tpl::showpage('category.add');
    }
    /**
     * ajax操作
     */
    public function ajaxOp(){
        $model_category = SCMModel('supplier_category');
        switch ($_GET['branch']){
            /**
             * 验证商品分类名称是否重复
             */
            case 'check_category_name':
                $condition['category_name']   = $_GET['category_name'];
                $condition['cate_id'] = array('neq',intval($_GET['cate_id']));
                $category = $model_category->getCategoryInfo($condition);
                if (empty($category)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
            /*
             * 修改商品分类名称
             */
            case 'category_name':
                $condition['category_name']  = trim($_GET['value']);
                $condition['cate_id'] = array('neq',intval($_GET['id']));
                $category = $model_category->getCategoryInfo($condition);
                if(empty($category)){
                    $category['cate_id'] = intval($_GET['id']);
                    $category['category_name'] = trim($_GET['value']);
                    if($model_category->updateCategory($category)){
                        $return = true;
                    }
                    else{
                        $return = false;
                    }
                }else{
                    $return = false;
                }
                exit(json_encode(array('result'=>$return)));
                break;
            /*
             * 修改商品分类的排序
             */
            case 'sort':
                $update_category = array();
                $update_category['cate_id'] = intval($_GET['id']);
                $category = $model_category->getCategoryInfo($update_category);
                if(!empty($category)){
                    $update_category['sort'] = trim($_GET['value']);
                    if($model_category->updateCategory($update_category)){
                        $return = true;
                    }
                    else{
                        $return = false;
                    }
                }else{
                    $return = false;
                }
                exit(json_encode(array('result'=>$return)));
                break;
            case 'getCateInfo':
                $category = $model_category->getCategoryInfo(array("cate_id" =>$_POST['cate_id'] ));
                echo json_encode($category);
                break;
        }
    }
    /*
     * 修改商品分类
     */
    public function category_editOp(){
        $model_category = SCMModel('supplier_category');
        if(chksubmit()){
            $category = array();
            $category['category_name'] = trim($_POST['category_name']);
            $category['pid'] = $_POST['pid'];
            $category['is_open'] = $_POST['is_open'];
            $category['sort'] = $_POST['sort'];
            $category['cate_id'] = $_POST['cate_id'];
            //设置修改后的分类的级别
            if($_POST['pid'] == 0){
                $category['level'] = 1;
            }else{
                $parent = $model_category->getCategoryInfo(array('cate_id'=>$_POST['pid'] ));
                $category['level'] = $parent['level'] + 1;
            }
            $result = $model_category->updateCategory($category);
            if ($result){
                $url = array(
                    array(
                        'url'=>'index.php?act=category&op=category&pid='.$_POST['pid'],
                        'msg'=>"返回商品分类列表",
                    ),
                    array(
                        'url'=>'index.php?act=category&op=category_edit&cate_id='.$_POST['cate_id'],
                        'msg'=>"重新编辑该商品分类",
                    ),
                );
                //$this->log(L('nc_add,member_index_name').'[ '.$_POST['member_name'].']',1);
                showMessage("修改商品分类成功",$url);
            }else {
                showMessage("修改商品分类失败");
            }
        }
        //获取当前供应商的详细信息
        $result = $this->getSupplier();
        //组合所有的分类为合法格式
        $top_list =  $model_category->getCategoryList(array('level' => 1, 'supp_id' =>$result['supp_id']));
        $parent_list = array();
        foreach($top_list as $k => $v){
            $parent_list[] = array('cate_id' => $v['cate_id'],'category_name' =>'&nbsp&nbsp;&nbsp;'. $v['category_name']);
            $second_list =  $model_category->getCategoryList(array('pid' => $v['cate_id']));
            foreach($second_list as $kk => $vv){
                $parent_list[] = array('cate_id' => $vv['cate_id'],'category_name' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$vv['category_name']);
            }
        }
        Tpl::output('parent_list', $parent_list);
        $category = $model_category->getCategoryInfo(array('cate_id'=>$_GET['cate_id']));
        Tpl::output('category',$category);
        Tpl::showpage('category.edit');
    }

    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_category = SCMModel('supplier_category');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['cate_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }

        $order = '';
        $field = 'cate_id,category_name,is_open,sort';
        $sortparam = array('user_id','admin_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        if (!is_numeric($_GET['curpage'])){
            //获取当前供应商的详细信息
            $result = $this->getSupplier();
            $condition['supp_id'] = $result['supp_id'];
            $count = $model_category->getCategoryCount($condition);
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

        $cate_list = $model_category->getCategoryList($condition, $field, null, $order, $limit);

        $this->createCsv($cate_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($cate_list) {
        $data = array();
        foreach ($cate_list as $value) {
            $param = array();
            $param['cate_id'] = $value['cate_id'];
            $param['category_name'] = iconv('utf-8','gb2312', $value['category_name']);
            $param['is_open'] = $value['is_open']?iconv('utf-8','gb2312', "开启"):iconv('utf-8','gb2312', "关闭");
            $param['sort'] = $value['sort'];
            $data[$value['cate_id']] = $param;
        }
        $header = array(
            "cate_id" => iconv('utf-8','gb2312',"商品类别ID"),
            "category_name" => iconv('utf-8','gb2312', "商品类别名称"),
            "is_open" => iconv('utf-8','gb2312', "是否开启"),
            "sort" => iconv('utf-8','gb2312', "排序"),
        );
        \Shopnc\Lib::exporter()->output('cate_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }
}
