<?php
/**
 * 前台control父类,店铺control父类,会员control父类
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class Control{

	/**
	 * 系统通知发送函数
	 *
	 * @param int $receiver_id 接受人编号
	 * @param string $tpl_code 模板标识码
	 * @param array $param 内容数组
	 * @param bool $flag 是否遵从系统设置
	 * @return boolean
	 */
	protected function send_notice($receiver_id,$tpl_code,$param,$flag = true){

		$mail_tpl_model	= Model('mail_templates');
		$mail_tpl	= $mail_tpl_model->getOneTemplates($tpl_code);
		if(empty($mail_tpl) || $mail_tpl['mail_switch'] == 0)return false;

		$member_model	= Model('member');
		$receiver	= $member_model->infoMember(array('member_id'=>$receiver_id));
		if(empty($receiver))return false;

		$subject	= ncReplaceText($mail_tpl['title'],$param);
		$message	= ncReplaceText($mail_tpl['content'],$param);
		//根据模板里面确定的通知类型采用对应模式发送通知
		$result	= false;
		switch($mail_tpl['type']){
			case '0':
				$email	= new Email();
				$result	= true;
				if($flag and $GLOBALS['setting_config']['email_enabled'] == '1' or $flag == false){
					$result	= $email->send_sys_email($receiver['member_email'],$subject,$message);
				}
				break;
			case '1':
				$model_message = Model('message');
				$param = array(
				'member_id'=>$receiver_id,
				'to_member_name'=>$receiver['member_name'],
				'msg_content'=>$message,
				'message_type'=>1//表示系统消息
				);
				$result = $model_message->saveMessage($param);
				break;
		}
		return $result;
	}

	/**
	 * 检查短消息数量
	 *
	 */
	protected function checkMessage() {
		if($_SESSION['member_id'] == '') return ;
		//判断cookie是否存在
		$cookie_name = 'msgnewnum'.$_SESSION['member_id'];
		if (cookie($cookie_name) != null && intval(cookie($cookie_name)) >=0){
			$countnum = intval(cookie($cookie_name));
		}else {
			$message_model = Model('message');
			$countnum = $message_model->countNewMessage($_SESSION['member_id']);
			setNcCookie($cookie_name,"$countnum",2*3600);//保存1天
		}
		Tpl::output('message_num',$countnum);
	}

	/**
	 *  查询购物车商品种类
	 *
	 */
	protected function queryCart() {
	    if (cookie('cart_goods_num') != null && intval(cookie('cart_goods_num')) >=0){
	        $cart_num = intval(cookie('cart_goods_num'));
	    }else {
	        //已登录状态，存入数据库,未登录时，优先存入缓存，否则存入COOKIE
	        if($_SESSION['member_id']) {
	            $save_type = 'db';
	        } else {
	            $save_type = C('cache.type') != 'file' ? 'cache' : 'cookie';
	        }
	        $cart_num = Model('cart')->getCartNum($save_type,array('buyer_id'=>$_SESSION['member_id']));
	    }
		Tpl::output('cart_goods_num',$cart_num);
	}

	/**
	 * 验证会员是否登录
	 *
	 */
	protected function checkLogin(){
		if ($_SESSION['is_login'] !== '1'){
			if (trim($_GET['op']) == 'favoritesgoods' || trim($_GET['op']) == 'favoritesstore'){
				$lang = Language::getLangContent('UTF-8');
				echo json_encode(array('done'=>false,'msg'=>$lang['no_login']));
				die;
			}
			$ref_url = request_uri();
			if ($_GET['inajax']){
				showDialog('','','js',"login_dialog();",200);
			}else {
				@header("location: index.php?act=login&ref_url=".urlencode($ref_url));
			}
			exit;
		}
	}

    /**
     * 添加到任务队列
     *
     * @param array $goods_array
     * @param boolean $ifdel 是否删除以原记录
     */
    protected function addcron($data = array(), $ifdel = false) {
        $model_cron = Model('cron');
        if (isset($data[0])) { // 批量插入
            $where = array();
            foreach ($data as $k => $v) {
                if (isset($v['content'])) {
                    $data[$k]['content'] = serialize($v['content']);
                }
                // 删除原纪录条件
                if ($ifdel) {
                    switch ($data['type']) {
                        case 1 :
                        case 3 :
                        case 4 :
                            $where[] = '(type = ' . $data['type'] . ' and exeid = ' . $data['exeid'] . ')';
                            break;
                        case 2 :
                            $where[] = '(type = 2 and code = ' .$data['code'] . ' and exeid = ' .$data['exeid'] . ')';
                            break;
                    }
                }
            }
            // 删除原纪录
            if ($ifdel) {
                $model_cron->delCron(implode(',', $where));
            }
            $model_cron->addCronAll($data);
        } else { // 单条插入
            if (isset($data['content'])) {
                $data['content'] = serialize($data['content']);
            }
            // 删除原纪录
            if ($ifdel) {
                switch ($data['type']) {
                    case 1 :
                    case 3 :
                    case 4 :
                        $model_cron->delCron(array('type' => $data['type'], 'exeid' => $data['exeid']));
                        break;
                    case 2 :
                        $model_cron->delCron(array('code' => $data ['code'], 'exeid' => $data['exeid']));
                        break;
                }
            }
            $model_cron->addCron($data);
        }
    }

	//文章输出
	protected function article() {
		if (file_exists(BASE_DATA_PATH.'/cache/index/article.php')){
			include(BASE_DATA_PATH.'/cache/index/article.php');
			Tpl::output('show_article',$show_article);
			Tpl::output('article_list',$article_list);
			return ;
		}
		$model_article_class	= Model('article_class');
		$model_article	= Model('article');
		$show_article = array();//商城公告
		$article_list	= array();//下方文章
		$notice_class	= array('notice');
		$code_array	= array('member','store','payment','sold','service','about');
		$notice_limit	= 5;
		$faq_limit	= 5;

		$class_condition	= array();
		$class_condition['home_index'] = 'home_index';
		$class_condition['order'] = 'ac_sort asc';
		$article_class	= $model_article_class->getClassList($class_condition);
		$class_list	= array();
		if(!empty($article_class) && is_array($article_class)){
			foreach ($article_class as $key => $val){
				$ac_code = $val['ac_code'];
				$ac_id = $val['ac_id'];
				$val['list']	= array();//文章
				$class_list[$ac_id]	= $val;
			}
		}

		$condition	= array();
		$condition['article_show'] = '1';
		$condition['home_index'] = 'home_index';
		$condition['field'] = 'article.article_id,article.ac_id,article.article_url,article.article_title,article.article_time,article_class.ac_name,article_class.ac_parent_id';
		$condition['order'] = 'article_sort asc,article_time desc';
		$condition['limit'] = '300';
		$article_array	= $model_article->getJoinList($condition);
		if(!empty($article_array) && is_array($article_array)){
			foreach ($article_array as $key => $val){
				$ac_id = $val['ac_id'];
				$ac_parent_id = $val['ac_parent_id'];
				if($ac_parent_id == 0) {//顶级分类
					$class_list[$ac_id]['list'][] = $val;
				} else {
					$class_list[$ac_parent_id]['list'][] = $val;
				}
			}
		}
		if(!empty($class_list) && is_array($class_list)){
			foreach ($class_list as $key => $val){
				$ac_code = $val['ac_code'];
				if(in_array($ac_code,$notice_class)) {
					$list = $val['list'];
					array_splice($list, $notice_limit);
					$val['list'] = $list;
					$show_article[$ac_code] = $val;
				}
				if (in_array($ac_code,$code_array)){
					$list = $val['list'];
					$val['class']['ac_name']	= $val['ac_name'];
					array_splice($list, $faq_limit);
					$val['list'] = $list;
					$article_list[] = $val;
				}
			}
		}
		$string = "<?php\n\$show_article=".var_export($show_article,true).";\n";
		$string .= "\$article_list=".var_export($article_list,true).";\n?>";
		file_put_contents(BASE_DATA_PATH.'/cache/index/article.php',compress_code($string));

		Tpl::output('show_article',$show_article);
		Tpl::output('article_list',$article_list);
	}

	/**
	 * 自动更新订单
	 *
	 * @param
	 * @return
	 */
	protected function updateOrder($update_all = 0){
		if (empty($_SESSION['order_update_time']) || $update_all == 1) {//登录后未更新或者在其它页面强制更新时执行
			$model_trade	= Model('trade');
			$model_trade->editOrderPay($_SESSION['member_id'], $_SESSION['store_id']);//更新超期未处理订单
			$model_trade->editRefundConfirm($_SESSION['member_id'], $_SESSION['store_id']);//更新超期未处理退款
			$_SESSION['order_update_time'] = time();//记录更新时间
		}
	}
}

