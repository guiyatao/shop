<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "合作终端店";?></h3>
                <h5><?=$output['subject']?></h5>
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
            <li><?=$output['explanation']?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=client&op=get_detail_xml&clie_id=<?=$output['clie_id']?>&con=<?=$output['condition']?>',
            colModel:[
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '商品名称', name : 'goods_nm', width : 300, sortable : true, align: 'center'},
                {display: '商品编码', name : 'goods_barcode', width : 100, sortable : true, align: 'center'},
                {display: '库存单位', name : 'goods_unit', width : 50, sortable : false, align: 'center'},
                {display: '商品规格', name : 'goods_spec', width : 150, sortable : false, align: 'center'},
                {display: '库存', name : 'goods_stock', width : 50, sortable : false, align: 'center'},
                {display: '库存下限', name : 'goods_low_stock', width : 50, sortable : false, align: 'center'},
                {display: '库存上限', name : 'goods_uper_stock', width : 50, sortable : false, align: 'center'},
                {display: '生产日期', name : 'production_date', width : 120, sortable : false, align: 'center'},
                {display: '有效期提醒天数', name : 'valid_remind', width : 90, sortable : false, align: 'center'},
                {display: '保质期', name : 'shelf_life', width : 60, sortable : false, align: 'center'},
                {display: '供应商名', name : 'supp_ch_name', width : 160, sortable : false, align: 'center'},

            ],
            buttons:[
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation },
            ],
            searchitems:[
                {display: '商品名称', name : 'goods_nm'},
                {display: '商品编码', name : 'goods_barcode'},
                {display: '供应商名称', name : 'supp_ch_name'},
            ],
            sortname:"number",
            sortorder:"asc",
            title:"<?=$output['title']?>"

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
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_detail_csv&id=' + id +'&clie_id=<?=$output['clie_id']?>&con=<?=$output['condition']?>';

    }

</script>
