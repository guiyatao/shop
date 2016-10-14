<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "资金表管理";?></h3>
                <h5><?php echo "当前供应商拥有的银行卡及管理";?></h5>
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
            <li><?php echo "通过供应商资金表管理，你可以进行查看、编辑当前供应商所有的银行卡等操作";?></li>
            <li><?php echo "你可以根据条件搜索银行卡信息，然后选择相应的操作";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=capital&op=get_xml',
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '资金表ID', name : 'capital_id', width : 80, sortable : true, align: 'center'},
                {display: '开户行', name : 'supp_bank', width : 150, sortable : false, align: 'center'},
                {display: '卡号', name : 'supp_cardno', width : 150, sortable : false, align: 'center'},
                {display: '资金(元)', name : 'supp_capital', width : 150, sortable : false, align: 'center'},
            ],
            buttons:[
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation },
                {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation },
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '资金表ID', name : 'capital_id'},
                {display: '开户行', name : 'supp_bank'},
            ],
            sortname:"capital_id",
            sortorder:"asc",
            title:"资金列表",

        });
    });

    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=capital&op=capital_add';
        }else if (name == 'csv') {
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
            $.getJSON('index.php?act=capital&op=capital_del', {id:id}, function(data){
                if (data.state) {
                    location.reload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }
</script>
