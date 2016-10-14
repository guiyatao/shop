<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['stockout_index_brand'];?></h3>
        <h5><?php echo $lang['stockout_subhead'];?></h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div id="flexigrid"></div>
</div>
<script>
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_purchase&op=get_hot_goods_xml',
        colModel : [
            {display: '<?php echo $lang['nc_handle'];?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '商品条码', name : 'admin_name', width : 100, sortable : false, align: 'left'}, 
      			{display: '商品名称', name : 'admin_login_time', width : 180, sortable : false, align : 'left'},           
      			{display: '价格', name : 'admin_login_num', width : 60, sortable : false, align: 'center'},
      			{display: '单位', name : 'gname', width : 80, sortable : false, align: 'left'},
            {display: '规格', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '供应商', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '生产厂家', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '产地', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '最小配量', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '订购数量', name : 'gname', width : 150, sortable : false, align: 'left'}
            ],
        title: '所有商品列表',
        height:'auto',// 高度自动
        usepager: false,// 不翻页
    });
});

function fg_add_cart(goods_id, supp_id) {
  var goods_num = $("#num_" + goods_id).val();
  if (!isNaN(goods_num) && goods_num > 0) {
    window.location.href = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_id + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
  } else {
    alert("请输入要订购的数量!!!");
  }
}
</script>