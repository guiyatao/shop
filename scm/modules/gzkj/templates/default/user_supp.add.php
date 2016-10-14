<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=user&op=show_supp" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>账户分配 - 添加供应商</h3>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
<!--    <div class="explanation" id="explanation">-->
<!--        <div class="title" id="checkZoom">-->
<!--            <i class="fa fa-lightbulb-o"></i>-->
<!--            <h4 title="--><?php //echo $lang['nc_prompts_title'];?><!--">--><?php //echo $lang['nc_prompts'];?><!--</h4>-->
<!--            <span id="explanationZoom" title="--><?php //echo $lang['nc_prompts_span'];?><!--"></span> </div>-->
<!--        <ul>-->
<!--        </ul>-->
<!--    </div>-->
    <form id="add_form" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="user_type"><em>*</em>用户类型</label>
                </dt>
                <dd class="opt">
                    <select name="user_type" id="user_type">
                        <option value="3">供应商</option>
                    </select>
                    <p class="notic"><?php echo $lang['ap_select_showstyle'];?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="user_name"><em>*</em>用户名</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="user_name" name="user_name" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"><?php echo $lang['admin_add_password_tip'];?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_clie_id"><em>*</em>用户编号</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="supp_clie_id" name="supp_clie_id" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">用户编号即为供应商编号</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="user_password"><em>*</em>密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="user_password" name="user_password" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"><?php echo $lang['admin_add_rpassword_tip'];?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="user_rpassword"><em>*</em>确认密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="user_rpassword" name="user_rpassword" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"><?php echo $lang['admin_add_rpassword_tip'];?></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="supp_ch_name">供应商名称</label>
                </dt>
                <dd class="opt">
                    <input id="supp_ch_name" name="supp_ch_name" value="<?php echo $output['supp_ch_name'];?>"
                           class="input-txt" type="text">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="area_province">地区设置</label>
                </dt>
                <dd class="opt">
                    <select id="cmbProvince" name="area_province">

                    </select>
                    <select id="cmbCity" name="area_city">

                    </select>
                    <select id="cmbArea" name="area_district">

                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="enterprise_nature">企业性质</label>
                </dt>
                <dd class="opt">
                    <select id="enterprise_nature" name="enterprise_nature">
                        <option value="国有企业">国有企业</option>
                        <option value="集体所有制企业">集体所有制企业</option>
                        <option value="联营企业">联营企业</option>
                        <option value="三资企业">三资企业</option>
                        <option selected="" value="私营企业">私营企业</option>
                        <option value="其他企业">其他企业</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="business_licences">营业执照号</label>
                </dt>
                <dd class="opt">
                    <input id="business_licences" name="business_licences" value="<?php echo $output['business_licences']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="tax_registration">税务登记号</label>
                </dt>
                <dd class="opt">
                    <input id="tax_registration" name="tax_registration" value="<?php echo $output['tax_registration']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_bank">开户行</label>
                </dt>
                <dd class="opt">
                    <input id="supp_bank" name="supp_bank" value="<?php echo $output['supp_bank']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_cardno">卡号</label>
                </dt>
                <dd class="opt">
                    <input id="supp_cardno" name="supp_cardno" value="<?php echo $output['supp_cardno']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="mail_address">邮件地址</label>
                </dt>
                <dd class="opt">
                    <input id="mail_address" name="mail_address" value="<?php echo $output['mail_address']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_address">详细地址</label>
                </dt>
                <dd class="opt">
                    <input id="supp_address" name="supp_address" value="<?php echo $output['supp_address']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_contacter">联系人</label>
                </dt>
                <dd class="opt">
                    <input id="supp_contacter" name="supp_contacter" value="<?php echo $output['supp_contacter']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_tel">电话</label>
                </dt>
                <dd class="opt">
                    <input id="supp_tel" name="supp_tel" value="<?php echo $output['supp_tel'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                    <p class="notic">格式:xxx-xxxxxxx</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_mobile">手机</label>
                </dt>
                <dd class="opt">
                    <input id="supp_mobile" name="supp_mobile" value="<?php echo $output['supp_mobile'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_tax">传真</label>
                </dt>
                <dd class="opt">
                    <input id="supp_tax" name="supp_tax" value="<?php echo $output['supp_tax'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="zip_code">邮编</label>
                </dt>
                <dd class="opt">
                    <input id="zip_code" name="zip_code" value="<?php echo $output['zip_code'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="comments">备注</label>
                </dt>
                <dd class="opt">
                    <input id="comments" name="comments" value="<?php echo $output['comments']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jsAddress.js"></script>
