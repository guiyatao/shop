<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['supp_index_brand'];?></h3>
        <h5>所有供应商信息列表，供应商信息查看</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="JavaScript:void(0);" class="current"><?php echo '管理';?></a></li>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>显示与当前终端店合作的供应商列表</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=supp_manage&op=get_xml',
        colModel : [
            {display: '供应商ID', name : 'supp_id', width : 100, sortable : true, align: 'left'},
            {display: '供应商名称', name : 'supp_ch_name', width : 120, sortable : true, align: 'left'},
            {display: '地区', name : 'supp_area', width : 120, sortable : true, align: 'left'},
            {display: '详细地址', name : 'supp_address', width : 150, sortable : true, align: 'left'},
            {display: '联系人', name : 'supp_contacter', width : 60, sortable : true, align: 'left'},
            {display: '电话', name : 'supp_tel', width : 120, sortable : true, align: 'left'},
            {display: '手机', name : 'supp_mobile', width : 120, sortable : true, align: 'left'},
            {display: '传真', name : 'supp_tax', width : 120, sortable : true, align: 'left'},
            {display: '备注', name : 'comments', width : 120, sortable : true, align: 'left'},
            {display: '供应商状态', name : 'status', width : 100, sortable : true, align: 'left'},            ],
        searchitems : [
            {display: '供应商ID', name : 'scm_supp_client.supp_id'}
            ],
        sortname: "scm_supp_client.supp_id",
        sortorder: "asc",
        title: '供应商列表'
    });
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=help_store&op=add_help';
    }
}
function fg_del(id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
        $.getJSON('index.php?act=help_store&op=del_help', {id:id}, function(data){
            if (data.state) {
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}
</script>