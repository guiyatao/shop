<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
      <div class="item-title">
        <div class="subject">
          <h3>批发订货</h3>
          <h5>显示所有供应商商品列表，选中商品可以添加到购物车中。</h5>
        </div>
      <?php echo $output['top_link'];?> </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li><?php echo "单位数量的含义是批发单位按照商品规格拆分的商品数量";?></li>
            <li><?php echo "最小配量的单位是批发单位";?></li>
        </ul>
    </div>

    <input type="hidden" value="<?php echo $output['clie_id'];?>" name="clie_id">
  <div id="flexigrid"></div>
</div>
<script>
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=client_purchase&op=get_all_goods_xml',
        colModel : [
            {display: '<?php echo $lang['nc_handle'];?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '商品条码', name : 'admin_name', width : 100, sortable : false, align: 'left'}, 
            {display: '商品名称', name : 'admin_login_time', width : 180, sortable : false, align : 'left'},
            {display: '价格', name : 'admin_login_num', width : 60, sortable : false, align: 'center'},
            {display: '批发单位', name : 'gname', width : 50, sortable : false, align: 'left'},
            {display: '单位数量', name : 'unit_num', width : 50, sortable : false, align: 'left'},
            {display: '规格', name : 'gname', width : 100, sortable : false, align: 'left'},
            {display: '供应商', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '生产厂家', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '产地', name : 'gname', width : 120, sortable : false, align: 'left'},
            {display: '最小配量', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '商城有无', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '新商品', name : 'gname', width : 60, sortable : false, align: 'left'},
            {display: '订购数量', name : 'gname', width : 150, sortable : false, align: 'left'}
        ],
        searchitems : [
            {display: '商品条码', name : 'goods_barcode'},
            {display: '商品名称', name : 'goods_nm'},
            {display: '供应商编号', name : 'supp_id'}
        ],
        buttons : [
            {display: '<i class="fa fa-list-alt"></i>一键购买', name : 'ob', bclass : 'csv', title : '将当前页面所有填入数量的商品添加到购物车', onpress : fg_ob_add_cart }
        ],
        title: '所有商品列表'
    });
});

function fg_add_cart(id,goods_barcode, supp_id) {
    var goods_num = $('#num_'+id).val();
    var min_set_num = $('#num_'+id).parent().parent().prev().prev().prev().children().text();

    if (!isNaN(goods_num) && goods_num > 0) {
        if(parseInt(goods_num) >= parseInt(min_set_num)) {
            window.location.href = 'index.php?act=client_purchase&op=add_cart&goods_id=' + goods_barcode + "&goods_num=" + goods_num + "&supp_id=" + supp_id;
        } else{
            alert("订购数量不能小于最小配量!!!");
        }
    } else {
        alert("请输入要订购的数量!!!");
    }
}

function fg_ob_add_cart() {
    var goods_arr = new Array();
    var flag = true;
    $('input[id^=num_]').each(function(){
        var min_set_num = $(this).parent().parent().prev().prev().prev().children().text();
        if (parseInt(this.value) > 0 && parseInt(this.value) >= parseInt(min_set_num)) {
            var goods_barcode = $(this).attr('goods_barcode');
            var supp_id = $(this).parent().parent().prev().prev().prev().prev().prev().prev().children().text();
            var goods_num = parseInt(this.value);
            var cart_good = {
                "goods_id" : goods_barcode,
                "supp_id" : supp_id,
                "goods_num" : goods_num,
                "clie_id" : $("input[name='clie_id']").val(),
            };
            goods_arr.push(cart_good);
        }else if( parseInt(this.value) > 0 && parseInt(this.value) < parseInt(min_set_num)) {
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