<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=user&op=show_client" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>账户分配 - 添加终端店</h3>
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
                        <option value="2">终端店</option>
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
                    <p class="notic">用户编号即为终端店编号</p>
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
                    <label for="clie_ch_name"><?php echo $lang['clie_ch_name'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_ch_name" name="clie_ch_name" value="<?php echo $output['list_setting']['clie_ch_name'];?>" class="input-txt" type="text" />
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
                    <label for="clie_address"><?php echo $lang['clie_address'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_address" name="clie_address" value="<?php echo $output['list_setting']['clie_address'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_longitude"><?php echo $lang['clie_longitude'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_longitude" name="clie_longitude" value="<?php echo $output['list_setting']['clie_longitude'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_latitude"><?php echo $lang['clie_latitude'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_latitude" name="clie_latitude" value="<?php echo $output['list_setting']['clie_latitude'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_contacter"><?php echo $lang['clie_contacter'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_contacter" name="clie_contacter" value="<?php echo $output['list_setting']['clie_contacter'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_tel"><?php echo $lang['clie_tel'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_tel" name="clie_tel" value="<?php echo $output['list_setting']['clie_tel'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_mobile"><?php echo $lang['clie_mobile'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_mobile" name="clie_mobile" value="<?php echo $output['list_setting']['clie_mobile'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clie_tax"><?php echo $lang['clie_tax'];?></label>
                </dt>
                <dd class="opt">
                    <input id="clie_tax" name="clie_tax" value="<?php echo $output['list_setting']['clie_tax'];?>" class="input-txt" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="comments"><?php echo $lang['comments'];?></label>
                </dt>
                <dd class="opt">
                    <input id="comments" name="comments" value="<?php echo $output['list_setting']['comments'];?>" class="input-txt" type="text" />
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
                    required : true,
                    supp_clie_id:true
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
                }
            }
        });


    });
</script>