/********************************** 前台control父类 **********************************************/

class BaseHomeControl extends Control {

	public function __construct(){
		//短消息检查
		$this->checkMessage();
		//购物车商品种数查询
		$this->queryCart();

		Language::read('common,home_layout');

		Tpl::setDir('home');

		Tpl::output('nav_list',($nav = H('nav'))? $nav :H('nav',true));

		Tpl::setLayout('home_layout');

		Tpl::output('hot_search',@explode(',',C('hot_search')));

		$model_class = Model('goods_class');
		$goods_class = $model_class->get_all_category();
		Tpl::output('show_goods_class',$goods_class);

		if ($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
			$_GET = Language::getGBK($_GET);
		}

		$this->article();//文章输出
		if(!C('site_status')) halt(C('closed_reason'));

	}
}

/********************************** 购买流程父类 **********************************************/

class BaseBuyControl extends Control {
	protected function __construct(){
		Language::read('common,home_layout');
		Tpl::setDir('buy');
		Tpl::setLayout('buy_layout');
		Tpl::output('nav_list',($nav = H('nav'))? $nav :H('nav',true));
		if ($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
			$_GET = Language::getGBK($_GET);
		}
        //文章输出
		$this->article();
		if(!C('site_status')) halt(C('closed_reason'));
	}

}

