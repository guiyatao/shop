<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="ncap-goods-goods">
    <div class="title">

        <h4 style="width: %20">终端店编码</h4>
        <h4 style="width: %20">商品名称</h4>
        <h4 style="width: %20">商品价格</h4>
        <h4 style="width: %20">商品数量</h4>
        <h4 style="width: %20">商品条码</h4>
    </div>
    <div class="content">
        <ul>
            <?php foreach ($output['goods_list'] as $val) { ?>
                <li>
                    <span style="width: %20"><?php echo $val['clie_id']; ?></span>
                    <span style="width: %20"><?php echo $val['goods_name']; ?></span>
                    <span style="width: %20"><?php echo $val['goods_price']; ?></span>
                    <span style="width: %20"><?php echo $val['goods_num']; ?></span>
                    <span style="width: %20"><?php echo $val['goods_barcode']; ?></span>
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