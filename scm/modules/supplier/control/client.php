<?php
/**
 * 预警
 *
 *
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class clientControl extends SCMControl
{
    const EXPORT_SIZE = 1000;
    protected $table_pre;
    protected $supp_info;

    public function __construct()
    {
        parent::__construct();
        $this->table_pre = C('tablepre');
        $adminInfo = $this->getAdminInfo();
        $condition = array("admin.admin_id" => $adminInfo['id'],);
        $this->supp_info =  SCMModel('supplier_account')->getSupplier($condition);
    }

    public function indexOp()
    {
        $this->clientOp();
    }

    /*
     * 合作终端店管理
     */
    public function clientOp()
    {
        Tpl::showpage('client.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp() {
        $model_client = SCMModel('supplier_client');
        //当前供应商
        $result = $this->supp_info;
        $condition['supp_id'] = trim($result['supp_id']);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'scm_client_order.clie_id,scm_client_order.clie_ch_name,scm_client.clie_tel,clie_mobile,clie_contacter,clie_address';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('clie_id','clie_ch_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $client_list = $model_client->getClientList($condition,'clie_id', $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_client->shownowpage();
        $temp_list = $model_client->gettotalnumon($condition,'scm_client_order.clie_id','scm_client_order,scm_client','scm_client_order.clie_id','scm_client_order.clie_id = scm_client.clie_id');
        $data['total_num'] = count($temp_list);

        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($client_list as $value) {
            $param = array();
            $index++;
            $param['number'] = $index;
            $param['clie_id'] = $value['clie_id'];
            //获取当前终端店信息
            $client_info = $model_client->getClientInfo(array('clie_id'=>$value['clie_id']));
            $param['clie_ch_name'] = $client_info['clie_ch_name'];
            $param['clie_tel'] = $value['clie_tel'];
            $param['clie_mobile'] = $value['clie_mobile'];
            $param['clie_contacter'] = $value['clie_contacter'];
            $param['clie_address'] = $value['clie_address'];
            //有效期预警
            $temp_goods_list = $this->validation_warn_sql(array('clie_id'=>$value['clie_id']));
            if(count($temp_goods_list) > 0)
                $param['validity_warn'] = '<a class="btn" style="background-color: yellow" href="index.php?act=client&op=validity_warn&clie_id=' .$value['clie_id'] . '">有预警</a>';
            else
                $param['validity_warn'] = '<a class="btn" href="javascript:void(0);">无预警</a>';
            //缺货预警
            $supp_result = $this->supp_info;
            $condition = array();
            $condition['scm_client_stock.clie_id'] = trim($value['clie_id']);
            $condition['scm_client_stock.supp_id'] = trim($supp_result['supp_id']);
            $condition[] = array('exp','goods_stock < goods_low_stock');
            $order = '';
            $field = 'scm_client_stock.id';
            $temp_stock_list = $model_client->getClientStockList($condition, $field, null, $order);
            if(count($temp_stock_list) > 0)
                $param['unavailable_warn'] = '<a class="btn" style="background-color: yellow" href="index.php?act=client&op=unavailable_warn&clie_id=' .$value['clie_id'] . '">有预警</a>';
            else
                $param['unavailable_warn'] = '<a class="btn" href="javascript:void(0);">无预警</a>';
            //滞销预警
            $goods_list = $this->unsalable_warn_sql(array('clie_id'=>$value['clie_id']));
            $count = count($goods_list);
            if($count> 0){
                $param['unsalable_warn'] = '<a class="btn" style="background-color: yellow" href="index.php?act=client&op=unsalable_warn&clie_id=' .$value['clie_id'] . '">有预警</a>';
            }else{
                $param['unsalable_warn'] = '<a class="btn" href="javascript:void(0);">无预警</a>';
            }
            $data['list'][$value['clie_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * csv导出
     */
    public function export_csvOp() {
        $model_client = SCMModel('supplier_client');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['scm_client_order.clie_id'] = array('in', $id_array);
        }
        $order = '';
        //当前供应商
        $result = $this->supp_info;
        $condition['supp_id'] = trim($result['supp_id']);
        $field = 'scm_client_order.clie_id,scm_client_order.clie_ch_name';
        if (!is_numeric($_GET['curpage'])){
            $count = $model_client->getClientCount($condition,'clie_id');
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?act=client&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }
        $client_list = $model_client->getClientList($condition,'clie_id', $field, null, $order,$limit);
        //clie_order 表没有clie_ch_name,clie_tel,clie_mobile
        foreach($client_list as $k => $v){
            $client_info = $model_client->getClientInfo(array('clie_id'=>$v['clie_id']));
            $client_list[$k]['clie_ch_name'] = $client_info['clie_ch_name'];
            $client_list[$k]['clie_tel'] = $client_info['clie_tel'];
            $client_list[$k]['clie_mobile'] = $client_info['clie_mobile'];
            $client_list[$k]['clie_contacter'] = $client_info['clie_contacter'];
            $client_list[$k]['clie_address'] = $client_info['clie_address'];
        }
        $this->createExcel($client_list);
    }

    private function createCsv($client_list){
        $data = array();
        foreach ($client_list as $value) {
            $param = array();
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['clie_tel'] = $value['clie_tel'];
            $param['clie_mobile'] = $value['clie_mobile'];
            $param['clie_contacter'] = iconv('utf-8', 'gb2312', $value['clie_contacter']);
            $param['clie_address'] = iconv('utf-8', 'gb2312',$value['clie_address']);
            $data[$value['clie_id']] = $param;
        }
        $header = array(
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "clie_tel" => iconv('utf-8','gb2312', "终端店电话"),
            "clie_mobile" => iconv('utf-8','gb2312', "终端店手机"),
            "clie_contacter" => iconv('utf-8', 'gb2312', "店主名"),
            "clie_address" => iconv('utf-8', 'gb2312', "地址"),
        );
        \Shopnc\Lib::exporter()->output('client_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 生成Excel文件
     * @param $client_list
     */
    private function createExcel($client_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店电话');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店手机');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'店主名');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'地址');
        //data
        foreach ((array)$client_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['clie_ch_name']);
            $tmp[] = array('data'=>$v['clie_tel']);
            $tmp[] = array('data'=>$v['clie_mobile']);
            $tmp[] = array('data'=>$v['clie_contacter']);
            $tmp[] = array('data'=>$v['clie_address']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 查看近效期预警的商品
     */
    public function validity_warnOp(){
        $model_client = SCMModel('supplier_client');
        Tpl::output('clie_id',$_GET['clie_id']);
        Tpl::output('subject','合作终端店商品有效期预警');
        Tpl::output('explanation_1','有效期提示:有效期-当前时间小于等于有效期提醒天数');
        Tpl::output('explanation_2','有效期=生产日期+保质期');
        Tpl::output('title','合作终端店商品近效期预警');
        $temp_goods_list = $this->validation_warn_sql(array('clie_id' => $_GET['clie_id']));
        //为每个入库信息表增加订单编号order_no 和生产厂家
        $count = array();
        if( count($temp_goods_list) > 0 ){
            foreach($temp_goods_list as $k => $v){
                if(!in_array($v['goods_barcode'],$count))
                    $count[] = $v['goods_barcode'];
            }
        }
        Tpl::output('explanation_3',"当前终端店有<span style='color:red;font-size:large;'>".count($count)."</span>种商品达到近效期预警");
        //获取当前终端店信息
        $client_info = $model_client->getClientInfo(array('clie_id'=>trim($_GET['clie_id'])));
        Tpl::output('client_info',$client_info);
        Tpl::showpage("client.validity_warn");
    }

    public function get_validation_warn_xmlOp() {
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $data['now_page'] = $model_client->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;
        $goods_list = $this->validation_warn_sql(array('clie_id' => $_GET['clie_id'],'index'=> $index, 'page_num'=> $page_num ));
        $data['total_num'] = count($this->validation_warn_sql(array('clie_id' => $_GET['clie_id'])));
        if(count($goods_list) > 0){
            foreach ($goods_list as $value) {
                $param = array();
                $index++;
                $param['number'] = $index;
                $param['clie_id'] = $value['clie_id'];
                $client_result = $model_client->getClientInfo(array('clie_id'=>$value['clie_id']));
                $param['clie_ch_name'] = $client_result['clie_ch_name'];
                $param['goods_barcode'] = $value['goods_barcode'];
                $param['goods_nm'] = $value['goods_nm'];
                $param['goods_unit'] = $value['goods_unit'];
                $param['goods_spec'] = $value['goods_spec'];
                $param['production_date'] = $value['production_date'];
                $param['expire_date'] = $value['expire_date'];
                $param['valid_remind'] = $value['valid_remind'];
                $param['shelf_life'] = $value['shelf_life'];
                $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$value['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
                $param['produce_company'] = $goods_info['produce_company'];
                $data['list'][$value['id']] = $param;
            }
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 当前终端店的有效期预警
     * return  goods_list
     */
    private function validation_warn_sql($condition){
        $model_client = SCMModel('supplier_client');
        //当前供应商
        $supp_result = $this->supp_info;
        $sql = "SELECT sii.id,sii.clie_id,sii.goods_barcode,sii.goods_nm,sii.goods_unit, sii.goods_spec,sii.set_num,sii.production_date,scs.valid_remind,scs.shelf_life,sii.order_id,
                CASE WHEN scs.shelf_life LIKE '%年'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 360) DAY )
                WHEN scs.shelf_life LIKE '%月'  THEN DATE_ADD( scs.production_date, INTERVAL (scs.shelf_life * 30) DAY )
                WHEN scs.shelf_life LIKE '%天'  THEN DATE_ADD( scs.production_date, INTERVAL scs.shelf_life DAY )
                END AS expire_date
                FROM ".C('tablepre')."scm_instock_info AS sii
                LEFT JOIN ".C('tablepre')."scm_client_stock AS scs ON sii.goods_barcode = scs.goods_barcode,
                (
                SELECT goods_barcode,MIN(production_date)AS riqi FROM ".C('tablepre')."scm_instock_info
                WHERE waring_flag = 1
                GROUP BY goods_barcode
                ) AS c
                WHERE sii.goods_barcode = c.goods_barcode
                AND sii.production_date = c.riqi
                AND sii.clie_id = '".$condition['clie_id']."'";
        if(isset($condition['ids']) && $condition['ids'] != '') {
            $sql .= " AND sii.id in (" . $condition['ids'] . ")";
        }
        $sql .= " AND sii.supp_id = '".$supp_result['supp_id']."'
                AND (
                    (
                        sii.shelf_life LIKE '%年'
                        AND datediff(
                            DATE_ADD(
                                sii.production_date,
                                INTERVAL (sii.shelf_life * 360) DAY
                            ),
                            NOW()
                        ) <= scs.valid_remind
                    )
                    OR(
                        sii.shelf_life LIKE '%月'
                        AND datediff(
                            DATE_ADD(
                                sii.production_date,
                                INTERVAL (sii.shelf_life * 30) DAY
                            ),
                            NOW()
                        ) <= scs.valid_remind
                    )
                    OR (
                        sii.shelf_life LIKE '%天'
                        AND datediff(
                            DATE_ADD(
                                sii.production_date,
                                INTERVAL sii.shelf_life DAY
                            ),
                            NOW()
                        ) <= scs.valid_remind
                    )
                )
                GROUP BY sii.production_date
                ORDER BY sii.production_date";
        if(isset($condition['index']) ){
            $sql.= " limit ".$condition['index'].",".$condition['page_num'];
        }
        $temp_goods_list = $model_client->execute_sql($sql);
        return $temp_goods_list;
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
     * 导出有效期预警的商品列表csv
     */
    public function export_validity_warn_csvOp()
    {
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        $temp_goods_list =$this->validation_warn_sql(array('clie_id' => $_GET['clie_id'],'ids'=> $_GET['id'] ));
        //获取当前终端店详细信息
        $client_info = $model_client->getClientInfo(array('clie_id'=>trim($_GET['clie_id'])));
        if( count($temp_goods_list) > 0 ){
            foreach($temp_goods_list as $k => $v){
                $order_info = $model_client->getOrderInfo(array('id'=>$v['order_id']),'order_no');
                $temp_goods_list[$k]['order_no'] = $order_info['order_no'];
                $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$v['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
                $temp_goods_list[$k]['produce_company'] = $goods_info['produce_company'];
                $temp_goods_list[$k]['clie_ch_name'] = $client_info['clie_ch_name'];
            }
        }
        $this->createValidityWarnExcel($temp_goods_list);
    }

    private function createValidityWarnCsv($goods_list){
        $data = array();
        foreach ($goods_list as $value) {
            $param = array();
            $param['id'] = iconv('utf-8','gb2312', $value['id']);
            $param['clie_id'] = iconv('utf-8','gb2312', $value['clie_id']);
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = iconv('utf-8','gb2312', $value['goods_nm']);
            $param['goods_unit'] = iconv('utf-8','gb2312', $value['goods_unit']);
            $param['goods_spec'] = iconv('utf-8','gb2312', $value['goods_spec']);
            $param['production_date'] = $value['production_date'];
            $param['expire_date'] = $value['expire_date'];
            $param['valid_remind'] =  $value['valid_remind'];
            $param['shelf_life'] = iconv('utf-8','gb2312', $value['shelf_life']);
            $param['produce_company'] = iconv('utf-8','gb2312', $value['produce_company']);
            $data[$value['id']] = $param;
        }
        $header = array(
            "id" =>  iconv('utf-8','gb2312', "编号"),
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "goods_barcode" => iconv('utf-8','gb2312', "商品编码"),
            "goods_nm" => iconv('utf-8','gb2312', "商品名称"),
            "goods_unit" => iconv('utf-8','gb2312', "库存单位"),
            "goods_spec" => iconv('utf-8','gb2312', "商品规格"),
            "production_date" => iconv('utf-8','gb2312', "生产日期"),
            "expire_date" => iconv('utf-8','gb2312', "有效期至"),
            "valid_remind" => iconv('utf-8','gb2312', "有效期提醒天数"),
            "shelf_life" => iconv('utf-8','gb2312', "保质期"),
            "produce_company" =>  iconv('utf-8','gb2312', "生产厂家"),
        );
        \Shopnc\Lib::exporter()->output('unsalable_stock_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    private function createValidityWarnExcel($goods_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品编码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产日期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期至');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'有效期提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'保质期');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产厂家');
        //data
        foreach ((array)$goods_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['id']);
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['clie_ch_name']);
            $tmp[] = array('data'=>$v['goods_barcode']);
            $tmp[] = array('data'=>$v['goods_nm']);
            $tmp[] = array('data'=>$v['goods_unit']);
            $tmp[] = array('data'=>$v['goods_spec']);
            $tmp[] = array('data'=>$v['production_date']);
            $tmp[] = array('data'=>$v['expire_date']);
            $tmp[] = array('data'=>$v['valid_remind']);
            $tmp[] = array('data'=>$v['shelf_life']);
            $tmp[] = array('data'=>$v['produce_company']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 查看缺货商品
     */
    public function unavailable_warnOp(){
        Tpl::output('clie_id',$_GET['clie_id']);
        Tpl::output('subject','合作终端店缺货商品');
        Tpl::output('explanation_1','缺货提示:库存<=库存下限');
        Tpl::output('explanation_2','黄色预警:库存下限*30%<=库存<=库存下限*70%');
        Tpl::output('explanation_3','红色预警:库存<=库存下限*30%');
        Tpl::output('title','合作终端店缺货商品表');
        Tpl::output('condition','unavailable_warn');
        $model_client = SCMModel('supplier_client');
        //当前供应商
        $supp_result = $this->supp_info;
        $condition['scm_client_stock.clie_id'] = trim($_GET['clie_id']);
        $condition['scm_client_stock.supp_id'] = trim($supp_result['supp_id']);
        $condition[] = array('exp','goods_stock < goods_low_stock');
        $order = '';
        $field = 'scm_client_stock.id,scm_client_stock.clie_id,scm_client.clie_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_unit,goods_spec,goods_rate,goods_stock,goods_low_stock,goods_uper_stock,new_product_flag,production_date,valid_remind,shelf_life,supp_ch_name';
        $temp_stock_list = $model_client->getClientStockList($condition, $field, null, $order);
        //获取当前终端店信息
        $client_info = $model_client->getClientInfo(array('clie_id'=>trim($_GET['clie_id'])));
        Tpl::output('explanation_4',"当前合作终端店有<span style='color:red;font-size:large;'>".count($temp_stock_list). "</span>种商品缺货");
        Tpl::output('client_info',$client_info);
        Tpl::showpage("client.unavailable_warn");
    }

    public function get_unavailable_warn_xmlOp(){
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $data['now_page'] = $model_client->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;
        //当前供应商
        $supp_result = $this->supp_info;
        $condition['scm_client_stock.clie_id'] = trim($_GET['clie_id']);
        $condition['scm_client_stock.supp_id'] = trim($supp_result['supp_id']);
        $condition[] = array('exp','goods_stock < goods_low_stock');
        $order = '';
        $field = 'scm_client_stock.id,scm_client_stock.clie_id,scm_client.clie_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_unit,goods_spec,goods_rate,goods_stock,goods_low_stock,goods_uper_stock,new_product_flag,production_date,valid_remind,shelf_life,supp_ch_name';
        $temp_stock_list = $model_client->getClientStockList($condition, $field, null, $order);
        $data['total_num'] = count($temp_stock_list);
        $goods_list =  $model_client->getClientStockList($condition, $field, $page_num, $order);
        $normal_stock_list = array();
        $yellow_stock_list = array();
        $red_stock_list = array();
        foreach($goods_list as $k => $v){
            $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$v['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
            $v['produce_company'] = $goods_info['produce_company'];
            if($v['goods_stock'] < $v['goods_low_stock'] * 0.3 ){
                $v['color'] = 'red';
                $red_stock_list[] = $v;
            }else if($v['goods_stock'] >= $v['goods_low_stock'] * 0.3 && $v['goods_stock'] <= $v['goods_low_stock'] * 0.7 ){
                $v['color'] = 'yellow';
                $yellow_stock_list[] = $v;
            }else{
                $v['color'] = '#8ac43f';
                $normal_stock_list[] = $v;
            }
        }
        $client_stock_list = array_merge($red_stock_list,$yellow_stock_list,$normal_stock_list);
        if(count($client_stock_list) > 0){
            foreach($client_stock_list as $value){
                $param = array();
                $index++;
                $param['number'] = $index;
                $param['clie_id'] = $value['clie_id'];
                $client_result = $model_client->getClientInfo(array('clie_id'=>$value['clie_id']));
                $param['clie_ch_name'] = $client_result['clie_ch_name'];
                $param['goods_barcode'] = $value['goods_barcode'];
                $param['goods_nm'] = $value['goods_nm'];
                $param['goods_unit'] = $value['goods_unit'];
                $param['goods_spec'] = $value['goods_spec'];
                $param['goods_stock'] = $value['goods_stock'];
                $param['goods_low_stock'] = $value['goods_low_stock'];
                $param['produce_company'] = $goods_info['produce_company'];
                $param['color'] = "<a class='btn' style='background-color:".$value['color']." ' > &nbsp;&nbsp;&nbsp;&nbsp;</a>";
                $data['list'][$value['id']] = $param;
            }
        }
        echo Tpl::flexigridXML($data);exit();
    }

    public function export_unavailable_warn_csvOp(){
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['scm_client_stock.id'] = array('in', $id_array);
        }
        //当前供应商
        $supp_result = $this->supp_info;
        $condition['scm_client_stock.clie_id'] = trim($_GET['clie_id']);
        $condition['scm_client_stock.supp_id'] = trim($supp_result['supp_id']);
        $condition[] = array('exp','goods_stock < goods_low_stock');
        $order = '';
        $field = 'scm_client_stock.id,scm_client_stock.clie_id,scm_client.clie_ch_name,goods_barcode,goods_nm,goods_price,goods_discount,goods_unit,goods_spec,goods_rate,goods_stock,goods_low_stock,goods_uper_stock,new_product_flag,production_date,valid_remind,shelf_life,supp_ch_name';
        $temp_stock_list = $model_client->getClientStockList($condition, $field, null, $order);
        $normal_stock_list = array();
        $yellow_stock_list = array();
        $red_stock_list = array();
        foreach($temp_stock_list as $k => $v){
            $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$v['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
            $v['produce_company'] = $goods_info['produce_company'];
            if($v['goods_stock'] < $v['goods_low_stock'] * 0.3 ){
                $v['color'] = 'red';
                $red_stock_list[] = $v;
            }else if($v['goods_stock'] >= $v['goods_low_stock'] * 0.3 && $v['goods_stock'] <= $v['goods_low_stock'] * 0.7 ){
                $v['color'] = 'yellow';
                $yellow_stock_list[] = $v;
            }else{
                $v['color'] = 'green';
                $normal_stock_list[] = $v;
            }
        }
        $client_stock_list = array_merge($red_stock_list,$yellow_stock_list,$normal_stock_list);
        $this->createUnavailableWarnExcel($client_stock_list);
    }
    private function createUnavailableWarnCsv($goods_list){
        $data = array();
        foreach ($goods_list as $value) {
            $param = array();
            $param['id'] = $value['id'];
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312', $value['clie_ch_name']);
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = iconv('utf-8','gb2312', $value['goods_nm']);
            $param['goods_unit'] = iconv('utf-8','gb2312', $value['goods_unit']);
            $param['goods_spec'] = iconv('utf-8','gb2312', $value['goods_spec']);
            $param['goods_stock'] = $value['goods_stock'];
            $param['goods_low_stock'] = $value['goods_low_stock'];
            $param['produce_company'] = iconv('utf-8','gb2312', $value['produce_company']);
            if($value['color'] == 'red')
                $param['color'] = iconv('utf-8','gb2312',"红色预警");
            else if($value['color'] == 'yellow')
                $param['color'] = iconv('utf-8','gb2312',"黄色预警");
            else if($value['color'] == 'green')
                $param['color'] = iconv('utf-8','gb2312',"缺货");
            $data[$value['id']] = $param;
        }
        $header = array(
            "id" => iconv('utf-8','gb2312', "商品编号"),
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "goods_barcode" => iconv('utf-8','gb2312', "商品条码"),
            "goods_nm" => iconv('utf-8','gb2312', "商品名称"),
            "goods_unit" => iconv('utf-8','gb2312', "库存单位"),
            "goods_spec" => iconv('utf-8','gb2312', "商品规格"),
            "goods_stock" => iconv('utf-8','gb2312', "库存"),
            "goods_low_stock" => iconv('utf-8','gb2312', "库存下限"),
            "produce_company" => iconv('utf-8','gb2312', "生产厂家"),
            "color" => iconv('utf-8','gb2312', "预警"),
        );
        \Shopnc\Lib::exporter()->output('unavailable_goods_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 创建Excel文件
     * @param $goods_list
     */
    private function createUnavailableWarnExcel($goods_list){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品条码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存下限');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产厂家');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'预警');
        //data
        foreach ((array)$goods_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['id']);
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$v['clie_ch_name']);
            $tmp[] = array('data'=>$v['goods_barcode']);
            $tmp[] = array('data'=>$v['goods_nm']);
            $tmp[] = array('data'=>$v['goods_unit']);
            $tmp[] = array('data'=>$v['goods_spec']);
            $tmp[] = array('data'=>$v['goods_stock']);
            $tmp[] = array('data'=>$v['goods_low_stock']);
            $tmp[] = array('data'=>$v['produce_company']);
            if($v['color'] == 'red')
                $tmp[] = array('data'=>"红色预警");
            else if($v['color'] == 'yellow')
                $tmp[] = array('data'=>"黄色预警");
            else if($v['color'] == 'green')
                $tmp[] = array('data'=>"缺货");
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 查看滞销的商品
     * 单个终端店的商品库存有商品并且该商品在一个月之内没有订货
     */
    public function unsalable_warnOp(){
        $model_client = SCMModel('supplier_client');
        Tpl::output('clie_id',$_GET['clie_id']);
        Tpl::output('subject','合作终端店滞销的商品');
        Tpl::output('explanation','滞销预警:该终端店内的商品在<span style="color: red;">滞销提醒天数</span>之内没有订货');
        Tpl::output('title','合作终端店滞销商品表');
        //获取当前终端店信息
        $client_info = $model_client->getClientInfo(array('clie_id'=>trim($_GET['clie_id'])));
        //当前供应商
        $supp_result = $this->supp_info;
        $goods_list = $this->unsalable_warn_sql(array('clie_id'=>$_GET['clie_id'],'supp_id'=>$supp_result['supp_id'] ));
        $count = count($goods_list);
        Tpl::output('explanation_1',"当前合作终端店有<span style='color:red;font-size:large;'>".$count."</span>种商品滞销");
        Tpl::output('client_info',$client_info);
        Tpl::showpage("client.unsalable_warn");
    }

    /**
     * 滞销预警
     */
    public function unsalable_warn_xmlOp(){
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $data['now_page'] = $model_client->shownowpage();
        $index = ($data['now_page'] - 1) * $page_num;
        $goods_list = $this->unsalable_warn_sql(array('clie_id'=>$_GET['clie_id'],'ids'=> $_GET['id'],'index'=>$index,'page_num'=>$page_num));
        $data['total_num'] = count($this->unsalable_warn_sql(array('clie_id'=>$_GET['clie_id'])));
        if(count($goods_list) > 0){
            foreach ($goods_list as $value) {
                $param = array();
                $index++;
                $param['number'] = $index;
                $param['clie_id'] = $value['clie_id'];
                $client_result = $model_client->getClientInfo(array('clie_id'=>$value['clie_id']));
                $param['clie_ch_name'] = $client_result['clie_ch_name'];
                $param['goods_barcode'] = $value['goods_barcode'];
                $param['goods_nm'] = $value['goods_nm'];
                $param['goods_unit'] = $value['goods_unit'];
                $param['goods_stock'] = $value['goods_stock'];
                $param['goods_spec'] = $value['goods_spec'];
                $param['drug_remind'] = $value['drug_remind'];
                $param['last_time'] = $value['last_time'];
                $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$value['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
                $param['produce_company'] = $goods_info['produce_company'];
                $data['list'][$value['id']] = $param;
            }
        }

        echo Tpl::flexigridXML($data);exit();
    }
    /**
     * 导出滞销的商品列表的csv
     */
    public function export_unsalable_warn_csvOp()
    {
        if ($_GET['id'] != '') {
            $goods_list = $this->unsalable_warn_sql(array('clie_id'=>$_GET['clie_id'],'ids'=> $_GET['id']));
        }else{
            $goods_list = $this->unsalable_warn_sql(array('clie_id'=>$_GET['clie_id']));
        }
        $this->createUnsalableWarnExcel($goods_list);
    }

    /**
     * 当前终端店的滞销预警
     * return  goods_list
     */
    private function unsalable_warn_sql($condition){
        $model_client = SCMModel('supplier_client');
        //当前供应商
        $supp_result = $this->supp_info;
        $sql = "SELECT
                	scs.id,
                	scs.clie_id,
                	sc.clie_ch_name,
                	scs.goods_barcode,
                	scs.goods_nm,
                	scs.goods_unit,
                	scs.goods_stock,
                	scs.goods_uper_stock,
                	scs.goods_spec,
                	scs.drug_remind,
                    max(sii.in_stock_date) as last_time
                FROM
                	".$this->table_pre."scm_client_stock AS scs
                LEFT JOIN ".$this->table_pre."scm_client AS sc ON sc.clie_id = scs.clie_id
                LEFT JOIN ".$this->table_pre."scm_instock_info sii ON scs.clie_id=sii.clie_id AND scs.supp_id=sii.supp_id AND scs.goods_barcode=sii.goods_barcode
                WHERE
                	scs.clie_id = '".$condition['clie_id']."'
                AND scs.supp_id='".$supp_result['supp_id']."'
                AND scs.goods_barcode NOT IN (
                	SELECT
                		goods_barcode
                	FROM
                		".$this->table_pre."scm_instock_info
                	WHERE
                		clie_id = '".$condition['clie_id']."'
                	AND in_stock_date > DATE_SUB(NOW(), INTERVAL scs.drug_remind DAY)
                )";
        if(isset($condition['ids']) && $condition['ids'] != '' ){
            $sql.= " AND scs.id in (" . $condition['ids'] . ") ";
        }
       $sql .= " GROUP BY scs.goods_barcode";
        if(isset($condition['index'])) {
            $sql .= " limit " . $condition['index'] . "," . $condition['page_num'];
        }
        $temp_goods_list = $model_client->execute_sql($sql);
        return $temp_goods_list;
    }

    private function createUnsalableWarnCsv($goods_list){
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        $data = array();
        foreach ($goods_list as $value) {
            $param = array();
            $param['id'] = $value['id'];
            $client_result = $model_client->getClientInfo(array('clie_id'=>$value['clie_id']));
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8','gb2312',  $client_result['clie_ch_name']);
            $param['goods_barcode'] = $value['goods_barcode'];
            $param['goods_nm'] = iconv('utf-8','gb2312', $value['goods_nm']);
            $param['goods_unit'] = iconv('utf-8','gb2312', $value['goods_unit']);
            $param['goods_stock'] = $value['goods_stock'];
            $param['goods_uper_stock'] = $value['goods_uper_stock'];
            $param['goods_spec'] = iconv('utf-8','gb2312', $value['goods_spec']);
            $param['drug_remind'] = $value['drug_remind'];
            $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$value['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
            $param['last_time'] = $value['last_time'];
            $param['produce_company'] = iconv('utf-8','gb2312', $goods_info['produce_company']);
            $data[$value['id']] = $param;
        }
        $header = array(
            "id" => iconv('utf-8','gb2312', "商品编号"),
            "clie_id" => iconv('utf-8','gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8','gb2312', "终端店名称"),
            "goods_barcode" => iconv('utf-8','gb2312', "商品条码"),
            "goods_nm" => iconv('utf-8','gb2312', "商品名称"),
            "goods_unit" => iconv('utf-8','gb2312', "库存单位"),
            "goods_stock" => iconv('utf-8','gb2312', "库存"),
            "goods_uper_stock" => iconv('utf-8','gb2312', "库存上限"),
            "goods_spec" => iconv('utf-8','gb2312', "商品规格"),
            "drug_remind" =>  iconv('utf-8','gb2312', "滞销提醒天数"),
            "last_time" => iconv('utf-8','gb2312', "最后一次进货时间"),
            "produce_company" => iconv('utf-8','gb2312', "生产厂家"),
        );
        \Shopnc\Lib::exporter()->output('unsalable_stock_list' .$_GET['curpage'] . '-'.date('Y-m-d'), $data, $header);
    }

    /**
     * 导出Excel
     * @param $goods_list
     */
    private function createUnsalableWarnExcel($goods_list){
        $model_client = SCMModel('supplier_client');
        $model_goods = SCMModel('supplier_goods');
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'终端店名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品条码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存单位');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'库存');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'商品规格');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'滞销提醒天数');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'最后一次进货时间');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'生产厂家');
        //data
        foreach ((array)$goods_list as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['id']);
            $client_result = $model_client->getClientInfo(array('clie_id'=>$v['clie_id']));
            $tmp[] = array('data'=>$v['clie_id']);
            $tmp[] = array('data'=>$client_result['clie_ch_name']);
            $tmp[] = array('data'=>$v['goods_barcode']);
            $tmp[] = array('data'=>$v['goods_nm']);
            $tmp[] = array('data'=>$v['goods_unit']);
            $tmp[] = array('data'=>$v['goods_stock']);
            $tmp[] = array('data'=>$v['goods_spec']);
            $tmp[] = array('data'=>$v['drug_remind']);
            $tmp[] = array('data'=>$v['last_time']);
            $goods_info = $model_goods->getGoodsInfo(array('goods_barcode'=>$v['goods_barcode'],'supp_id' =>$this->supp_info['supp_id']));
            $tmp[] = array('data'=>$goods_info['produce_company']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
        $excel_obj->generateXML('orders-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

}