
<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>供应商基本信息</h3>
        <h5>查看和修改当前供应商的基本信息</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>查看和修改当前供应商的详细信息</li>
    </ul>
  </div>
  <form id="supp_form" method="post" name="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="supp_id">供应商编码</label>
        </dt>
        <dd class="opt">
          <?php echo $output['supp_id'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="supp_ch_name"><em>*</em>供应商名称</label>
        </dt>
        <dd class="opt">
          <input id="supp_ch_name" name="supp_ch_name" value="<?php echo $output['supp_ch_name'];?>"
                 class="input-txt" type="text">
          <span class="err"></span>
          <!--<p class="notic">&nbsp;</p>-->
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
                    <?php if(!empty($output['enterprise_nature_items']) && is_array($output['enterprise_nature_items'])){ ?>
                        <?php foreach($output['enterprise_nature_items'] as $k => $v){ ?>
                            <option  <?php if($output['enterprise_nature'] == $v) {?> selected <?php }?> value="<?php echo $v;?>"><?php echo $v;?></option>
                        <?php } ?>
                    <?php } ?>
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
                <?php echo $output['supp_bank'];?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="supp_cardno">卡号</label>
            </dt>
            <dd class="opt">
                <?php echo $output['supp_cardno'];?>
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
      <input name="id" type="hidden" value="<?php echo $output['id']; ?>" />
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jsAddress.js"></script>
<script type="text/javascript">
    addressInit('cmbProvince', 'cmbCity', 'cmbArea','<?=$output['area_province'] ?>','<?=$output['area_city'] ?>','<?=$output['area_district'] ?>');

    $(function(){
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

        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){
            if($("#supp_form").valid()){
                $("#supp_form").submit();
            }
        });
        $('#supp_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                supp_ch_name:{
                    required: true,
                },
                business_licences:{
                    minlength:15,
                    maxlength:15,
                    positiveInteger:true,
                },
                tax_registration:{
                    tax_registration:true,
                },
                mail_address:{
                    email:true,
                },
                supp_address:{
                    maxlength:50,
                },
                supp_contacter:{
                    minlength:2,
                    maxlength:10,
                },
                supp_tel:{
                    isTel:true,
                },
                supp_mobile:{
                    isPhone:true,
                },
                supp_tax:{
                    isTel:true,
                },
                zip_code:{
                    isZipCode:true,
                },
                comments:{
                    maxlength:50,
                }

            },
            messages : {
                supp_ch_name:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "供应商的名称不能为空";?>',
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