<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="ncap-goods-goods">
    <div class="title">
        <h4>商品条码</h4>
        <h4>商品名称</h4>
        <h4>订购数量</h4>
        <h4>生产厂家</h4>
    </div>
    <div class="content">
        <ul>
            <?php foreach ($output['goods_list'] as $val) { ?>
                <li><span><?php echo $val['goods_barcode']; ?></span>
                    <span><?php echo $val['goods_nm']; ?></span>
                    <span><?php echo $val['set_num']; ?></span>
                    <span><?php echo $val['produce_company']; ?></span>
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