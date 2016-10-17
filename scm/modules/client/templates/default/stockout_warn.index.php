<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>库存预警</h3>
        <h5>库存低于最低库存设置商品查看</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a class="current" href="JavaScript:void(0);">库存预警</a></li>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
        <li>查询店内所有商品库存预警情况</li>
        <li>红色预警:库存<=库存下限*30%</li>
        <li>黄色预警:库存下限*30%<=库存<=库存下限*70%</li>
        <li>缺货提示:库存<=库存下限</li>
    </ul>
  </div>
  <div id="flexigrid"></div>

</div>
<script type="text/javascript">
$(function(){
    // 高级搜索提交
    $('#ncsubmit').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=bill&op=get_bill_xml&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
    });

    // 高级搜索重置
    $('#ncreset').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=bill&op=get_bill_xml'}).flexReload();
        $("#formSearch")[0].reset();
    });
    $("#flexigrid").flexigrid({
        url: 'index.php?act=stockout_warn&op=get_xml',
        colModel : [
            {display: '终端店编号', name : 'clie_id', width : 120, sortable : true, align: 'center'},
            {display: '商品条码', name : 'goods_barcode', width: 120, sortable : true, align : 'center'},
            {display: '商品名称', name : 'goods_nm', width : 120, sortable : true, align: 'center'},
            {display: '商品原价', name : 'goods_price', width: 80, sortable : true, align : 'center'},
            {display: '商品折扣', name : 'goods_discount', width: 60, sortable : true, align : 'center'},
            {display: '单位', name : 'goods_unit', width : 60, sortable : true, align: 'center'},
            {display: '规格', name : 'goods_spec', width : 60, sortable : true, align: 'center'},
            {display: '商品库存', name : 'goods_stock', width : 80, sortable : true, align: 'center'},
            {display: '库存下限', name : 'goods_low_stock', width : 80, sortable : true, align: 'center'},
            {display: '供应商名称', name : 'supp_ch_name', width : 120, sortable : false, align: 'center'},
            {display: '供应商联系人', name : 'supp_contacter', width : 120, sortable : false, align: 'center'},
            {display: '供应商电话', name : 'supp_tel', width : 120, sortable : false, align: 'center'},
            {display: '供应商手机', name : 'supp_mobile', width : 120, sortable : false, align: 'center'},
            {display: '商品预警状态', name : 'warn_status', width : 90, sortable : true, align : 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出Excel文件,如果不选中行，将导出列表所有数据', onpress : fg_operate}
        ],
        searchitems : [
           {display: '供应商名称', name : 'supp_ch_name'},
		   {display: '商品条码', name : 'goods_barcode'},
           {display: '商品名称', name : 'goods_nm'}
        ],
        sortname: "supp_id",
        sortorder: "desc",
        title: '库存预警列表'
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
    window.location.href = 'index.php?act=stockout_warn&op=export_stockout_warn&id=' + id;
}
</script> 
