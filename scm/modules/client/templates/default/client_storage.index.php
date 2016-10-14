<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>已发货订单</h3>
        <h5><?php echo $lang['client_order_help1'];?></h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li> 页面显示已经发货的订单，在收到商品之后可以进行入库操作。</li>
      <li> 可以进行退货操作。</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_storage&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 200, sortable : false, align: 'center'},
            {display: '订单编号', name : 'order_no', width : 150, sortable : true, align : 'center'},
			{display: '终端店编号', name : 'clie_id', width : 120, sortable : true, align : 'center'},
			{display: '供应商编号', name : 'supp_id', width : 120, sortable : true, align: 'center'},
            {display: '付款金额', name : 'order_pay', width : 60, sortable : true, align: 'center'},
            {display: '订单日期', name : 'order_date', width : 120, sortable : true, align: 'center'},
            {display: '出货日期', name : 'out_date', width : 120, sortable : true, align: 'center'},
            {display: '退货状态', name : 'refund_flag', width : 120, sortable : true, align: 'center'},
			{display: '备注', name : 'comments', width : 160, sortable : true, align: 'left'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出excel文件,如果不选中行，将导出列表所有数据', onpress : fg_operate }
        ],
        searchitems : [
            {display: '订单编号', name : 'order_no', isdefault: true},
            {display: '商品条形码', name : 'goods_barcode'},
            {display: '商品名称', name : 'goods_nm'},
            {display: '供应商编号', name : 'supp_id'}
            ],
        sortname: "order_date",
        sortorder: "desc",
        title: '已发货订单列表'
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

function fg_storage_order(order_id, order_no) {
    _uri = "index.php?act=client_storage&op=storage_confirm&order_id=" + order_id + "&order_no=" + order_no;
    CUR_DIALOG = ajax_form('storage_order', '商品入库确认', _uri, 640);
}

function fg_refund_order(order_no) {
    _uri = "index.php?act=client_storage&op=order_refund&order_no=" + order_no;
    CUR_DIALOG = ajax_form('storage_order', '订单退货确认', _uri, 640);
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
