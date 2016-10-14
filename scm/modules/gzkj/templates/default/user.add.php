<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=user&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>账户分配 - 添加用户</h3>
            </div>
        </div>
    </div>

    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom">
            <i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
        </ul>
    </div>
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
                    <p class="notic">用户编号即为终端店或供应商编号</p>
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
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script>
    //按钮先执行验证再提交表
    $(document).ready(function(){
        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){
            if($("#add_form").valid()){
                $("#add_form").submit();
            }
        });

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
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空'
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
