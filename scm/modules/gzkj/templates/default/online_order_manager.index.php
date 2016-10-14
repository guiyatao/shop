<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>商城订单</h3>
                <h5>商城订单及详情显示</h5>
            </div>
        </div>
    </div>
    <div id="flexigrid"></div>
        <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
        <div class="ncap-search-bar">
            <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
            <div class="title">
                <h3>高级搜索</h3>
            </div>
            <form method="get" name="formSearch" id="formSearch">
                <input type="hidden" name="advanced" value="1" />
                <div id="searchCon" class="content">
                    <div class="layout-box">
                        <dl>
                            <dt>处理状态</dt>
                            <dd>
                                <select name="order_state" class="s-select">
                                    <option value="">-请选择-</option>
                                    <option value="0">已取消 </option>
                                    <option value="10">未付款</option>
                                    <option value="20">已付款</option>
                                    <option value="30">已发货</option>
                                    <option value="40">已收货</option>
                                </select>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bottom"> <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a> <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a> </div>
            </form>
        </div>
    </div>
    <script>
        $(function () {
            var flexUrl = 'index.php?act=online_order_manager&op=get_xml';
            $("#flexigrid").flexigrid({
                url:flexUrl,
                colModel: [
                    {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center'},
                    {display: '订单号', name: 'order_sn', width: 120, sortable: false, align: 'left'},
                    {display: '订单总价格', name: 'order_amount', width: 120, sortable: false, align: 'left'},
                    {display: '分单个数', name: 'order_num', width: 120, sortable: false, align: 'left'},
                    {display: '买家id', name: 'buyer_id', width: 120, sortable: false, align: 'left'},
                    {display: '买家姓名', name: 'buyer_name', width: 120, sortable: false, align: 'left'},
                    {display: '买家电子邮箱', name: 'buyer_email', width: 120, sortable: false, align: 'left'},
                    {display: '买家手机', name: 'buyer_phone', width: 120, sortable: false, align: 'left'},
                    {display: '订单生成时间', name: 'add_time', width: 120, sortable: false, align: 'left'},
                    {display: '订单状态', name: 'order_state', width: 120, sortable: false, align: 'left'},
//                    {display: '支付方式', name: 'payment_code', width: 120, sortable: false, align: 'left'},

                ],
                searchitems: [
                    {display: '终端店编码', name: 'clie_id'},
                    {display: '订单号', name: 'order_no'},
                    {display: '终端店名', name: 'clie_ch_name'},
                ],

                title: '商城订单列表'
            });
            // 高级搜索提交
            $('#ncsubmit').click(function(){
                $("#flexigrid").flexOptions({url: flexUrl + '&' + $("#formSearch").serialize(),query:'',qtype:''}).flexReload();
            });

            // 高级搜索重置
            $('#ncreset').click(function(){
                $("#flexigrid").flexOptions({url: flexUrl}).flexReload();
                $("#formSearch")[0].reset();
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
//        function fg_sku(id) {
//            _uri = "index.php?act=online_order&op=show_goods&id=" + id;
//            CUR_DIALOG = ajax_form('hehhe','订单"' + id +'"的商品列表',_uri, 480);
//        }
        function fg_sku1(order_id) {
            window.location.href = 'index.php?act=online_order_manager&op=show_orders&order_id='+order_id;
        }
    </script>