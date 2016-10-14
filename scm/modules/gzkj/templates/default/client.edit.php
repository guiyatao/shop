<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=client&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo $lang['nc_limit_manage'];?> - <?php echo $lang['nc_edit'];?>管理员“<?php echo $output['admininfo']['admin_name'];?>”</h3>
                <h5><?php echo $lang['nc_limit_manage_subhead'];?></h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>编辑管理员修改所属权限组，登录密码不变时不必重新填写。</li>
        </ul>
    </div>
    <form id="admin_form" method="post" action='index.php?act=client&op=client_edit&clie_id=<?php echo $output['clientinfo'][' id'];?>'>
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label>终端店英文名称名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" id="clie_id" name="clie_id" value="<?php echo $output['clientinfo']['clie_id'];?>" readonly />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>终端店名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_ch_name" value="<?php echo $output['clientinfo']['clie_ch_name'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="region">上级地区</label>
                </dt>
                <dd class="opt">
                    <input id="region" name="region" type="hidden" value="" >
                    <div class="area-region-select"><input id="region" name="region" type="hidden" value="" >
                        <span class="err"></span></div>
                    <p class="notic">系统将根据所选择的上级菜单层级决定新增项的所在级，即选择上级菜单为“北京”，则新增项为北京地区的下级区域，以此类推。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>详细地址</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_address" value="<?php echo $output['clientinfo']['clie_address'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>经度</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_longitude" value="<?php echo $output['clientinfo']['clie_longitude'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>纬度</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_latitude" value="<?php echo $output['clientinfo']['clie_latitude'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>店主姓名</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_contacter" value="<?php echo $output['clientinfo']['clie_contacter'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl> <dl class="row">
                <dt class="tit">
                    <label>店主电话</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_tel" value="<?php echo $output['clientinfo']['clie_tel'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>店主手机</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_mobile" value="<?php echo $output['clientinfo']['clie_mobile'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl> <dl class="row">
                <dt class="tit">
                    <label>店主传真</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="clie_tax" value="<?php echo $output['clientinfo']['clie_tax'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>
            </dl> <dl class="row">
                <dt class="tit">
                    <label>备注</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="comments" value="<?php echo $output['clientinfo']['comments'];?>"  />
                    <span class="err"></span>
                </dd>
            </dl>


            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script>
    //按钮先执行验证再提交表单
    $(function(){$("#submitBtn").click(function(){
        if($("#admin_form").valid()){
            $("#admin_form").submit();
        }
    });
    });
    $(document).ready(function(){
        $("#region").nc_region();
        $("#admin_form").validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                new_pw : {
                    minlength: 6,
                    maxlength: 20
                },
                new_pw2 : {
                    minlength: 6,
                    maxlength: 20,
                    equalTo: '#new_pw'
                },
                gid : {
                    required : true
                }
            },
            messages : {
                new_pw : {
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_add_password_max'];?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_add_password_max'];?>'
                },
                new_pw2 : {
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_add_password_max'];?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_add_password_max'];?>',
                    equalTo:   '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_edit_repeat_error'];?>'
                },
                gid : {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['admin_add_gid_null'];?>'
                }
            }
        });
    });
</script>