/********************************** 会员control父类 **********************************************/

class BaseMemberControl extends Control {
	public function __construct(){

		if(!C('site_status')) halt(C('closed_reason'));

		Language::read('common,member_layout');

		if ($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
			$_GET = Language::getGBK($_GET);
		}
		//会员验证
		$this->checkLogin();
		//短消息检查
		$this->checkMessage();
		Tpl::setDir('member');
		Tpl::setLayout('member_layout');
		Tpl::output('header_menu_sign','setting');//默认选中顶部“设置”菜单
		//获取导航
		Tpl::output('nav_list',($nav = H('nav')) ? $nav : H('nav',true));
		//自动更新订单，执行一次
		if(empty($_SESSION['order_update_time'])) $this->updateOrder();
	}

	/**
	 * 检查店铺名称是否存在
	 * @return boolean
	 */
	public function checknameinner() {
		$model_store	= Model('store');

		$store_name	= trim($_GET['store_name']);
		$store_info	= $model_store->getStoreInfo(array('store_name'=>$store_name));
		if($store_info['store_name'] != ''&&$store_info['member_id'] != $_SESSION['member_id']) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 买家的左侧上部的头像和订单数量
	 *
	 */
	public function get_member_info() {
		//生成缓存的键值
		$hash_key = $_SESSION['member_id'];
		//写入缓存的数据
		$cachekey_arr = array('member_name','store_id','member_avatar','member_qq','member_email','member_ww','member_goldnum','member_points',
							'available_predeposit','member_snsvisitnum','credit_arr','order_nopay','order_noreceiving','order_noeval','fan_count');
		if (false){
			foreach ($_cache as $k=>$v){
				$member_info[$k] = $v;
			}
		} else {
		    $model_order = Model('order');
		    $model_member = Model('member');
		    $member_info = $model_member->getMemberInfo(array('member_id'=>$_SESSION['member_id']));
			$member_info['order_nopay'] = $model_order->getOrderStateNewCount(array('buyer_id'=>$_SESSION['member_id']));
			$member_info['order_noreceiving'] = $model_order->getOrderStateSendCount(array('buyer_id'=>$_SESSION['member_id']));
			$member_info['order_noeval'] = $model_order->getOrderStateEvalCount(array('buyer_id'=>$_SESSION['member_id']));
		}
		Tpl::output('member_info',$member_info);
		Tpl::output('header_menu_sign','snsindex');//默认选中顶部“买家首页”菜单
	}
}

/********************************** SNS control父类 **********************************************/

class BaseSNSControl extends Control {
	protected $relation = 0;//浏览者与主人的关系：0 表示游客 1 表示一般普通会员 2表示朋友 3表示自己4表示已关注主人
	protected $master_id = 0; //主人编号
	const MAX_RECORDNUM = 20;//允许插入新记录的最大条数
	protected $member_info;

	public function __construct(){

		Tpl::setDir('sns');

		Tpl::setLayout('sns_layout');

		Language::read('common,sns_layout');

		Tpl::output('nav_list',($nav = F('nav'))? $nav :H('nav',true));

		//验证会员及与主人关系
		$this->check_relation();

		//查询会员信息
		$this->member_info = $this->get_member_info();

		//添加访问记录
		$this->add_visit();

		//我的关注
		$this->my_attention();

		//获取设置
		$this->get_setting();

		//允许插入新记录的最大条数
		Tpl::output('max_recordnum',self::MAX_RECORDNUM);
	}

	/**
	 * 格式化时间
	 * @param string $time时间戳
	 */
	protected function formatDate($time){
		$handle_date = @date('Y-m-d',$time);//需要格式化的时间
		$reference_date = @date('Y-m-d',time());//参照时间
		$handle_date_time = strtotime($handle_date);//需要格式化的时间戳
		$reference_date_time = strtotime($reference_date);//参照时间戳
		if ($reference_date_time == $handle_date_time){
			$timetext = @date('H:i',$time);//今天访问的显示具体的时间点
		}elseif (($reference_date_time-$handle_date_time)==60*60*24){
			$timetext = Language::get('sns_yesterday');
		}elseif ($reference_date_time-$handle_date_time==60*60*48){
			$timetext = Language::get('sns_beforeyesterday');
		}else {
			$month_text = Language::get('nc_month');
			$day_text = Language::get('nc_day');
			$timetext = @date("m{$month_text}d{$day_text}",$time);
		}
		return $timetext;
	}

