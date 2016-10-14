<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>订单结算</h3>
                <h5>订单结算及资金流的显示</h5>
            </div>
            <?php echo $output['top_link']; ?>
        </div>
    </div>
    <div id="flexigrid"></div>
</div>
<script>
    $(function () {
        $("#flexigrid").flexigrid({
            url: 'index.php?act=settlement&op=get_unsettlement_xml',
            colModel: [
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '订单号', name : 'order_no', width : 150, sortable : false, align: 'center'},
                {display: '终端店编号', name : 'clie_id', width : 100, sortable : false, align: 'center'},
                {display: '终端店名称', name : 'clie_ch_name', width : 150, sortable : false, align: 'center'},
                {display: '实付款(元)', name : 'order_pay', width : 80, sortable : false, align: 'center'},
                {display: '总额(元)', name : 'total_amount', width : 80, sortable : false, align: 'center'},
                {display: '订货日期', name : 'order_date', width : 120, sortable : true, align: 'center'},
                {display: '订单状态', name : 'order_status', width : 120, sortable : true, align: 'center'},
                {display: '结算状态', name : 'pay_flag', width : 150, sortable : false, align: 'center'},
            ],
            searchitems: [
                {display: '终端店编号', name: 'scm_client_order.clie_id'},
                {display: '终端店名称', name: 'scm_client.clie_ch_name'},
            ],
            title: '未结算订单列表',

        });
    });

</script>