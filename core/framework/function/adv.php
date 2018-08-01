<?php

defined('InShopNC') or exit('Access Invalid!');
/**
 * 取广告内容
 *
 * @param unknown_type $ap_id
 * @param unknown_type $type html,js
 */
function advshow($ap_id, $type = 'js'){
	if($ap_id < 1)return;
	$time    = time();
	//加载广告位缓存文件
	$ap_cache_file = BASE_DATA_PATH.'/cache/adv/'.$ap_id.'.php';
	if(file_exists($ap_cache_file)){
		$ap_info = require($ap_cache_file);
	}else{
		return ;
	}
	$adv_list = $ap_info['adv_list'];unset($ap_info['adv_list']);
	extract($ap_info);
	$tt = 3600*24;
	foreach ((array)$adv_list as $k=>$v){
		if($v['adv_start_date'] < $time&&$v['adv_end_date']+$tt > $time&&$v['is_allow'] == '1'){
		}else{
			unset($adv_list[$k]);
		}
	}
	//获取广告信息并展示		
	if($ap_class == '2'){
		//如果没有广告可供展示则显示默认图片
		if($is_use == '1'){
			//收集没有过期的广告

		if(empty($adv_list)){
			$pic = UPLOAD_SITE_URL."/".ATTACH_ADV."/".$default_content;
			$content .= "document.write(\"<div id='KinSlideshow' style='visibility:hidden;'>";
			$content .= "<a href=''><img src='".$pic."' width='".$ap_width."' height='".$ap_height."' /></a>";
			$content .= "</div>\");";
		}else{
			$width    = $ap_width;
			$height   = $ap_height;
			$content .= "document.write(\"<div id='KinSlideshow' >";
			foreach ($adv_list as $v){
				//加载幻灯广告缓存并展示
				extract($v);
				$pic_content = unserialize($adv_content);
				$pic = $pic_content['adv_slide_pic'];
				$pic = UPLOAD_SITE_URL."/".ATTACH_ADV."/".$pic;
				$url = $pic_content['adv_slide_url'];
				$content .= "<a href='http://".$pic_content['adv_slide_url']."' target='_blank'><img src='".$pic."' width='".$width."' height='".$height."' /></a>";
//					$content .= "<a href='".SHOP_SITE_URL.DS."index.php?act=advclick&op=advclick&adv_id=".$adv_id."&ap_class=".$ap_class."' target='_blank'><img src='".$pic."' width='".$width."' height='".$height."' /></a>";
			}
			$content .= "</div>\");";	 
		 }
		}
	}else{	
		switch ($ap_display){
			case '1'://多广告随机展示
				/**
		          * 如果没有广告可供展示则显示默认图片或文字
		          */
				if($is_use == '1'){
		        if(empty($adv_list)){
		        	switch ($ap_class){
		        		case '0':
		        			$width   = $ap_width;
					        $height  = $ap_height;
		        			$content .= "document.write(\"<a href=''>";
					        $content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					        $content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$default_content;
				            $content .= "' alt=''/>";
					        $content .= "</a>\");";
		        			break;
		        		case '1':
		        			$content .= "document.write(\"<a href=''>";
					        $content .= $default_content;
					        $content .= "</a>\");";
		        			break;
		        		case '3':
		        			$width   = $ap_width;
					        $height  = $ap_height;
		        			$content .= "document.write(\"<a href=''>";
					        $content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					        $content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$default_content;
				            $content .= "' alt=''/>";
					        $content .= "</a>\");";
		        			break;
		        	}
		        }else {
				/**
				 * 获取广告信息
				 */
					$select = array_rand($adv_list);
				    $adv_info_select = $adv_list[$select];

				extract($adv_info_select);
				//图片广告
		       if($ap_class == '0' && $is_use == '1'){
					$width   = $ap_width;
					$height  = $ap_height;
					$pic_content = unserialize($adv_content);
					$pic     = $pic_content['adv_pic'];
					$url     = $pic_content['adv_pic_url'];
//					$content .= "document.write(\"<a href='".SHOP_SITE_URL.DS."index.php?act=advclick&op=advclick&adv_id=".$adv_id."&ap_class=".$ap_class."' target='_blank'>";
					$content .= "document.write(\"<a href='http://".$pic_content['adv_pic_url']."' target='_blank'>";
					$content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$pic;
				    $content .= "' alt='".$adv_title."'/>";
					$content .= "</a>\");";
				}
				//文字广告
		       if($ap_class == '1' && $is_use == '1'){
					$word_content = unserialize($adv_content);
					$word    = $word_content['adv_word'];
					$url     = $word_content['adv_word_url'];
//					$content .= "document.write(\"<a href='".SHOP_SITE_URL.DS."index.php?act=advclick&op=advclick&adv_id=".$adv_id."&ap_class=".$ap_class."' target='_blank'>";
					$content .= "document.write(\"<a href='http://".$pic_content['adv_word_url']."' target='_blank'>";
					$content .= $word;
					$content .= "</a>\");";
				}
				//Flash广告
				if($ap_class == '3' && $is_use == '1'){
					$width   = $ap_width;
					$height  = $ap_height;
					$flash_content = unserialize($adv_content);
					$flash   = $flash_content['flash_swf'];
					$url     = $flash_content['flash_url'];
					$content .= "document.write(\"<a href='".$url."' target='_blank'><button style='width:".$width."px; height:".$height."px; border:none; padding:0; background:none;' disabled><object id='FlashID' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='".$width."' height='".$height."'>";
					$content .= "<param name='movie' value='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$flash;
					$content .= "' /><param name='quality' value='high' /><param name='wmode' value='opaque' /><param name='swfversion' value='9.0.45.0' /><!-- 此 param 标签提示使用 Flash Player 6.0 r65 和更高版本的用户下载最新版本的 Flash Player。如果您不想让用户看到该提示，请将其删除。 --><param name='expressinstall' value='";
					$content .= SHOP_RESOURCE_SITE_URL."/js/expressInstall.swf'/><!-- 下一个对象标签用于非 IE 浏览器。所以使用 IECC 将其从 IE 隐藏。 --><!--[if !IE]>--><object type='application/x-shockwave-flash' data='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$flash;
					$content .= "' width='".$width."' height='".$height."'><!--<![endif]--><param name='quality' value='high' /><param name='wmode' value='opaque' /><param name='swfversion' value='9.0.45.0' /><param name='expressinstall' value='";
					$content .= SHOP_RESOURCE_SITE_URL."/js/expressInstall.swf'/><!-- 浏览器将以下替代内容显示给使用 Flash Player 6.0 和更低版本的用户。 --><div><h4>此页面上的内容需要较新版本的 Adobe Flash Player。</h4><p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='获取 Adobe Flash Player' width='112' height='33' /></a></p></div><!--[if !IE]>--></object><!--<![endif]--></object></button></a>";
					$content .= "<script type='text/javascript'>swfobject.registerObject('FlashID');</script>\");";
				}
		        }
			}
				
				break;
			case '2'://单广告展示					
		        /**
		          * 如果没有广告可供展示则显示默认图片或文字
		          */
				if($is_use == '1'){
		        if(empty($adv_list)){
		        	switch ($ap_class){
		        		case '0':
		        			$width   = $ap_width;
					        $height  = $ap_height;
		        			$content .= "document.write(\"<a href=''>";
					        $content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					        $content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$default_content;
				            $content .= "' alt=''/>";
					        $content .= "</a>\");";
		        			break;
		        		case '1':
		        			$content .= "document.write(\"<a href=''>";
					        $content .= $default_content;
					        $content .= "</a>\");";
		        			break;
		        		case '3':
		        			$width   = $ap_width;
					        $height  = $ap_height;
		        			$content .= "document.write(\"<a href=''>";
					        $content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					        $content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$default_content;
				            $content .= "' alt=''/>";
					        $content .= "</a>\");";
		        			break;
		        	}
		        }else {
					extract($adv_list[0]);
				//图片广告
				if($ap_class == '0' && $is_use == '1' && $is_allow == '1'){
					$width   = $ap_width;
					$height  = $ap_height;
					$pic_content = unserialize($adv_content);
					$pic     = $pic_content['adv_pic'];
					$url     = $pic_content['adv_pic_url'];
					$content .= "document.write(\"<a href='http://".$pic_content['adv_pic_url']."' target='_blank'>";
//					$content .= "document.write(\"<a href='".SHOP_SITE_URL.DS."index.php?act=advclick&op=advclick&adv_id=".$adv_id."&ap_class=".$ap_class."' target='_blank'>";
					$content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$pic;
				    $content .= "' alt='".$adv_title."'/>";
					$content .= "</a>\");";
				}
				//文字广告
				if($ap_class == '1' && $is_use == '1' && $is_allow == '1'){
					$word_content = unserialize($adv_content);
					$word    = $word_content['adv_word'];
					$url     = $word_content['adv_word_url'];
					$content .= "document.write(\"<a href='http://".$pic_content['adv_word_url']."' target='_blank'>";
//					$content .= "document.write(\"<a href='".SHOP_SITE_URL.DS."index.php?act=advclick&op=advclick&adv_id=".$adv_id."&ap_class=".$ap_class."' target='_blank'>";
					$content .= $word;
					$content .= "</a>\");";
				}
		        //Flash广告
				if($ap_class == '3' && $is_use == '1' && $is_allow == '1'){
					$width   = $ap_width;
					$height  = $ap_height;
					$flash_content = unserialize($adv_content);
					$flash   = $flash_content['flash_swf'];
					$url     = $flash_content['flash_url'];
					$content .= "document.write(\"<a href='http://".$url."' target='_blank'><button style='width:".$width."px; height:".$height."px; border:none; padding:0; background:none;' disabled><object id='FlashID' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='".$width."' height='".$height."'>";
					$content .= "<param name='movie' value='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$flash;
					$content .= "' /><param name='quality' value='high' /><param name='wmode' value='opaque' /><param name='swfversion' value='9.0.45.0' /><!-- 此 param 标签提示使用 Flash Player 6.0 r65 和更高版本的用户下载最新版本的 Flash Player。如果您不想让用户看到该提示，请将其删除。 --><param name='expressinstall' value='";
					$content .= SHOP_RESOURCE_SITE_URL."/js/expressInstall.swf'/><!-- 下一个对象标签用于非 IE 浏览器。所以使用 IECC 将其从 IE 隐藏。 --><!--[if !IE]>--><object type='application/x-shockwave-flash' data='";
					$content .= UPLOAD_SITE_URL."/".ATTACH_ADV."/".$flash;
					$content .= "' width='".$width."' height='".$height."'><!--<![endif]--><param name='quality' value='high' /><param name='wmode' value='opaque' /><param name='swfversion' value='9.0.45.0' /><param name='expressinstall' value='";
					$content .= SHOP_RESOURCE_SITE_URL."/js/expressInstall.swf'/><!-- 浏览器将以下替代内容显示给使用 Flash Player 6.0 和更低版本的用户。 --><div><h4>此页面上的内容需要较新版本的 Adobe Flash Player。</h4><p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='获取 Adobe Flash Player' width='112' height='33' /></a></p></div><!--[if !IE]>--></object><!--<![endif]--></object></button></a>";
					$content .= "<script type='text/javascript'>swfobject.registerObject('FlashID');</script>\");";
				}
		        }	
			}				
				break;
		}
	}
	if ($type == 'html'){
		$content = str_replace('document.write(','',$content);
		$content = trim($content,'");');
	}
	return $content;
}
?>