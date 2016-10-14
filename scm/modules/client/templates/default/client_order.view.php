<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.ncm-goods-gift {
	text-align: left;
}
.ncm-goods-gift ul {
    display: inline-block;
    font-size: 0;
    vertical-align: middle;
}
.ncm-goods-gift li {
    display: inline-block;
    letter-spacing: normal;
    margin-right: 4px;
    vertical-align: top;
    word-spacing: normal;
}
.ncm-goods-gift li a {
    background-color: #fff;
    display: table-cell;
    height: 30px;
    line-height: 0;
    overflow: hidden;
    text-align: center;
    vertical-align: middle;
    width: 30px;
}
.ncm-goods-gift li a img {
    max-height: 30px;
    max-width: 30px;
}
</style>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back(-1)" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['client_order_manage'];?></h3>
        <h5><?php echo $lang['client_order_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <div class="ncap-order-style">
    <div class="titile">
      <h3></h3>
    </div>
    <div class="ncap-order-flow">
      <ol class="num5">
        <li class="current">
          <h5>生成订单</h5>
          <i class="fa fa-arrow-circle-right"></i>
          <time><?php echo $output['order_info']['order_date'];?></time>
        </li>
        <?php if($output['order_info']['order_status'] == 3){ ?>
          <li class="current">
            <h5>已付款</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>已取消</h5>
          </li>
        <?php }elseif($output['order_info']['order_status'] == 4){ ?>
          <li class="current">
            <h5>已付款</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>已发货</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>申请退货</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>已退货</h5>
          </li>
        <?php }elseif($output['order_info']['order_status'] == 1){ ?>
          <li class="current">
            <h5>已付款</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>已发货</h5>
            <i class="fa fa-arrow-circle-right"></i>
          </li>
          <li class="current">
            <h5>已入库</h5>
          </li>
        <?php }elseif($output['order_info']['order_status'] == 0){ ?>
          <?php if($output['order_info']['pay_flag'] == 0){ ?>
            <li class="current">
              <h5>未付款</h5>
            </li>
          <?php }else{ ?>
            <li class="current">
              <h5>已付款</h5>
              <i class="fa fa-arrow-circle-right"></i>
            </li>
              <?php if($output['order_info']['out_flag'] == 0){ ?>
              <li class="current">
                <h5>未发货</h5>
              </li>
            <?php }else{ ?>
              <li class="current">
                <h5>已发货</h5>
                <i class="fa fa-arrow-circle-right"></i>
              </li>
              <?php if($output['order_info']['refund_flag'] == 0) { ?>
                <li class="current">
                  <h5>未入库</h5>
                </li>
                <?php }else{ ?>
                <li class="current">
                  <h5>未入库</h5>
                  <i class="fa fa-arrow-circle-right"></i>
                </li>
                <li class="current">
                  <h5>申请退货</h5>
                </li>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        <?php } ?>
      </ol>
    </div>
    <div class="ncap-order-details">
      <ul class="tabs-nav">
        <li class="current"><a href="javascript:void(0);">订单详情</a></li>
      </ul>
      <div class="tabs-panels">
        <div class="misc-info">
          <h4>下单/支付</h4>
          <dl>
            <dt>订单号：</dt>
            <dd><?= $output['order_info']['order_no'];?></dd>
            <dt>下单时间：</dt>
            <dd><?= $output['order_info']['order_date'];?></dd>
          </dl>
        </div>
        <div class="add-note">
          <h4>购买/终端店信息</h4>
          <dl>
            <dt>终端店编号：</dt>
            <dd><?= $output['client_info']['clie_id'];?></dd>
            <dt>终端店名称：</dt>
            <dd><?= $output['client_info']['clie_ch_name'];?></dd>
            <dt>联系方式：</dt>
            <dd><?= $output['client_info']['clie_tel'];?></dd>
          </dl>
          <dl>
            <dt>收货地址：</dt>
            <dd><?= $output['client_info']['clie_address'];?></dd>
            <dt>买家留言：</dt>
            <dd><?= $output['order_info']['comments'];?></dd>
          </dl>
        </div>
        <div class="contact-info">
          <h4>销售/供应商信息</h4>
          <dl>
            <dt>供应商编号：</dt>
            <dd><?= $output['supplier_info']['supp_id'];?></dd>
            <dt>供应商名称：</dt>
            <dd><?= $output['supplier_info']['supp_ch_name'] ?></dd>
            <dt>联系方式：</dt>
            <dd><?= $output['supplier_info']['supp_tel'] ?></dd>
          </dl>
          <dl>
            <dt>供应商地址：</dt>
            <dd><?= $output['supplier_info']['supp_address'] ?></dd>
          </dl>
          <dl>
            <dt>有无赠品：</dt>
            <dd>
              <?php if(!empty($output['order_info']['gift_flag']) && ($output['order_info']['gift_flag'] == 1)){ ?>
                有赠品
              <?php }else if(empty($output['order_info']['gift_flag']) || ($output['order_info']['gift_flag'] == 0)){ ?>
                无赠品
              <?php } ?>
            </dd>
          </dl>
          <dl>
            <dt>备注：</dt>
            <dd><label><?=$output['order_info']['comments']?></label></dd>
          </dl>
        </div>
        <div class="goods-info">
          <h4><?php echo $lang['product_info'];?></h4>
          <table>
            <thead>
              <tr>
                <th>商品条码</th>
                <th>商品名称</th>
                <th>原价</th>
                <th>折扣</th>
                <th>折扣单价</th>
                <th>订购数量</th>
                <th>付款金额</th>
              </tr>
            </thead>
            <tbody>
          <?php if(!empty($output['order_info']['extend_order_goods']) && is_array($output['order_info']['extend_order_goods'])){ ?>
          <?php foreach($output['order_info']['extend_order_goods'] as $val){ ?>
              <tr>
                <td class="w80"><?php echo $val['goods_barcode'];?></td>
                <td class="w80"><?php echo $val['goods_nm'];?></td>
                <td class="w80"><?php echo $val['goods_price'];?></td>
                <td class="w60"><?php echo $val['goods_discount'];?></td>
                <td class="w100"><?php echo $val['goods_discount_price']; ?></td>
                <td class="w60"><?php echo $val['order_num'];?></td>
                <td class="w80"><?php echo $val['actual_amount'];?></td>
              </tr>
          <?php } ?>
          <?php }else { ?>
          <tr>
            <td class="no-data" colspan="100"><i class="fa fa-exclamation-triangle"></i><?php echo $lang['nc_no_record'];?></td>
          </tr>
          <?php } ?>
            </tbody>
            <!-- S 促销信息 -->
            <?php $pinfo = $output['order_info']['extend_order_common']['promotion_info'];?>
            <?php if(!empty($pinfo)){ ?>
            <?php $pinfo = unserialize($pinfo);?>
            <tfoot>
              <tr>
                <th colspan="10">其它信息</th>
              </tr>
              <tr>
                <td colspan="10">
              <?php if($pinfo == false){ ?>
              <?php echo $output['order_info']['extend_order_common']['promotion_info'];?>
              <?php }elseif (is_array($pinfo)){ ?>
              <?php foreach ($pinfo as $v) {?>
              <dl class="nc-store-sales"><dt><?php echo $v[0];?></dt><dd><?php echo $v[1];?></dd></dl>
              <?php }?>
              <?php }?>
                </td>
              </tr>
            </tfoot>
            <?php } ?>
            <!-- E 促销信息 -->
          </table>
        </div>
        <div class="total-amount">
          <h3><?php echo $lang['order_total_price'];?><?php echo $lang['nc_colon'];?><strong class="red_common"><?php echo $lang['currency'].ncPriceFormat($output['order_info']['order_pay']);?></strong></h3>
          <?php if($output['order_info']['refund_amount'] > 0) { ?>
          (<?php echo $lang['order_refund'];?><?php echo $lang['nc_colon'];?><?php echo $lang['currency'].ncPriceFormat($output['order_info']['refund_amount']);?>)
          <?php } ?>
        </div>
      </div>

    </div>
  </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".tabs-nav > li > a").mousemove(function(e) {
            if (e.target == this) {
                var tabs = $(this).parent().parent().children("li");
                var panels = $(this).parents('.ncap-order-details:first').children(".tabs-panels");
                var index = $.inArray(this, $(this).parents('ul').find("a"));
                if (panels.eq(index)[0]) {
                    tabs.removeClass("current").eq(index).addClass("current");
                   panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
                }
            }
        });
    });
</script>
