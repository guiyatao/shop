<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
	<div class="fixed-bar">
		<div class="item-title">
			<div class="subject">
				<h3>购物车</h3>
				<h5>显示添加到购物车中的商品，可以直接向供应商下订单</h5>
			</div>
      <?php echo $output['top_link'];?> </div>
	</div>
	<form method="post" name="orderForm" id="orderForm">
		<input type="hidden" name="form_submit" value="ok" />
		<div class="ncap-form-default">
			<table class="flex-table">
				<thead>
					<tr>
						<th width="24" align="center" class="sign"><i class="ico-check"></i></th>
						<th width="60" align="center" class="handle-s"><?php echo $lang['nc_handle'];?></th>
						<th width="100" align="center">商品条码</th>
						<th width="160" align="center">商品名称</th>
						<th width="60" align="center">价格</th>
						<th width="120" align="center">供应商</th>
						<th width="100" align="center">数量</th>
						<th width="80" align="center">金额</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
          <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
          <?php foreach($output['list'] as $val){ ?>
          <tr data-id="<?php echo $val['clie_id']; ?>">
						<td class="sign"><i class="ico-check"></i></td>
						<td class="handle-s"><a
							href="index.php?act=client_purchase&op=del_cart&cart_id=<?php echo $val['cart_id'];?>"
							class="btn red confirm-del"><i class="fa fa-trash-o"></i><?php echo $lang['nc_del'];?></a></td>
						<td class="sort"><span column_id="<?php echo $val['clie_id'];?>"
							title="<?php echo $val['goods_barcode'];?>"><?php echo $val['goods_barcode'];?></span></td>
						<td class="sort"><span column_id="<?php echo $val['clie_id'];?>"
							title="<?php echo $val['goods_name'];?>"><?php echo $val['goods_name'];?></span></td>
						<td class="sort"><span id="price_<?php echo $val['goods_barcode'];?>"
							column_id="<?php echo $val['clie_id'];?>"
							title="<?php echo $val['goods_price'];?>"><?php echo $val['goods_price'];?></span></td>
						<td class="sort"><span column_id="<?php echo $val['clie_id'];?>"
							title="<?php echo $val['supp_id'];?>"><?php echo $val['supp_id'];?></span></td>
						<td class="sort"><input id="num_<?php echo $val['goods_barcode'];?>"
							type="number" min="0" name="goods_num[]"
							style="width: 50px"  title="数量" class="editable"
							value="<?php echo $val['goods_num'];?>" /></td>
						<td class="sort"><span class="unit_price"
							id="unitprice_<?php echo $val['goods_barcode'];?>"
							column_id="<?php echo $val['clie_id'];?>" title="0.00"><?php echo $val['goods_unit_price'];?></span></td>
						<td><input type="hidden" name="supp_id[]" value="<?php echo $val['supp_id'];?>" />
							<input type="hidden" name="goods_barcode[]" value="<?php echo $val['goods_barcode'];?>" />
						</td>

					</tr>
          <?php } ?>
          <?php }else { ?>
          <tr>
						<td class="no-data" colspan="100"><i
							class="fa fa-exclamation-triangle"></i><?php echo $lang['nc_no_record'];?></td>
					</tr>
          <?php } ?>
        </tbody>
			</table>
			<dl class="row">
				<dt class="tit">
					<label for="total_price">已选商品总价</label>
				</dt>
				<dd class="opt red">
					<label id="total_price" for="total_price">0.00</label>
				</dd>
			</dl>

			<dl class="row">
				<dt class="tit">选择支付方式:</dt>
				<dd class="opt">
					<ul class="ncc-payment-list">
						<li payment_code="alipay"><label for="pay_alipay"> <i></i>
								<div for="pay_2" class="logo">
									<img
										src="/shop/templates/default/images/payment/alipay_logo.gif">
								</div>
						</label></li>

					</ul>
				</dd>
			</dl>

			<div class="bot">
				<a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green"
					id="submitBtn">确认支付</a>
			</div>
		</div>
	</form>

</div>
<script type="text/javascript"
	src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js"
	charset="utf-8"></script>
<script type="text/javascript">
$(function(){
    $('.flex-table').flexigrid({
        height:'auto',// 高度自动
        usepager: false,// 不翻页
        striped:false,// 不使用斑马线
        resizable: false,// 不调节大小
        title: '购物车',// 表格标题
        reload: false,// 不使用刷新
        columnControl: false// 不使用列控制
    });

    var total_price = 0;
    $(".unit_price").each(function(index, element){
      total_price += parseFloat(element.innerText);
    });
    $("#total_price").html(total_price.toFixed(2));

    $('a.confirm-del').live('click', function() {
        if (!confirm('确定删除？')) {
            return false;
        }
    });

    $("input[type='number']").bind('input propertychange', function() {  
        var barcode = this.id.split('_')[1];
        $("#unitprice_" + barcode).html((parseFloat($("#price_" + barcode).html()) * this.value).toFixed(2));
        var total_price = 0;
        $(".unit_price").each(function(index, element){
          total_price += parseFloat(element.innerText);
        });
        $("#total_price").html(total_price.toFixed(2));
    });  

    $('.ncc-payment-list > li').on('click',function(){
    	$('.ncc-payment-list > li').removeClass('using');
    	if ($('#payment_code').val() != $(this).attr('payment_code')) {
    		$('#payment_code').val($(this).attr('payment_code'));
    		$(this).addClass('using');
        } else {
            $('#payment_code').val('');
        }
    });
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=client_stock&op=add_goods';
    }
}
function fg_del(id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
        $.getJSON('index.php?act=help_store&op=del_help', {id:id}, function(data){
            if (data.state) {
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}

$(function(){$("#submitBtn").click(function(){
    if($("#orderForm").valid()){
      $("#orderForm").submit();
  }
  });
});
</script>