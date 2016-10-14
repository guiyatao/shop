<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>合作终端店订单管理</h3>
                <h5>显示合作终端店提交的订单详情</h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span>
        </div>
        <ul>
            <li><?php echo "您可以查看合作终端店已提交的订单并且修改发货数量";?></li>
            <li><?php echo "发货数量应该在最小配量和订货数量之间，如果不填，则系统默认提交最小配量";?></li>
        </ul>
    </div>
    <form id="order_detail_form" method='post'>
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <input type="hidden" name="order_id" id="order_id" value="<?=$output['order_info']['id']?>" />
        <input type="hidden" name="prepare_flag" id="prepare_flag" value="<?=$output['order_info']['prepare_flag']?>" />
        <div class="ncap-form-default">
            <table class="flex-table">
            <thead>
            <tr>
                <th width="150" align="center">订单号</th>
                <th width="180" align="center">终端店名称</th>
                <th width="120" align="center">商品条码</th>
                <th width="180" align="center">商品名称</th>
                <th width="80" align="center">原价(元)</th>
                <th width="80" align="center">折扣</th>
                <th width="80" align="center">折扣价(元)</th>
                <th width="80" align="center">单位</th>
                <th width="80" align="center">最小配量</th>
                <th width="80" align="center">订货数量</th>
<!--                <th width="60" align="center">发货数量</th>-->
                <th width="80" align="center">总额(元)</th>
                <th width="80" align="center">规格</th>
                <th width="130" align="center">生产厂家</th>
                <th width="130" align="center">产地</th>
                <th width="130" align="center">订货日期</th>
                <th width="130" align="center">生产日期</th>
                <th width="100" align="center">有效期提醒天数</th>
                <th width="100" align="center">保质期</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
                <?php foreach($output['goods_list'] as $k => $v){ ?>
                    <tr data-id="<?=$v['id']?>">
                        <input type="hidden" value="<?php echo $v['id'];?>" name="ids[]" />
                        <td><?=$v['order_no'] ?></td>
                        <td><?=$output['client_info']['clie_ch_name'] ?></td>
                        <td><?=$v['goods_barcode'] ?></td>
                        <td><?=$v['goods_nm'] ?></td>
                        <td><?=$v['goods_price'] ?></td>
                        <td><?=$v['goods_discount'] ?></td>
                        <td><?=$v['goods_discount_price'] ?></td>
                        <td><?=$v['goods_unit'] ?></td>
                        <td><?=$v['min_set_num'] ?></td>
                        <td><?=$v['order_num'] ?></td>
<!--                        <td class="sort"> <input min_set_num ="--><?//=$v['min_set_num'] ?><!--" order_num="--><?//=$v['order_num'] ?><!--"  data_price="--><?//=$v['goods_price'] ?><!--" data_discount="--><?//=$v['goods_discount'] ?><!--" type="number" class="edit_set_num" name="set_num[]" value="--><?php //if($v['set_num'] != null) echo $v['set_num'];else echo 0;?><!--" /> </td>-->
                        <td class="actual_amount"><?=$v['actual_amount'] ?></td>
                        <td><?=$v['goods_spec'] ?></td>
                        <td><?=$v['produce_company'] ?></td>
                        <td><?=$v['produce_area'] ?></td>
                        <td><?=$v['order_date'] ?></td>
                        <th width="130" align="center"> <input type="text" style="width: 120px;" name="production_date[]" value="<?=$v['production_date'] ?>" class="production_date" /></th>
                        <td><input type="text" style="width: 30px;" name="valid_remind[]" class="valid_remind" value="<?=$v['valid_remind'] ?>" /></td>
                        <td><?=$v['shelf_life'] ?><input type="hidden"  name="shelf_life[]" value="<?=$v['shelf_life'] ?>" /></td>
<!--                        <td></td>-->
                    </tr>
                <?php } ?>
            <?php }else { ?>
                <tr>
                    <td class="no-data" colspan="100"><i class="fa fa-exclamation-circle"></i><?php echo $lang['nc_no_record'];?> </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
            <dl class="row">
                <dt class="tit">
                    <label for="total_amount">已订商品总额</label>
                </dt>
                <dd class="opt red">
                    <label id="total_amount" name="total_amount" for="total_amount"><?=$output['total_amount']?></label><label>&nbsp;元</label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>已支付金额</label>
                </dt>
                <dd class="opt red">
                    <label><?=$output['order_info']['order_pay']?></label><label>&nbsp;元</label>
                </dd>
            </dl>
            <div class="bot"><?php if($output['order_info']['prepare_flag'] == 0){?> <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">备货完成</a> <?php }else{?> <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认发货</a><?php }?></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
    $(function(){
        $(".production_date").datepicker({dateFormat: 'yy-mm-dd'});
//        $("#production_date").datepicker({dateFormat: 'yy-mm-dd'});
        $('.flex-table').flexigrid({
            height:'auto',// 高度自动
            usepager: false,// 不翻页
            striped:false,// 不使用斑马线
            resizable: false,// 不调节大小
            title: '订单详情页',// 表格标题
            reload: false,// 不使用刷新
            columnControl: false,// 不使用列控制
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation },
            ]

        });
        $('span[nc_type="inline_edit"]').inline_edit({act: 'client_order',op: 'ajax'});

        $('.edit_set_num').blur(
            function(){
                //最小配量
                var min_set_num = $(this).attr("min_set_num");
                //订货数量
                var order_num = $(this).attr("order_num");
                //发货数量
                //如果不是数字或者为空默认发货数量为最小配量
                if(isNaN($(this).val()) || $(this).val() == ''){
                    //最小配量
                    set_num = min_set_num;

                }else{
                    var set_num = parseInt($(this).val());
                    if(parseInt(set_num) < parseInt(min_set_num)){
                        set_num = min_set_num;
                    }
                    if(parseInt(set_num) > parseInt(order_num)){
                        set_num = order_num;
                    }
                }
                $(this).val(set_num);
                var data_price = $(this).attr("data_price");
                var data_discount = $(this).attr("data_discount");
                //当前商品总额
                var order_pay = (set_num*data_price*data_discount).toFixed(2);
                $(this).parent().parent().next().children().text(order_pay);
                var total_amount = 0 ;
                $(".actual_amount").each(function()
                {
                    total_amount = total_amount + parseFloat($(this).text());
                })

                $('#total_amount').text(total_amount);
            }
        );
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

    });

    function fg_operation(name, bDiv) {
        if (name == 'csv') {
            if ($('.trSelected', bDiv).length == 0) {
                if (!confirm('您确定要下载全部数据吗？')) {
                    return false;
                }
            }
            var itemids = new Array();
            $('.trSelected', bDiv).each(function(i){
                itemids[i] = $(this).attr('data-id');
            });
            fg_csv(itemids);
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = 'index.php?act=client_order&op=export_detail_csv&order_id=<?=$output['order_info']['id']?>&id=' + id;
    }
    function fg_del(ids) {
        if (typeof ids == 'number') {
            var ids = new Array(ids.toString());
        };
        id = ids.join(',');
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            $.getJSON('index.php?act=goods&op=goods_del', {id:id}, function(data){
                if (data.state) {
                    location.reload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }
    $(function(){
        $("#submitBtn").click(function(){
            if($("#order_detail_form").valid()){
                $("#order_detail_form").submit();
            }
        });
    });
</script>
