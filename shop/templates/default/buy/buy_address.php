<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="ncc-receipt-info">
  <div class="ncc-receipt-info-title">
    <h3>收货人信息</h3>
    <a href="javascript:void(0)" nc_type="buy_edit" id="edit_reciver">[修改]</a></div>
  <div id="addr_list" class="ncc-candidate-items">
    <ul>
      <li><span class="true-name"><?php echo $output['address_info']['true_name'];?></span><span class="address"><?php echo $output['address_info']['area_info'],$output['address_info']['address'];?></span><span class="phone"><i class="icon-mobile-phone"></i><?php echo $output['address_info']['mob_phone'] ? $output['address_info']['mob_phone'] : $output['address_info']['tel_phone'];?></span></li>
    </ul>
  </div>
</div>
<script type="text/javascript">
//隐藏收货地址列表
function hideAddrList(addr_id,true_name,address,phone) {
    $('#edit_reciver').show();
	$("#address_id").val(addr_id);
	$("#addr_list").html('<ul><li><span class="true-name">'+true_name+'</span><span class="address">'+address+'</span><span class="phone"><i class="icon-mobile-phone"></i>'+phone+'</span></li></ul>');
	$('.current_box').removeClass('current_box');
	ableOtherEdit();
	$('#edit_payment').click();
}
//加载收货地址列表
$('#edit_reciver').on('click',function(){
    $(this).hide();
    disableOtherEdit('如需修改，请先保存收货人信息 ');
    $(this).parent().parent().addClass('current_box');
    var url = SITEURL+'/index.php?act=buy&op=load_addr';
    <?php if ($output['ifshow_chainpay']) { ?>
    url += '&ifchain=1';
    <?php } ?>
    $('#addr_list').load(url);
});
var addressinfo = new Array();
var phoneinfo = new Array();
//异步显示每个店铺运费 city_id计算运费area_id计算是否支持货到付款
function showShippingPrice(city_id,area_id) {
	$('#buy_city_id').val('');
    $.post(SITEURL + '/index.php?act=buy&op=change_addr', {'freight_hash':'<?php echo $output['freight_hash'];?>',city_id:city_id,'area_id':area_id}, function(data){
    	if(data.state == 'success') {
    	    $('#buy_city_id').val(city_id ? city_id : area_id);
    	    $('#allow_offpay').val(data.allow_offpay);
            if (data.allow_offpay_batch) {
                var arr = new Array();
                $.each(data.allow_offpay_batch, function(k, v) {
                    arr.push('' + k + ':' + (v ? 1 : 0));
                });
                $('#allow_offpay_batch').val(arr.join(";"));
            }
    	    $('#offpay_hash').val(data.offpay_hash);
    	    $('#offpay_hash_batch').val(data.offpay_hash_batch);
    	    var content = data.content;var tpl_ids = data.no_send_tpl_ids;
    	    no_send_tpl_ids = [];no_chain_goods_ids = [];
            for(var i in content){
                if (content[i] !== false) {
             	   $('#eachStoreFreight_'+i).html(number_format(content[i],2));
                } else {
                	no_send_store_ids[i] = true;
                }
            }
            for(var i in tpl_ids){
            	no_send_tpl_ids[tpl_ids[i]] = true;
            }
            calcOrder();

            $.post(SITEURL + '/index.php?act=buy&op=aroundstore', {'goodsids':$("#order_pickup_goodsids").val(),'goodscnt':$("#order_pickup_goodscnt").val(),'address':$("#addr_list .address").text(),'area_id':area_id}, function(data){
                $(".choose_store_row .label").hide();
            	$("#order_pickup_store").hide();
                $("#storemsg").html("");
            	if(data.state == 'success') {
            		$(".choose_store_row").show();
            	    result = data.result;
            	    hasstore = data.hasstore;
            	    $("#order_pickup_store").html("<option value=-1>请选择</option>");
            	    $("#storecnt").val(result.length);
            	    addressinfo = new Array();
            	    phoneinfo = new Array();
            	    for(var s in result) {
            	        id = result[s]['id'];
            	        address = result[s]['address'];
            	        addressinfo[id] = address;
            	        phone = "";
            	        if(result[s]['tel'] && result[s]['mobile']) {
                	        phone = result[s]['tel'] + "/" + result[s]['mobile'];
            	        } else if (result[s]['tel']) {
                	        phone = result[s]['tel'];
            	        } else {
            	        	phone = result[s]['mobile'];
            	        }
            	        phoneinfo[id] = phone;
            	        $("#order_pickup_store").append("<option address='" +address + "' phone='" + phone + "'  value=" + id + ">" + result[s]['name'] + "</option>");
            	    }
            	    if(result.length==0 && hasstore==0) {
            	    	disableSubmitOrder();
            	    	$(".choosetype").attr("disabled","true")
            	    	$("#storemsg").html("您选择的商品附近2公里内无货。");
            	    	return;
            	    }
            	    if(result.length==0 && hasstore == 1) {
         	    	    $("#choose_self_pickup").attr("disabled","true")
          	    	    $("#choose_send_by_store").attr("checked","checked");
          	    	    $("#storecnt").val(1);
            	    } else {
            	    	$(".choosetype").removeAttr("disabled");
            	    }
            	    
            	    $pick_type = $("input[name='order_pickup_type']:checked").val();
            	    if($pick_type==1) {
                        $(".choose_store_row .label").show();
                    	$("#order_pickup_store").show();
            	    }      

            	    ableSubmitOrder();
            	    $("#order_pickup_store").change(function(){
            	    	ableSubmitOrder();

         	    	    pickupaddress = "";
         	    	    pickupphone = "";
            	    	if($(this).val()==-1) {
            	    		pickupaddress = "";
            	    		pickupphone = "";
            	    	} else {
            	    		pickupaddress = "地址:" + addressinfo[$(this).val()];
            	    		pickupphone = "电话:" + phoneinfo[$(this).val()];
            	    	}
            	    	if($(this).val()==-1) {
            	    		$("#addressmsg").html("");
            	    	} else {
            	    		$("#addressmsg").html(pickupaddress + "<br/>" + pickupphone + "");
            	    	}
            	    	
            	    });

            	} else {
            		$("#order_pickup_store").html("<option value=-1>请选择</option>");
            		showDialog('系统出现异常', 'error','','','','','','','','',2);
            	}

            },'json');
                        
    	} else {
    		showDialog('系统出现异常', 'error','','','','','','','','',2);
    	}

    },'json');
}

//根据门店自提站ID计算商品是否有库存（有库存即支持自提）
function showProductChain(city_id) {
	$('#buy_city_id').val('');
    var product = [];
	$('input[name="goods_id[]"]').each(function(){
		product.push($(this).val());
	});
	$.post(SITEURL+'/index.php?act=buy&op=change_chain',{chain_id:chain_id,product:product.join('-')},function(data){
		if (data.state == 'success') {
			$('#buy_city_id').val(city_id);
			$('em[nc_type="eachStoreFreight"]').html('0.00');
			no_send_tpl_ids = [];no_chain_goods_ids = [];
			if (data.product.length > 0) {
	            for (var i in data.product) {
	            	no_chain_goods_ids[data.product[i]] = true;
	            }
			}
			calcOrder();
		} else {
			showDialog('系统出现异常', 'error','','','','','','','','',2);
		}
	},'json');
}
$(function(){
    <?php if (!empty($output['address_info']['address_id'])) {?>
    showShippingPrice(<?php echo $output['address_info']['city_id'];?>,<?php echo $output['address_info']['area_id'];?>);
    <?php } else {?>
    $('#edit_reciver').click();
    <?php }?>
});
</script>