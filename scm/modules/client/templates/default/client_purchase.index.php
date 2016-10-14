<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['stockout_index_brand'];?></h3>
        <h5>显示当前终端店的缺货商品列表，选中商品可以添加到购物车中。</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <input type="hidden" value="<?php echo $output['clie_id'];?>" name="clie_id">
  <div id="flexigrid"></div>
</div>
<script>
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_purchase&op=get_stockout_xml',
        colModel : [
            {display: '<?php echo $lang['nc_handle'];?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '商品条码', name : 'admin_name', width : 100, sortable : false, align: 'left'}, 
            {display: '商品名称', name : 'admin_login_time', width : 180, sortable : false, align : 'left'},
            {display: '价格', name : 'admin_login_num', width : 60, sortable : false, align: 'center'},
            {display: '批发单位', name : 'gname', width : 80, sortable : false, align: 'left'},
            {display: '规格', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '供应商', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '最小配量', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '库存', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '订购数量', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '商品状态', name : 'status', width : 160, sortable : false, align: 'left'}
            ],
        buttons : [
            {display: '<i class="fa fa-list-alt"></i>一键购买', name : 'ob', bclass : 'csv', title : '将当前页面所有填入数量的商品添加到购物车', onpress : fg_ob_add_cart }
        ],
        title: '缺货列表'
    });

});

function fg_add_cart(button, goods_id) {
    if( $(button).attr("disabled") != "disabled"){
        var goods_num = $("#num_" + goods_id).val();
        var min_set_num = $("#num_" + goods_id).parent().parent().prev().prev().children().text();
        var supp_id = $("#num_" + goods_id).parent().parent().prev().prev().prev().children().text();
        if (!isNaN(goods_num) && goods_num > 0) {
            if(parseInt(goods_num) >= parseInt(min_set_num)){
                window.location.href = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_id + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
            } else{
                alert("订购数量不能小于最小配量!!!");
            }
            // _uri = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_id + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
            // CUR_DIALOG = ajax_form('add_cart', '添加商品到购物车', _uri, 640);
        } else {
            alert("请输入要订购的数量!!!");
        }
    }
}

function change_supp(select){
    console.log($(select).attr('value'));
    if($(select).attr('value') == '0'){
        $(select).parent().parent().prev().children().children(":input").val('');
        $(select).parent().parent().prev().children().children(":input").attr({"disabled":true});
        $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().prev().children().children("a").attr({"style":"background-color:#EEEEEE","disabled":"disabled" });
        $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().prev().children().children("a").removeClass('blue');
    }else{
        $.post(
            "index.php?act=client_purchase&op=ajax&branch=get_goods_by_id",
            {goods_id:$(select).attr('value') },
            function(data){
                $(select).parent().parent().prev().prev().prev().children().text(data.min_set_num) ;
                $(select).parent().parent().prev().prev().prev().prev().children().text(data.supp_id);
                $(select).parent().parent().prev().prev().prev().prev().prev().children().text(data.goods_spec);
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().children().text(data.goods_unit);
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().children().text(data.goods_price*data.goods_discount);
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().children().text(data.goods_nm);

                $(select).parent().parent().prev().children().children(":input").removeAttrs('disabled');
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().prev().children().children("a").attr({"style":"white"});
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().prev().children().children("a").removeAttrs('disabled');
                $(select).parent().parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().prev().children().children("a").addClass('blue');

            },
            "json"
        );

    }
}

function fg_ob_add_cart() {
    var goods_arr = new Array();
    var flag = true;
    $('input[id^=num_]').each(function(){
        var min_set_num = $(this).parent().parent().prev().prev().children().text();
        if (parseInt(this.value) > 0 && parseInt(this.value) >= parseInt(min_set_num)) {
            var goods_id = this.id.split('_')[1];
            var supp_id = $(this).parent().parent().prev().prev().prev().children().text();
            var goods_num = parseInt(this.value);
            var cart_good = {
            "goods_id" : goods_id,
            "supp_id" : supp_id,
            "goods_num" : goods_num,
            "clie_id" : $("input[name='clie_id']").val(),
            };
            goods_arr.push(cart_good);
        }else if(parseInt(this.value) > 0 && parseInt(this.value) < parseInt(min_set_num)){
            flag = false;

        }
    });
    console.log(goods_arr);
    if(flag == false){
        goods_arr = new Array();
        alert("订购数量不能小于最小配量!!!");
    }
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