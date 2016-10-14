<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>终端店</h3>
                <h5>终端店管理</h5>
            </div>
            <?php echo $output['top_link'];?>
        </div>
    </div>
    <div id="flexigrid"></div>
</div>
<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url: 'index.php?act=user&op=get_xml_client',
            colModel : [
                {display: '操作', name : 'operation', width : 120, sortable : false, align: 'center'},
                {display: 'ID', name : 'user_id', width : 100, sortable : false, align: 'left'},
                {display: '用户名', name : 'user_name', width : 120, sortable : false, align : 'left'},
                {display: '权限等级', name : 'user_degree', width : 60, sortable : false, align: 'center'},
                {display: '用户编号', name : 'supp_clie_id', width : 60, sortable : false, align: 'center'},
                {display: '用户类型', name : 'user_type', width : 60, sortable : false, align: 'center'},
                {display: '状态', name : 'is_close', width : 60, sortable : false, align: 'center'},
            ],
            buttons : [
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', onpress : fg_operation }

            ],
            title: '终端店列表'
        });
    });

    function fg_operation(name, grid) {
        if (name == 'add') {
            window.location.href = 'index.php?act=user&op=clie_add';
        }
    }
    function fg_operation_del(user_id,user_type){
        console.log("1234354354");
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            var _url = 'index.php?act=user&op=user_del&user_id='+user_id+'&user_type='+user_type;
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