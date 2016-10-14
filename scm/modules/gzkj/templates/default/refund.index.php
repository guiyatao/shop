<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo $lang['nc_limit_manage'];?></h3>
                <h5><?php echo $lang['nc_limit_manage_subhead'];?></h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div id="flexigrid"></div>
</div>
<script>
//    $(function(){
//        $("#flexigrid").flexigrid({
//            url: 'index.php?act=refund&op=get_xml',
//            colModel : [
//                {display: '退单号', name : 'refund_no', width : 120, sortable : false, align : 'left'},
//                {display: '终端店编码', name : 'clie_id', width : 120, sortable : false, align : 'left'},
//                {display: '终端店名', name : 'clie_ch_name', width : 60, sortable : false, align: 'center'},
//                {display: '供应商编码', name : 'supp_id', width : 120, sortable : false, align : 'left'},
//                {display: '供应商名', name : 'supp_ch_name', width : 60, sortable : false, align: 'center'},
//                {display: '商品条码', name : 'goods_barcode', width : 100, sortable : false, align: 'left'},
//                {display: '商品名', name : 'goods_nm', width : 120, sortable : false, align : 'left'},
//                {display: '原价', name : 'goods_price', width : 120, sortable : false, align : 'left'},
//                {display: '折扣', name : 'goods_discount', width : 120, sortable : false, align : 'left'},
//                {display: '折扣价', name : 'goods_discount_price', width : 60, sortable : false, align: 'center'},
//                {display: '退货数量', name : 'refund_num', width : 100, sortable : false, align: 'left'},
//                {display: '结算金额', name : 'refund_amount', width : 120, sortable : false, align : 'left'}
//
//            ],
//            searchitems : [
//                {display: '终端店编码', name : 'clie_id'},
//                {display: '订单号', name : 'order_no'},
//                {display: '终端店名', name : 'clie_ch_name'}
//                {display: '供应商编码', name : 'supp_id'},
//                {display: '供应商名', name : 'supp_ch_name'}
//            ],
//
//            title: '退单结算列表'
//        });
//    });

$(function () {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=refund&op=get_xml',
        colModel: [
                {display: '退单号', name : 'refund_no', width : 120, sortable : false, align : 'left'},
                {display: '终端店编码', name : 'clie_id', width : 120, sortable : false, align : 'left'},
                {display: '终端店名', name : 'clie_ch_name', width : 60, sortable : false, align: 'center'},
                {display: '供应商编码', name : 'supp_id', width : 120, sortable : false, align : 'left'},
                {display: '供应商名', name : 'supp_ch_name', width : 60, sortable : false, align: 'center'},
                {display: '商品条码', name : 'goods_barcode', width : 100, sortable : false, align: 'left'},
                {display: '商品名', name : 'goods_nm', width : 120, sortable : false, align : 'left'},
                {display: '资金流向', name: 'cash_flow', width: 120, sortable: false, align: 'left'},
                {display: '结算金额', name : 'refund_amount', width : 120, sortable : false, align : 'left'}
        ],
        searchitems: [
            {display: '终端店编码', name: 'clie_id'},
            {display: '订单号', name: 'order_no'},
            {display: '终端店名', name: 'clie_ch_name'},
            {display: '供应商编码', name: 'supp_id'},
            {display: '终端店名', name: 'supp_ch_name'}
        ],

        title: '订单结算列表'
    });
});

    function fg_operation(name, grid) {
        if (name == 'add') {
            window.location.href = 'index.php?act=client&op=client_add';
        }
    }
    function fg_operation_del(id){
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            var _url = 'index.php?act=client&op=client_del&id='+id
            $.getJSON(_url, function(data){
                if (data.state) {
                    $("#flexigrid").flexReload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }
</script>