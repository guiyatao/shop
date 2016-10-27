<?php defined('InShopNC') or exit('Access Invalid!');?>


<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=good_manage&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "商品管理"?> - <?php echo "修改商品"?></h3>
                <h5><?php echo "终端店所有商品的索引及管理"?></h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>标识“*”的选项为必填项，其余为选填项。</li>
        </ul>
    </div>
    <form method="post" name="form1" id="form1" action="<?php echo urlSCMClient('good_manage', 'goods_edit');?>">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" value="<?php echo $output['goods_info']['id'];?>" id="id" name="id">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><em>*</em>商品条码</dt><dd class="opt"><input type="text" id="goods_barcode" name="goods_barcode" value="<?= $output['goods_info']['goods_barcode']?>"  /><span class="err"></span> </dd></dl>
            <dl class="row">
                <dt class="tit"><em>*</em>商品名称</dt><dd class="opt"><input type="text" id="goods_nm" name="goods_nm" value="<?= $output['goods_info']['goods_nm']?>"  class="input-txt" /><span class="err"></span> </dd></dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_spec">规格</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="goods_spec" name="goods_spec" value="<?= $output['goods_info']['goods_spec']?>">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_spec">供应商</label>
                </dt>
                <dd class="opt">
                    <select id="supp_id" name="supp_id">
                        <?php foreach( $output['supp_list'] as $k => $v )  {?>
                            <option value="<?php echo $v['supp_id'] ?>"  <?php if($v['supp_id'] == $output['goods_info']['supp_id']) { ?> selected  <?php } ?> ><?php echo $v['supp_ch_name'] ?> </option>
                        <?php } ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><em>*</em>零售价</dt><dd class="opt"><input type="text" id="goods_price" name="goods_price" value="<?= $output['goods_info']['goods_price']?>" /><span class="err"></span> </dd></dl>
            <dl class="row">
                <dt class="tit"><em>*</em>库存单位</dt><dd class="opt"><input type="text" id="goods_unit" name="goods_unit" value="<?= $output['goods_info']['goods_unit']?>" style="width: 100px;"><span class="err"></span></dd></dl>
            <dl class="row">
                <dt class="tit">库存</dt><dd class="opt"><input type="text" id="goods_stock" name="goods_stock" value="<?= $output['goods_info']['goods_stock']?>" style="width: 100px;"><span class="err"></span></dd></dl>
            <dl class="row">
                <dt class="tit">库存下限</dt><dd class="opt"><input type="text" id="goods_low_stock" name="goods_low_stock" value="<?= $output['goods_info']['goods_low_stock']?>" /><span class="err"></span> </dd></dl>
            <dl class="row">
                <dt class="tit">生产日期</dt><dd class="opt"><input type="text" id="production_date" name="production_date" value="<?= $output['goods_info']['production_date']?>" /><span class="err"></span> </dd></dl>
            <dl class="row">
                <dt class="tit">有效期提醒天数</dt><dd class="opt"><input type="text" id="valid_remind" name="valid_remind" value="<?= $output['goods_info']['valid_remind']?>" style="width: 100px;" /> 天 <span class="err"></span></dd></dl>
            <dl class="row">
                <dt class="tit">
                    <label for="shelf_life"><em>*</em>保质期</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="shelf_life" name="shelf_life" value="<?= $output['shelf_life']?>" style="width: 100px;"><select name="shelf_life_unit"><option value="天" <?php if($output['shelf_life_unit'] == '天') {?> selected <?php } ?> >天</option><option <?php if($output['shelf_life_unit'] == '个月') {?> selected <?php } ?> value="个月" >个月</option><option <?php if($output['shelf_life_unit'] == '年') {?> selected <?php } ?> value="年">年</option></select>
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">滞销提醒天数</dt><dd class="opt"><input type="text" id="drug_remind" name="drug_remind" value="<?= $output['goods_info']['drug_remind']?>"  style="width: 100px;" /> 天  <span class="err"></span></dd></dl>

            <div class="bot"><a href="javascript:void(0);" class="ncap-btn-big ncap-btn-green" nctype="btn_submit"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function(){
        $("#production_date").datepicker({dateFormat: 'yy-mm-dd'});

        //验证价格
        jQuery.validator.addMethod( "price",function(value,element){
//            var pattern =/(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/;
            var pattern = /^[0-9]+(\.[0-9]{0,2})?$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入有效的价格' );
        //验证0-1之间的小数
        jQuery.validator.addMethod( "discount",function(value,element){
            var pattern =/^(0\.(?!0+$)\d{1,2}|1(\.0{1,2})?)$/;  //不允许0.00
//            var pattern = /^(0\.\d{1,2}|1(\.0{1,2})?)$/;  //允许0.00
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入有效的折扣' );
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

        //验证非负整数
        jQuery.validator.addMethod( "noNegInteger",function(value,element){
            var pattern =/^[1-9]\d*$|^0$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入非负整数' );
        $('#form1').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                goods_barcode: {
                    required: true,
                    maxlength:13,
                    minlength:13,
                    positiveInteger:true,
                    remote   : {
                        url :'index.php?act=good_manage&op=ajax&branch=check_goods_barcode',
                        type:'get',
                        data:{
                            goods_barcode : function(){
                                return $('#goods_barcode').val();
                            },
                            goods_id : function(){
                                return $('#id').val();
                            },
                        }
                    }
                },
                goods_nm: {
                    required : true,
                    minlength: 3,
                    maxlength: 30,
                    remote   : {
                        url :'index.php?act=good_manage&op=ajax&branch=check_goods_name',
                        type:'get',
                        data:{
                            goods_name : function(){
                                return $('#goods_nm').val();
                            },
                            goods_id : function(){
                                return $('#id').val();
                            },
                        }
                    }
                },
                goods_spec:{
                    maxlength: 20,
                    minlength:1,
                },
                goods_price:{
                    required: true,
                    price:true,
                },
                goods_unit:{
                    required: true,
                },
                goods_stock:{
                    noNegInteger:true,
                },
                goods_low_stock:{
                    noNegInteger:true,
                },

                valid_remind:{
                    positiveInteger:true,
                },
                shelf_life:{
                    required:true,
                    positiveInteger:true,
                },
                drug_remind:{
                    positiveInteger:true,
                },
            },
            messages : {
                goods_barcode: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo  "必须为13位";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo  "必须为13位";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为正整数";?>',
                    remote: '<i class="fa fa-exclamation-circle"></i><?php echo  "商品条码有重复,请您换一个";?>'
                },
                goods_nm: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "商品名不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "商品名必须在3-30位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "商品名必须在3-30位之间";?>',
                    remote   : '<i class="fa fa-exclamation-circle"></i><?php echo  "该终端店内的商品名有重复,请您换一个";?>'
                },
                goods_spec:{
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-20位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-20位之间";?>',
                },
                goods_price:{
                    required:  '<i class="fa fa-exclamation-circle"></i><?php echo "商品原价不能为空";?>',
                    price:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入有效的价格";?>',
                },
                goods_unit:{
                    required: '<i class="fa fa-exclamation-circle"></i><?php echo "库存单位不能为空";?>',
                },
                goods_stock:{
                    noNegInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为非负整数";?>',
                },
                goods_low_stock:{
                    noNegInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为非负整数";?>',
                },

                valid_remind:{
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                shelf_life:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "不能为空";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为正整数";?>',
                },
                drug_remind:{
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为正整数";?>',
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