<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>商品审核</h3>
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
                            <select name="status" class="s-select">
                                <option value="">-请选择-</option>
                                <option value="0">失效</option>
                                <option value="1">通过</option>
                                <option value="2">未审核</option>
                                <option value="3">拒绝</option>

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
    $(function(){
        var flexUrl ='index.php?act=supp_stock&op=get_xml';
        $("#flexigrid").flexigrid({
            url:flexUrl ,
            colModel : [
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '状态', name : 'status', width : 50, sortable : false, align: 'center'},
                {display: '供应商编码', name : 'supp_id', width : 80, sortable : true, align: 'center'},
                {display: '供应商名字', name : 'supp_ch_name', width : 80, sortable : true, align: 'center'},
                {display: '商品ID', name : 'id', width : 80, sortable : true, align: 'center'},
                {display: '商品名称', name : 'goods_nm', width : 300, sortable : true, align: 'left'},
                {display: '商品编码', name : 'goods_barcode', width : 100, sortable : false, align: 'center'},
                {display: '商品单价(元)', name : 'goods_price', width : 100, sortable : true, align: 'center'},
                {display: '折扣', name : 'goods_discount', width : 100, sortable : false, align: 'center'},
                {display: '库存单位', name : 'goods_unit', width : 50, sortable : false, align: 'center'},
                {display: '最小配量', name : 'min_set_num', width : 50, sortable : false, align: 'center'},
                {display: '规格', name : 'goods_spec', width : 150, sortable : false, align: 'center'},
                {display: '生产日期', name : 'production_date', width : 150, sortable : false, align: 'center'},
                {display: '有效期提醒天数', name : 'valid_remind', width : 100, sortable : false, align: 'center'},
                {display: '保质期', name : 'shelf_life', width : 80, sortable : false, align: 'center'},


            ],
            buttons : [
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出excel文件,如果不选中行，将导出列表所有数据', onpress : fg_operate }
            ],
            title: '商品列表',
            searchitems: [
                    {display: '供应商编码', name: 'scm_supp_stock.supp_id'},
                    {display: '供应商名字', name: 'supp_ch_name'},
                    {display: '商品名称', name: 'goods_nm'},
                {display: '商品编码', name: 'goods_barcode'}
            ]
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

    function fg_operate(name, grid) {
        if (name == 'csv') {
            var itemlist = new Array();
            if($('.trSelected',grid).length>0){
                $('.trSelected',grid).each(function(){
                    itemlist.push($(this).attr('data-id'));
                });
            }
            fg_csv(itemlist);
        }
    }
    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_step1&id=' + id;
    }
    function fg_operation_del(supp_id){
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            var _url = 'index.php?act=supplier&op=supplier_del&supp_id='+supp_id;
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