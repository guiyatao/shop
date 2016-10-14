<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>账户维护</h3>
        <h5>终端店账户的相关设置</h5>
      </div>
    </div>
  </div>
  <form method="post" name="settingForm" id="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
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
<script type="text/javascript">
addressInit('cmbProvince', 'cmbCity', 'cmbArea','<?=$output['list_setting']['area_province'] ?>','<?=$output['list_setting']['area_city'] ?>','<?=$output['list_setting']['area_district'] ?>');
$(function(){$("#submitBtn").click(function(){
    if($("#settingForm").valid()){
      $("#settingForm").submit();
  }
  });
});
</script>
