<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=online_order_manager&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>订单列表</h3>
                <h5>订单详情显示</h5>
            </div>

        </div>
    </div>
    <div id="flexigrid"></div>
</div>

    <script>
        $(function () {
            $("#flexigrid").flexigrid({
                url: 'index.php?act=online_order_manager&op=get_order_xml&order_id=<?=$output['order_id']?>',
                colModel: [
                    {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center'},
                    {display: '订单号', name: 'order_no', width: 120, sortable: false, align: 'left'},
                    {display: '终端店编码', name: 'clie_id', width: 120, sortable: false, align: 'left'},
                    {display: '终端店名', name: 'clie_ch_name', width: 60, sortable: false, align: 'center'},
                    {display: '买家姓名', name: 'buyer_name', width: 60, sortable: false, align: 'center'},
                    {display: '买家手机', name: 'buyer_phone', width: 60, sortable: false, align: 'center'},
                    {display: '买家地址', name: 'buyer_address', width: 60, sortable: false, align: 'center'},
                ],
//                searchitems: [
//                    {display: '终端店编码', name: 'clie_id'},
//                    {display: '订单号', name: 'order_no'},
//                    {display: '终端店名', name: 'clie_ch_name'},
//                    {display: '供应商编码', name: 'supp_id'},
//                    {display: '终端店名', name: 'supp_ch_name'}
//                ],

                title: '订单列表',
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

        function fg_sku(order_id,order_no) {
            console.log(order_no);
            _uri = "index.php?act=online_order_manager&op=show_goods&order_id=" +order_id;
            CUR_DIALOG = ajax_form('hehhe','订单"' + order_no +'"的商品列表',_uri, 840);
        }
//        function fg_sku1(supp_id) {
//            _uri = "index.php?act=order&op=show_orders&supp_id=" + supp_id;
//            CUR_DIALOG = ajax_form('hehhe','供应商"' + supp_id +'"的订单列表',_uri, 480);
//        }
    </script>