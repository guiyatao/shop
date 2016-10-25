<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "供应商商品管理";?></h3>
                <h5><?php echo "当前供应商所有商品的索引及管理";?></h5>
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
            <li><?php echo "通过供应商管理，你可以进行查看、编辑商品资料等操作";?></li>
            <li><?php echo "你可以根据条件搜索商品，然后选择相应的操作";?></li>
            <li><?php echo "单位数量的含义是批发单位按照商品规格拆分的商品数量";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>

<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url:'index.php?act=goods&op=get_xml',
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '商品名称', name : 'goods_nm', width : 300, sortable : true, align: 'left'},
                {display: '商品编码', name : 'goods_barcode', width : 100, sortable : false, align: 'center'},
                {display: '商品单价(元)', name : 'goods_price', width : 100, sortable : true, align: 'center'},
                {display: '折扣', name : 'goods_discount', width : 100, sortable : false, align: 'center'},
                {display: '批发单位', name : 'goods_unit', width : 50, sortable : false, align: 'center'},
                {display: '最小配量', name : 'min_set_num', width : 50, sortable : false, align: 'center'},
                {display: '单位数量', name : 'unit_num', width : 50, sortable : false, align: 'center'},
                {display: '规格', name : 'goods_spec', width : 150, sortable : false, align: 'center'},
                {display: '生产日期', name : 'production_date', width : 150, sortable : false, align: 'center'},
                {display: '有效期提醒天数', name : 'valid_remind', width : 100, sortable : false, align: 'center'},
                {display: '保质期', name : 'shelf_life', width : 80, sortable : false, align: 'center'},
                {display: '状态', name : 'status', width : 80, sortable : false, align: 'center'},
            ],
            buttons:[
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation },
                {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation },
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出Excel文件', onpress : fg_operation },
                {display: '<i class="fa fa-file-excel-o"></i>导入数据', name : 'import_csv', bclass : 'csv', title : '将选定行数据导出Excel文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '商品编码', name : 'goods_barcode'},
                {display: '商品名称', name : 'goods_nm'},
            ],
            sortname:"id",
            sortorder:"asc",
            title:"商品列表",
        });
    });

    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=goods&op=goods_add';
//            window.location.href = 'index.php?act=goods&op=goods_import';
        }else if(name=='import_csv'){
            window.location.href = 'index.php?act=goods&op=goods_import';
        }else if (name == 'csv') {
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
        }else if(name == 'del'){
            if ($('.trSelected', bDiv).length == 0) {
                showError('请选择要操作的数据项！');
            }else{
                var itemids = new Array();
                $('.trSelected', bDiv).each(function(i){
                    itemids[i] = $(this).attr('data-id');
                });
                fg_del(itemids);
            }
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_step1&id=' + id;
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
