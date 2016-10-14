<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=activity&op=index" title="返回活动列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "活动表管理"?> - <?php echo "新增"?></h3>
                <h5><?php echo "当前供应商的所有活动管理"?></h5>
            </div>
        </div>
    </div>

    <form id="activity_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="act_name"><em>*</em>活动名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?=$output['act_name']?>" name="act_name" id="act_name" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="act_info"><em>*</em>活动详情</label>
                </dt>
                <dd class="opt">
                    <textarea class="tarea" rows="6" name="act_info" id="act_info"><?=$output['act_info']?></textarea>
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>横幅图片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="type-file-box">
                            <input type="file" class="type-file-file" id="act_banner" name="act_banner" size="30" hidefocus="true"   title="点击按钮选择文件并提交表单后上传生效" />
                        </span>
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="set_type"><em>*</em>发送方式</label>
                </dt>
                <dd class="opt">
                    <select name="set_type">
                        <option value="V">微信</option>
                        <option value="D">短信</option>
                    </select>
                </dd>
            </dl>

<!--            <dl class="row">-->
<!--                <dt class="tit">发送状态</dt>-->
<!--                <dd class="opt">-->
<!--                    <div class="onoff">-->
<!--                        <label for="status_1" class="cb-enable">已发送</label>-->
<!--                        <label for="status_2" class="cb-disable selected">未发送</label>-->
<!--                        <input id="status_1" name="status" value="1" type="radio">-->
<!--                        <input id="status_2" name="status" value="0" checked type="radio">-->
<!--                    </div>-->
<!--                </dd>-->
<!--            </dl>-->

            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>

        </div>
    </form>
</div>


<script type="text/javascript">
    $(function(){

        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){

            if($("#activity_form").valid()){
                $("#activity_form").submit();
            }
        });
        $('#activity_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                act_name: {
                    required :true,
                    minlength: 1,
                    maxlength: 15,
                },
                act_info:{
                    required :true,
                }
            },
            messages : {
                act_name: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "活动名称不能为空";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "活动名称必须在1-15位之间";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "活动名称必须在1-15位之间";?>',

                },
                act_info:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "活动详情不能为空";?>',
                }
            }
        });
    });

    $(function(){
        // 模拟活动页面横幅Banner上传input type='file'样式
        var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='选择上传...' class='type-file-button' />"
        $(textButton).insertBefore("#act_banner");
        $("#act_banner").change(function(){
            $("#textfield1").val($("#act_banner").val());
        });
    });
</script>