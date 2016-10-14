<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>未发货订单</h3>
        <h5><?php echo $lang['cancel_order_subhead'];?></h5>
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
        url: 'index.php?act=cancel_order&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 100, sortable : false, align: 'center', className: 'handle'},
            // {display: '订单编号', name : 'order_id', width : 60, sortable : false, align: 'left', hide : true},
            {display: '订单号', name : 'order_no', width : 150, sortable : false, align: 'left'},
			{display: '终端店编号', name : 'clie_id', width : 150, sortable : true, align : 'left'},
			{display: '供应商编号', name : 'supp_id', width : 150, sortable : true, align: 'left'},
			// {display: '商品条形码', name : 'goods_barcode', width : 80, sortable : true, align: 'left'},
			// {display: '商品名称', name : 'goods_nm', width: 80, sortable : true, align : 'left'},
   //          {display: '原价', name : 'goods_price', width : 60, sortable : false, align: 'left'},
			// {display: '折扣', name : 'goods_discount', width: 60, sortable : true, align : 'center'},
			// {display: '折扣单价', name : 'goods_discount_price', width: 60, sortable : true, align : 'left'},
   //          {display: '订购数量', name : 'order_num', width : 60, sortable : true, align: 'center'},
   //          {display: '付款金额', name : 'order_pay', width : 60, sortable : true, align: 'center'},
   //          {display: '税率', name : 'goods_rate', width : 60, sortable : false, align : 'left'},
			// {display: '税金', name : 'goods_tax', width : 60, sortable : true, align: 'center'},
			// {display: '库存', name : 'goods_stock', width: 50, sortable : true, align : 'left'},
   //          {display: '库存下限', name : 'goods_low_stock', width : 50, sortable : true, align: 'center'},
   //          {display: '赠品条码', name : 'gift_barcode', width : 80, sortable : true, align: 'center'},
			// {display: '赠品名称', name : 'gift_nm', width : 80, sortable : true, align: 'left'},
			// {display: '赠品数量', name : 'gift_num', width : 80, sortable : true, align: 'center'},
            {display: '订单日期', name : 'order_date', width : 150, sortable : true, align: 'center'},
   //          {display: '订单有效期', name : 'valid_date', width : 120, sortable : true, align: 'center'},
   //          {display: '出货日期', name : 'out_date', width : 120, sortable : true, align: 'center'},
   //          {display: '入库日期', name : 'in_date', width : 120, sortable : true, align: 'center'},
   //          {display: '付款日期', name : 'pay_date', width : 120, sortable : true, align: 'center'},
   //          {display: '周期订单标志', name : 'cycle_flag', width : 80, sortable : true, align: 'center'},
   //          {display: '周期天数', name : 'cycle_num', width : 80, sortable : true, align: 'center'},
   //          {display: '预警标志', name : 'warn_flag', width : 50, sortable : true, align: 'center'},
   //          {display: '订货标志', name : 'order_flag', width : 50, sortable : true, align: 'center'},
   //          {display: '发货标志', name : 'out_flag', width : 50, sortable : true, align: 'center'},
          {display: '付款金额', name : 'order_pay', width : 50, sortable : true, align: 'center'},
          {display: '备货状态', name : 'prepare_flag', width : 100, sortable : true, align: 'center'},
			// {display: '备注', name : 'comments', width : 120, sortable : true, align: 'left'}
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
        title: '未发货订单列表'
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

function fg_cancel_order(order_no) {
    _uri = "index.php?act=cancel_order&op=order_cancel&order_no=" + order_no;
    CUR_DIALOG = ajax_form('cancel_order', '取消订单', _uri, 640);
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
