<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.head-app { display: none; }
.public-nav-layout { width: 1000px; }
.public-nav-layout .site-menu { max-width: 788px; }
.wrapper { width: 1000px !important; }
.no-content { font: normal 16px/20px Arial, "microsoft yahei"; color: #999999; text-align: center; padding: 150px 0; }
.nc-appbar-tabs a.compare { display: none !important; }
.public-nav-layout .category .sub-class { width: 746px;}
.public-nav-layout .category .sub-class-right  { display: none;}
</style>
<div id="body">
  <div id="cms_special_content" class="cms-content">
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#cms_special_content").load("<?php echo $output['special_file']; ?>");
});
</script> 

