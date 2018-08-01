<?php
defined('InShopNC') or exit('Access Invalid!');
$config['site_url']					= '';	//可以留空
$config['fullindexer']['open'] 		= 0;
$config['fullindexer']['host']		= '127.0.0.1';
$config['fullindexer']['port']		= 9312;
$config['fullindexer']['index_product']	= 'main_product';
$config['fullindexer']['index_shop']	= 'main_store';
$config['fullindexer']['maxquerytime']	= 0;
$config['fullindexer']['matchmode']		= 1;
$config['fullindexer']['querylimit']	= 1000;
$config['payment'] 			= 1;
$config['index_expire']		= 600;