<script>
    addressInit('cmbProvince', 'cmbCity', 'cmbArea','<?=$output['area_province'] ?>','<?=$output['area_city'] ?>','<?=$output['area_district'] ?>');
    //按钮先执行验证再提交表
    $(document).ready(function(){
        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){
            if($("#add_form").valid()){
                $("#add_form").submit();
            }
        });
        //用户编码验证
        jQuery.validator.addMethod( "supp_clie_id",function(value,element){
            var pattern =  /^[A-Z]{4}[0-9]{7}$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正确的用户编码' );


        //验证正整数
        jQuery.validator.addMethod( "positiveInteger",function(value,element){
            var pattern =/^[0-9]*[1-9][0-9]*$/;  //不允许0.00
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正整数' );
        //验证税务登记号
        jQuery.validator.addMethod( "tax_registration",function(value,element){
            var len = value.length;
            var pattern =/^[0-9]*[1-9][0-9]*$/;
            if(len == 15){
                if(!pattern.exec(value))
                    return false;
                else
                    return true;
            }
            else if (len == 18){
                if(!pattern.exec(value))
                    return false;
                else
                    return true;
            }
            else{
                return false;
            }
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正整数' );

        //验证电话
        jQuery.validator.addMethod( "isTel",function(value,element){
            var pattern =/^\d{3,4}-?\d{7,9}$/; //电话号码格式010-12345678
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正确的电话号码' );
        //联系电话验证
        jQuery.validator.addMethod( "isPhone",function(value,element){
            var pattern = /^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正确的手机号码' );
        //邮政编码验证
        jQuery.validator.addMethod( "isZipCode",function(value,element){
            var pattern =  /^[0-9]{6}$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入正确的邮政编码' );
        $("#add_form").validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },

            rules : {
                user_name : {
                    required : true,
                    minlength: 3,
                    maxlength: 20,
                    remote	: {
                        url :'index.php?act=user&op=ajax&branch=check_admin_name',
                        type:'get',
                        data:{
                            user_name : function(){
                                return $('#user_name').val();
                            }
                        }
                    }
                },
                user_type:{
                    required : true
                },
                supp_clie_id:{
                    supp_clie_id:true,
                    required : true
                },
                user_degree:{
                    required : true
                },
                user_password:{
                    required : true,
                    minlength: 6,
                    maxlength: 20
                },
                user_rpassword:{
                    required : true,
                    equalTo  : '#user_password'
                },
                business_licences:{
                    minlength:15,
                    maxlength:15,
                    positiveInteger:true
                },
                tax_registration:{
                    tax_registration:true
                },
                mail_address:{
                    email:true
                },
                supp_address:{
                    maxlength:50
                },
                supp_contacter:{
                    minlength:2,
                    maxlength:10
                },
                supp_tel:{
                    isTel:true
                },
                supp_mobile:{
                    isPhone:true
                },
                supp_tax:{
                    isTel:true
                },
                zip_code:{
                    isZipCode:true
                },
                comments:{
                    maxlength:50
                }
            },
            messages : {
                user_name : {
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空',
                    minlength: '<i class="fa fa-exclamation-circle"></i>长度少于3个字符',
                    maxlength: '<i class="fa fa-exclamation-circle"></i>长度大于20个字符',
                    remote   : '<i class="fa fa-exclamation-circle"></i>用户名重复'
                },
                user_type:{
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空'
                },
                supp_clie_id:{
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空',
                    supp_clie_id : '<i class="fa fa-exclamation-circle"></i>以四位大写字母开头，七位数字结尾'
                },
                user_degree:{
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空'
                },
                user_password:{
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空',
                    minlength: '<i class="fa fa-exclamation-circle"></i>长度少于6个字符',
                    maxlength: '<i class="fa fa-exclamation-circle"></i>长度大于20个字符'
                },
                user_rpassword:{
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空',
                    equalTo  : '<i class="fa fa-exclamation-circle"></i>密码不一致'
                },
                business_licences:{
                    minlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须为15位";?>',
                    maxlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须为15位";?>',
                    positiveInteger:'<i class="fa fa-exclamation-circle"></i><?php echo "必须为正整数";?>',
                },
                tax_registration:{
                    tax_registration:'<i class="fa fa-exclamation-circle"></i><?php echo "必须为15位或18位正整数";?>',
                },
                mail_address:{
                    email:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入正确的电子邮件";?>',
                },
                supp_address:{
                    maxlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须在50位以内";?>',
                },
                supp_contacter:{
                    minlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须为2-10位";?>',
                    maxlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须为2-10位";?>',
                },
                supp_tel:{
                    isTel:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入正确的电话号码";?>',
                },
                supp_mobile:{
                    isPhone:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入正确的手机号码";?>',
                },
                supp_tax:{
                    isTel:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入正确的传真号码";?>',
                },
                zip_code:{
                    isZipCode:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入正确的邮政编码";?>',
                },
                comments:{
                    maxlength:'<i class="fa fa-exclamation-circle"></i><?php echo "长度必须在50位以内";?>',
                }
            }
        });


    });
</script>
