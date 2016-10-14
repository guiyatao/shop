<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>合作终端店退货单管理</h3>
                <h5>显示终端店全部的退货单</h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span>
        </div>
        <ul>
            <li><?php echo "您可以查看合作终端店所有的退货单状态";?></li>
            <li><?php echo "你可以根据条件搜索合作终端店提交的退货单，然后选择相应的操作";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=all_delivered_refund&op=get_xml',
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '退单号', name : 'refund_no', width : 150, sortable : false, align: 'center'},
                {display: '终端店编号', name : 'clie_id', width : 80, sortable : false, align: 'center'},
                {display: '终端店名称', name : 'clie_ch_name', width : 80, sortable : false, align: 'center'},
                {display: '供应商编号', name : 'clie_id', width : 80, sortable : false, align: 'center'},
                {display: '终端店名称', name : 'clie_ch_name', width : 80, sortable : false, align: 'center'},
                {display: '退款总额(元)', name : 'total', width : 80, sortable : false, align: 'center'},
                {display: '退货日期', name : 'refund_date', width : 120, sortable : true, align: 'center'},
            ],
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '终端店名', name : 'clie_ch_name'},
                {display: '退单号', name : 'refund_no'},
            ],
            sortname:"refund_date",
            sortorder:"desc",
            title:"全部退货单列表"
        });
    });

    function fg_operation(name, bDiv) {
        if (name == 'csv') {
            if ($('.trSelected', bDiv).length == 0) {
                if (!confirm('您确定要下载全部数据吗？')) {
                    return false;
                }
            }
            var itemids = new Array();
            $('.trSelected', bDiv).each(function(i){
                itemids[i] = $(this).attr('data-id');
            });
            fg_csv(itemids);
        }else if(name == 'del'){
            if ($('.trSelected', bDiv).length == 0) {
                showError('请选择要操作的数据项！');
            }else{
                var itemids = new Array();
                $('.trSelected', bDiv).each(function(i){
                    itemids[i] = $(this).attr('data-id');
                });
                fg_del(itemids);
            }
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&id=' + id;
    }
    function fg_del(ids) {
        if (typeof ids == 'number') {
            var ids = new Array(ids.toString());
        };
        id = ids.join(',');
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            $.getJSON('index.php?act=goods&op=goods_del', {id:id}, function(data){
                if (data.state) {
                    location.reload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }
</script>
