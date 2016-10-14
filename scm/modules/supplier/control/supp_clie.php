<?php
/**
 * 合作终端店管理
 *
 *
 */

use Shopnc\Tpl;

defined('InShopNC') or exit('Access Invalid!');
class supp_clieControl extends SCMControl
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
        $this->clientOp();
    }

    /*
     * 合作终端店管理
     */
    public function clientOp()
    {

        Tpl::showpage('supp_clie.index');
    }

    /**
     * 输出XML数据
     * 返回分页数据给flexigrid
     */
    public function get_xmlOp()
    {
        $model_client = SCMModel('supplier_client');
        $result = $this->supp_info;
        $condition['supp_id'] = trim($result['supp_id']);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $field = 'scm_client_order.clie_id,scm_client_order.clie_ch_name,scm_client.clie_tel,clie_mobile,clie_contacter,clie_address';
        //罗列可能要排序的字段并且与前台传递过来的字段相比较
        $sortparam = array('clie_id', 'clie_ch_name');
        if (in_array($_POST['sortname'], $sortparam) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        //每页显示的数据数量
        $page_num = $_POST['rp'];
        $client_list = $model_client->getClientList($condition, 'clie_id', $field, $page_num, $order);
        $data = array();
        $data['now_page'] = $model_client->shownowpage();
        $temp_list = $model_client->gettotalnumon($condition, 'scm_client_order.clie_id', 'scm_client_order,scm_client', 'scm_client_order.clie_id', 'scm_client_order.clie_id = scm_client.clie_id');
        $data['total_num'] = count($temp_list);

        $index = ($data['now_page'] - 1) * $page_num;
        foreach ($client_list as $value) {
            $param = array();
            $index++;
            $param['number'] = $index;
            $param['clie_id'] = $value['clie_id'];
            //获取当前终端店信息
            $client_info = $model_client->getClientInfo(array('clie_id' => $value['clie_id']));
            $param['clie_ch_name'] = $client_info['clie_ch_name'];
            $param['clie_tel'] = $value['clie_tel'];
            $param['clie_mobile'] = $value['clie_mobile'];
            $param['clie_contacter'] = $value['clie_contacter'];
            $param['clie_address'] = $value['clie_address'];
            $data['list'][$value['clie_id']] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }

    /**
     * csv导出
     */
    public function export_csvOp()
    {
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
        if (!is_numeric($_GET['curpage'])) {
            $count = $model_client->getClientCount($condition, 'clie_id');
            if ($count > self::EXPORT_SIZE) {   //显示下载链接
                $array = array();
                $page = ceil($count / self::EXPORT_SIZE);
                for ($i = 1; $i <= $page; $i++) {
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[$i] = $limit1 . ' ~ ' . $limit2;
                }
                Tpl::output('list', $array);
                Tpl::output('murl', 'index.php?act=client&op=index');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage'] - 1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 . ',' . $limit2;
        }
        $client_list = $model_client->getClientList($condition, 'clie_id', $field, null, $order, $limit);
        //clie_order 表没有clie_ch_name,clie_tel,clie_mobile
        foreach ($client_list as $k => $v) {
            $client_info = $model_client->getClientInfo(array('clie_id' => $v['clie_id']));
            $client_list[$k]['clie_ch_name'] = $client_info['clie_ch_name'];
            $client_list[$k]['clie_tel'] = $client_info['clie_tel'];
            $client_list[$k]['clie_mobile'] = $client_info['clie_mobile'];
            $client_list[$k]['clie_contacter'] = $client_info['clie_contacter'];
            $client_list[$k]['clie_address'] = $client_info['clie_address'];
        }
        $this->createExcel($client_list);
    }

    /**
     * 导出CSV文件
     * @param $client_list
     */
    private function createCsv($client_list)
    {
        $data = array();
        foreach ($client_list as $value) {
            $param = array();
            $param['clie_id'] = $value['clie_id'];
            $param['clie_ch_name'] = iconv('utf-8', 'gb2312', $value['clie_ch_name']);
            $param['clie_tel'] = $value['clie_tel'];
            $param['clie_mobile'] = $value['clie_mobile'];
            $param['clie_contacter'] = iconv('utf-8', 'gb2312', $value['clie_contacter']);
            $param['clie_address'] = iconv('utf-8', 'gb2312',$value['clie_address']);
            $data[$value['clie_id']] = $param;
        }
        $header = array(
            "clie_id" => iconv('utf-8', 'gb2312', "终端店编号"),
            "clie_ch_name" => iconv('utf-8', 'gb2312', "终端店名称"),
            "clie_tel" => iconv('utf-8', 'gb2312', "终端店电话"),
            "clie_mobile" => iconv('utf-8', 'gb2312', "终端店手机"),
            "clie_contacter" => iconv('utf-8', 'gb2312', "店主名"),
            "clie_address" => iconv('utf-8', 'gb2312', "地址"),
        );
        \Shopnc\Lib::exporter()->output('client_list' . $_GET['curpage'] . '-' . date('Y-m-d'), $data, $header);
    }

    /**
     * 导出Excel文件
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
        $excel_obj->generateXML('client-'.$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

}