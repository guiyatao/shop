<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=user&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>权限设置 - 编辑用户</h3>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
        </ul>
    </div>
    <form id="add_form" method="post" action='index.php?act=user&op=user_edit&user_id=<?php echo $output['userinfo']['user_id'];?>'>
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="user_type">用户类型</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="user_type" name="user_type" class="input-txt" value="<?php echo $output['userinfo']['user_type'];?>"readonly>
                    <span class="err"></span>
                    <p class="notic">用户类型不可修改。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="user_name">用户名</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="user_name" name="user_name" class="input-txt" value="<?php echo $output['userinfo']['user_name'];?>">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="user_password">密码</label>
                </dt>
                <dd class="opt">
                    <input id="user_password" name="user_password" class="input-txt" type="password">
                    <span class="err"></span>
                    <p class="notic">不修改留空即可。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="user_rpassword">确认密码</label>
                </dt>
                <dd class="opt">
                    <input id="user_rpassword" name="user_rpassword" class="input-txt" type="password">
                    <span class="err"></span>
                    <p class="notic">再次确认密码。</p>
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
                        url :'index.php?act=user&op=ajax&branch=check_admin_name&user_id=<?php echo $output['userinfo']['user_id']?>',
                        type:'get',
                        data:{
                            user_name : function(){
                                return $('#user_name').val();
                            }
                        }
                    }
                },
                user_password : {
                    minlength: 6,
                    maxlength: 20
                },
                user_rpassword : {
                    equalTo  : '#user_password'
                }
            },
            messages : {
                user_name : {
                    required : '<i class="fa fa-exclamation-circle"></i>不能为空',
                    minlength: '<i class="fa fa-exclamation-circle"></i>长度少于3个字符',
                    maxlength: '<i class="fa fa-exclamation-circle"></i>长度大于20个字符',
                    remote: '<i class="fa fa-exclamation-circle"></i>用户名重复'
                },
                user_password : {
                    minlength: '<i class="fa fa-exclamation-circle"></i>长度少于6个字符',
                    maxlength: '<i class="fa fa-exclamation-circle"></i>长度大于20个字符'
                },
                user_rpassword : {
                    equalTo  : '<i class="fa fa-exclamation-circle"></i>密码不一致'
                }
            }
        });
    });
</script>
