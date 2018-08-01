<?php defined('InShopNC') or exit('Access Invalid!');?>
<article id="content">
    <div class="layout expanded mt10">
        <article class="nc-goods-main"><div class="nc-s-c-s3">
                <div class="title"><h4><?php echo nl2br($output['store_navigation_info']['sn_title']);?></h4></div>

                <div class="content"><div class="default"><?php echo $output['store_navigation_info']['sn_content'];?></div>
                </div>
            </div>
        </article>
        <aside class="nc-sidebar">
            <?php include template('store/left');?>
        </aside>
    </div>
    <div class="clear"></div>
</article>
