<?php defined('InShopNC') or exit('Access Invalid!');?>

<style>
  .container {
    font: 12px/20px "microsoft yahei";
    color: #333;
    background-color: #FFF;
    vertical-align: top;
    letter-spacing: normal;
    word-spacing: normal;
    display: inline-block;
    width: 400px;
    height: 480px;
    padding: 25px 19px;
    overflow: hidden;
    border: solid #F5F5F5;
    border-width: 1px 0 0 1px;
    zoom: 1;
    float:left;
  }
  .container .hd h3 {
    font: normal 16px/18px "microsoft yahei";
    border-left: solid 3px #28B779;
    height: 18px;
    padding-left: 6px;
    margin-bottom: 4px;
  }
  .container .hd h3.warn {
    font: normal 16px/18px "microsoft yahei";
    border-left: solid 3px red;
    height: 18px;
    padding-left: 6px;
    margin-bottom: 4px;
  }
  .container .hd h5 {
    font: normal 12px/16px "microsoft yahei";
    color: #AAA;
    margin-left: 8px;
  }
  .content dl {
    display: block;
    clear: both;
    margin-top:10px;
    padding: 10px;
    overflow: hidden;
    background-color: #FCF8E3;
    border: 1px solid #FBEED5;
    zoom: 1;
  }
  .type-a .content ul {
    clear: both;
    margin: 5px 0;
    overflow: hidden;
    zoom: 1;
  }
  .type-a .content ul li {
    width: 22%;
    float: left;
    margin: 16px 0 0 2%;
    _margin: 5px 0 0 1%/* if IE6*/;
  }
  .type-a .content ul li.num a {
    color: #28B779;
    display: inline-block;
    padding: 0 8px 0 4px;
    border: solid 1px #28B779;
    border-radius: 3px;
    position: relative;
    z-index: 1;
  }
  .type-a .content ul li.num a:hover {
    color: #FFF;
    background-color: #28B779;
  }
  .type-a .content ul li.num a strong{
    font: normal 10px/14px Tahoma;
    color: #FFF;
    background-color: #28B779;
    text-align: center;
    display: block;
    min-width: 16px;
    height: 14px;
    padding: 1px 0;
    border-radius: 8px;
    position: absolute;
    z-index: 1;
    top: -8px;
    right: -8px;
  }
</style>


