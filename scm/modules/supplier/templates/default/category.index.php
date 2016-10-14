<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?php echo "商品分类管理";?></h3>
                <h5><?php echo "商品分类列表";?></h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>当管理员添加商品时可选择商品分类，用户可根据分类查询商品列表</li>
        </ul>
    </div>
    <form method='post'>
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <table class="flex-table">
            <thead>
            <tr>
                <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
                <th width="150" class="handle" align="center"><?php echo $lang['nc_handle'];?></th>
                <th width="80" align="center">商品分类编号</th>
                <th width="60" align="center"><?php echo $lang['nc_sort'];?></th>
                <th width="300" align="left"><?php echo "分类名称";?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($output['category_list']) && is_array($output['category_list'])){ ?>
                <?php foreach($output['category_list'] as $k => $v){ ?>
                    <tr data-id="<?php echo $v['cate_id'];?>">
                        <td class="sign"><i class="ico-check"></i></td>
                        <td class="handle">
                            <a class="btn red" href="javascript:void(0);" onclick="fg_del(<?php echo $v['cate_id'];?>);"><i class="fa fa-trash-o"></i><?php echo $lang['nc_del'];?></a>
                            <span class="btn"><em><i class="fa fa-cog"></i><?php echo $lang['nc_set'];?><i class="arrow"></i></em>
                            <ul>
                                <li><a href="index.php?act=category&op=category_edit&cate_id=<?php echo $v['cate_id'];?>">编辑分类信息</a></li>
                                <?php if($output['level'] < 3) {?>
                                    <li><a href = "index.php?act=category&op=category_add&pid=<?php echo $v['cate_id'];?>" > 新增下级分类</a ></li >
                                <?php }?>
                                <?php if ($v['have_child'] == 1) {?>
                                    <li><a href="index.php?act=category&op=category&pid=<?php echo $v['cate_id'];?>">查看下级分类</a></li>
                                <?php }?>
                                <?php if($output['level'] == 1) {?>
                                    <li><a href="index.php?act=category&op=nav_edit&gc_id=<?php echo $v['cate_id'];?>">编辑分类导航</a></li>
                                <?php }?>
                            </ul>
                            </span></td>
                        <td><?=$v['cate_id']?></td>
                        <td class="sort"><span title="<?php echo $lang['nc_editable'];?>" column_id="<?php echo $v['cate_id'];?>" fieldname="sort" nc_type="inline_edit" class="editable "><?php echo $v['sort'];?></span></td>
                        <td class="name"><span title="<?php echo $lang['nc_editable'];?>"  column_id="<?php echo $v['cate_id'];?>" fieldname="category_name" nc_type="inline_edit" class="editable "><?php echo $v['category_name'];?></span></td>
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
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
    $(function(){
        $('.flex-table').flexigrid({
            url:'index.php?act=category&op=get_xml',
            height:'auto',// 高度自动
            usepager: false,// 不翻页
            striped:false,// 不使用斑马线
            resizable: false,// 不调节大小
            title: '<?php echo $output['title']?>',// 表格标题
            reload: false,// 不使用刷新
            columnControl: false,// 不使用列控制
            buttons:[
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation },
                {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation },
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }
            ]

        });
        $('span[nc_type="inline_edit"]').inline_edit({act: 'category',op: 'ajax'});
    });

    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=category&op=category_add&pid=<?php echo $output['pid']?>';
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
        window.location.href = 'index.php?act=category&op=export_csv&id=' + id;
    }
    function fg_del(ids) {
        if (typeof ids == 'number') {
            var ids = new Array(ids.toString());
        };
        id = ids.join(',');
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            $.getJSON('index.php?act=category&op=category_del', {id:id}, function(data){
                if (data.state) {
                    location.reload();
                } else {
                    showError(data.msg)
                }
            });
        }
    }
</script>
