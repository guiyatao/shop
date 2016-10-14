<?php
/**
 * 退单结算
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
class refundControl extends SCMControl
{

    public function __construct()
    {
        parent::__construct();
    }
    public function indexOp()
    {
        return $this->showOp();
    }

    /**
     * 显示
     */
    public function showOp()
    {
        Tpl::showpage('refund.index');
    }

    public function get_xmlOp()
    {
        $refund = SCMModel('gzkj_client_refund');
        if (strlen($q = trim($_REQUEST['query'])) > 0) {
            switch ($_REQUEST['qtype']) {
                case 'refund_no':
                    $condition['refund_no'] = $q;
                    break;
                case 'clie_id':
                    $condition['clie_id'] = $q;
                    break;

                case'clie_ch_name':
                    $condition['clie_ch_name'] = $q;
                    break;
                case 'supp_id':
                    $condition['supp_id'] = $q;
                    break;
                case'supp_ch_name':
                    $condition['supp_ch_name'] = $q;
                    break;
            }
        }
        $condition['refund_flag']=1;
        $refunds = $refund->field('id,refund_no,clie_id,clie_ch_name,supp_id,supp_ch_name,goods_barcode,goods_nm,sum(refund_amount) as total')->where($condition)->group('refund_no')->page($_POST['rp'])->select();

        $data = array();
        $data['now_page'] = $refund->shownowpage();
        $data['total_num'] = $refund->gettotalnum();
        foreach ($refunds as $k => $info) {
            $list = array();
//            $list['operation'].= '<a class="btn red" href="javascript:fg_operation_del('.$info['id'].');"><i class="fa fa-trash-o"></i>删除</a>';
//            $list['operation'].= '<a class="btn blue" href="index.php?act=client&op=client_edit&id='.$info['id'].'"><i class="fa fa-pencil-square-o"></i>'.L('nc_edit').'</a>';
//            $list['id'] = $info['id'];

            $list['refund_no'] = $info['refund_no'];
            $list['clie_id'] = $info['clie_id'];
            $list['clie_ch_name'] = $info['clie_ch_name'];
            $list['supp_id'] = $info['supp_id'];
            $list['supp_ch_name'] = $info['supp_ch_name'];
            $list['goods_barcode'] = $info['goods_barcode'];
            $list['goods_nm'] = $info['goods_nm'];
            $list['cash_flow']='共铸科技->终端店';
            $list['refund_amount'] = $info['total'];
            $data['list'][$info['id']] = $list;
        }
        echo Tpl::flexigridXML($data);
        exit();

    }


}