<div class="container type-a">
  <div class="hd" style="height: 130px;">
      <div style="float: left;">
        <img width="91px" height="91px" src="<?php echo SCM_SITE_URL;?>/resource/images/gzkj_logo.jpg" />

      </div>
      <div style="float:left;margin:30px 10px;">
        <?php $admin_info = unserialize(decrypt(cookie('sys_key'),MD5_KEY)); ?>
        <span style="font-size: large" >用户名 <?=$admin_info['name']?></span> <br>
        <span>最后登录时间：<?php  echo date('Y-m-d H:i:s', $admin_info['time']); ?></span>
      </div>

  </div>
  <div class="hd" style="height: 100px;">
    <h3>平台联系方式</h3>
    <?php $phone_array = explode(',',C('site_phone'));
        if(is_array($phone_array) && !empty($phone_array)) {
          foreach ($phone_array as $key => $val) { ?>
          <span>电话：<?=$val ?> &nbsp;&nbsp;&nbsp;&nbsp;
      <?php }
      } ?>
      邮箱：<?php echo C('site_email');?></span>
  </div>
  <?php if( isset($output['statistics']['aclist'])) {?>

    <div class="hd">
      <h3 class="warn">审核提示</h3>
      <h5>您需要关注的审核信息</h5>
    </div>
  <?php }else{?>
    <div class="hd">
      <h3 class="warn">预警提示</h3>
      <h5>您需要关注的货物预警信息</h5>
    </div>
  <?php }?>

  <div class="content">
    <ul>
      <?php if( isset($output['statistics']['aclist'])) {?>


      <?php }else{?>
        <?php if($output['statistics']['supplier_validity_warn'] > 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMSupplier('client','index');?>" onclick="$('.dialog_close_button').click();openItem('supplier|client')" >近效期<strong><?=$output['statistics']['supplier_validity_warn'] ?></strong></a></li>
        <?php }else if ($output['statistics']['client_date_warn_count'] > 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMClient('validity_warn','index');?>" onclick="$('.dialog_close_button').click();openItem('client|validity_warn')" >近效期<strong><?=$output['statistics']['client_date_warn_count'] ?></strong></a></li>
        <?php }else{?>
          <li><a href="#" style="color: black">近效期</a></li>
        <?php }?>
        <?php if($output['statistics']['supplier_unavailable_warn'] > 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMSupplier('client','index');?>" onclick="$('.dialog_close_button').click();openItem('supplier|client')" >缺货<strong><?=$output['statistics']['supplier_unavailable_warn'] ?></strong></a></li>
        <?php }else if ($output['statistics']['client_stockout_count'] > 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMClient('stockout_warn','index');?>" onclick="$('.dialog_close_button').click();openItem('client|stockout_warn')" >缺货<strong><?=$output['statistics']['client_stockout_count'] ?></strong></a></li>
        <?php }else{?>
          <li><a href="#" style="color: black">缺货</a></li>
        <?php }?>
        <?php if($output['statistics']['supplier_unsalable_warn']> 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMSupplier('client','index');?>" onclick="$('.dialog_close_button').click();openItem('supplier|client')" >滞销<strong><?=$output['statistics']['supplier_unsalable_warn'] ?></strong></a></li>
        <?php }else if ($output['statistics']['client_unsalable_count'] > 0) {?>
          <li class="num"><a target="workspace" href="<?php echo urlSCMClient('unsalable_warn','index');?>" onclick="$('.dialog_close_button').click();openItem('client|unsalable_warn')" >滞销<strong><?=$output['statistics']['client_unsalable_count'] ?></strong></a></li>
        <?php }else{?>
          <li><a href="#" style="color: black">滞销</a></li>
        <?php }?>

      <?php }?>
      
    </ul>  
    <ul>
     <?php if( isset($output['statistics']['aclist']) && $output['statistics']['aclist'] > 0) {?>
        <li class="num"><a target="workspace" href="<?php echo urlSCMGzkj('activity','index');?>" onclick="$('.dialog_close_button').click();openItem('gzkj|activity')" >活动审核<strong><?=$output['statistics']['aclist'] ?></strong></a></li>
      <?php }elseif(isset($output['statistics']['aclist'])){?>
        <li><a href="#" style="color: black">活动审核</a></li>
      <?php }?>
      <?php if(isset($output['statistics']['sulist']) && $output['statistics']['sulist'] > 0) {?>
        <li class="num"><a target="workspace" href="<?php echo urlSCMGzkj('supp_stock','index');?>" onclick="$('.dialog_close_button').click();openItem('gzkj|supp_stock')" >商品审核<strong><?=$output['statistics']['sulist'] ?></strong></a></li>
      <?php }elseif(isset($output['statistics']['sulist'])){?>
        <li><a href="#" style="color: black">商品审核</a></li>
      <?php }?>
    </ul>
  </div>

</div>

