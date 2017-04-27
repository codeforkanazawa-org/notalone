<?php 
$include_dir = dirname($_SERVER["SCRIPT_NAME"])."/";
//$file_name = basename($_SERVER['PHP_SELF']);
if($include_dir==="//" || $include_dir==="/" || $include_dir==="/notalone/"){$css_dir = "admin/";}else{$css_dir = "./";}
 ?>
 
<link rel="stylesheet" href="<?php echo $css_dir; ?>admin_style.css?ver=170414">
<script type="text/javascript" src="<?php echo $css_dir; ?>../js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo $css_dir; ?>admin_script.js"></script>

