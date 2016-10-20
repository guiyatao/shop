<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="ncap-goods-goods">
    <div class="title">
        <h4>订单索引编号</h4>
        <h4>终端店编码</h4>
        <h4>商品名称</h4>
        <h4>商品价格</h4>
        <h4>商品数量</h4>
        <h4>商品条码</h4>
    </div>
    <div class="content">
        <ul>
            <?php foreach ($output['goods_list'] as $val) { ?>
                <li><span><?php echo $val['order_id']; ?></span>
                    <span><?php echo $val['clie_id']; ?></span>
                    <span><?php echo $val['goods_name']; ?></span>
                    <span><?php echo $val['goods_price']; ?></span>
                    <span><?php echo $val['goods_num']; ?></span>
                    <span><?php echo $val['goods_barcode']; ?></span>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(function () {
//自动加载滚动条
        $('.content').perfectScrollbar();
    });
</script>