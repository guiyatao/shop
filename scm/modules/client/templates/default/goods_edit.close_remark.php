<?php defined('InShopNC') or exit('Access Invalid!');?>

<form method="post" name="form1" id="form1" action="<?php echo urlSCMClient('good_manage', 'goods_edit');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" value="<?php echo $output['goods_info']['id'];?>" name="id">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">商品名称</dt><dd class="opt"><?=$output['goods_info']['goods_nm'];?></dd></dl>
        <dl class="row">
            <dt class="tit">库存</dt><dd class="opt"><?=$output['goods_info']['goods_stock'];?><?=$output['goods_info']['goods_unit'] ?></dd></dl>
        <dl class="row">
            <dt class="tit">库存下限</dt><dd class="opt"><input type="text" id="goods_low_stock" name="goods_low_stock" value="<?=$output['goods_info']['goods_low_stock'];?>" /> <?=$output['goods_info']['goods_unit'] ?><span class="err"></span> </dd></dl>
        <dl class="row">
            <dt class="tit">有效期提醒天数</dt><dd class="opt"><input type="text" id="valid_remind" name="valid_remind" value="<?=$output['goods_info']['valid_remind'];?>" /> 天 <span class="err"></span></dd></dl>
        <dl class="row">
            <dt class="tit">滞销提醒天数</dt><dd class="opt"><input type="text" id="drug_remind" name="drug_remind" value="<?=$output['goods_info']['drug_remind'];?>" /> 天  <span class="err"></span></dd></dl>

        <div class="bot"><a href="javascript:void(0);" class="ncap-btn-big ncap-btn-green" nctype="btn_submit"><?php echo $lang['nc_submit'];?></a></div>
    </div>
</form>
<script>
    $(function(){
        //验证正整数
        jQuery.validator.addMethod( "positiveInteger",function(value,element){
            var pattern =/^[1-9]*[1-9][0-9]*$/;  //不允许0.00
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正整数' );

        $('#form1').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                goods_low_stock:{
                    positiveInteger:true,
                },
                valid_remind:{
                    positiveInteger:true,
                },
                drug_remind:{
                    positiveInteger:true,
                },

            },
            messages : {
                goods_low_stock:{
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                valid_remind:{
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                drug_remind:{
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
            }
        });

        $('a[nctype="btn_submit"]').click(function(){
            if($("#form1").valid()){
                ajaxpost('form1', '', '', 'onerror');
            }
        });
    });
</script>