	/**
	 * 会员信息
	 *
	 * @return array
	 */
	public function get_member_info() {
		//生成缓存的键值
		$hash_key  = $this->master_id;
		if($hash_key <= 0){
			showMessage(L('wrong_argument'), '', '', 'error');
		}
		//写入缓存的数据
		$cachekey_arr = array('member_name','store_id','member_avatar','member_qq','member_email','member_ww','member_goldnum','member_points',
				'available_predeposit','member_snsvisitnum','credit_arr','fan_count','attention_count');
		//先查找$hash_key缓存
		if ($_cache = rcache($hash_key,'sns_member')){
			foreach ($_cache as $k=>$v){
				$member_info[$k] = $v;
			}
		} else {
			$model = Model();
			$member_info = $model->table('member')->where(array('member_id'=>$this->master_id))->find();
			if(empty($member_info)){
				showMessage(L('wrong_argument'), 'index.php?act=member_snshome', '', 'error');
			}
			//粉丝数
			$fan_count = $model->table('sns_friend')->where(array('friend_tomid'=>$this->master_id))->count();
			$member_info['fan_count'] = $fan_count;
			//关注数
			$attention_count = $model->table('sns_friend')->where(array('friend_frommid'=>$this->master_id))->count();
			$member_info['attention_count'] = $attention_count;
			//兴趣标签
			$mtag_list = $model->table('sns_membertag,sns_mtagmember')->field('mtag_name')->on('sns_membertag.mtag_id = sns_mtagmember.mtag_id')->join('inner')->where(array('sns_mtagmember.member_id'=>$this->master_id))->select();
			$tagname_array = array();
			if(!empty($mtag_list)){
				foreach ($mtag_list as $val){
					$tagname_array[] = $val['mtag_name'];
				}
			}
			$member_info['tagname'] = $tagname_array;

			wcache($hash_key,$member_info,'sns_member');
		}
		Tpl::output('member_info',$member_info);
		return $member_info;
	}

	/**
	 * 访客信息
	 */
	protected function get_visitor(){
		$model = Model();
		//查询谁来看过我
		$visitme_list = $model->table('sns_visitor')->where(array('v_ownermid'=>$this->master_id))->limit(9)->order('v_addtime desc')->select();
		if (!empty($visitme_list)){
			foreach ($visitme_list as $k=>$v){
				$v['adddate_text'] = $this->formatDate($v['v_addtime']);
				$v['addtime_text'] = @date('H:i',$v['v_addtime']);
				$visitme_list[$k] = $v;
			}
		}
		Tpl::output('visitme_list',$visitme_list);
		if($this->relation == 3){	// 主人自己才有我访问过的人
			//查询我访问过的人
			$visitother_list = $model->table('sns_visitor')->where(array('v_mid'=>$this->master_id))->limit(9)->order('v_addtime desc')->select();
			if (!empty($visitother_list)){
				foreach ($visitother_list as $k=>$v){
					$v['adddate_text'] = $this->formatDate($v['v_addtime']);
					$visitother_list[$k] = $v;
				}
			}
			Tpl::output('visitother_list',$visitother_list);
		}
	}

	/**
	 * 验证会员及主人关系
	 */
	private function check_relation(){
		$model = Model();
		//验证主人会员编号
		$this->master_id = intval($_GET['mid']);
		if ($this->master_id <= 0){
			if ($_SESSION['is_login'] == 1){
				$this->master_id = $_SESSION['member_id'];
			}else {
				@header("location: index.php?act=login&ref_url=".urlencode('index.php?act=member_snshome'));
			}
		}
		Tpl::output('master_id', $this->master_id);

		$model = Model();

		//判断浏览者与主人的关系
		if ($_SESSION['is_login'] == '1'){
			if ($this->master_id == $_SESSION['member_id']){//主人自己
				$this->relation = 3;
			}else{
				$this->relation = 1;
				//查询好友表
				$friend_arr = $model->table('sns_friend')->where(array('friend_frommid'=>$_SESSION['member_id'],'friend_tomid'=>$this->master_id))->find();
				if (!empty($friend_arr) && $friend_arr['friend_followstate'] == 2){
					$this->relation = 2;
				}elseif($friend_arr['friend_followstate'] == 1){
					$this->relation = 4;
				}
			}
		}
		Tpl::output('relation',$this->relation);
	}

