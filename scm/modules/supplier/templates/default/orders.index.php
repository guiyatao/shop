<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>合作终端店订单管理</h3>
                <h5>显示合作终端店提交的商品订单</h5>
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
            <li><?php echo "您可以查看合作终端店已提交的订货单";?></li>
            <li><?php echo "你可以根据条件搜索合作终端店提交的订单，然后选择相应的操作";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=client_order&op=get_xml',
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '订单号', name : 'order_no', width : 150, sortable : false, align: 'center'},
                {display: '终端店编号', name : 'clie_id', width : 100, sortable : false, align: 'center'},
                {display: '终端店名称', name : 'clie_ch_name', width : 150, sortable : false, align: 'center'},
                {display: '实付款(元)', name : 'order_pay', width : 80, sortable : false, align: 'center'},
                {display: '总额(元)', name : 'total_amount', width : 80, sortable : false, align: 'center'},
                {display: '订货日期', name : 'order_date', width : 120, sortable : true, align: 'center'},
                {display: '订单状态', name : 'order_status', width : 120, sortable : false, align: 'center'},
                {display: '备货状态', name : 'prepare_flag', width : 120, sortable : false, align: 'center'},
                {display: '发货状态', name : 'out_flag', width : 120, sortable : false, align: 'center'},
            ],
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出Excel文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '终端店名', name : 'scm_client.clie_ch_name'},
                {display: '订单号', name : 'order_no'},
            ],
            sortname:"order_date",
            sortorder:"desc",
            title:"订单列表",

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
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_step1&id=' + id;
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