<div class="container">
  <div class="hd">
    <h3 class="warn">待处理事项</h3>
    <h5>按时间序列显示您需要关注的信息，待处理事项以及订单。</h5>
  </div>
  <div class="content">
  <?php if ($output['statistics']['user_type'] == 'supplier') {?>
    <dl>
      <dt>事件数量：<strong style="color:red;"> <?php echo $output['statistics']['supplier_order']+$output['statistics']['supplier_refund']  ?> </strong></dt>
    </dl>
    <ul class="cp-toast-list">
      <?php if ($output['statistics']['supplier_order'] > 0) {?>
        <li>
          <span>[供应商-订单]</span>
          <a target="workspace" href="<?php echo urlSCMSupplier('client_order','index');?>" onclick="$('.dialog_close_button').click();openItem('supplier|client_order')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['supplier_order'];?></strong>个订单申请需要处理。</a>
        </li>
      <?php }?>
      <?php if ($output['statistics']['supplier_refund'] > 0) {?>
        <li>
          <span>[供应商-退货单]</span>
          <a target="workspace" href="<?php echo urlSCMSupplier('delivering_refund','index');?>" onclick="$('.dialog_close_button').click();openItem('supplier|delivering_refund')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['supplier_refund'];?></strong>个未入库退货单申请需要处理。</a>
        </li>
      <?php }?>
<!--      --><?php //if ($output['statistics']['supplier_warn'] == true) {?>
<!--        <li>-->
<!--          <span>[供应商-预警]</span>-->
<!--          <a target="workspace" href="--><?php //echo urlSCMSupplier('client','index');?><!--" onclick="openItem('supplier|client')"><i class="fa fa-bell-o"></i>您合作的终端店有预警信息。 </a>-->
<!--        </li>-->
<!--      --><?php //}?>
<?php }else if ($output['statistics']['user_type'] == 'client') {?>
    <dl>
      <dt>事件数量：<strong style="color:red;"> <?php echo $output['statistics']['client_online_order_count']+$output['statistics']['client_order_count']  ?> </strong></dt>
    </dl>
    <ul class="cp-toast-list">
      <?php if ($output['statistics']['client_online_order_count'] > 0) {?>
        <li>
          <span>[终端店-商城订单]</span>
          <a target="workspace" href="<?php echo urlSCMClient('accept_order','index');?>" onclick="$('.dialog_close_button').click();openItem('client|accept_order')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['client_online_order_count'];?></strong>个商城订单需要处理。</a>
        </li>
      <?php }?>
      <?php if ($output['statistics']['client_order_count'] > 0) {?>
        <li>
          <span>[终端店-已发货订单]</span>
          <a target="workspace" href="<?php echo urlSCMClient('client_storage','index');?>" onclick="$('.dialog_close_button').click();openItem('client|client_storage')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['client_order_count'];?></strong>个已发货订单需要处理。</a>
        </li>
      <?php }?>
<?php }else{?>
  <ul class="cp-toast-list">
  <?php if ($output['statistics']['cashlist'] > 0) {?>
    <li>
      <span>[商城-会员]</span>
      <a target="workspace" href="<?php echo urlAdminShop('predeposit','pd_cash_list');?>" onclick="openItem('shop|predeposit')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['cashlist'];?></strong>条预存款提现申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['store_joinin'] > 0) {?>
    <li>
      <span>[商城-店铺]</span>
      <a target="workspace" href="<?php echo urlAdminShop('store', 'store_joinin');?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_joinin'];?></strong>条开店申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['store_reopen_applay'] > 0) {?>
    <li>
      <span>[商城-店铺]</span>
      <a target="workspace" href="<?php echo urlAdminShop('store', 'reopen_list');?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_reopen_applay'];?></strong>条开店续签申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['store_bind_class_applay']) {?>
    <li>
      <span>[商城-店铺]</span>
      <a target="workspace" href="<?php echo urlAdminShop('store', 'store_bind_class_applay_list');?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_bind_class_applay'];?></strong>条经营类目申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['store_expire']) {?>
    <li>
      <span>[商城-店铺]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_expire'];?></strong>家店铺即将到期。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['store_expired']) {?>
    <li>
      <span>[商城-店铺]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_expired'];?></strong>家店铺已经到期。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['product_verify']) {?>
    <li>
      <span>[商城-商品]</span>
      <a target="workspace" href="<?php echo urlAdminShop('goods', 'goods', array('type' => 'waitverify'));?>" onclick="openItem('shop|goods')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['product_verify'];?></strong>个商品需要审核。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['inform']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|inform')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['inform'];?></strong>条举报需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['brand_apply']) {?>
    <li>
      <span>[商城-商品]</span>
      <a target="workspace" href="<?php echo urlAdminShop('brand', 'brand_apply');?>" onclick="openItem('shop|brand')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['brand_apply'];?></strong>个新增品牌需要审核。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['refund']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|refund')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['refund'];?></strong>个实物退款申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['return']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|return')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['return'];?></strong>个实物退货申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['vr_refund']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|vr_refund')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['vr_refund'];?></strong>个虚拟退款申请需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['complain_new']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|complain')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['complain_new'];?></strong>条投诉需要处理。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['complain_handle']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="<?php echo urlAdminShop('complain', 'complain_handle_list')?>" onclick="openItem('shop|complain')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['complain_handle'];?></strong>条投诉等待仲裁。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['groupbuy_verify']) {?>
    <li>
      <span>[商城-促销]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|groupbuy')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['groupbuy_verify'];?></strong>个团购申请需要审核。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['points_order']) {?>
    <li>
      <span>[商城-交易]</span>
      <a target="workspace" href="<?php echo urlAdminShop('pointprod', 'pointorder_list');?>" onclick="openItem('shop|pointprod')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['points_order'];?></strong>个积分订单需要发货。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['check_billno']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="<?php echo urlAdminShop('bill', 'show_statis');?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['check_billno'];?></strong>个实物账单等待审核。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['check_vr_billno']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="<?php echo urlAdminShop('bill', 'show_statis')?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['check_vr_billno'];?></strong>个虚拟订单等待审核。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['pay_billno']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="<?php echo urlAdminShop('bill', 'show_statis')?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['pay_billno'];?></strong>个实物账单等待支付。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['pay_vr_billno']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="<?php echo urlAdminShop('bill', 'show_statis')?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['pay_vr_billno'];?></strong>个虚拟账单等待支付。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['mall_consult']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|mall_consult')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['mall_consult'];?></strong>个客户提问需要回复。</a>
    </li>
  <?php }?>
  <?php if ($output['statistics']['delivery_point']) {?>
    <li>
      <span>[商城-运营]</span>
      <a target="workspace" href="<?php echo urlAdminShop('delivery', 'index', array('sign' => 'verify'));?>" onclick="openItem('shop|delivery')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['delivery_point'];?></strong>个物流自提服务站申请需要处理。</a>
    </li>
  <?php }?>
  <?php if (C('cms_isuse')) {?>
    <?php if ($output['statistics']['cms_article_verify']) {?>
      <li>
        <span>[资讯-文章]</span>
        <a target="workspace" href="<?php echo urlAdminCms('cms_article', 'cms_article_list_verify');?>" onclick="openItem('cms|cms_article')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['cms_article_verify'];?></strong>个文章需要审核。</a>
      </li>
    <?php }?>
    <?php if ($output['statistics']['cms_picture_verify']) {?>
      <li>
        <span>[资讯-画报]</span>
        <a target="workspace" href="<?php echo urlAdminCms('cms_picture', 'cms_picture_list_verify');?>" onclick="openItem('cms|cms_picture')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['cms_picture_verify'];?></strong>个画报需要审核。</a>
      </li>
    <?php }?>
  <?php }?>
  <?php if (C('circle_isuse')) {?>
    <?php if ($output['statistics']['circle_verify']) {?>
      <li>
        <span>[圈子-圈组]</span>
        <a target="workspace" href="<?php echo urlAdminCircle('circle_manage', 'circle_verify');?>" onclick="openItem('circle|circle_manage')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['circle_verify'];?></strong>个圈子需要审核。</a>
      </li>
    <?php }?>
    </ul>

<?php }?>


    <?php }?>

  </div>
</div>


