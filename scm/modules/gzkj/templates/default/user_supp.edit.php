<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=user&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>编辑供应商</h3>
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
    <form id="add_form" method="post" action='index.php?act=user&op=supp_edit&user_id=<?php echo $output['userinfo']['user_id'];?>'>
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

            <dl class="row">
                <dt class="tit">
                    <label for="supp_id">供应商编码</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['suppinfo']['supp_id'];?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_ch_name"><em>*</em>供应商名称</label>
                </dt>
                <dd class="opt">
                    <input id="supp_ch_name" name="supp_ch_name" value="<?php echo $output['suppinfo']['supp_ch_name'];?>"
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
                    <input id="business_licences" name="business_licences" value="<?php echo $output['suppinfo']['business_licences']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="tax_registration">税务登记号</label>
                </dt>
                <dd class="opt">
                    <input id="tax_registration" name="tax_registration" value="<?php echo $output['suppinfo']['tax_registration']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_bank">开户行</label>
                </dt>
                <dd class="opt">
                    <input id="supp_bank" name="supp_bank" value="<?php echo $output['suppinfo']['supp_bank']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_cardno">卡号</label>
                </dt>
                <dd class="opt">
                    <input id="supp_cardno" name="supp_cardno" value="<?php echo $output['suppinfo']['supp_cardno']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="mail_address">邮件地址</label>
                </dt>
                <dd class="opt">
                    <input id="mail_address" name="mail_address" value="<?php echo $output['suppinfo']['mail_address']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_address">详细地址</label>
                </dt>
                <dd class="opt">
                    <input id="supp_address" name="supp_address" value="<?php echo $output['suppinfo']['supp_address']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_contacter">联系人</label>
                </dt>
                <dd class="opt">
                    <input id="supp_contacter" name="supp_contacter" value="<?php echo $output['suppinfo']['supp_contacter']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_tel">电话</label>
                </dt>
                <dd class="opt">
                    <input id="supp_tel" name="supp_tel" value="<?php echo $output['suppinfo']['supp_tel'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                    <p class="notic">格式:xxx-xxxxxxx</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_mobile">手机</label>
                </dt>
                <dd class="opt">
                    <input id="supp_mobile" name="supp_mobile" value="<?php echo $output['suppinfo']['supp_mobile'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="supp_tax">传真</label>
                </dt>
                <dd class="opt">
                    <input id="supp_tax" name="supp_tax" value="<?php echo $output['suppinfo']['supp_tax'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="zip_code">邮编</label>
                </dt>
                <dd class="opt">
                    <input id="zip_code" name="zip_code" value="<?php echo $output['suppinfo']['zip_code'];?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="comments">备注</label>
                </dt>
                <dd class="opt">
                    <input id="comments" name="comments" value="<?php echo $output['suppinfo']['comments']; ?>" class="input-txt" type="text" />
                    <span class="err"></span>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jsAddress.js"></script>
<script>
    addressInit('cmbProvince', 'cmbCity', 'cmbArea','<?=$output['suppinfo']['area_province'] ?>','<?=$output['suppinfo']['area_city'] ?>','<?=$output['suppinfo']['area_district'] ?>');
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
