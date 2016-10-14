<?php if (!empty($output['client_info'])){?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>

<div class="print-layout">
    <div class="print-btn" id="printbtn" title="<?php echo "选择喷墨或激光打印机<br/>根据下列纸张描述进行<br/>设置并打印发货单据";?>"><i></i><a href="javascript:void(0);"><?php echo "打印";?></a></div>
    <div class="a5-size"></div>
    <dl class="a5-tip">
        <dt>
        <h1>A5</h1>
        <em>Size: 210mm x 148mm</em></dt>
        <dd><?php echo "当打印设置选择A5纸张、横向打印、无边距时每张A5打印纸可输出1页订单。";?></dd>
    </dl>
    <div class="a4-size"></div>
    <dl class="a4-tip">
        <dt>
        <h1>A4</h1>
        <em>Size: 210mm x 297mm</em></dt>
        <dd><?php echo "当打印设置选择A4纸张、竖向打印、无边距时每张A4打印纸可输出2页订单。";?></dd>
    </dl>
    <div class="print-page">
        <div id="printarea">

            <div class="orderprint">
                <div class="top">
                    <div class="full-title">发货单</div>

                </div>
                <table class="buyer-info">
                    <tr>
                        <td class="w200">终端店：<?php echo $output['client_info']['clie_ch_name'];?></td>
                        <td>电话：<?php echo @$output['client_info']['clie_tel'];?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3">地址：<?php echo @$output['client_info']['clie_address'];?></td>
                    </tr>
                    <tr>
                        <td>订单号：<?php echo $output['client_info']['order_no'];?></td>
                        <td>下单时间：<?php echo $output['client_info']['order_date']?></td>
                    </tr>
                </table>
                <table class="order-info">
                    <thead>
                        <tr>
                            <th class="w40">序号</th>
                            <th style="text-align: left !important;">商品名称</th>
                            <th class="w70" style="text-align: left !important;">单价(元)</th>
                            <th class="w50">数量</th>
                            <th class="w70" style="text-align: left !important;">小计(元)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
                            <?php foreach($output['goods_list'] as $k => $v){?>
                                <tr>
                                    <td><?=$v['number']?></td>
                                    <td style="text-align: left !important;"><?=$v['goods_nm']?></td>
                                    <td style="text-align: left !important;"><?=$v['goods_price']?></td>
                                    <td><?=$v['set_num']?></td>
                                    <td style="text-align: left !important;"><?=$v['actual_amount']?></td>
                                </tr>
                            <?php }?>
                        <?php }?>
                        <tr>
                            <th></th>
                            <th colspan="2" style="text-align: left !important;">合计</th>
                            <th><?=$output['total_num']?></th>
                            <th style="text-align: left !important;"> <?php echo @$output['client_info']['total_amount'];?></th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="10">
                                <span>总计：¥<?=$output['client_info']['total_amount'];?></span>
                                <span>运费：¥<?=$output['trans_fee'];?></span>
                                <span>优惠：¥<?=$output['discount'];?></span>
                                <span>订单总额：¥<?=$output['client_info']['total_amount'];?></span>
                                <span>供应商：<?=$output['supplier']['supp_ch_name']?></span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div style="margin-top:30px;" id="qrcode"></div>
            </div>

        </div>
        <?php }?>
    </div>
</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.printarea.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.qrcode.min.js"></script>
<script>
    $(function(){
        $("#printbtn").click(function(){
            $("#printarea").printArea();
        });
    });

    $('#qrcode').qrcode({
        render: "table", //table方式
        width: 120, //宽度
        height:120, //高度
        text: "<?php echo $output['client_info']['order_no'];?>" //任意内容
    });
    //打印提示
    $('#printbtn').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'bottom',
        offsetY: 5,
        allowTipHover: false
    });
</script>