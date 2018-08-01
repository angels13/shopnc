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

class statistics_probabilityControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_statistics');
	}

	/**
	 * 购买率统计（销售量/访问量）
	 *
	 * @param
	 * @return
	 */
	public function probability_statisticsOp() {
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
			$main_title = intval($month).Language::get('month_ps_title');
			$sub_title  = $year.'.'.$month.'.01-'.$year.'.'.$month.'.'.$daynum;
			$result_date_str  = '';
			$request_date_str = '';
			for($i = 1;$i<=$daynum;$i++){
				$result_date_str  .= $i.',';
				$request_date_str .= $i<10?$year.$month.'0'.$i.',':$year.$month.$i.',';
			}
			$result_date_str  = trim($result_date_str,',');
			$request_date_str = trim($request_date_str,',');
		}elseif ($_GET['type'] == 'year'){
			$year = date('Y',time());
			$main_title = $year.Language::get('year_ps_title');
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
			}else{
				//默认显示本周购买率
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
				$main_title = Language::get('week_ps_title');
			}
		}
		$model = Model();
		$table = 'salenum,'.$flow_tablename;
		$field = 'sum(salenum.salenum) as sum,'.$flow_tablename.'.clicknum,salenum.date';
		$where = "salenum.date in (".$request_date_str.") and ".$flow_tablename.".store_id = '".$_SESSION['store_id']."' and salenum.store_id = '".$_SESSION['store_id']."' and ".$flow_tablename.".type = 'sum'";
		$group = 'salenum.date';
		$on    = 'salenum.date='.$flow_tablename.'.date';
		$result_array = $model->table($table)->field($field)->join('left')->on($on)->where($where)->group($group)->select();
		//整理数组数据
		if($_GET['type'] == 'month'){
			$result_num_str = '';
			$request_date_array = explode(',', $request_date_str);
			if(empty($result_array)){
				for($i = 1;$i<=$daynum;$i++){
					$result_num_str .= '0,';
				}
				$result_num_str = trim($result_num_str,',');
			}else{
				foreach ($request_date_array as $val){
					$find = false;
					foreach ($result_array as $fk=>$fv){
						if($fv['date'] == $val){
							$result_num_str .= round($fv['sum']/$fv['clicknum'],2).',';
							$find = true;
							break;
						}
					}
					if(!$find){
						$result_num_str .= '0,';
					}
				}
				$result_num_str = trim($result_num_str,',');
			}

			Tpl::output('usextip','yes');
			Tpl::output('xtip',Language::get('stat_day'));
		}elseif ($_GET['type'] == 'year'){
			$result_sum = array('jan'=>0,'feb'=>0,'mar'=>0,'apr'=>0,'may'=>0,'jun'=>0,'jul'=>0,'aug'=>0,'sep'=>0,'oct'=>0,'nov'=>0,'dec'=>0);
			$result_clicknum = array('jan'=>0,'feb'=>0,'mar'=>0,'apr'=>0,'may'=>0,'jun'=>0,'jul'=>0,'aug'=>0,'sep'=>0,'oct'=>0,'nov'=>0,'dec'=>0);
			$result_num_str = '';
			if(!empty($result_array)){
				foreach ($result_array as $k=>$v){
					$ym = substr(strval($v['date']), 4, 2);
					switch ($ym){
						case '01':
							$result_sum['jan'] += $v['sum'];
							$result_clicknum['jan'] += $v['clicknum'];
							break;
						case '02':
							$result_sum['feb'] += $v['sum'];
							$result_clicknum['feb'] += $v['clicknum'];
							break;
						case '03':
							$result_sum['mar'] += $v['sum'];
							$result_clicknum['mar'] += $v['clicknum'];
							break;
						case '04':
							$result_sum['apr'] += $v['sum'];
							$result_clicknum['apr'] += $v['clicknum'];
							break;
						case '05':
							$result_sum['may'] += $v['sum'];
							$result_clicknum['may'] += $v['clicknum'];
							break;
						case '06':
							$result_sum['jun'] += $v['sum'];
							$result_clicknum['jun'] += $v['clicknum'];
							break;
						case '07':
							$result_sum['jul'] += $v['sum'];
							$result_clicknum['jul'] += $v['clicknum'];
							break;
						case '08':
							$result_sum['aug'] += $v['sum'];
							$result_clicknum['aug'] += $v['clicknum'];
							break;
						case '09':
							$result_sum['sep'] += $v['sum'];
							$result_clicknum['sep'] += $v['clicknum'];
							break;
						case '10':
							$result_sum['oct'] += $v['sum'];
							$result_clicknum['oct'] += $v['clicknum'];
							break;
						case '11':
							$result_sum['nov'] += $v['sum'];
							$result_clicknum['nov'] += $v['clicknum'];
							break;
						case '12':
							$result_sum['dec'] += $v['sum'];
							$result_clicknum['dec'] += $v['clicknum'];
							break;
					}
				}
			}
			foreach ($result_sum as $k=>$v){
				if($result_clicknum[$k] == 0){
					$result_num_str .= '0,';
				}else{
					$result_num_str .= round($v/$result_clicknum[$k],2).',';
				}
			}
			$result_num_str = trim($result_num_str,',');

			Tpl::output('usextip','yes');
			Tpl::output('xtip',Language::get('stat_month'));
		}else{
			if($_GET['add_time_from'] != '' && $_GET['add_time_to'] != ''){
				$result_num_str = '';
				$request_date_array = explode(',', $request_date_str);
				$daynum = count($request_date_array);
				if(empty($result_array)){
					for($i = 1;$i<=$daynum;$i++){
						$result_num_str .= '0,';
					}
					$result_num_str = trim($result_num_str,',');
				}else{
					foreach ($request_date_array as $val){
						$find = false;
						foreach ($result_array as $fk=>$fv){
							if($fv['date'] == $val){
								$result_num_str .= round($fv['sum']/$fv['clicknum'],2).',';
								$find = true;
								break;
							}
						}
						if(!$find){
							$result_num_str .= '0,';
						}
					}
					$result_num_str = trim($result_num_str,',');
				}
				$result_date_str = $request_date_str;

				$from = $_GET['add_time_from'];
				$to = $_GET['add_time_to'];
				$main_title = Language::get('store_ps_search_result');
				$sub_title  = substr($from,0,4).'.'.substr($from,4,2).'.'.substr($from,6,2).'-'.substr($to,0,4).'.'.substr($to,4,2).'.'.substr($to,6,2);

				if($daynum > 7){
					Tpl::output('labellean','yes');
				}
			}else{
				$result_num_str = '';
				$result_date_array = explode(',', $result_date_str);
				if(empty($result_array)){
					$result_num_str = '0,0,0,0,0,0,0';
				}else{
					foreach ($result_date_array as $val){
						$find = false;
						foreach ($result_array as $fk=>$fv){
							if($fv['date'] == $val){
								$result_num_str .= round($fv['sum']/$fv['clicknum'],2).',';
								$find = true;
								break;
							}
						}
						if(!$find){
							$result_num_str .= '0,';
						}
					}
					$result_num_str = trim($result_num_str,',');
				}
			}
		}
		//模版输出
		Tpl::output('result_date_str',$result_date_str);
		Tpl::output('result_num_str',$result_num_str);
		Tpl::output('main_title',$main_title);
		Tpl::output('sub_title',$sub_title);
		self::profile_menu('probability_statistics');
		Tpl::output('menu_sign','probability_statistics');
		Tpl::output('menu_sign_url','index.php?act=statistics&op=probability_statistics');
		Tpl::showpage('prob_stat');
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
            1=>array('menu_key'=>'probability_statistics','menu_name'=>Language::get('stat_probability_statistics'),	'menu_url'=>'index.php?act=statistics_probability&op=probability_statistics')
        );
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}

}
