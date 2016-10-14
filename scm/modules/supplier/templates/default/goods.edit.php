<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=goods&op=goods" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "商品管理"?> - <?php echo "修改商品"?></h3>
                <h5><?php echo "供应商所有商品的索引及管理"?></h5>
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
    <form id="goods_form" enctype="multipart/form-data" method="post">
        <input type="hidden" id="goods_id" name="goods_id" value="<?=$output['goods']['id']?>" />
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="goods_name"><em>*</em>商品名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?= $output['goods']['goods_nm']?>" name="goods_name" id="goods_name" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">3-30位字符，可由中文、英文、数字及“_”、“-”组成。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_barcode"><em>*</em>商品条码</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="goods_barcode" name="goods_barcode" value="<?= $output['goods']['goods_barcode']?>" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_price"><em>*</em>原价</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="goods_price" name="goods_price" value="<?= $output['goods']['goods_price']?>" style="width: 100px;"> 元
                    <span class="err"></span>
                    <p class="notic">正数，最多两位小数。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_discount">折扣</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="goods_discount" name="goods_discount" value="<?= $output['goods']['goods_discount']?>" style="width: 100px;">
                    <span class="err"></span>
                    <p class="notic">范围在0~1.0之间(例如0.9代表9折)。不填默认为1</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_tax_rate">税率</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="goods_tax_rate" name="goods_tax_rate" value="<?= $output['goods']['goods_rate']?>" style="width: 100px;"> %
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="stock_unit"><em>*</em>批发单位</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="stock_unit" name="stock_unit" value="<?= $output['goods']['goods_unit']?>"  style="width: 100px;">
                    <span class="err"></span>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="min_supp_num_value"><em>*</em>最小配量</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="min_supp_num" name="min_supp_num" value="<?= $output['goods']['min_set_num']?>" style="width: 100px;">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="goods_tax_rate"><em>*</em>单位数量</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="unit_num" name="unit_num" value="<?= $output['goods']['unit_num']?>" style="width: 100px;">
                    <span class="err"></span>
                    <p class="notic">批发单位按照商品规格拆分的商品数量</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="specification">规格</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="specification" name="specification" value="<?= $output['goods']['goods_spec']?>" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="stock_unit">生产厂家</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="manufacturer" name="manufacturer" value="<?= $output['goods']['produce_company']?>" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="stock_unit">产地</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="origin" name="origin" value="<?= $output['goods']['produce_area']?>" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="production_date">生产日期</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="production_date" name="production_date" value="<?= $output['goods']['production_date']?>" class="input-txt" />
                    <span class="err"></span>

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="valid_remind"><em>*</em>有效期提醒天数</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="valid_remind" name="valid_remind" value="<?= $output['goods']['valid_remind']?>" style="width: 100px;"> 天
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="shelf_life"><em>*</em>保质期</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="shelf_life" name="shelf_life" value="<?= $output['shelf_life']?>" style="width: 100px;"><select name="shelf_life_unit"><option value="天" <?php if($output['shelf_life_unit'] == '天') {?> selected <?php } ?> >天</option><option <?php if($output['shelf_life_unit'] == '月') {?> selected <?php } ?> value="月" >月</option><option <?php if($output['shelf_life_unit'] == '年') {?> selected <?php } ?> value="年">年</option></select>
                    <span class="err"></span>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
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

        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){
            if($("#goods_form").valid()){
                $("#goods_form").submit();
            }
        });
        $('#goods_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                goods_name: {
                    required : true,
                    minlength: 3,
                    maxlength: 30,
                    remote   : {
                        url :'index.php?act=goods&op=ajax&branch=check_goods_name',
                        type:'get',
                        data:{
                            goods_name : function(){
                                return $('#goods_name').val();
                            },
                            goods_id : function(){
                                return $('#goods_id').val();
                            }
                        }
                    }
                },
                goods_barcode: {
                    required : true,
                    maxlength:13,
                    minlength:13,
                    positiveInteger:true,
                    remote   : {
                        url :'index.php?act=goods&op=ajax&branch=check_goods_barcode',
                        type:'get',
                        data:{
                            goods_barcode : function(){
                                return $('#goods_barcode').val();
                            },
                            goods_id : function(){
                                return $('#goods_id').val();
                            }
                        }
                    }
                },
                sort:{
                    required: true,
                    range:[0,255],
                    digits:true,
                },
                stock_unit:{
                    required: true,
                },
                min_supp_num:{
                    required: true,
                    positiveInteger:true,
                },
                unit_num:{
                    required: true,
                    positiveInteger:true,
                },
                goods_price:{
                    required: true,
                    price:true,
                },
                goods_discount:{
                    discount:true,
                },
                goods_tax_rate:{
                    price:true,
                },
                valid_remind:{
                    required: true,
                    positiveInteger:true,
                },
                shelf_life:{
                    required:true,
                    positiveInteger:true,
                },
                specification:{
                    maxlength: 20,
                    minlength:1,
                },
                manufacturer:{
                    maxlength: 50,
                    minlength:1,
                },
                origin:{
                    maxlength: 25,
                    minlength:1,
                },

            },
            messages : {
                goods_name: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "商品名不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "商品名必须在3-30位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "商品名必须在3-30位之间";?>',
                    remote   : '<i class="fa fa-exclamation-circle"></i><?php echo  "该终端店内的商品名有重复,请您换一个";?>'
                },
                goods_barcode: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo  "必须为13位";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo  "必须为13位";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为正整数";?>',
                    remote   : '<i class="fa fa-exclamation-circle"></i><?php echo  "商品条码有重复,请您换一个";?>'
                },
                sort:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "排序不能为空";?>',
                    range:'<i class="fa fa-exclamation-circle"></i><?php echo "排序值必须在0-255之间";?>',
                    digits:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入整数";?>',
                },
                stock_unit:{
                    required: '<i class="fa fa-exclamation-circle"></i><?php echo "库存单位不能为空";?>',
                },
                min_supp_num:{
                    required:  '<i class="fa fa-exclamation-circle"></i><?php echo "最小配量不能为空";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                unit_num:{
                    required:  '<i class="fa fa-exclamation-circle"></i><?php echo "单位数量不能为空";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                goods_price:{
                    required:  '<i class="fa fa-exclamation-circle"></i><?php echo "商品原价不能为空";?>',
                    price:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入有效的价格";?>',
                },
                goods_discount:{
                    discount:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入有效的折扣";?>',
                },
                goods_tax_rate:{
                    price:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入合法的税率";?>',
                },
                valid_remind:{
                    required: '<i class="fa fa-exclamation-circle"></i><?php echo "不能为空";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "请输入正整数";?>',
                },
                shelf_life:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "不能为空";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo  "必须为正整数";?>',
                },
                specification:{
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-20位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-20位之间";?>',
                },
                manufacturer:{
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-50位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-50位之间";?>',
                },
                origin:{
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-25位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "必须在1-25位之间";?>',
                },
            }
        });
    });
</script>
