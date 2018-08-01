<?php defined('InShopNC') or exit('Access Invalid!');?>

<div id="body">
  <div class="cms-content">
    <?php 
$file = getCMSSpecialHtml($_GET['special_id']);
if(is_file($file)) {
    require($file);
}
?>
  </div>
</div>
