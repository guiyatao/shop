$(document).ready(function(){var e={};e.current_block_id=null;e.current_dialog=null;e.current_block_edit_button=null;e.editor=null;e.slide_image_limit=5;e.default_nav_style=".ncs-nav { background-color: #D93600; border: 1px solid #B22D00; height: 38px;  width: 998px; }";e.default_nav_style+=".ncs-nav ul { white-space: nowrap; display: block; width: 999px; height: 38px; margin-left: -1px; }";e.default_nav_style+=".ncs-nav li a span { font-size: 14px; font-weight: 600; line-height: 20px; text-overflow: ellipsis; white-space: nowrap; max-width:160px; color: #FFF; float: left; height: 20px; padding: 9px 15px; margin-left: 4px; cursor:pointer;}";e.$hot_area_image=null;e.hot_area_index=1;e.ajax_post=function(e,a,t,o){$.ajax({type:"POST",url:e,data:a,dataType:"json"}).done(function(e){if(typeof e.error=="undefined"){t(e)}else{showError(e.error)}}).fail(function(){showError("操作失败")}).always(o)};e.show_dialog_module=function(a,t,o){if(typeof o=="undefined"){o=false}var i=$("#dialog_module_"+a);if(i.length>0){e.current_dialog=i;$("#dialog_select_module").hide();var _="show_dialog_module_"+a;e[_](i,t,o)}else{showError("模块不存在")}};e.show_dialog_module_html=function(a,t){a.nc_show_dialog({width:1020,title:"自定义模块"});if(!e.editor){e.editor=KindEditor.create("#module_html_editor",{items:["source","|","fullscreen","undo","redo","cut","copy","paste","|","fontname","fontsize","forecolor","hilitecolor","bold","italic","underline","removeformat","justifyleft","justifycenter","justifyright","insertorderedlist","insertunorderedlist","|","image","flash","media","link","|","about"],allowImageUpload:false,allowFlashUpload:false,allowMediaUpload:false,allowFileManager:false,filterMode:false})}e.editor.html(t)};e.show_dialog_module_slide=function(e,a,t){e.nc_show_dialog({width:1020,title:"图片和幻灯"});var o="";$(a).find("li").each(function(){var e={};e.image_url=$(this).attr("data-image-url");e.image_name=$(this).attr("data-image-name");e.image_link=$(this).attr("data-image-link");o+=template.render("template_module_slide_image_list",e)});$("#txt_slide_full_width").attr("checked",t);$("#module_slide_html ul").html(o);$("#div_module_slide_upload").hide();$("#btn_add_slide_image").show()};e.show_dialog_module_hot_area=function(a,t){e.hot_area_index=1;$("#div_module_hot_area_image").html($(t).find("img"));e.$hot_area_image=$("#div_module_hot_area_image").find("img");e.$hot_area_image.imgAreaSelect({handles:true,zIndex:1200,fadeSpeed:200});$("#module_hot_area_url").val("");var o="";$("#module_hot_area_select_list").html("");$(t).find("area").each(function(){var a=$(this).attr("coords");var t=$(this).attr("href");e.add_hot_area(a,t)});a.nc_show_dialog({width:1020,title:"图片热点模块",close_callback:function(){e.hot_area_cancel_selection()}})};e.show_dialog_module_goods=function(e,a){var t="";$(a).find('[nctype="goods_item"]').each(function(){$(this).append('<a class="ncbtn-mini" nctype="btn_module_goods_operate" href="javascript:;"><i class="icon-ban-circle"></i>取消选择</a>');t+=$("<div />").append($(this)).html()});$("#div_module_goods_list").html(t);e.nc_show_dialog({width:1020,title:"店铺商品模块"})};e.sort_decoration_block=function(){var e="";$block_list=$("#store_decoration_area").children();$block_list.each(function(a,t){e+=$(t).attr("data-block-id")+","});$.post(URL_DECORATION_BLOCK_SORT,{sort_string:e},function(e){if(typeof e.error!="undefined"){showError(e.error)}},"json")};e.save_decoration_block=function(a,t,o){if(typeof o=="undefined"){o=0}else{o=1}var i={block_id:e.current_block_id,module_type:t,full_width:o,content:a};e.ajax_post(URL_DECORATION_BLOCK_SAVE,i,function(a){e.current_block_edit_button.attr("data-module-type",t);var i=$("#block_"+e.current_block_id);if(o){i.addClass("store-decoration-block-full-width")}else{i.removeClass("store-decoration-block-full-width")}if(t=="html"){a.html=a.html.replace(/\\"/g,'"')}i.find('[nctype="store_decoration_block_module"]').html(a.html);e.current_dialog.hide()})};e.apply_nav_style=function(a,t){if(a=="true"){$("#decoration_nav").show()}else{$("#decoration_nav").hide()}$("#style_nav").remove();if(t==""){t=e.default_nav_style;$("#decoration_nav_style").val(e.default_nav_style)}$("head").append('<style id="style_nav">'+t+"</style>")};e.apply_banner=function(e,a){var t=$("#decoration_banner");if(e=="true"&&a!=""){t.show()}else{t.hide()}t.html('<img src="'+a+'" alt="">')};e.add_hot_area=function(a,t){var o={};o.link=t;o.position=a;o.index=e.hot_area_index;var i=template.render("template_module_hot_area_list",o);$("#module_hot_area_select_list").append(i);var _=a.split(",");var n={};n.width=_[2]-_[0];n.height=_[3]-_[1];n.left=_[0];n.top=_[1];n.index=e.hot_area_index;var d=template.render("template_module_hot_area_display",n);$("#div_module_hot_area_image").append(d);e.hot_area_index=e.hot_area_index+1};e.hot_area_cancel_selection=function(){var a=e.$hot_area_image.imgAreaSelect({instance:true});if(typeof a!="undefined"){a.cancelSelection()}};e.apply_banner($("input[name='decoration_banner_display']:checked").val(),$("#img_banner_image").attr("src"));e.apply_nav_style($("input[name='decoration_nav_display']:checked").val(),$("#decoration_nav_style").val());$("#btn_edit_background").on("click",function(){$("#dialog_edit_background").nc_show_dialog({width:640,title:"编辑背景"})});$("#file_background_image").fileupload({dataType:"json",url:URL_DECORATION_ALBUM_UPLOAD,add:function(e,a){$("#img_background_image").attr("src",LOADING_IMAGE);$("#img_background_image").addClass("loading");$("#div_background_image").show();a.submit()},done:function(e,a){var t=a.result;$("#img_background_image").removeClass("loading");if(typeof t.error=="undefined"){$("#img_background_image").attr("src",t.image_url);$("#txt_background_image").val(t.image_name);$("#div_background_image").show()}else{$("#div_background_image").hide();showError(t.error)}}});$("#btn_del_background_image").on("click",function(){$("#img_background_image").attr("src","");$("#txt_background_image").val("");$("#div_background_image").hide()});$("#btn_save_background").on("click",function(){var a={decoration_id:DECORATION_ID,background_color:$("#txt_background_color").val(),background_image:$("#txt_background_image").val(),background_image_repeat:$("input[name='background_repeat']:checked").val(),background_position_x:$("#txt_background_position_x").val(),background_position_y:$("#txt_background_position_y").val(),background_attachment:$("#txt_background_attachment").val()};e.ajax_post(URL_DECORATION_BACKGROUND_SETTING_SAVE,a,function(e){$("#store_decoration_content").attr("style",e.decoration_background_style)},function(){$("#dialog_edit_background").hide()})});$("#btn_edit_head").on("click",function(){$("#dialog_edit_head").nc_show_dialog({width:640,title:"编辑头部"})});$("#dialog_edit_head_tabs").tabs();$("#btn_default_nav_style").on("click",function(){$("#decoration_nav_style").val(e.default_nav_style)});$("#btn_save_decoration_nav").on("click",function(){var a=$("input[name='decoration_nav_display']:checked").val();var t=$("#decoration_nav_style").val();var o={decoration_id:DECORATION_ID,nav_display:a,content:t};e.ajax_post(URL_DECORATION_NAV_SAVE,o,function(o){e.apply_nav_style(a,t);$("#dialog_edit_head").hide()})});$("#file_decoration_banner").fileupload({dataType:"json",url:URL_DECORATION_ALBUM_UPLOAD,add:function(e,a){$("#img_banner_image").attr("src",LOADING_IMAGE);$("#img_banner_image").addClass("loading");$("#div_banner_image").show();a.submit()},done:function(e,a){var t=a.result;$("#img_banner_image").removeClass("loading");if(typeof t.error=="undefined"){$("#img_banner_image").attr("src",t.image_url);$("#txt_banner_image").val(t.image_name);$("#div_banner_image").show()}else{$("#div_banner_image").hide();showError(t.error)}}});$("#btn_del_banner_image").on("click",function(){$("#txt_banner_image").val("");$("#div_banner_image").hide()});$("#btn_save_decoration_banner").on("click",function(){var a=$("input[name='decoration_banner_display']:checked").val();var t=$("#txt_banner_image").val();var o={decoration_id:DECORATION_ID,banner_display:a,content:t};e.ajax_post(URL_DECORATION_BANNER_SAVE,o,function(t){e.apply_banner(a,t.image_url);$("#dialog_edit_head").hide()})});$("#btn_add_block").on("click",function(){var a={decoration_id:DECORATION_ID,block_layout:"block_1"};e.ajax_post(URL_DECORATION_BLOCK_ADD,a,function(a){$("#store_decoration_area").append(a.html);$(".tip").poshytip(POSHYTIP);$("html, body").animate({scrollTop:$(document).height()},1e3);e.sort_decoration_block()})});$("#store_decoration_area").on("click",'[nctype="btn_del_block"]',function(){$this=$(this);if(confirm("确认删除？")){var a={block_id:$(this).attr("data-block-id")};e.ajax_post(URL_DECORATION_BLOCK_DEL,a,function(e){$this.parents('[nctype="store_decoration_block"]').hide()})}});$("#store_decoration_area").sortable({update:function(a,t){e.sort_decoration_block()}});$("#store_decoration_area").on("click",'[nctype="btn_edit_module"]',function(){var a=$(this).attr("data-module-type");e.current_block_id=$(this).attr("data-block-id");e.current_block_edit_button=$(this);if(a==""){$("#dialog_select_module").nc_show_dialog({width:480,title:"选择模块"})}else{var t=$("#block_"+e.current_block_id);var o=t.find('[nctype="store_decoration_block_module"]').html();var i=t.hasClass("store-decoration-block-full-width");e.show_dialog_module(a,o,i)}});$('[nctype="btn_show_module_dialog"]').on("click",function(){var a=$(this).attr("data-module-type");e.show_dialog_module(a)});$("#btn_save_module_html").on("click",function(){e.editor.sync();var a=$("#module_html_editor").val();e.editor.html("");e.save_decoration_block(a,"html")});$("#btn_add_slide_image").on("click",function(){var a=$("#module_slide_html ul").children().length;if(a>=e.slide_image_limit){showError("每个幻灯片最多只能上传"+e.slide_image_limit+"张图片");return}$("#div_module_slide_image").html("");$("#module_slide_url").val("");$("#div_module_slide_upload").show();$("#btn_add_slide_image").hide()});$('[nctype="btn_module_slide_upload"]').fileupload({dataType:"json",url:URL_DECORATION_ALBUM_UPLOAD,add:function(e,a){$("#div_module_slide_image").html('<img class="loading" src="'+LOADING_IMAGE+'">');a.submit()},done:function(e,a){var t=a.result;if(typeof t.error=="undefined"){$("#div_module_slide_image").html('<img src="'+t.image_url+'" data-image-name="'+t.image_name+'">')}else{$("#div_module_slide_image").html("");showError(t.error)}}});$("#btn_save_add_slide_image").on("click",function(){var e={};$image=$("#div_module_slide_image img");if($image.length>0){e.image_url=$image.attr("src");e.image_name=$image.attr("data-image-name");e.image_link=$("#module_slide_url").val();var a=template.render("template_module_slide_image_list",e);$("#module_slide_html ul").append(a);$("#div_module_slide_upload").hide();$("#btn_add_slide_image").show()}else{showError("请上传图片")}});$("#module_slide_html").on("click",'[nctype="btn_del_slide_image"]',function(){$(this).parents("li").remove()});$("#btn_cancel_add_slide_image").on("click",function(){$("#div_module_slide_upload").hide();$("#btn_add_slide_image").show()});$("#btn_save_module_slide").on("click",function(){var a={};var t=0;a.height=parseInt($("#txt_slide_height").val(),10);if(isNaN(a.height)){showError("请输入正确的显示高度");return}a.images=[];$("#module_slide_html li").each(function(){var e={};e.image_name=$(this).attr("data-image-name");e.image_link=$(this).attr("data-image-link");a.images[t]=e;t++});e.save_decoration_block(a,"slide",$("#txt_slide_full_width").attr("checked"))});$('[nctype="btn_module_hot_area_upload"]').fileupload({dataType:"json",url:URL_DECORATION_ALBUM_UPLOAD,add:function(e,a){$("#div_module_hot_area_image").html('<img class="loading" src="'+LOADING_IMAGE+'">');a.submit()},done:function(a,t){var o=t.result;if(typeof o.error=="undefined"){$("#div_module_hot_area_image").html('<img src="'+o.image_url+'" data-image-name="'+o.image_name+'">');e.$hot_area_image=$("#div_module_hot_area_image").find("img");e.$hot_area_image.imgAreaSelect({handles:true,zIndex:1200,fadeSpeed:200})}else{$("#div_module_hot_area_image").html("");showError(o.error)}}});$("#btn_module_hot_area_add").on("click",function(){var a=e.$hot_area_image.imgAreaSelect({instance:true});var t=a.getSelection();if(!t.width||!t.height){showError("请选择热点区域");return}var o=t.x1+","+t.y1+","+t.x2+","+t.y2;var i=$("#module_hot_area_url").val();e.add_hot_area(o,i);e.hot_area_cancel_selection()});$("#dialog_module_hot_area").on("click",'[nctype="btn_module_hot_area_select"]',function(){var a=$(this).attr("data-hot-area-position").split(",");var t=e.$hot_area_image.imgAreaSelect({instance:true});t.setSelection(a[0],a[1],a[2],a[3],true);t.setOptions({show:true});t.update()});$("#dialog_module_hot_area").on("click",'[nctype="btn_module_hot_area_del"]',function(){var e=$(this).attr("data-index");$("#hot_area_display_"+e).remove();$(this).parents("li").remove()});$("#btn_save_module_hot_area").on("click",function(){var a={};var t=0;a.image=e.$hot_area_image.attr("data-image-name");if(a.image==""){showError("请首先上传图片并添加热点");return}a.areas=[];$("#module_hot_area_select_list li").each(function(){var e={};var o=$(this).attr("data-hot-area-position").split(",");e.x1=o[0];e.y1=o[1];e.x2=o[2];e.y2=o[3];e.link=$(this).attr("data-hot-area-link");a.areas[t]=e;t++});e.hot_area_cancel_selection();e.save_decoration_block(a,"hot_area")});$("#btn_module_goods_search").on("click",function(){var e="&"+$.param({keyword:$("#txt_goods_search_keyword").val()});$("#div_module_goods_search_list").load(URL_DECORATION_GOODS_SEARCH+e)});$("#div_module_goods_search_list").on("click","a.demo",function(){$("#div_module_goods_search_list").load($(this).attr("href"));return false});$("#div_module_goods_search_list").on("click",'[nctype="btn_module_goods_operate"]',function(){var e=$(this).parents('[nctype="goods_item"]').clone();e.find('[nctype="btn_module_goods_operate"]').html('<i class="icon-ban-circle"></i>取消选择');$("#div_module_goods_list").append(e)});$("#div_module_goods_list").on("click",'[nctype="btn_module_goods_operate"]',function(){$(this).parents('[nctype="goods_item"]').remove()});$("#btn_save_module_goods").on("click",function(){var a=[];var t=0;$("#div_module_goods_list").find('[nctype="goods_item"]').each(function(){var e={};e.goods_id=$(this).attr("data-goods-id");e.goods_name=$(this).attr("data-goods-name");e.goods_price=$(this).attr("data-goods-price");e.goods_image=$(this).attr("data-goods-image");a[t]=e;t++});e.save_decoration_block(a,"goods")});$("#btn_close").on("click",function(){window.close()})});
