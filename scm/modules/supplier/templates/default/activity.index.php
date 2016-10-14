<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "活动管理";?></h3>
                <h5><?php echo "当前供应商的新产品/新活动推荐菜单";?></h5>
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
            <li><?php echo "通过供应商活动管理，你可以进行查看、编辑当前供应商所有的活动";?></li>
            <li><?php echo "你可以根据条件搜索活动信息，然后选择相应的操作";?></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
        <div class="title">
            <h3>高级搜索</h3>
        </div>
        <form method="get" name="formSearch" id="formSearch">
            <div id="searchCon" class="content">
                <div class="layout-box">
                    <dl>
                        <dt>活动状态</dt>
                        <dd>
                            <select name="act_status" class="s-select">
                                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                                <option value="1">未开始</option>
                                <option value="2">进行中</option>
                                <option value="3">已结束</option>
                            </select>
                        </dd>
                    </dl>
                    <dl>
                        <dt>大于开始时间</dt>
                        <dd>
                            <input type="text" value="" name="start_date" id="start_date" class="s-input-txt">
                        </dd>
                    </dl>
                    <dl>
                        <dt>小于结束时间</dt>
                        <dd>
                            <input type="text" value="" name="end_date" id="end_date" class="s-input-txt">
                        </dd>
                    </dl>

                </div>
            </div>
            <div class="bottom">
                <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a>
                <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        $("#start_date").datepicker({dateFormat: 'yy-mm-dd'});
        $("#end_date").datepicker({dateFormat: 'yy-mm-dd'});
        // 高级搜索提交
        $('#ncsubmit').click(function(){
            $("#flexigrid").flexOptions({url: 'index.php?act=activity&op=get_xml&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        // 高级搜索重置
        $('#ncreset').click(function(){
            $("#flexigrid").flexOptions({url: 'index.php?act=activity&op=get_xml'}).flexReload();
            $("#formSearch")[0].reset();
        });

        $("#flexigrid").flexigrid({
            url:'index.php?act=activity&op=get_xml',
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '活动ID', name : 'act_id', width : 80, sortable : true, align: 'center'},
                {display: '活动名称', name : 'act_name', width : 150, sortable : false, align: 'center'},
                {display: '活动图片', name : 'act_banner', width : 100, sortable : false, align: 'center'},
                {display: '状态', name : 'status', width : 100, sortable : false, align: 'center'},
                {display: '开始时间', name : 'start_date', width : 130, sortable : false, align: 'center'},
                {display: '结束时间', name : 'end_date', width : 130, sortable : false, align: 'center'},
                {display: '活动状态', name : 'act_status', width : 100, sortable : false, align: 'center'},
            ],
            buttons:[
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation },
                {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation },
//                {display: '<i class="fa fa-check"></i>批量发送', name : 'sent', bclass : 'add', title : '将选定行数据批量发送', onpress : fg_operation },
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出Excel文件', onpress : fg_operation }
            ],
            searchitems:[
                {display: '活动ID', name : 'act_id'},
                {display: '活动名称', name : 'act_name'},
            ],
            sortname:"act_id",
            sortorder:"asc",
            title:"活动列表"

        });
    });

    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=activity&op=activity_add';
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
        }else if(name == 'sent'){
            if ($('.trSelected', bDiv).length == 0) {
                showError('请选择要操作的数据项！');
            }else{
                var itemids = new Array();
                $('.trSelected', bDiv).each(function(i){
                    itemids[i] = $(this).attr('data-id');
                });
                fg_sent(itemids);
            }
        }
    }

    function fg_csv(ids) {
        id = ids.join(',');
        window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&id=' + id;
    }
    function fg_del(ids) {
        if (typeof ids == 'number') {
            var ids = new Array(ids.toString());
        };
        id = ids.join(',');
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            $.getJSON('index.php?act=activity&op=activity_del', {id:id}, function(data){
                if (data.state) {
                    location.reload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }

</script>
