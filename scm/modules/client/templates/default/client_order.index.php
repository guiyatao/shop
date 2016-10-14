<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>批发订单结算</h3>
        <h5>订单结算及资金流的显示</h5>
      </div>
        <?php echo $output['top_link']; ?>
    </div>
  </div>
    <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_order&op=get_xml',
        colModel : [
            {display: '终端店编号', name : 'clie_id', width : 100, sortable : true, align : 'left'},
            {display: '订单号', name : 'order_no', width : 150, sortable : false, align: 'left'},
			{display: '供应商编号', name : 'supp_id', width : 80, sortable : true, align: 'left'},
            {display: '付款金额(元)', name : 'order_pay', width : 80, sortable : true, align: 'center'},
            {display: '订单日期', name : 'order_date', width : 120, sortable : true, align: 'center'},
            {display: '订单状态', name : 'order_status', width : 80, sortable : false, align: 'left'},
            {display: '结算状态', name : 'pay_flag', width : 80, sortable : false, align: 'left'},
            {display: '资金流向', name : 'cash_flow', width : 150, sortable : false, align: 'left'},
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出excel文件,如果不选中行，将导出列表所有数据', onpress : fg_operate }
        ],
        searchitems : [
            {display: '订单编号', name : 'order_no', isdefault: true},
            ],
        sortname: "order_date",
        sortorder: "desc",
        title: '已结算订单'
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
