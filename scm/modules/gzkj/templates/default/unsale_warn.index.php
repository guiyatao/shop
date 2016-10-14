<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>滞销预警</h3>
        <h5>库存商品滞销查看</h5>
      </div>
    </div>
  </div>

  <div id="flexigrid"></div>

</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=unsale_warn&op=get_xml',
        colModel : [
            {display: '序号', name : 'number', width : 80, align: 'center'},
            {display: '终端店编号', name : 'clie_id', width : 80, align: 'center'},
            {display: '终端店名称', name : 'clie_ch_name', width : 80,  align: 'center'},
            {display: '终端店联系人', name : 'clie_contacter', width : 80,  align: 'center'},
            {display: '终端店手机', name : 'clie_mobile', width : 80,  align: 'center'},
            {display: '终端店电话', name : 'clie_tel', width : 80,  align: 'center'},
			{display: '商品条码', name : 'goods_barcode', width: 80,  align : 'center'},
            {display: '商品名称', name : 'goods_nm', width : 80,  align: 'center'},
            {display: '单位', name : 'goods_unit', width : 60,  align: 'center'},
            {display: '规格', name : 'goods_spec', width : 60,  align: 'center'},
            {display: '库存', name : 'goods_stock', width : 120,  align: 'center'},
            {display: '库存上限', name : 'goods_uper_stock', width : 90,  align : 'center'},
            {display: '供应商名称', name : 'supp_ch_name', width : 80,  align: 'center'},
            {display: '供应商联系人', name : 'supp_contacter', width : 80,  align: 'center'},
            {display: '供应商电话', name : 'supp_tel', width : 80,  align: 'center'},
            {display: '供应商手机', name : 'supp_moblie', width : 80,  align: 'center'},
            {display: '最后一次进货时间', name : 'last_time', width : 80,  align: 'center'},
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出csv文件,如果不选中行，将导出列表所有数据', onpress : fg_operate}
        ],
        searchitems: [
            {display: '终端店ID', name: 'clie_id', isdefault: true},
        ],
        title: '滞销预警列表',
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
    window.location.href = 'index.php?act=unsale_warn&op=export_unsale_warn&id=' + id;
}
</script> 