	/**
	 * 增加访问记录
	 */
	private function add_visit(){
		$model = Model();
		//记录访客
		if ($_SESSION['is_login'] == '1' && $this->relation != 3){
			//访客为会员且不是空间主人则添加访客记录
			$visitor_info = $model->table('member')->find($_SESSION['member_id']);
			if (!empty($visitor_info)){
				//查询访客记录是否存在
				$existevisitor_info = $model->table('sns_visitor')->where(array('v_ownermid'=>$this->master_id, 'v_mid'=>$visitor_info['member_id']))->find();
				if (!empty($existevisitor_info)){//访问记录存在则更新访问时间
					$update_arr = array();
					$update_arr['v_addtime'] = time();
					$model->table('sns_visitor')->update(array('v_id'=>$existevisitor_info['v_id'], 'v_addtime'=>time()));
				}else {//添加新访问记录
					$insert_arr = array();
					$insert_arr['v_mid']			= $visitor_info['member_id'];
					$insert_arr['v_mname']			= $visitor_info['member_name'];
					$insert_arr['v_mavatar']		= $visitor_info['member_avatar'];
					$insert_arr['v_ownermid']		= $this->member_info['member_id'];
					$insert_arr['v_ownermname']		= $this->member_info['member_name'];
					$insert_arr['v_ownermavatar']	= $this->member_info['member_avatar'];
					$insert_arr['v_addtime']		= time();
					$model->table('sns_visitor')->insert($insert_arr);
				}
			}
		}

		//增加主人访问次数
		$cookie_str = cookie('visitor');
		$cookie_arr = array();
		$is_increase = false;
		if (empty($cookie_str)){
			//cookie不存在则直接增加访问次数
			$is_increase = true;
		}else{
			//cookie存在但是为空则直接增加访问次数
			$cookie_arr = explode('_',$cookie_str);
			if(!in_array($this->master_id,$cookie_arr)){
				$is_increase = true;
			}
		}
		if ($is_increase == true){
			//增加访问次数
			$model->table('member')->update(array('member_id'=>$this->master_id, 'member_snsvisitnum'=>array('exp', 'member_snsvisitnum+1')));
			//设置cookie，24小时之内不再累加
			$cookie_arr[] = $this->master_id;
			setNcCookie('visitor',implode('_',$cookie_arr),24*3600);//保存24小时
		}
	}
	//我的关注
	private function my_attention(){
		if(intval($_SESSION['member_id']) >0){
			$my_attention = Model()->table('sns_friend')->where(array('friend_frommid'=>$_SESSION['member_id']))->order('friend_addtime desc')->limit(4)->select();
			Tpl::output('my_attention', $my_attention);
		}
	}

	/**
	 * 获取设置信息
	 */
	private function get_setting(){
		$m_setting = Model()->table('sns_setting')->find($this->master_id);
		Tpl::output('skin_style', (!empty($m_setting['setting_skin'])?$m_setting['setting_skin']:'skin_01'));
	}
}
/********************************** 店铺 control父类 **********************************************/



class BaseStoreControl extends Control {

    protected $store_info;

	public function __construct(){

		Language::read('common,store_layout,store_show_store_index');

		if(!C('site_status')) halt(C('closed_reason'));

		Tpl::setDir('store');
		Tpl::setLayout('store_layout');

        $store_id = intval($_GET['store_id']);
        if($store_id <= 0) {
            showMessage(L('nc_store_close'), '', '', 'error');
        }

		$model_store = Model('store');
        $store_info = $model_store->getStoreOnlineInfoByID($store_id);
        if(empty($store_info)) {
            showMessage(L('nc_store_close'), '', '', 'error');
        } else {
            $this->store_info = $store_info;
        }

        $this->outputStoreInfo($this->store_info);
		Tpl::output('nav_list',($g = H('nav')) ? $g : H('nav',true));
        $this->getStoreNavigation($store_id);
        $this->outputSeoInfo($this->store_info);
	}

	/**
	 * 检查店铺开启状态
	 *
	 * @param int $store_id 店铺编号
	 * @param string $msg 警告信息
	 */
	protected function outputStoreInfo($store_info){
		$model_store = Model('store');
        $model_seller = Model('seller');

        //店铺分类
        $goodsclass_model = Model('my_goods_class');
        $goods_class_list = $goodsclass_model->getShowTreeList($store_info['store_id']);
        Tpl::output('goods_class_list', $goods_class_list);

        //热销排行
        $hot_sales = $model_store->getHotSalesList($store_info['store_id'], 5);
        Tpl::output('hot_sales', $hot_sales);

        //收藏排行
        $hot_collect = $model_store->getHotCollectList($store_info['store_id'], 5);
        Tpl::output('hot_collect', $hot_collect);

        //卖家列表
        $seller_list = $model_seller->getSellerList(array('store_id' => $store_info['store_id'], 'is_admin' => '0'), null, 'seller_id asc');
        Tpl::output('seller_list', $seller_list);

        Tpl::output('store_info', $store_info);
        Tpl::output('page_title', $store_info['store_name']);
        if (function_exists('getChat')) {
            getChat('');//调用一次向页面抛出变量
        }
	}

    protected function getStoreNavigation($store_id) {
        $model_store_navigation = Model('store_navigation');
        $store_navigation_list = $model_store_navigation->getStoreNavigationList(array('sn_store_id' => $store_id));
        Tpl::output('store_navigation_list', $store_navigation_list);
    }

    protected function outputSeoInfo($store_info) {
		$seo_param = array();
        $seo_param['shopname'] = $store_info['store_name'];
        $seo_param['key']  = $store_info['store_keywords'];
		$seo_param['description'] = $store_info['store_description'];
		Model('seo')->type('shop')->param($seo_param)->show();
    }

}

class BaseGoodsControl extends BaseStoreControl {

	public function __construct(){

		Language::read('common,store_layout');

		if(!C('site_status')) halt(C('closed_reason'));

		Tpl::setDir('store');
		Tpl::setLayout('home_layout');
		Tpl::output('hot_search',@explode(',',C('hot_search')));

		$model_class = Model('goods_class');
		$goods_class = $model_class->get_all_category();
		Tpl::output('show_goods_class',$goods_class);

		$this->article();//文章输出
		$this->checkMessage();
		$this->queryCart();
	}

