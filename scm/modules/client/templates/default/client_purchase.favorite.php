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
  <input type="hidden" value="<?php echo $output['clie_id'];?>" name="clie_id">
  <div id="flexigrid"></div>
</div>
<script>
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_purchase&op=get_favorite_xml',
        colModel : [
            {display: '<?php echo $lang['nc_handle'];?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '商品条码', name : 'admin_name', width : 100, sortable : false, align: 'left'}, 
      			{display: '商品名称', name : 'admin_login_time', width : 180, sortable : false, align : 'left'},           
      			{display: '价格', name : 'admin_login_num', width : 60, sortable : false, align: 'center'},
      			{display: '单位', name : 'gname', width : 80, sortable : false, align: 'left'},
            {display: '规格', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '供应商', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '最小配量', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '订购数量', name : 'gname', width : 60, sortable : false, align: 'left'}
            ],
        title: '缺货列表'
    });
});

function fg_add_cart(goods_id, supp_id) {
  var goods_num = $("#num_" + goods_id).val();
  if (!isNaN(goods_num) && goods_num > 0) {
    window.location.href = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_id + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
    // _uri = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_id + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
    // CUR_DIALOG = ajax_form('add_cart', '添加商品到购物车', _uri, 640);
  } else {
    alert("请输入要订购的数量!!!");
  }
}

function fg_ob_add_cart() {
  var goods_arr = new Array();
  $('input[id^=num_]').each(function(){
    if (parseInt(this.value) > 0) {
      var goods_id = this.id.split('_')[1];
      var supp_id = $('#'+goods_id).attr('name');
      var goods_num = parseInt(this.value);
      var cart_good = {
        "goods_id" : goods_id,
        "supp_id" : supp_id,
        "goods_num" : goods_num,
        "clie_id" : $("input[name='clie_id']").val(),
      };
      goods_arr.push(cart_good);
    }
  });
  $.ajax({
      type: "POST",
      url: 'index.php?act=client_purchase&op=batch_cart',
      data: {'goods_arr': goods_arr},
      dataType: 'json',
      success: function(data){
        console.log(data);
            if (data == null) {
                showError('添加到购物车失败!');
            } else{
                showSucc('添加到购物车成功!');
            };
        }
  });
}
</script>