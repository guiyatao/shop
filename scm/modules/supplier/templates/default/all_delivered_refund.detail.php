<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>合作终端店退货单管理</h3>
                <h5>显示合作终端店提交的退货单详情</h5>
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
            <li><?php echo "您可以查看退货单详情";?></li>
        </ul>
    </div>
    <form id="refund_detail_form" method='post'>
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <table class="flex-table">
            <thead>
            <tr>
                <th width="120" align="center">退货单号</th>
                <th width="80" align="center">终端店名称</th>
                <th width="80" align="center">商品条码</th>
                <th width="80" align="center">商品名称</th>
                <th width="50" align="center">原价(元)</th>
                <th width="40" align="center">折扣</th>
                <th width="40" align="center">单位</th>
                <th width="60" align="center">退货数量</th>
                <th width="70" align="center">退款金额(元)</th>
                <th width="80" align="center">规格</th>
                <th width="80" align="center">供应商名</th>
                <th width="60" align="center">退货状态</th>
                <th width="120" align="center">退货日期</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($output['refund_list']) && is_array($output['refund_list'])){ ?>
                <?php foreach($output['refund_list'] as $k => $v){ ?>
                    <tr>
                        <input type="hidden" value="<?php echo $v['id'];?>" name="ids[]" />
                        <td><?=$v['refund_no'] ?></td>
                        <td><?=$v['clie_ch_name'] ?></td>
                        <td><?=$v['goods_barcode'] ?></td>
                        <td><?=$v['goods_nm'] ?></td>
                        <td><?=$v['goods_price'] ?></td>
                        <td><?=$v['goods_discount'] ?></td>
                        <td><?=$v['goods_unit'] ?></td>
                        <td><?php if($v['refund_num'] != null) echo $v['refund_num'];else echo 0;?></td>
                        <td><?php if($v['goods_discount_price'] == null) echo 0; else echo $v['goods_discount_price']; ?></td>
                        <td><?=$v['goods_spec'] ?></td>
                        <td><?=$v['supp_ch_name'] ?></td>
                        <td><?php if($v['refund_flag'] == 0) echo "已提交退货"; else if($v['refund_flag'] == 1) echo "退货成功"; else if($v['refund_flag'] == 2) echo "退货失败"; ?></td>
                        <td><?=$v['refund_date'] ?></td>
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
    </form>
</div>

<script>
    $(function(){
        $('.flex-table').flexigrid({
            height:'auto',// 高度自动
            usepager: false,// 不翻页
            striped:false,// 不使用斑马线
            resizable: false,// 不调节大小
            title: '退货单详情页',// 表格标题
            reload: false,// 不使用刷新
            columnControl: false,// 不使用列控制
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation },
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
        }else if(name == 'confirm'){
            $("#refund_detail_form").submit();
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = 'index.php?act=all_delivered_refund&op=export_detail_csv&refund_no=<?=$v['refund_no'] ?>&id=' + id;
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
</script>