    protected function getStoreInfo($store_id) {
        $model_store = Model('store');
        $store_info = $model_store->getStoreOnlineInfoByID($store_id);
        if(empty($store_info)) {
            showMessage(L('nc_store_close'), '', '', 'error');
        }

        $this->outputStoreInfo($store_info);
		Tpl::output('nav_list',($g = H('nav')) ? $g : H('nav',true));
        $this->getStoreNavigation($store_id);
        $this->outputSeoInfo($store_info);
    }
}
/**
 * 店铺 control新父类
 *
 */
class BaseSellerControl extends Control {

    //店铺信息
    protected $store_info = array();
    //店铺等级
    protected $store_grade = array();

    public function __construct(){
        Language::read('common,store_layout,member_layout');
        if(!C('site_status')) halt(C('closed_reason'));
        Tpl::setDir('seller');
        Tpl::setLayout('seller_layout');
        Tpl::output('nav_list',($nav = H('nav'))? $nav :H('nav',true));

        if ($_GET['act'] !== 'seller_login') {

            if(empty($_SESSION['seller_id'])) {
                @header('location: index.php?act=seller_login&op=show_login');die;
            }

            // 验证店铺是否存在
            $model_store = Model('store');
            $this->store_info = $model_store->getStoreInfoByID($_SESSION['store_id']);
            if (empty($this->store_info)) {
                @header('location: index.php?act=seller_login&op=show_login');die;
            }

            // 店铺关闭标志
            if (intval($this->store_info['store_state']) === 0) {
                Tpl::output('store_closed', true);
                Tpl::output('store_close_info', $this->store_info['store_close_info']);
            }

            // 店铺等级
            $store_grade = H('store_grade');
            $this->store_grade = $store_grade[$this->store_info['grade_id']];

            if ($_SESSION['seller_is_admin'] !== 1 && $_GET['act'] !== 'seller_center' && $_GET['act'] !== 'seller_logout') {
                if (!in_array($_GET['act'], $_SESSION['seller_limits'])) {
                    showMessage('没有权限', '', '', 'error');
                }
            }

            // 卖家菜单
            Tpl::output('menu', $_SESSION['seller_menu']);
            // 当前菜单
            $current_menu = $this->_getCurrentMenu($_SESSION['seller_function_list']);
            Tpl::output('current_menu', $current_menu);
            // 左侧菜单
            if($_GET['act'] == 'seller_center') {
                if(!empty($_SESSION['seller_quicklink'])) {
                    $left_menu = array();
                    foreach ($_SESSION['seller_quicklink'] as $value) {
                        $left_menu[] = $_SESSION['seller_function_list'][$value];
                    }
                }
            } else {
                $left_menu = $_SESSION['seller_menu'][$current_menu['model']]['child'];
            }
            Tpl::output('left_menu', $left_menu);
            Tpl::output('seller_quicklink', $_SESSION['seller_quicklink']);

            $this->checkMessage();
    		//自动更新订单，执行一次
    		if(empty($_SESSION['order_update_time'])) $this->updateOrder();
        }
    }

	/**
	 * 记录卖家日志
	 *
	 * @param $content 日志内容
	 * @param $state 1成功 0失败
	 */
	protected function recordSellerLog($content = '', $state = 1){
        $seller_info = array();
        $seller_info['log_content'] = $content;
		$seller_info['log_time'] = TIMESTAMP;
		$seller_info['log_seller_id'] = $_SESSION['seller_id'];
		$seller_info['log_seller_name'] = $_SESSION['seller_name'];
		$seller_info['log_store_id'] = $_SESSION['store_id'];
		$seller_info['log_seller_ip'] = getIp();
		$seller_info['log_url'] = $_GET['act'].'&'.$_GET['op'];
		$seller_info['log_state'] = $state;
        $model_seller_log = Model('seller_log');
        $model_seller_log->addSellerLog($seller_info);
	}

	/**
	 * 记录店铺费用
	 *
	 * @param $cost_price 费用金额
	 * @param $cost_remark 费用备注
	 */
    protected function recordStoreCost($cost_price, $cost_remark) {
        // 平台店铺不记录店铺费用
        if ($_SESSION['store_id'] == DEFAULT_PLATFORM_STORE_ID) {
            return false;
        }
        $model_store_cost = Model('store_cost');
        $param = array();
        $param['cost_store_id'] = $_SESSION['store_id'];
        $param['cost_seller_id'] = $_SESSION['seller_id'];
        $param['cost_price'] = $cost_price;
        $param['cost_remark'] = $cost_remark;
        $param['cost_state'] = 0;
        $param['cost_time'] = TIMESTAMP;
        $model_store_cost->addStoreCost($param);
    }

