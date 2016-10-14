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
    $(function(){
        $("#flexigrid").flexigrid({
            url: 'index.php?act=supplier&op=get_xml',
            colModel : [
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center'},
                {display: 'ID', name : 'supp_id', width : 100, sortable : false, align: 'left'},
                {display: '英文名字', name : 'supp_en_name', width : 120, sortable : false, align : 'left'},
                {display: '名字', name : 'supp_ch_name', width : 120, sortable : false, align : 'left'},
                {display: '城市', name : 'supp_area', width : 60, sortable : false, align: 'center'},
            ],
            buttons : [
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', onpress : fg_operation }
            ],
            title: '终端店列表'
        });
    });

    function fg_operation(name, grid) {
        if (name == 'add') {
            window.location.href = 'index.php?act=supplier&op=supplier_add';
        }
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