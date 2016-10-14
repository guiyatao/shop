<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['warning_set'];?></h3>
        <h5><?php echo $lang['warning_set_subhead'];?></h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>设置SCM系统是否推送提示信息以及推送方式。</li>
    </ul>
  </div>
  <form method="post" enctype="multipart/form-data" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">

      <dl class="row">
        <dt class="tit"><?php echo $lang['msg_push_switch'];?></dt>
        <dd class="opt">
          <div class="onoff">
            <label for="site_status1" class="cb-enable <?php if($output['list_setting']['site_status'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
            <label for="site_status0" class="cb-disable <?php if($output['list_setting']['site_status'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
            <input id="site_status1" name="site_status" <?php if($output['list_setting']['site_status'] == '1'){ ?>checked="checked"<?php } ?>  value="1" type="radio">
            <input id="site_status0" name="site_status" <?php if($output['list_setting']['site_status'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic"><?php echo $lang['site_state_notice'];?></p>
        </dd>
      </dl>

      <dl class="row">
        <dt class="tit"><?php echo $lang['stock_warn_type_scm'];?></dt>
        <dd class="opt">
          <div class="onoff">
            <label for="site_status1" class="cb-enable <?php if($output['list_setting']['site_status'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
            <label for="site_status0" class="cb-disable <?php if($output['list_setting']['site_status'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
            <input id="site_status1" name="site_status" <?php if($output['list_setting']['site_status'] == '1'){ ?>checked="checked"<?php } ?>  value="1" type="radio">
            <input id="site_status0" name="site_status" <?php if($output['list_setting']['site_status'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic"><?php echo $lang['site_state_notice'];?></p>
        </dd>
      </dl>

      <dl class="row">
        <dt class="tit"><?php echo $lang['stock_warn_type_wx'];?></dt>
        <dd class="opt">
          <div class="onoff">
            <label for="site_status1" class="cb-enable <?php if($output['list_setting']['site_status'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
            <label for="site_status0" class="cb-disable <?php if($output['list_setting']['site_status'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
            <input id="site_status1" name="site_status" <?php if($output['list_setting']['site_status'] == '1'){ ?>checked="checked"<?php } ?>  value="1" type="radio">
            <input id="site_status0" name="site_status" <?php if($output['list_setting']['site_status'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic"><?php echo $lang['site_state_notice'];?></p>
        </dd>
      </dl>

      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>

