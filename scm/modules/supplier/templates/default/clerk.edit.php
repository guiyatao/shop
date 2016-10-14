<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=clerk&op=clerk" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "业务员管理"?> - <?php echo "修改业务员"?></h3>
                <h5><?php echo "供应商所有业务员的索引及管理"?></h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>编辑业务员选项，业务员名称不可变，其余内容可重新填写，忽略或留空则保持原有信息数据。</li>
        </ul>
    </div>
    <form id="clerk_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="user_id" value="<?php echo $output['clerk_array']['user_id'];?>" />
        <input type="hidden" name="admin_id" value="<?php echo $output['clerk_array']['admin_id'];?>" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="member_name"><em>*</em>业务员登录名</label>
                </dt>
                <dd class="opt">
                    <input type="text"  name="clerk_en_name" id="clerk_en_name" value="<?php echo $output['clerk_array']['admin_name'];?>" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">3-15位字符，可由中文、英文、数字及“_”、“-”组成。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clerk_passwd">业务员密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="clerk_passwd" name="clerk_passwd" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">6-20位字符，可由英文、数字及标点符号组成。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="clerk_re_passwd">确认密码</label>
                </dt>
                <dd class="opt">
                    <input type="password" id="clerk_re_passwd" name="clerk_re_passwd" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">6-20位字符，可由英文、数字及标点符号组成。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>头像</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="show"><a class="nyroModal" rel="gal" href="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR; ?>/<?php echo $output['clerk_array']['admin_avatar'];?>"><i class="fa fa-picture-o" onMouseOver="toolTip('<img src=<?php echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR; ?>/<?php echo $output['clerk_array']['admin_avatar'];?>>')" id="view_img" onMouseOut="toolTip()"></i></a></span><span class="type-file-box">
                    <input class="type-file-file" id="_pic" name="_pic" type="file" size="30" hidefocus="true" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                    <input type="text" name="avatar" id="avatar" class="type-file-text" />
                    <input type="button" name="button" id="button" value="选择上传..." class="type-file-button" />
                    </span></div>
                    <span class="err"></span>
                    <p class="notic">默认会员头像图片请使用100*100像素jpg/gif/png格式的图片。</p>
                </dd>
            </dl>

            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/ajaxfileupload/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.js"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript">
    //裁剪图片后返回接收函数
    function call_back(picname){
        $('#avatar').val(picname);
        $('#view_img').attr('src','<?php echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR;?>/'+picname)
            .attr('onmouseover','toolTip("<img src=<?php echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR;?>/'+picname+'>")');
    }

$(function(){
    $('input[class="type-file-file"]').change(uploadChange);
    function uploadChange(){
        var filepath=$(this).val();
        var extStart=filepath.lastIndexOf(".");
        var ext=filepath.substring(extStart,filepath.length).toUpperCase();
        if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
            alert("file type error");
            $(this).attr('value','');
            return false;
        }
        if ($(this).val() == '') return false;
        ajaxFileUpload();
    }
    function ajaxFileUpload()
    {
        $.ajaxFileUpload
        (
            {
                url : '<?php echo SCM_SITE_URL?>/index.php?act=common&op=pic_upload&form_submit=ok&uploadpath=<?php echo ATTACH_AVATAR;?>',
                secureuri:false,
                fileElementId:'_pic',
                dataType: 'json',
                success: function (data, status)
                {
                    if (data.status == 1){
                        ajax_form('cutpic','<?php echo $lang['nc_cut'];?>','<?php echo SCM_SITE_URL?>/index.php?act=common&op=pic_cut&type=member&x=120&y=120&resize=1&ratio=1&url='+data.url,690);
                    }else{
                        alert(data.msg);
                    }
                    $('input[class="type-file-file"]').bind('change',uploadChange);
                },
                error: function (data, status, e)
                {
                    alert('上传失败');
                    $('input[class="type-file-file"]').bind('change',uploadChange);
                }
            }
        )
    };
    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#clerk_form").valid()){
            $("#clerk_form").submit();
        }
    });
    $('#clerk_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
            clerk_en_name: {
                required : true,
                minlength: 3,
                maxlength: 15,
                remote   : {
                    url :'index.php?act=clerk&op=ajax&branch=check_clerk_name',
                    type:'get',
                    data:{
                        clerk_name : function(){
                            return $('#clerk_en_name').val();
                        },
                        clerk_id :'<?php echo $output['clerk_array']['user_id'];?>'
                    }
                }
            },
            clerk_passwd:{
                maxlength: 20,
                minlength: 6
            },
            clerk_re_passwd:{
                maxlength: 20,
                minlength: 6,
                equalTo: "#clerk_passwd"
            }

        },
        messages : {
            clerk_en_name: {
                required : '<i class="fa fa-exclamation-circle"></i><?php echo "业务员登录名不能为空";?>',
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "业务员登录名必须在3-15位之间";?>',
                minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "业务员登录名必须在3-15位之间";?>',
                remote   : '<i class="fa fa-exclamation-circle"></i><?php echo "业务员登录名有重复,请您换一个";?>'
            },
            clerk_passwd:{
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "密码必须在6-20位之间";?>',
                minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "密码必须在6-20位之间";?>',
            },
            clerk_re_passwd:{
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "确认密码必须在6-20位之间";?>',
                minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "确认密码必须在6-20位之间";?>',
                equalTo: '<i class="fa fa-exclamation-circle"></i><?php echo "请输入相同的密码";?>',
            }
        }
    });
});
</script>

