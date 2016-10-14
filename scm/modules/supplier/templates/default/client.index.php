<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "预警详情";?></h3>
                <h5><?php echo "合作终端店列表以及显示各个终端店是否有预警信息";?></h5>
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
            <li><?php echo "您可以查看合作终端店的详细";?></li>
            <li><?php echo "如果终端店有预警信息，该终端店下的按钮颜色为黄色，点击查看预警详情";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=client&op=get_xml',
            colModel:[
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '终端店ID', name : 'clie_id', width : 120, sortable : true, align: 'center'},
                {display: '终端店名称', name : 'clie_ch_name', width : 180, sortable : true, align: 'center'},
                {display: '终端店电话', name : 'clie_tel', width : 100, sortable : false, align: 'center'},
                {display: '终端店手机', name : 'clie_mobile', width : 150, sortable : false, align: 'center'},
                {display: '店主名', name : 'clie_contacter', width : 100, sortable : false, align: 'center'},
                {display: '地址', name : 'clie_address', width : 150, sortable : false, align: 'left'},
                {display: '近效期预警', name : 'validation_warn', width : 100, sortable : false, align: 'center'},
                {display: '缺货预警', name : 'unavailable_warn', width : 100, sortable : false, align: 'center'},
                {display: '滞销预警', name : 'unsalable_warn', width : 100, sortable : false, align: 'center'},

            ],
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '终端店ID', name : 'scm_client.clie_id'},
                {display: '终端店名', name : 'scm_client.clie_ch_name'},
            ],
            sortname:"number",
            sortorder:"asc",
            title:"预警详情",


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
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&id=' + id;
    }

</script>
