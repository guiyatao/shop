<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>待付款订单</h3>
        <h5><?php echo $lang['client_pending_pay_subhead'];?></h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li><?php echo $lang['client_order_help1'];?></li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_pending_pay&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 100, sortable : false, align: 'center', className: 'handle'},
            {display: '订单号', name : 'order_no', width : 150, sortable : false, align: 'left'},
			{display: '终端店编号', name : 'clie_id', width : 150, sortable : true, align : 'left'},
			{display: '供应商编号', name : 'supp_id', width : 150, sortable : true, align: 'left'},
            {display: '订单日期', name : 'order_date', width : 150, sortable : true, align: 'center'},
            {display: '付款金额', name : 'order_pay', width : 50, sortable : true, align: 'center'},
            {display: '支付', name : 'start_payment', width : 50, sortable : true, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出excel文件,如果不选中行，将导出列表所有数据', onpress : fg_operate }
        ],
        searchitems : [
            {display: '订单编号', name : 'order_no', isdefault: true},
            {display: '供应商编号', name : 'supp_id'}
            ],
        sortname: "order_date",
        sortorder: "desc",
        title: '待付款订单列表'
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
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_step1&order_id=' + id;
}

function fg_client_pending_pay(order_no) {
    _uri = "index.php?act=client_pending_pay&op=order_cancel_confirm&order_no=" + order_no;
    CUR_DIALOG = ajax_form('client_pending_pay', '取消订单', _uri, 640);
}

function fg_cancel(id) {
	if (typeof id == 'number') {
    	var id = new Array(id.toString());
	};
	if(confirm('取消后将不能恢复，确认取消这 ' + id.length + ' 项吗？')){
		id = id.join(',');
	} else {
        return false;
    }
	$.ajax({
        type: "GET",
        dataType: "json",
        url: "index.php?act=order&op=change_state&state_type=cancel",
        data: "order_id="+id,
        success: function(data){
            if (data.state){ 
                $("#flexigrid").flexReload();
            } else {
            	alert(data.msg);
            }
        }
    });
}
</script> 
