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
                <h3>未发货订单</h3>
                <h5>显示终端店提交的商品订单</h5>
            </div>
        </div>
    </div>
    <div class="ncap-order-style">
        <div class="title">
            <h3></h3>
        </div>
        <div class="ncap-order-flow">
            <ol class="num5">
                <li class="current">
                    <h5>生成订单</h5>
                    <i class="fa fa-arrow-circle-right"></i>
                    <time><?php echo $output['order_info']['order_date'];?></time>
                </li>
                <li class="current">
                    <h5>未发货</h5>
                </li>
        </div>
        <div class="ncap-order-details">
            <ul class="tabs-nav">
                <li class="current"><a href="javascript:void(0);">订单详情</a></li>
            </ul>
            <div class="tabs-panels">
                <form id="order_detail_form" method='post'>
                    <input type="hidden" name="form_submit" value="ok" />
                    <input type="hidden" name="submit_type" id="submit_type" value="" />
                    <input type="hidden" name="order_id" id="order_id" value="<?=$output['order_info']['id']?>" />
                    <input type="hidden" name="prepare_flag" id="prepare_flag" value="<?=$output['order_info']['prepare_flag']?>" />
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
                            <dd></dd>
                        </dl>
                        <dl>
                            <dt>有无赠品：</dt>
                            <dd>
                                <div class="onoff">
                                    <?php if(!empty($output['order_info']['gift_flag']) && ($output['order_info']['gift_flag'] == 1)){ ?>
                                        <label for="have_gift" class="cb-enable selected">有赠品</label>
                                        <label for="no_gift" class="cb-disable" >无赠品</label>
                                        <input id="have_gift" name="gift_flag" value="1"  checked="checked" type="radio"   />
                                        <input id="no_gift" name="gift_flag" value="0" type="radio"  />
                                    <?php }else if(empty($output['order_info']['gift_flag']) || ($output['order_info']['gift_flag'] == 0)){ ?>
                                        <label for="have_gift" class="cb-enable  ">有赠品</label>
                                        <label for="no_gift" class="cb-disable selected" >无赠品</label>
                                        <input id="have_gift" name="gift_flag" value="1" type="radio"   />
                                        <input id="no_gift" name="gift_flag" value="0" checked="checked" type="radio"  />
                                    <?php } ?>
                                </div>
                            </dd>
                        </dl>
                        <dl>
                            <dt>备注：</dt>
                            <dd><input type="text" style="width:400px;" name="comments" value="<?=$output['order_info']['comments']?>" /></dd>
                        </dl>
                    </div>
                    <div class="goods-info">
                        <h4>商品信息</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>商品条码</th>
                                    <th>商品名称</th>
                                    <th>原价</th>
                                    <th>折扣</th>
                                    <th>折扣价</th>
                                    <th>订购数量</th>
                                    <th>生产日期</th>
                                    <th>有效期提醒天数</th>
                                    <th>保质期</th>
                                    <th>付款金额</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
                                <?php foreach($output['goods_list'] as $k => $v){ ?>
                                <tr>
                                    <input type="hidden" value="<?php echo $v['id'];?>" name="ids[]" />
                                    <td><?=$v['goods_barcode'] ?></td>
                                    <td><?=$v['goods_nm'] ?></td>
                                    <td><?=$v['goods_price'] ?></td>
                                    <td><?=$v['goods_discount'] ?></td>
                                    <td><?=$v['goods_discount_price'] ?></td>
                                    <td><?=$v['order_num'] ?></td>
                                    <td><input type="text" style="width: 120px;" name="production_date[]" value="<?=$v['production_date'] ?>" class="production_date" /></td>
                                    <td><input type="text" style="width: 30px;" name="valid_remind[]" class="valid_remind" value="<?=$v['valid_remind'] ?>" /></td>
                                    <td><?=$v['shelf_life'] ?><input type="hidden"  name="shelf_life[]" value="<?=$v['shelf_life'] ?>" /></td>
                                    <td><?=$v['actual_amount'] ?></td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="total-amount">
                        <h3>订单总额：<strong class="red_common">￥<?=$output['order_info']['order_pay']?></strong></h3>
                    </div>
                    <div class="bot"><?php if($output['order_info']['prepare_flag'] == 0){?> <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">备货完成</a> <?php }else{?> <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认发货</a><?php }?></div>
                </form>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".production_date").datepicker({dateFormat: 'yy-mm-dd'});
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
        $('.valid_remind').blur(
            function(){
                //获取当前的有效期提醒天数
                //如果不是数字或者为空
                if(isNaN($(this).val()) || $(this).val() == ''){
                    //默认提醒天数30天
                    valid_remind = 30;
                }else{
                    var valid_remind = parseInt($(this).val());
                    if(valid_remind < 0){
                        valid_remind = 0;
                    }else if(valid_remind > 1000){
                        valid_remind = 1000;
                    }
                }
                $(this).val(valid_remind);
            }
        );
        $(function(){
            $("#submitBtn").click(function(){
                if($("#order_detail_form").valid()){
                    $("#order_detail_form").submit();
                }
            });
        });
    });
</script>


