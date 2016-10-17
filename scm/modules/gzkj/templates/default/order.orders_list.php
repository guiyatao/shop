<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">

        <div class="item-title">
            <a class="back" href="index.php?act=order&op=show_flow" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>订单结算</h3>
                <h5>订单结算及资金流的显示</h5>
            </div>
        </div>
    </div>
    <div id="flexigrid"></div>

</div>

    <script>
        $(function () {
            $("#flexigrid").flexigrid({
                url: 'index.php?act=order&op=get_order_xml&settlement_id=<?=$output['settlement_id']?>&clie_id=<?=$output['clie_id']?>',
                colModel: [
                    {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center'},
                    {display: '终端店编码', name: 'clie_id', width: 120, sortable: false, align: 'left'},
                    {display: '订单号', name: 'order_no', width: 120, sortable: false, align: 'left'},
                    {display: '终端店名', name: 'clie_ch_name', width: 60, sortable: false, align: 'center'},
                    {display: '供应商编码', name: 'supp_id', width: 120, sortable: false, align: 'left'},
                    {display: '供应商名', name: 'supp_ch_name', width: 60, sortable: false, align: 'center'},
                    {display: '结算金额', name: 'order_pay', width: 120, sortable: false, align: 'left'},
                    {display: '结算日期', name: 'time', width: 120, sortable: false, align: 'left'},

                ],
//                searchitems: [
//                    {display: '终端店编码', name: 'clie_id'},
//                    {display: '订单号', name: 'order_no'},
//                    {display: '终端店名', name: 'clie_ch_name'},
//                    {display: '供应商编码', name: 'supp_id'},
//                    {display: '终端店名', name: 'supp_ch_name'}
//                ],

                title: '订单结算列表',
//                rpOptions: [1,2],
//                rp :1,
            });
        });

        function fg_operation(name, grid) {
            if (name == 'add') {
                console.log("1234354354");
                window.location.href = 'index.php?act=client&op=client_add';
            }
        }
        function fg_operation_del(id) {
            if (confirm('删除后将不能恢复，确认删除这项吗？')) {
                var _url = 'index.php?act=client&op=client_del&id=' + id
                $.getJSON(_url, function (data) {
                    if (data.state) {
                        $("#flexigrid").flexReload();
                    } else {
                        showError(data.msg)
                    }
                });
            }
        }

        function fg_sku(id) {
            _uri = "index.php?act=order&op=show_goods&id=" + id;
            CUR_DIALOG = ajax_form('hehhe','订单"' + id +'"的商品列表',_uri, 480);
        }
    </script>