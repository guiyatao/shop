<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>滞销预警</h3>
        <h5>库存商品滞销查看</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a class="current" href="JavaScript:void(0);">滞销预警</a></li>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>查询店内所有商品滞销情况</li>
      <li>滞销预警:该终端店内的商品在<span style="color: red;">滞销提醒天数</span>之内没有订货</li>
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
        url: 'index.php?act=unsalable_warn&op=get_xml',
        colModel : [
            {display: '终端店编号', name : 'clie_id', width : 120, sortable : true, align: 'center'},
            {display: '商品条码', name : 'goods_barcode', width: 120, sortable : true, align : 'center'},
            {display: '商品名称', name : 'goods_nm', width : 150, sortable : true, align: 'center'},
            {display: '单位', name : 'goods_unit', width : 60, sortable : true, align: 'center'},
            {display: '规格', name : 'goods_spec', width : 80, sortable : true, align: 'center'},
            {display: '商品库存', name : 'goods_stock', width : 80, sortable : true, align: 'center'},
            {display: '滞销提醒天数', name : 'drug_remind', width : 80, sortable : true, align: 'center'},
            {display: '供应商名称', name : 'supp_ch_name', width : 120, sortable : false, align: 'center'},
            {display: '供应商联系人', name : 'supp_contacter', width : 120, sortable : false, align: 'center'},
            {display: '供应商电话', name : 'supp_tel', width : 120, sortable : false, align: 'center'},
            {display: '供应商手机', name : 'supp_mobile', width : 120, sortable : false, align: 'center'}
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
        title: '滞销预警列表'
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
    window.location.href = 'index.php?act=unsalable_warn&op=export_unsalable_warn&id=' + id;
}
</script> 
