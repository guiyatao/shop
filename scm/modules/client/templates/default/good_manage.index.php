<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>商品管理</h3>
        <h5>该终端店所有库存商品及管理</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
        <li><?php echo "通过终端店商品管理，你可以进行查看、编辑商品资料等操作";?></li>
        <li><?php echo "你可以根据条件搜索商品，然后选择相应的操作";?></li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=good_manage&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 120, sortable : true, align: 'center'},
            {display: '终端店编号', name : 'clie_id', width : 120, sortable : true, align: 'center'},
            {display: '供应商编号', name : 'supp_id', width : 120, sortable : false, align: 'center'},
            {display: '商品条码', name : 'goods_barcode', width: 120, sortable : true, align : 'center'},
            {display: '商品名称', name : 'goods_nm', width : 180, sortable : true, align: 'center'},
            {display: '零售价', name : 'goods_price', width: 80, sortable : true, align : 'center'},
            {display: '库存单位', name : 'goods_unit', width : 60, sortable : true, align: 'center'},
            {display: '商品库存', name : 'goods_stock', width : 80, sortable : true, align: 'center'},
            {display: '库存下限', name : 'goods_low_stock', width : 80, sortable : true, align: 'center'},
            {display: '生产日期', name : 'production_date', width : 120, sortable : false, align: 'center'},
            {display: '有效期提醒天数', name : 'valid_remind', width : 100, sortable : false, align: 'center'},
            {display: '保质期', name : 'shelf_life', width : 60, sortable : false, align: 'center'},
            {display: '滞销期提醒天数', name : 'drug_remind', width : 100, sortable : false, align: 'center'},
            {display: '商品状态', name : 'status', width : 150, sortable : false, align: 'center'},
            ],
        buttons : [
            {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation },
            {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation },
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出Excel文件', onpress : fg_operation }
            ],
        searchitems : [
            {display: '供应商编号', name : 'supp_id'},
            {display: '商品条码', name : 'goods_barcode'},
            {display: '商品名称', name : 'goods_nm'}
            ],
        sortname: "id",
        sortorder: "desc",
        title: '商品列表'
    });

    // 高级搜索提交
    $('#ncsubmit').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=goods&op=get_xml&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
    });

    // 高级搜索重置
    $('#ncreset').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=goods&op=get_xml'}).flexReload();
        $("#formSearch")[0].reset();
    });


});

function fg_operation(name, bDiv) {
    if (name == 'csv') {
        if ($('.trSelected', bDiv).length == 0) {
            if (!confirm('您确定要下载全部数据吗？')) {
                return false;
            }
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_csv(itemids);
    }else if (name == 'add') {
//        _uri = "index.php?act=good_manage&op=goods_add";
//        CUR_DIALOG = ajax_form('goods_add', '新增商品', _uri, 840);
        window.location.href = "index.php?act=good_manage&op=goods_add";
    }else if(name == 'del'){
        if ($('.trSelected', bDiv).length == 0) {
            showError('请选择要操作的数据项！');
        }else{
            var itemids = new Array();
            $('.trSelected', bDiv).each(function(i){
                itemids[i] = $(this).attr('data-id');
            });
            fg_del(itemids);
        }
    }
}

function fg_csv(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&type=<?php echo $output['type'];?>&id=' + id;
}

function fg_sku(commonid) {
    CUR_DIALOG = ajax_form('login','商品"' + commonid +'"的SKU列表','<?php echo urlSCMClient('good_manage', 'get_goods_sku_list');?>&commonid=' + commonid, 480);
}

function goods_edit(goods_id){
//    console.log(goods_id);
//    _uri = "index.php?act=good_manage&op=goods_edit&id="+goods_id;
//    CUR_DIALOG = ajax_form('goods_edit', '编辑商品', _uri, 840);
    window.location.href = "index.php?act=good_manage&op=goods_edit&id="+goods_id;
}
// 删除
function fg_del(id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){

        $.getJSON('index.php?act=good_manage&op=goods_del', {id:id}, function(data){
            if (data.state) {
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}
</script>