    protected function getSellerMenuList($is_admin, $limits) {
        $seller_menu = array();
        if (intval($is_admin) !== 1) {
            $menu_list = $this->_getMenuList();
            foreach ($menu_list as $key => $value) {
                foreach ($value['child'] as $child_key => $child_value) {
                    if (!in_array($child_value['act'], $limits)) {
                        unset($menu_list[$key]['child'][$child_key]);
                    }
                }

                if(count($menu_list[$key]['child']) > 0) {
                    $seller_menu[$key] = $menu_list[$key];
                }
            }
        } else {
            $seller_menu = $this->_getMenuList();
        }
        $seller_function_list = $this->_getSellerFunctionList($seller_menu);
        return array('seller_menu' => $seller_menu, 'seller_function_list' => $seller_function_list);
    }

    private function _getCurrentMenu($seller_function_list) {
        $current_menu = $seller_function_list[$_GET['act']];
        if(empty($current_menu)) {
            $current_menu = array(
                'model' => 'index',
                'model_name' => '首页'
            );
        }
        return $current_menu;
    }

    private function _getMenuList() {
        $menu_list = array(
            'goods' => array('name' => '商品', 'child' => array(
                array('name' => '商品发布', 'act'=>'store_goods_add', 'op'=>'index'),
                array('name' => '出售中的商品', 'act'=>'store_goods_online', 'op'=>'index'),
                array('name' => '仓库中的商品', 'act'=>'store_goods_offline', 'op'=>'index'),
                array('name' => '库存警报', 'act'=>'store_storage_alarm', 'op' => 'index'),
                array('name' => '关联板式', 'act'=>'store_plate', 'op'=>'index'),
                array('name' => '商品规格', 'act' => 'store_spec', 'op' => 'index'),
                array('name' => '图片空间', 'act'=>'store_album', 'op'=>'album_cate'),
            )),
            'order' => array('name' => '订单', 'child' => array(
                array('name' => '订单管理', 'act'=>'store_order', 'op'=>'index'),
                array('name' => '发货', 'act'=>'store_deliver', 'op'=>'index'),
                array('name' => '发货设置', 'act'=>'store_deliver_set', 'op'=>'daddress_list'),
                array('name' => '评价管理', 'act'=>'store_evaluate', 'op'=>'list'),
                array('name' => '打印设置', 'act'=>'store_printsetup', 'op'=>'index'),
            )),
            'promotion' => array('name' => '促销', 'child' => array(
                array('name' => '团购管理', 'act'=>'store_groupbuy', 'op'=>'index'),
                array('name' => '限时折扣', 'act'=>'store_promotion_xianshi', 'op'=>'xianshi_list'),
                array('name' => '满即送', 'act'=>'store_promotion_mansong', 'op'=>'mansong_list'),
                array('name' => '优惠套装', 'act'=>'store_promotion_bundling', 'op'=>'bundling_list'),
                array('name' => '推荐展位', 'act' => 'store_promotion_booth', 'op' => 'booth_goods_list'),
                array('name' => '代金券管理', 'act'=>'store_voucher', 'op'=>'templatelist'),
                array('name' => '活动管理', 'act'=>'store_activity', 'op'=>'store_activity'),
            )),
            'store' => array('name' => '店铺', 'child' => array(
                array('name' => '店铺设置', 'act'=>'store_setting', 'op'=>'store_setting'),
                array('name' => '店铺导航', 'act'=>'store_navigation', 'op'=>'navigation_list'),
                array('name' => '店铺动态', 'act'=>'store_sns', 'op'=>'index'),
                array('name' => '店铺信息', 'act'=>'store_info', 'op'=>'store_info'),
                array('name' => '店铺分类', 'act'=>'store_goods_class', 'op'=>'index'),
                array('name' => '品牌申请', 'act'=>'store_brand', 'op'=>'brand_list'),
            )),
            'transport' => array('name' => '物流', 'child' => array(
                array('name' => '物流工具', 'act'=>'store_transport', 'op'=>'index'),
                array('name' => '免运费额度', 'act'=>'store_free_freight', 'op'=>'index'),
            )),
            'consult' => array('name' => '客服', 'child' => array(
                array('name' => '客服设置', 'act'=>'store_callcenter', 'op'=>'index'),
                array('name' => '咨询管理', 'act'=>'store_consult', 'op'=>'consult_list'),
                array('name' => '投诉管理', 'act'=>'store_complain', 'op'=>'list'),
            )),
            'service' => array('name' => '售后', 'child' => array(
                array('name' => '退款记录', 'act'=>'store_refund', 'op'=>'index'),
                array('name' => '退货记录', 'act'=>'store_return', 'op'=>'index'),
            )),
            'settle' => array('name' => '结算', 'child' => array(
                    array('name' => '结算管理', 'act'=>'store_bill', 'op'=>'index'),
            )),
            'statistics' => array('name' => '统计', 'child' => array(
                array('name' => '流量统计', 'act'=>'statistics_flow', 'op'=>'flow_statistics'),
                array('name' => '销量统计', 'act'=>'statistics_sale', 'op'=>'sale_statistics'),
                array('name' => '购买率统计', 'act'=>'statistics_probability', 'op'=>'probability_statistics'),
            )),
            'account' => array('name' => '帐号', 'child' => array(
                array('name' => '帐号列表', 'act'=>'store_account', 'op'=>'account_list'),
                array('name' => '帐号组', 'act'=>'store_account_group', 'op'=>'group_list'),
                array('name' => '帐号日志', 'act'=>'seller_log', 'op'=>'log_list'),
                array('name' => '店铺消费', 'act'=>'store_cost', 'op'=>'cost_list'),
            ))
        );
        return $menu_list;
    }

