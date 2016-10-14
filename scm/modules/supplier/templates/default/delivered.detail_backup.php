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
                <h3>已完成订单</h3>
                <h5>显示供应商的已完成订单</h5>
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
                <?php if($output['order_info']['order_status'] == 3) {?>
                    <li class="current">
                        <h5>已取消</h5>
                    </li>
                <?php }else{ ?>
                    <li class="current">
                        <h5>已发货</h5>
                        <i class="fa fa-arrow-circle-right"></i>
                        <time><?php echo $output['order_info']['out_date'];?></time>
                    </li>
                    <?php if($output['order_info']['refund_flag'] == 1) {?>
                        <li class="current">
                            <h5>申请退货</h5>
                            <i class="fa fa-arrow-circle-right"></i>
                        </li>
                        <li class="current">
                            <h5>退货完成/结算日期</h5>
                            <time><?php echo $output['order_info']['pay_start_time'];?></time>
                        </li>
                    <?php }else{?>
                        <li class="current">
                            <h5>已入库</h5>
                            <time><?php echo $output['order_info']['in_date'];?></time>
                        </li>
                    <?php } ?>
                <?php } ?>
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
                        <dt>发货时间：</dt>
                        <dd><?= $output['order_info']['out_date'];?></dd>
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
                                    <td><?=$v['production_date'] ?></td>
                                    <td><?=$v['valid_remind'] ?></td>
                                    <td><?=$v['shelf_life'] ?></td>
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
            </div>
        </div>
    </div>
</div>

