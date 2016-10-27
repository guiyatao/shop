<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>批量导入商品</h3>
                <h5>导入csv文件中的商品数据到数据库</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li><?php echo "点击导入按钮读取csv文件中的数据到数据库";?></li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="charset" value="gbk" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label>请选择文件</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="type-file-box">
                    <input type="file" name="csv" id="csv" class="type-file-file"  size="30"  />
                    </span>
                    </div>
                    <span class="err"></span>
                    <p class="notic">如果导入速度较慢，建议您把文件拆分为几个小文件，然后分别导入</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>文件格式</label>
                </dt>
                <dd class="opt">
                    <a href="<?php echo RESOURCE_SITE_URL;?>/examples/client_goods_template.csv" class="ncap-btn">点击下载导入例子文件</a>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:document.form1.submit();" class="ncap-btn-big ncap-btn-green">导入</a></div>
        </div>
    </form>

</div>
<script type="text/javascript">
    $(function(){
        var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='选择上传...' class='type-file-button' />"
        $(textButton).insertBefore("#csv");
        $("#csv").change(function(){
            $("#textfield1").val($("#csv").val());
        });
    });
</script>
