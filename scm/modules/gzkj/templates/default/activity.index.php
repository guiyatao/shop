<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "活动管理";?></h3>
                <h5><?php echo "平台新产品/新活动审核";?></h5>
            </div>
        </div>
    </div>
<!--    <!-- 操作说明 -->
<!--    <div class="explanation" id="explanation">-->
<!--        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>-->
<!--            <h4 title="--><?php //echo $lang['nc_prompts_title'];?><!--">--><?php //echo $lang['nc_prompts'];?><!--</h4>-->
<!--            <span id="explanationZoom" title="--><?php //echo $lang['nc_prompts_span'];?><!--"></span>-->
<!--        </div>-->
<!--        <ul>-->
<!--        </ul>-->
<!--    </div>-->
    <div id="flexigrid"></div>
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
        <div class="title">
            <h3>高级搜索</h3>
        </div>
        <form method="get" name="formSearch" id="formSearch">
            <input type="hidden" name="advanced" value="1" />
            <div id="searchCon" class="content">
                <div class="layout-box">
                    <dl>
                        <dt>处理状态</dt>
                        <dd>
                            <select name="activity_status" class="s-select">
                                <option value="">-请选择-</option>
                                <option value="2">待审核</option>
                                <option value="1">已通过</option>
                                <option value="3">已拒绝</option>
                                <option value="0">失效</option>
                            </select>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bottom"> <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a> <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a> </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script>
<script>
    $(function(){
        var flexUrl = 'index.php?act=activity&op=get_xml';
        $("#flexigrid").flexigrid({
            url:flexUrl,
            colModel:[
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '审核状态', name : 'activity_status', width : 100, sortable : false, align: 'center'},
                {display: '序号', name : 'number', width : 50, sortable : false, align: 'center'},
                {display: '活动ID', name : 'act_id', width : 80, sortable : true, align: 'center'},
                {display: '供应商编号', name : 'supp_id', width : 150, sortable : false, align: 'center'},
                {display: '供应商名称', name : 'supp_ch_name', width : 150, sortable : false, align: 'center'},
                {display: '活动名称', name : 'act_name', width : 150, sortable : false, align: 'center'},
                {display: '活动图片', name : 'act_banner', width : 100, sortable : false, align: 'center'},
                {display: '开始时间', name : 'start_date', width : 100, sortable : false, align: 'center'},
                {display: '结束时间', name : 'end_date', width : 100, sortable : false, align: 'center'},
            ],
            buttons:[
            ],
            searchitems:[
                {display: '供应商编号', name : 'supp_id'},
                {display: '供应商名称', name : 'supp_ch_name'},
                {display: '活动ID', name : 'act_id'},
                {display: '活动名称', name : 'act_name'},
            ],
            sortname:"act_id",
            sortorder:"asc",
            title:"活动列表"

        });
        // 高级搜索提交
        $('#ncsubmit').click(function(){
            $("#flexigrid").flexOptions({url: flexUrl + '&' + $("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        // 高级搜索重置
        $('#ncreset').click(function(){
            $("#flexigrid").flexOptions({url: flexUrl}).flexReload();
            $("#formSearch")[0].reset();
        });

        $('.nyroModal').nyroModal();
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
    function fg_sent(ids){
        
    }


</script>