    private function _getSellerFunctionList($menu_list) {
        $format_menu = array();
        foreach ($menu_list as $key => $menu_value) {
            foreach ($menu_value['child'] as $submenu_value) {
                $format_menu[$submenu_value['act']] = array(
                    'model' => $key,
                    'model_name' => $menu_value['name'],
                    'name' => $submenu_value['name'],
                    'act' => $submenu_value['act'],
                    'op' => $submenu_value['op'],
                );
            }
        }
        return $format_menu;
    }

    /**
     * 自动发布店铺动态
     *
     * @param array $data 相关数据
     * @param string $type 类型 'new','coupon','xianshi','mansong','bundling','groupbuy'
     *            所需字段
     *            new       goods表'             goods_id,store_id,goods_name,goods_image,goods_price,goods_transfee_charge,goods_freight
     *            xianshi   p_xianshi_goods表'   goods_id,store_id,goods_name,goods_image,goods_price,goods_freight,xianshi_price
     *            mansong   p_mansong表'         mansong_name,start_time,end_time,store_id
     *            bundling  p_bundling表'        bl_id,bl_name,bl_img,bl_discount_price,bl_freight_choose,bl_freight,store_id
     *            groupbuy  goods_group表'       group_id,group_name,goods_id,goods_price,groupbuy_price,group_pic,rebate,start_time,end_time
     *            coupon在后台发布
     */
    public function storeAutoShare($data, $type) {
        $param = array(
                3 => 'new',
                4 => 'coupon',
                5 => 'xianshi',
                6 => 'mansong',
                7 => 'bundling',
                8 => 'groupbuy'
            );
        $param_flip = array_flip($param);
        if (!in_array($type, $param) || empty($data)) {
            return false;
        }

        $auto_setting = Model('store_sns_setting')->getStoreSnsSettingInfo(array('sauto_storeid' => $_SESSION ['store_id']));
        $auto_sign = false; // 自动发布开启标志

        if ($auto_setting['sauto_' . $type] == 1) {
            $auto_sign = true;
            if (CHARSET == 'GBK') {
                foreach ((array)$data as $k => $v) {
                    $data[$k] = Language::getUTF8($v);
                }
            }
            $goodsdata = addslashes(json_encode($data));
            if ($auto_setting['sauto_' . $type . 'title'] != '') {
                $title = $auto_setting['sauto_' . $type . 'title'];
            } else {
                $auto_title = 'nc_store_auto_share_' . $type . rand(1, 5);
                $title = Language::get($auto_title);
            }
        }
        if ($auto_sign) {
            // 插入数据
            $stracelog_array = array();
            $stracelog_array['strace_storeid'] = $this->store_info['store_id'];
            $stracelog_array['strace_storename'] = $this->store_info['store_name'];
            $stracelog_array['strace_storelogo'] = empty($this->store_info['store_label']) ? '' : $this->store_info['store_label'];
            $stracelog_array['strace_title'] = $title;
            $stracelog_array['strace_content'] = '';
            $stracelog_array['strace_time'] = TIMESTAMP;
            $stracelog_array['strace_type'] = $param_flip[$type];
            $stracelog_array['strace_goodsdata'] = $goodsdata;
            Model('store_sns_tracelog')->saveStoreSnsTracelog($stracelog_array);
            return true;
        } else {
            return false;
        }
    }

}

class BaseStoreSnsControl extends Control {
    const MAX_RECORDNUM = 20;	// 允许插入新记录的最大次数，sns页面该常量是一样的。
    public function __construct(){
        Language::read('common,store_layout');
        Tpl::output('max_recordnum', self::MAX_RECORDNUM);
        Tpl::setDir('store');
        Tpl::setLayout('store_sns_layout');

        Tpl::output('nav_list',($nav = H('nav'))? $nav :H('nav',true));
    }

    protected function getStoreInfo($store_id) {
        //得到店铺等级信息
        $store_info = Model('store')->getStoreInfoByID($store_id);
        if (empty($store_info)) {
            showMessage(Language::get('store_sns_store_not_exists'),'','html','error');
        }
        //处理地区信息
        $area_array	= array();
        $area_array = explode("\t",$store_info["area_info"]);
        $map_city	= Language::get('store_sns_city');
        $city	= '';
        if(strpos($area_array[0], $map_city) !== false){
            $city	= $area_array[0];
        }else {
            $city	= $area_array[1];
        }
        $store_info['city'] = $city;
        Tpl::output('store_info', $store_info);
    }
}
