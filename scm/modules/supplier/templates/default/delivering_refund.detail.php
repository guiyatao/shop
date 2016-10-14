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
            <li><?php echo "您可以查看未入库之前终端店申请的退货单";?></li>
            <li><?php echo "您可以选择对整个订单退货或者不允许退货";?></li>
        </ul>
    </div>
    <form id="refund_detail_form" method='post'>
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <input type="hidden" name="order_id" id="order_id" value="<?=$output['order_info']['id']?>" />
        <input type="hidden" id="allow_refund" name="allow_refund" />
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
                    <th width="80" align="center">订货数量</th>
<!--                    <th width="60" align="center">发货数量</th>-->
                    <th width="70" align="center">总额(元)</th>
                    <th width="80" align="center">规格</th>
                    <th width="130" align="center">生产厂家</th>
                    <th width="130" align="center">产地</th>
                    <th width="130" align="center">订货日期</th>
                    <th width="130" align="center">发货日期</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
                    <?php foreach($output['goods_list'] as $k => $v){ ?>
                        <tr data-id="<?=$v['id']?>">
                            <input type="hidden" value="<?php echo $v['id'];?>" name="ids[]" />
                            <input type="hidden" value="<?php echo $v['id'];?>" name="ids[]" />
                            <td><?=$v['order_no'] ?></td>
                            <td><?=$output['client_info']['clie_ch_name'] ?></td>
                            <td><?=$v['goods_barcode'] ?></td>
                            <td><?=$v['goods_nm'] ?></td>
                            <td><?=$v['goods_price'] ?></td>
                            <td><?=$v['goods_discount'] ?></td>
                            <td><?=$v['goods_discount_price'] ?></td>
                            <td><?=$v['goods_unit'] ?></td>
                            <td><?=$v['order_num'] ?></td>
                            <td><?=$v['set_num'] ?> </td>
                            <td><?=$v['actual_amount'] ?></td>
                            <td><?=$v['goods_spec'] ?></td>
                            <td><?=$v['produce_company'] ?></td>
                            <td><?=$v['produce_area'] ?></td>
                            <td><?=$v['order_date'] ?></td>
                            <td><?=$v['out_date'] ?></td>
                            <td></td>
                        </tr>
                    <?php } ?>
                <?php }else { ?>
                    <tr>
                        <td class="no-data" colspan="100"><i class="fa fa-exclamation-circle"></i><?php echo $lang['nc_no_record'];?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <dl class="row">
                <dt class="tit">
                    <label>已订商品总额</label>
                </dt>
                <dd class="opt red">
                    <label><?=$output['total_amount']?></label><label>&nbsp;元</label>
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
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="refund_confirm">确认退货</a></div>
        </div>
    </form>
</div>

<script>
    $(function(){
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
//                {display: '<i class="fa fa-pencil-square-o"></i>允许退货', name : 'confirm', bclass : 'add', title : '允许退货', onpress : fg_operation },
//                {display: '<i class="fa fa-pencil-square-o"></i>不允许退货', name : 'no_confirm', bclass : 'add', title : '不允许退货', onpress : fg_operation }
            ]
        });
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
        }else if(name == 'confirm' || name == 'no_confirm'){
            if(name == 'confirm')
                $('#allow_refund').val(4);
            else if(name == 'no_confirm')
                $('#allow_refund').val(5);
            $("#refund_detail_form").submit();
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = 'index.php?act=delivering_refund&op=export_detail_csv&order_id=<?=$v['order_id'] ?>&id=' + id;
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
        $("#refund_confirm").click(function(){
            $('#allow_refund').val(4);
            $("#refund_detail_form").submit();
        });
        $("#no_refund_confirm").click(function(){
            $('#allow_refund').val(5);
            $("#refund_detail_form").submit();
        });
    });
</script>
