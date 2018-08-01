<?php
/**
 * 用户中心店铺统计
 * 
 * 
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class statistics_flowControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_statistics');
	}
	/**
	 * 流量统计
	 *
	 * @param 
	 * @return 
	 */
	public function flow_statisticsOp() {
		//确定统计分表名称
		$flow_tableid = 0;
		$len = strlen(strval(intval($_SESSION['store_id'])));
		$last_num = substr(strval(intval($_SESSION['store_id'])), $len-1,1);
		switch ($last_num){
			case 1:
				$flow_tableid = 1;
				break;
			case 2:
				$flow_tableid = 1;
				break;
			case 3:
				$flow_tableid = 2;
				break;
			case 4:
				$flow_tableid = 2;
				break;
			case 5:
				$flow_tableid = 3;
				break;
			case 6:
				$flow_tableid = 3;
				break;
			case 7:
				$flow_tableid = 4;
				break;
			case 8:
				$flow_tableid = 4;
				break;
			case 9:
				$flow_tableid = 5;
				break;
			case 0:
				$flow_tableid = 5;
				break;
		}
		$flow_tablename = 'flowstat_'.$flow_tableid;
		if($_GET['type'] == 'month'){
			$year  = date('Y',time());
			$month = date('m',time());
			$day31 = array('01','03','05','07','08','10','12');
			if(in_array($month, $day31)){
				$daynum = 31;
			}else{
				if($month == '02'){
					//二月判断是否是闰月
					if ($year%4==0 && ($year%100!=0 || $year%400==0)){
						$daynum = 29;
					}else{
						$daynum = 28;
					}
				}else{
					$daynum = 30;
				}
			}
			$main_title = intval($month).Language::get('month_flow_title');
			$sub_title  = $year.'.'.$month.'.01-'.$year.'.'.$month.'.'.$daynum;
			$result_date_str  = '';
			$request_date_str = '';
			for($i = 1;$i<=$daynum;$i++){
				$result_date_str  .= $i.',';
				$request_date_str .= $i<10?$year.$month.'0'.$i.',':$year.$month.$i.',';
			}
			$result_date_str  = trim($result_date_str,',');
			$request_date_str = trim($request_date_str,',');
			$model = Model();
			$flow_array = $model->table($flow_tablename)->field('date,clicknum')->where(array('date'=>array('in',$request_date_str),'store_id'=>$_SESSION['store_id'],'type'=>'sum'))->select();
			//整理流量信息数组
			$result_clicknum_str = '';
			$request_date_array = explode(',', $request_date_str);
			if(empty($flow_array)){
				for($i = 1;$i<=$daynum;$i++){
					$result_clicknum_str .= '0,';
				}
				$result_clicknum_str = trim($result_clicknum_str,',');
			}else{
				foreach ($request_date_array as $val){
					$find = false;
					foreach ($flow_array as $fk=>$fv){
						if($fv['date'] == $val){
							$result_clicknum_str .= $fv['clicknum'].',';
							$find = true;
							break;
						}
					}
					if(!$find){
						$result_clicknum_str .= '0,';
					}
				}
				$result_clicknum_str = trim($result_clicknum_str,',');
			}
			
			Tpl::output('usextip','yes');
			Tpl::output('xtip',Language::get('stat_day'));
		}elseif ($_GET['type'] == 'year'){
			$year = date('Y',time());
			$main_title = $year.Language::get('year_flow_title');
			$sub_title  = $year.'.01-'.$year.'.12';
			$request_date_str = '';
			$day31 = array('01','03','05','07','08','10','12');
			for($i=1;$i<=12;$i++){
				$month = $i<10?'0'.$i:$i;
				if(in_array($month, $day31)){
					for($j=1;$j<=31;$j++){
						$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
					}
				}else{
					if($month == '02'){
						//二月判断是否是闰月
						if ($year%4==0 && ($year%100!=0 || $year%400==0)){
							for($j=1;$j<=29;$j++){
								$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
							}
						}else{
							for($j=1;$j<=28;$j++){
								$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
							}
						}
					}else{
						for($j=1;$j<=30;$j++){
							$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
						}
					}
				}
			}
			$request_date_str = trim($request_date_str,',');
			$result_date_str  = '1,2,3,4,5,6,7,8,9,10,11,12';
			$model = Model();
			$flow_array = $model->table($flow_tablename)->field('date,clicknum')->where(array('date'=>array('in',$request_date_str),'store_id'=>$_SESSION['store_id'],'type'=>'sum'))->select();
			//整理流量信息数组
			$result = array('jan'=>0,'feb'=>0,'mar'=>0,'apr'=>0,'may'=>0,'jun'=>0,'jul'=>0,'aug'=>0,'sep'=>0,'oct'=>0,'nov'=>0,'dec'=>0);
			$result_clicknum_str = '';
			if(!empty($flow_array)){
				foreach ($flow_array as $k=>$v){
					$ym = substr(strval($v['date']), 4, 2);
					switch ($ym){
						case '01':
							$result['jan'] += $v['clicknum'];
							break;
						case '02':
							$result['feb'] += $v['clicknum'];
							break;
						case '03':
							$result['mar'] += $v['clicknum'];
							break;
						case '04':
							$result['apr'] += $v['clicknum'];
							break;
						case '05':
							$result['may'] += $v['clicknum'];
							break;
						case '06':
							$result['jun'] += $v['clicknum'];
							break;
						case '07':
							$result['jul'] += $v['clicknum'];
							break;
						case '08':
							$result['aug'] += $v['clicknum'];
							break;
						case '09':
							$result['sep'] += $v['clicknum'];
							break;
						case '10':
							$result['oct'] += $v['clicknum'];
							break;
						case '11':
							$result['nov'] += $v['clicknum'];
							break;
						case '12':
							$result['dec'] += $v['clicknum'];
							break;						
					}
				}
			}
			foreach ($result as $val){
				$result_clicknum_str .= $val.',';
			}
			$result_clicknum_str = trim($result_clicknum_str,',');
			
			Tpl::output('usextip','yes');
			Tpl::output('xtip',Language::get('stat_month'));
		}else{
			if($_GET['add_time_from'] != '' && $_GET['add_time_to'] != ''){
				$request_date_str = '';
				$fromsp = strtotime($_GET['add_time_from']);
				$tosp   = strtotime($_GET['add_time_to']);
				while ($fromsp<=$tosp){
					$request_date_str .= date('Ymd',$fromsp).',';
					$fromsp += 86400;
				}
				$request_date_str = trim($request_date_str,',');
				
				$model = Model();
				$flow_array = $model->table($flow_tablename)->field('date,clicknum')->where(array('date'=>array('in',$request_date_str),'store_id'=>$_SESSION['store_id'],'type'=>'sum'))->select();
				//整理数组信息
				$result_clicknum_str = '';
				$request_date_array = explode(',', $request_date_str);
				$daynum = count($request_date_array);
				if(empty($flow_array)){
					for($i = 1;$i<=$daynum;$i++){
						$result_clicknum_str .= '0,';
					}
					$result_clicknum_str = trim($result_clicknum_str,',');
				}else{
					foreach ($request_date_array as $val){
						$find = false;
						foreach ($flow_array as $fk=>$fv){
							if($fv['date'] == $val){
								$result_clicknum_str .= $fv['clicknum'].',';
								$find = true;
								break;
							}
						}
						if(!$find){
							$result_clicknum_str .= '0,';
						}
					}
					$result_clicknum_str = trim($result_clicknum_str,',');
				}
				$result_date_str = $request_date_str;
				
				$from = $_GET['add_time_from'];
				$to = $_GET['add_time_to'];
				$main_title = Language::get('store_flow_search_result');
				$sub_title  = substr($from,0,4).'.'.substr($from,4,2).'.'.substr($from,6,2).'-'.substr($to,0,4).'.'.substr($to,4,2).'.'.substr($to,6,2);
				
				if($daynum > 7){
					Tpl::output('labellean','yes');
				}
			}else{
				//默认调取本周店铺流量数据
				$day = date('l',time());
				switch ($day){
					case 'Monday':
						$request_date_str = date('Ymd',time());
						$result_date_str  = date('Ymd',time()).','.date('Ymd',time()+86400).','.date('Ymd',time()+86400*2).','.date('Ymd',time()+86400*3).','.date('Ymd',time()+86400*4).','.date('Ymd',time()+86400*5).','.date('Ymd',time()+86400*6);
						$sub_title = date('Y.m.d',time()).'-'.date('Y.m.d',time()+86400*6);
						break;
					case 'Tuesday':
						$request_date_str = date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400).','.date('Ymd',time()).','.date('Ymd',time()+86400).','.date('Ymd',time()+86400*2).','.date('Ymd',time()+86400*3).','.date('Ymd',time()+86400*4).','.date('Ymd',time()+86400*5);
						$sub_title = date('Y.m.d',time()-86400).'-'.date('Y.m.d',time()+86400*5);
						break;
					case 'Wednesday':
						$request_date_str = date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time()).','.date('Ymd',time()+86400).','.date('Ymd',time()+86400*2).','.date('Ymd',time()+86400*3).','.date('Ymd',time()+86400*4);
						$sub_title = date('Y.m.d',time()-86400*2).'-'.date('Y.m.d',time()+86400*4);
						break;
					case 'Thursday':
						$request_date_str = date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time()).','.date('Ymd',time()+86400).','.date('Ymd',time()+86400*2).','.date('Ymd',time()+86400*3);
						$sub_title = date('Y.m.d',time()-86400*3).'-'.date('Y.m.d',time()+86400*3);
						break;
					case 'Friday':
						$request_date_str = date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time()).','.date('Ymd',time()+86400).','.date('Ymd',time()+86400*2);
						$sub_title = date('Y.m.d',time()-86400*4).'-'.date('Y.m.d',time()+86400*2);
						break;
					case 'Saturday':
						$request_date_str = date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time()).','.date('Ymd',time()+86400);
						$sub_title = date('Y.m.d',time()-86400*5).'-'.date('Y.m.d',time()+86400);
						break;
					case 'Sunday':
						$request_date_str = date('Ymd',time()-86400*6).','.date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$result_date_str  = date('Ymd',time()-86400*6).','.date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*6).'-'.date('Y.m.d',time());
						break;
				}
				$main_title = Language::get('week_flow_title');
				$model = Model();
				$flow_array = $model->table($flow_tablename)->field('date,clicknum')->where(array('date'=>array('in',$request_date_str),'store_id'=>$_SESSION['store_id'],'type'=>'sum'))->select();
				//整理流量信息数组
				$result_clicknum_str = '';
				$result_date_array = explode(',', $result_date_str);
				if(empty($flow_array)){
					$result_clicknum_str = '0,0,0,0,0,0,0';
				}else{
					foreach ($result_date_array as $val){
						$find = false;
						foreach ($flow_array as $fk=>$fv){
							if($fv['date'] == $val){
								$result_clicknum_str .= $fv['clicknum'].',';
								$find = true;
								break;
							}
						}
						if(!$find){
							$result_clicknum_str .= '0,';
						}
					}
					$result_clicknum_str = trim($result_clicknum_str,',');
				}
			}
		}
		//模版输出
		Tpl::output('result_date_str',$result_date_str);
		Tpl::output('result_clicknum_str',$result_clicknum_str);
		Tpl::output('main_title',$main_title);
		Tpl::output('sub_title',$sub_title);
		self::profile_menu('store_flow');
		Tpl::output('menu_sign','flow_statistics');
		Tpl::output('menu_sign_url','index.php?act=statistics&op=flow_statistics');
		Tpl::output('menu_sign1','store_flow');
		Tpl::showpage('flow_stat');
	}
	/**
	 * 商品流量排名
	 *
	 * @param 
	 * @return 
	 */
	public function goods_flow_statisticsOp(){
		//确定统计分表名称
		$flow_tableid = 0;
		$len = strlen(strval(intval($_SESSION['store_id'])));
		$last_num = substr(strval(intval($_SESSION['store_id'])), $len-1,1);
		switch ($last_num){
			case 1:
				$flow_tableid = 1;
				break;
			case 2:
				$flow_tableid = 1;
				break;
			case 3:
				$flow_tableid = 2;
				break;
			case 4:
				$flow_tableid = 2;
				break;
			case 5:
				$flow_tableid = 3;
				break;
			case 6:
				$flow_tableid = 3;
				break;
			case 7:
				$flow_tableid = 4;
				break;
			case 8:
				$flow_tableid = 4;
				break;
			case 9:
				$flow_tableid = 5;
				break;
			case 0:
				$flow_tableid = 5;
				break;
		}
		$flow_tablename = 'flowstat_'.$flow_tableid;
		if($_GET['type'] == 'month'){
			$year  = date('Y',time());
			$month = date('m',time());
			$day31 = array('01','03','05','07','08','10','12');
			if(in_array($month, $day31)){
				$daynum = 31;
			}else{
				if($month == '02'){
					//二月判断是否是闰月
					if ($year%4==0 && ($year%100!=0 || $year%400==0)){
						$daynum = 29;
					}else{
						$daynum = 28;
					}
				}else{
					$daynum = 30;
				}
			}
			$main_title = intval($month).Language::get('month_flow_rank_title');
			$sub_title  = $year.'.'.$month.'.01-'.$year.'.'.$month.'.'.$daynum;
			$request_date_str = '';
			for($i = 1;$i<=$daynum;$i++){
				$result_date_str  .= $i.',';
				$request_date_str .= $i<10?$year.$month.'0'.$i.',':$year.$month.$i.',';
			}
			$request_date_str = trim($request_date_str,',');
		}elseif ($_GET['type'] == 'year'){
			$year = date('Y',time());
			$main_title = $year.Language::get('year_flow_rank_title');
			$sub_title  = $year.'.01-'.$year.'.12';
			$request_date_str = '';
			$day31 = array('01','03','05','07','08','10','12');
			for($i=1;$i<=12;$i++){
				$month = $i<10?'0'.$i:$i;
				if(in_array($month, $day31)){
					for($j=1;$j<=31;$j++){
						$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
					}
				}else{
					if($month == '02'){
						//二月判断是否是闰月
						if ($year%4==0 && ($year%100!=0 || $year%400==0)){
							for($j=1;$j<=29;$j++){
								$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
							}
						}else{
							for($j=1;$j<=28;$j++){
								$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
							}
						}
					}else{
						for($j=1;$j<=30;$j++){
							$request_date_str .= $j<10?$year.$month.'0'.$j.',':$year.$month.$j.',';
						}
					}
				}
			}
			$request_date_str = trim($request_date_str,',');
		}else{
			if($_GET['add_time_from'] != '' && $_GET['add_time_to'] != ''){
				$request_date_str = '';
				$fromsp = strtotime($_GET['add_time_from']);
				$tosp   = strtotime($_GET['add_time_to']);
				while ($fromsp<=$tosp){
					$request_date_str .= date('Ymd',$fromsp).',';
					$fromsp += 86400;
				}
				$request_date_str = trim($request_date_str,',');
				
				$from = $_GET['add_time_from'];
				$to = $_GET['add_time_to'];
				$main_title = Language::get('store_flow_rank_search_result');
				$sub_title  = substr($from,0,4).'.'.substr($from,4,2).'.'.substr($from,6,2).'-'.substr($to,0,4).'.'.substr($to,4,2).'.'.substr($to,6,2);
			}else{
				//默认显示本周商品流量排名
				$day = date('l',time());
				switch ($day){
					case 'Monday':
						$request_date_str = date('Ymd',time());
						$sub_title = date('Y.m.d',time()).'-'.date('Y.m.d',time()+86400*6);
						break;
					case 'Tuesday':
						$request_date_str = date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400).'-'.date('Y.m.d',time()+86400*5);
						break;
					case 'Wednesday':
						$request_date_str = date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*2).'-'.date('Y.m.d',time()+86400*4);
						break;
					case 'Thursday':
						$request_date_str = date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*3).'-'.date('Y.m.d',time()+86400*3);
						break;
					case 'Friday':
						$request_date_str = date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*4).'-'.date('Y.m.d',time()+86400*2);
						break;
					case 'Saturday':
						$request_date_str = date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*5).'-'.date('Y.m.d',time()+86400);
						break;
					case 'Sunday':
						$request_date_str = date('Ymd',time()-86400*6).','.date('Ymd',time()-86400*5).','.date('Ymd',time()-86400*4).','.date('Ymd',time()-86400*3).','.date('Ymd',time()-86400*2).','.date('Ymd',time()-86400).','.date('Ymd',time());
						$sub_title = date('Y.m.d',time()-86400*6).'-'.date('Y.m.d',time());
						break;
				}
				$main_title = Language::get('week_flow_rank_title');
				
			}
		}
		$model = Model();
		$table = $flow_tablename.',goods';
		$field = 'sum('.$flow_tablename.'.clicknum) as sum,'.$flow_tablename.'.goods_id,goods.goods_name';
		$where = $flow_tablename.".type = 'goods' and ".$flow_tablename.".date in (".$request_date_str.") and ".$flow_tablename.".store_id = '".$_SESSION['store_id']."'";
		$group = $flow_tablename.'.goods_id';
		$on    = 'goods.goods_id='.$flow_tablename.'.goods_id';
		$order = 'sum desc';
		$limit = '10';
		$flow_array = $model->table($table)->field($field)->join('left')->on($on)->where($where)->group($group)->limit($limit)->order($order)->select();
		//处理数组信息
		$result_goods_str = '';
		$result_clicknum_str = '';
		if(!empty($flow_array)){
			foreach ($flow_array as $k=>$v){
				$result_goods_str .= "'".addslashes(html_entity_decode($v['goods_name']))."',";//还原被转换的双引号，加斜线防止中间有单引号使页面的JS数组出错
				$result_clicknum_str .= $v['sum'].',';
			}
		}
		$result_goods_str = trim($result_goods_str,',');
		$result_clicknum_str = trim($result_clicknum_str,',');
		//模版输出
		Tpl::output('result_goods_str',$result_goods_str);
		Tpl::output('result_clicknum_str',$result_clicknum_str);
		Tpl::output('main_title',$main_title);
		Tpl::output('sub_title',$sub_title);
		self::profile_menu('goods_flow');
		Tpl::output('menu_sign','flow_statistics');
		Tpl::output('menu_sign_url','index.php?act=statistics&op=flow_statistics');
		Tpl::output('menu_sign1','goods_flow');
		Tpl::showpage('goods_flow');
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_key='') {
        $menu_array	= array(
            1=>array('menu_key'=>'store_flow','menu_name'=>Language::get('stat_store_flow'),	'menu_url'=>'index.php?act=statistics_flow&op=flow_statistics'),
            2=>array('menu_key'=>'goods_flow','menu_name'=>Language::get('stat_goods_flow'),	'menu_url'=>'index.php?act=statistics_flow&op=goods_flow_statistics')
        );
        Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
