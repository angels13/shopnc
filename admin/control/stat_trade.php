<?php
/**
 * 统计管理（销量分析）
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class stat_tradeControl extends SystemControl{
	private $links = array(
        array('url'=>'act=stat_trade&op=goods','lang'=>'stat_goods_ranking'),
        array('url'=>'act=stat_trade&op=income','lang'=>'stat_sale_income'),
        array('url'=>'act=stat_trade&op=predeposit','lang'=>'stat_predeposit'),
        array('url'=>'act=stat_trade&op=goods_sale','lang'=>'stat_goods_sale'),
        array('url'=>'act=stat_trade&op=class_sale','lang'=>'stat_class_sale'),
        array('url'=>'act=stat_trade&op=sale','lang'=>'stat_sale')
    );
	public function __construct(){
        parent::__construct();
        Language::read('stat');
        import('function.statistics');
        import('function.datehelper');
    }
    /**
     * 商品统计排行
     */
    public function goodsOp(){
		if(!$_REQUEST['search_type']){
			$_REQUEST['search_type'] = 'day';
		}
		//初始化时间
		//天
		if(!$_REQUEST['search_time']){
			$_REQUEST['search_time'] = date('Y-m-d', time());
		}
		$search_time = strtotime($_REQUEST['search_time']);//搜索的时间
		Tpl::output('search_time',$_REQUEST['search_time']);
		//周
		if(!$_REQUEST['search_time_year']){
			$_REQUEST['search_time_year'] = date('Y', time());
		}
		if(!$_REQUEST['search_time_month']){
			$_REQUEST['search_time_month'] = date('m', time());
		}
		if(!$_REQUEST['search_time_week']){
			$_REQUEST['search_time_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$current_year = $_REQUEST['search_time_year'];
		$current_month = $_REQUEST['search_time_month'];
		$current_week = $_REQUEST['search_time_week'];
		$year_arr = getSystemYearArr();
		$month_arr = getSystemMonthArr();
		$week_arr = getMonthWeekArr($current_year, $current_month);
		
		Tpl::output('current_year', $current_year);
		Tpl::output('current_month', $current_month);
		Tpl::output('current_week', $current_week);
		Tpl::output('year_arr', $year_arr);
		Tpl::output('month_arr', $month_arr);
		Tpl::output('week_arr', $week_arr);
		if($_REQUEST['search_type'] == 'day'){
			$stime = $search_time;//昨天0点
			$etime = $search_time + 86400 - 1;//今天24点
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods&search_type=day&search_time='.date('Y-m-d',$search_time).'&rank_type='.trim($_GET['rank_type']));
		}
		if($_REQUEST['search_type'] == 'week'){
			$current_weekarr = explode('|', $current_week);
			$stime = strtotime($current_weekarr[0])-86400*7;
			$etime = strtotime($current_weekarr[1])+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods&search_type=week&search_time_year='.$current_year.'&search_time_month='.$current_month.'&search_time_week='.$current_week.'&rank_type='.trim($_GET['rank_type']));
		}
		if($_REQUEST['search_type'] == 'month'){
			$stime = strtotime($current_year.'-'.$current_month."-01 0 month");
			$etime = getMonthLastDay($current_year,$current_month)+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods&search_type=month&search_time_year='.$current_year.'&search_time_month='.$current_month.'&rank_type='.trim($_GET['rank_type']));
		}
		/*
		 * 获取数据
		 */
		$model = Model('stat');
		//获取上架商品数量
		Tpl::output('goods_on_allnum',$model->getGoodsNum(array('goods_state'=>1)));
		//下单商品数
		Tpl::output('order_goods_num',$model->getTradeInfo('order_goods_num',$stime,$etime));
		//下单单量
		Tpl::output('order_num',$model->getTradeInfo('order_num',$stime,$etime));
		//下单客户数
		Tpl::output('order_buyer_num',$model->getTradeInfo('order_buyer_num',$stime,$etime));
		//合计金额
		Tpl::output('order_amount',$model->getTradeInfo('order_amount',$stime,$etime));
		//获取商品销售排名TOP15数据
		switch (trim($_GET['rank_type'])){
			case '':
			case 'trade_num':
				$goods_list = $model->getGoodsTradeRanking('trade_num',$stime,$etime);
				Tpl::output('table_tip','销量');
				Tpl::output('chart_tip','商品销量排行TOP15');
				break;
			case 'trade_amount':
				$goods_list = $model->getGoodsTradeRanking('trade_amount',$stime,$etime);
				Tpl::output('table_tip','销售额');
				Tpl::output('chart_tip','商品销售额排行TOP15');
				break;
		}
		if($_GET['exporttype'] == 'excel'){
			//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
			$excel_data[0][0] = array('styleid'=>'s_title','data'=>'排名');
			$excel_data[0][1] = array('styleid'=>'s_title','data'=>'商品名称');
			$excel_data[0][2] = array('styleid'=>'s_title','data'=>trim($_GET['rank_type'])=='trade_amount'?'销售额':'销量');
			//data
			foreach ($goods_list as $k=>$v){
				$excel_data[$k+1][0] = array('data'=>$k+1);
				$excel_data[$k+1][1] = array('data'=>$v['goods_name']);
				$excel_data[$k+1][2] = array('data'=>$v['allnum']);
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
			$excel_obj->addWorksheet($excel_obj->charset(trim($_GET['rank_type'])=='trade_amount'?'商品销售额排行TOP15 ':'商品销量排行TOP15 ',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset(trim($_GET['rank_type'])=='trade_amount'?'商品销售额排行TOP15 ':'商品销量排行TOP15 ',CHARSET).date('Y-m-d-H',time()));
			exit();
		}else{
			//构造横轴数据
			for($i=1; $i<=15; $i++){
				//横轴
				$stat_arr['xAxis']['categories'][] = $i;
			}
			$stat_arr['title'] = trim($_GET['rank_type'])=='trade_amount'?'商品销售额排行TOP15 ':'商品销量排行TOP15 ';
            $stat_arr['yAxis'] = trim($_GET['rank_type'])=='trade_amount'?'销售额':'销量';
			$stat_arr['series'][0]['name'] = trim($_GET['rank_type'])=='trade_amount'?'销售额':'销量';
			$stat_arr['series'][0]['data'] = array();
			for ($i = 0; $i < 15; $i++){
			    $stat_arr['series'][0]['data'][] = array('name'=>strval($goods_list[$i]['goods_name']),'y'=>floatval($goods_list[$i]['allnum']));
			}
			$stat_arr['legend']['enabled'] = false;
    		$stat_json = getStatData_Column2D($stat_arr);
    		Tpl::output('stat_json',$stat_json);
			Tpl::output('goods_list',$goods_list);
			Tpl::output('top_link',$this->sublink($this->links, 'goods'));
			Tpl::showpage('stat.goods');
		}
    }
    /**
     * 销售收入统计
     */
    public function incomeOp(){
    	$model = Model('stat');
    	if($_GET['search_year'] == '' || $_GET['search_month'] == ''){
    		$now_year = date('Y',time());
    		$now_month = date('m',time());
    		if($now_month == 1){
    			$_GET['search_year'] = $now_year-1;
    			$_GET['search_month'] = 12;
    		}else{
    			$_GET['search_year'] = $now_year;
    			if(m>10){
    				$_GET['search_month'] = m-1;
    			}else{
    				$_GET['search_month'] = '0'.(m-1);
    			}
    		}
    	}
    	$year = intval($_GET['search_year']);
    	$month = trim($_GET['search_month']);
    	$condition['os_month'] = $year.$month;
    	if($_GET['exporttype'] == 'excel'){
    		//获取全部店铺结账数据
    		$bill_list = $model->getBillList($condition,'ob',false);
    		//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
			$excel_data[0][0] = array('styleid'=>'s_title','data'=>'店铺名称');
			$excel_data[0][1] = array('styleid'=>'s_title','data'=>'卖家账号');
			$excel_data[0][2] = array('styleid'=>'s_title','data'=>'订单金额');
			$excel_data[0][3] = array('styleid'=>'s_title','data'=>'收取佣金');
			$excel_data[0][4] = array('styleid'=>'s_title','data'=>'退单金额');
			$excel_data[0][5] = array('styleid'=>'s_title','data'=>'退回佣金');
			$excel_data[0][6] = array('styleid'=>'s_title','data'=>'店铺费用');
			$excel_data[0][7] = array('styleid'=>'s_title','data'=>'结算金额');
			//data
			foreach ($bill_list as $k=>$v){
				$excel_data[$k+1][0] = array('data'=>$v['ob_store_name']);
				$excel_data[$k+1][1] = array('data'=>$v['member_name']);
				$excel_data[$k+1][2] = array('data'=>$v['ob_order_totals']);
				$excel_data[$k+1][3] = array('data'=>$v['ob_commis_totals']);
				$excel_data[$k+1][4] = array('data'=>$v['ob_order_return_totals']);
				$excel_data[$k+1][5] = array('data'=>$v['ob_commis_return_totals']);
				$excel_data[$k+1][6] = array('data'=>$v['ob_store_cost_totals']);
				$excel_data[$k+1][7] = array('data'=>$v['ob_result_totals']);
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
			$excel_obj->addWorksheet($excel_obj->charset('店铺佣金统计',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset('店铺佣金统计',CHARSET).date('Y-m-d-H',time()));
			exit();
    	}else{
    		//获取平台总数据
	    	$plat_data = $model->getBillList($condition,'os');
	    	Tpl::output('plat_data',$plat_data[0]);
	    	//店铺数据
	    	Tpl::output('store_list',$model->getBillList($condition,'ob'));
	    	Tpl::output('show_page',$model->showpage());
	    	Tpl::output('top_link',$this->sublink($this->links, 'income'));
			Tpl::showpage('stat.income');
    	}
    }
    /**
     * 预存款统计
     */
    public function predepositOp(){
    	$where = array();
    	if(trim($_GET['pd_type'])=='cash_pay'){
    		$field = 'sum(lg_freeze_amount) as allnum';
    	}else{
    		$field = 'sum(lg_av_amount) as allnum';
    	}
		if(!$_REQUEST['search_type']){
			$_REQUEST['search_type'] = 'day';
		}
		$where['lg_type'] = trim($_GET['pd_type'])==''?'recharge':trim($_GET['pd_type']);
		//初始化时间
		//天
		if(!$_REQUEST['search_time']){
			$_REQUEST['search_time'] = date('Y-m-d', time());
		}
		$search_time = strtotime($_REQUEST['search_time']);//搜索的时间
		Tpl::output('search_time',$_REQUEST['search_time']);
		//周
		if(!$_REQUEST['search_time_year']){
			$_REQUEST['search_time_year'] = date('Y', time());
		}
		if(!$_REQUEST['search_time_month']){
			$_REQUEST['search_time_month'] = date('m', time());
		}
		if(!$_REQUEST['search_time_week']){
			$_REQUEST['search_time_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$current_year = $_REQUEST['search_time_year'];
		$current_month = $_REQUEST['search_time_month'];
		$current_week = $_REQUEST['search_time_week'];
		$year_arr = getSystemYearArr();
		$month_arr = getSystemMonthArr();
		$week_arr = getMonthWeekArr($current_year, $current_month);
		
		Tpl::output('current_year', $current_year);
		Tpl::output('current_month', $current_month);
		Tpl::output('current_week', $current_week);
		Tpl::output('year_arr', $year_arr);
		Tpl::output('month_arr', $month_arr);
		Tpl::output('week_arr', $week_arr);
		
    	$model = Model('stat');
		$statlist = array();//统计数据列表
		if($_REQUEST['search_type'] == 'day'){
			//构造横轴数据
			for($i=0; $i<24; $i++){
				//统计图数据
				$curr_arr[$i] = 0;//今天
				$up_arr[$i] = 0;//昨天
				//统计表数据
				$uplist_arr[$i]['timetext'] = $i;
				$currlist_arr[$i]['timetext'] = $i;
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = "$i";
			}
			$stime = $search_time - 86400;//昨天0点
			$etime = $search_time + 86400 - 1;//今天24点
			
			$today_day = @date('d', $search_time);//今天日期
			$yesterday_day = @date('d', $stime);//昨天日期
			
			$where['lg_add_time'] = array('between',array($stime,$etime));
			$field .= ' ,DAY(FROM_UNIXTIME(lg_add_time)) as dayval,HOUR(FROM_UNIXTIME(lg_add_time)) as hourval ';
			$memberlist = $model->getPredepositInfo($where, $field, 0, '', 0, 'dayval,hourval');
			if($memberlist){
				foreach($memberlist as $k => $v){
					if($today_day == $v['dayval']){
						$curr_arr[$v['hourval']] = abs($v['allnum']);
						$currlist_arr[$v['hourval']]['val'] = abs($v['allnum']);
					}
					if($yesterday_day == $v['dayval']){
						$up_arr[$v['hourval']] = abs($v['allnum']);
						$uplist_arr[$v['hourval']]['val'] = abs($v['allnum']);
					}
				}
			}
			$stat_arr['series'][0]['name'] = '昨天';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '今天';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
			
			//统计数据标题
			$statlist['headertitle'] = array('小时','昨天','今天','同比');
			Tpl::output('actionurl','index.php?act=stat_trade&op=predeposit&search_type=day&search_time='.date('Y-m-d',$search_time));
		}
		
		if($_REQUEST['search_type'] == 'week'){
			$current_weekarr = explode('|', $current_week);
			$stime = strtotime($current_weekarr[0])-86400*7;
			$etime = strtotime($current_weekarr[1])+86400-1;
			$up_week = @date('W', $stime);//上周
			$curr_week = @date('W', $etime);//本周
			//构造横轴数据
			for($i=1; $i<=7; $i++){
				//统计图数据
				$up_arr[$i] = 0;
				$curr_arr[$i] = 0;
				$tmp_weekarr = getSystemWeekArr();
				//统计表数据
				$uplist_arr[$i]['timetext'] = $tmp_weekarr[$i];
				$currlist_arr[$i]['timetext'] = $tmp_weekarr[$i];
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = $tmp_weekarr[$i];
				unset($tmp_weekarr);
			}
			$where['lg_add_time'] = array('between', array($stime,$etime));
			$field .= ',WEEKOFYEAR(FROM_UNIXTIME(lg_add_time)) as weekval,DAYOFWEEK(FROM_UNIXTIME(lg_add_time)) as dayofweekval ';
			$memberlist = $model->getPredepositInfo($where, $field, 0, '', 0, 'weekval,dayofweekval');
			if($memberlist){
				foreach($memberlist as $k=>$v){
					if ($up_week == $v['weekval']){
						$up_arr[$v['dayofweekval']] = abs($v['allnum']);
						$uplist_arr[$v['dayofweekval']]['val'] = abs($v['allnum']);
					}
					if ($curr_week == $v['weekval']){
						$curr_arr[$v['dayofweekval']] = abs($v['allnum']);
						$currlist_arr[$v['dayofweekval']]['val'] = abs($v['allnum']);
					}
				}
			}
			$stat_arr['series'][0]['name'] = '上周';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '本周';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
			//统计数据标题
			$statlist['headertitle'] = array('星期','上周','本周','同比');
			Tpl::output('actionurl','index.php?act=stat_trade&op=predeposit&search_type=week&search_time_year='.$current_year.'&search_time_month='.$current_month.'&search_time_week='.$current_week);
		}
		
		if($_REQUEST['search_type'] == 'month'){
			$stime = strtotime($current_year.'-'.$current_month."-01 -1 month");
			$etime = getMonthLastDay($current_year,$current_month)+86400-1;
			
			$up_month = date('m',$stime);
			$curr_month = date('m',$etime);
			//计算横轴的最大量（由于每个月的天数不同）
			$up_dayofmonth = date('t',$stime);
			$curr_dayofmonth = date('t',$etime);
			$x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;
			
		    //构造横轴数据
			for($i=1; $i<=$x_max; $i++){
				//统计图数据
				$up_arr[$i] = 0;
				$curr_arr[$i] = 0;
				//统计表数据
				$uplist_arr[$i]['timetext'] = $i;
				$currlist_arr[$i]['timetext'] = $i;
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = $i;
			}
			$where['lg_add_time'] = array('between', array($stime,$etime));
			$field .= ',MONTH(FROM_UNIXTIME(lg_add_time)) as monthval,day(FROM_UNIXTIME(lg_add_time)) as dayval ';
			$memberlist = $model->getPredepositInfo($where, $field, 0, '', 0, 'monthval,dayval');
		    if($memberlist){
				foreach($memberlist as $k=>$v){
					if ($up_month == $v['monthval']){
						$up_arr[$v['dayval']] = abs($v['allnum']);
						$uplist_arr[$v['dayval']]['val'] = abs($v['allnum']);
					}
					if ($curr_month == $v['monthval']){
						$curr_arr[$v['dayval']] = abs($v['allnum']);
						$currlist_arr[$v['dayval']]['val'] = abs($v['allnum']);
					}
				}
			}
			$stat_arr['series'][0]['name'] = '上月';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '本月';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
			//统计数据标题
			$statlist['headertitle'] = array('日期','上月','本月','同比');
			Tpl::output('actionurl','index.php?act=stat_trade&op=predeposit&search_type=month&search_time_year='.$current_year.'&search_time_month='.$current_month);
		}
		
		//计算同比
		foreach ((array)$currlist_arr as $k=>$v){
			$tmp = array();
			$tmp['timetext'] = $v['timetext'];
			$tmp['currentdata'] = $v['val'];
			$tmp['updata'] = $uplist_arr[$k]['val'];
			$tmp['tbrate'] = getTb($tmp['updata'], $tmp['currentdata']);
			$statlist['data'][]  = $tmp;
		}
		//导出Excel
        if ($_GET['exporttype'] == 'excel'){
        	//获取数据
        	$log_list = $model->getPredepositInfo($where, '*', '');
			//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'会员名称');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'创建时间');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'可用金额（元）');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'冻结金额（元）');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'管理员名称');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'类型');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'描述');
			//data
			foreach ($log_list as $k=>$v){
				$excel_data[$k+1][] = array('data'=>$v['lg_member_name']);
				$excel_data[$k+1][] = array('data'=>date('Y-m-d H:i:s',$v['lg_add_time']));
				$excel_data[$k+1][] = array('data'=>$v['lg_av_amount']);
				$excel_data[$k+1][] = array('data'=>$v['lg_freeze_amount']);
				$excel_data[$k+1][] = array('data'=>$v['lg_admin_name']);
				switch ($v['lg_type']){
					case 'recharge':
						$excel_data[$k+1][] = array('data'=>'充值');
						break;
					case 'order_pay':
						$excel_data[$k+1][] = array('data'=>'消费');
						break;
					case 'cash_pay':
						$excel_data[$k+1][] = array('data'=>'提现');
						break;
					case 'refund':
						$excel_data[$k+1][] = array('data'=>'退款');
						break;
				}
				$excel_data[$k+1][] = array('data'=>$v['lg_desc']);
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
		    $excel_obj->addWorksheet($excel_obj->charset('预存款统计',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset('预存款统计',CHARSET).date('Y-m-d-H',time()));
			exit();
		} else {
			$log_list = $model->getPredepositInfo($where, '*', 15);
			Tpl::output('log_list',$log_list);
			Tpl::output('show_page',$model->showpage());
			//总数统计部分
			$recharge_amount = $model->getPredepositInfo(array('lg_type'=>'recharge','lg_add_time'=>array('between', array($stime,$etime))), 'sum(lg_av_amount) as allnum');
			$order_amount = $model->getPredepositInfo(array('lg_type'=>'order_pay','lg_add_time'=>array('between', array($stime,$etime))), 'sum(lg_av_amount) as allnum');
			$cash_amount = $model->getPredepositInfo(array('lg_type'=>'cash_pay','lg_add_time'=>array('between', array($stime,$etime))), 'sum(lg_freeze_amount) as allnum');
			Tpl::output('stat_array',array('recharge_amount'=>$recharge_amount[0]['allnum'],'order_amount'=>abs($order_amount[0]['allnum']),'cash_amount'=>abs($cash_amount[0]['allnum'])));
			$user_amount = $model->getPredepositInfo(true, 'distinct lg_member_id');
			Tpl::output('user_amount',count($user_amount));
			$usable_amount = $model->getPredepositInfo(true, 'sum(lg_av_amount+lg_freeze_amount) as allnum');
			Tpl::output('usable_amount',$usable_amount[0]['allnum']);
			//得到统计图数据
    		$stat_arr['title'] = '预存款统计';
            $stat_arr['yAxis'] = '金额';
    		$stat_json = getStatData_LineLabels($stat_arr);
    		Tpl::output('stat_json',$stat_json);
    		Tpl::output('statlist',$statlist);
    		Tpl::output('top_link',$this->sublink($this->links, 'predeposit'));
			Tpl::showpage('stat.predeposit');
		}
    }
    /**
     * 商品销售明细
     */
    public function goods_saleOp(){
    	if(!$_REQUEST['search_type']){
			$_REQUEST['search_type'] = 'day';
		}
		//初始化时间
		//天
		if(!$_REQUEST['search_time']){
			$_REQUEST['search_time'] = date('Y-m-d', time());
		}
		$search_time = strtotime($_REQUEST['search_time']);//搜索的时间
		Tpl::output('search_time',$_REQUEST['search_time']);
		//周
		if(!$_REQUEST['search_time_year']){
			$_REQUEST['search_time_year'] = date('Y', time());
		}
		if(!$_REQUEST['search_time_month']){
			$_REQUEST['search_time_month'] = date('m', time());
		}
		if(!$_REQUEST['search_time_week']){
			$_REQUEST['search_time_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$current_year = $_REQUEST['search_time_year'];
		$current_month = $_REQUEST['search_time_month'];
		$current_week = $_REQUEST['search_time_week'];
		$year_arr = getSystemYearArr();
		$month_arr = getSystemMonthArr();
		$week_arr = getMonthWeekArr($current_year, $current_month);
		
		Tpl::output('current_year', $current_year);
		Tpl::output('current_month', $current_month);
		Tpl::output('current_week', $current_week);
		Tpl::output('year_arr', $year_arr);
		Tpl::output('month_arr', $month_arr);
		Tpl::output('week_arr', $week_arr);
		if($_REQUEST['search_type'] == 'day'){
			$stime = $search_time;//昨天0点
			$etime = $search_time + 86400 - 1;//今天24点
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods_sale&search_type=day&search_time='.date('Y-m-d',$search_time));
		}
		if($_REQUEST['search_type'] == 'week'){
			$current_weekarr = explode('|', $current_week);
			$stime = strtotime($current_weekarr[0])-86400*7;
			$etime = strtotime($current_weekarr[1])+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods_sale&search_type=week&search_time_year='.$current_year.'&search_time_month='.$current_month.'&search_time_week='.$current_week);
		}
		if($_REQUEST['search_type'] == 'month'){
			$stime = strtotime($current_year.'-'.$current_month."-01 0 month");
			$etime = getMonthLastDay($current_year,$current_month)+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=goods_sale&search_type=month&search_time_year='.$current_year.'&search_time_month='.$current_month);
		}
		//获取相关数据
		$where = array();
		$where['order.add_time'] = array('between', array($stime,$etime));
		$where['order.order_state'] = array('neq',ORDER_STATE_NEW);//去除未支付订单
		$where['order.refund_state'] = array('exp',"!(order_state = '".ORDER_STATE_CANCEL."' and refund_state = 0)");//没有参与退款的取消订单，不记录到统计中
		$where['order.payment_code'] = array('exp',"!(order.payment_code='offline' and order.order_state <> '".ORDER_STATE_SUCCESS."')");//货到付款订单，订单成功之后才计入统计
		if(trim($_GET['store_name'])){
			$where['goods.store_name'] = array('like','%'.trim($_GET['store_name']).'%');
		}
    	if(trim($_GET['goods_name'])){
			$where['goods.goods_name'] = array('like','%'.trim($_GET['goods_name']).'%');
		}
    	if(trim($_GET['goods_serial'])){
			$where['goods.goods_serial'] = array('like','%'.trim($_GET['goods_serial']).'%');
		}
		if(intval($_GET['brand_id']) > 0){
			$where['goods.brand_id'] = intval($_GET['search_brand_id']);
		}
    	if(intval($_GET['cate_id']) > 0){
			$where['goods.gc_id'] = intval($_GET['cate_id']);
		}
		$model = Model('stat');
		//导出Excel
        if ($_GET['exporttype'] == 'excel'){
        	//获取数据
        	$goods_list = $model->getGoodsTradeDetailList($where,'');
			//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺名称');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'最近上架时间');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单商品件数');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单单量');
			$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单金额');
			//data
			foreach ($goods_list as $k=>$v){
				$excel_data[$k+1][] = array('data'=>$v['goods_name']);
				$excel_data[$k+1][] = array('data'=>$v['store_name']);
				$excel_data[$k+1][] = array('data'=>$v['goods_selltime']==0?date('Y-m-d H:i:s',$v['goods_addtime']):date('Y-m-d H:i:s',$v['goods_selltime']));
				$excel_data[$k+1][] = array('data'=>$v['gnum']);
				$excel_data[$k+1][] = array('data'=>$v['onum']);
				$excel_data[$k+1][] = array('data'=>$v['pnum']);
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
		    $excel_obj->addWorksheet($excel_obj->charset('商品销售明细',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset('商品销售明细',CHARSET).date('Y-m-d-H',time()));
			exit();
		} else {
			$goods_list = $model->getGoodsTradeDetailList($where);
			Tpl::output('goods_list',$goods_list);
			Tpl::output('show_page',$model->showpage());
			Tpl::output('brand_list',Model('brand')->getBrandList(array('brand_apply'=>1)));
			Tpl::output('goods_class',Model('goods_class')->getTreeClassList(1));
			Tpl::output('top_link',$this->sublink($this->links, 'goods_sale'));
			Tpl::showpage('stat.goodssale');
		}
    }
    /**
     * 类目销售统计
     */
    public function class_saleOp(){
    	if(!$_REQUEST['search_type']){
			$_REQUEST['search_type'] = 'day';
		}
		//初始化时间
		//天
		if(!$_REQUEST['search_time']){
			$_REQUEST['search_time'] = date('Y-m-d', time());
		}
		$search_time = strtotime($_REQUEST['search_time']);//搜索的时间
		Tpl::output('search_time',$_REQUEST['search_time']);
		//周
		if(!$_REQUEST['search_time_year']){
			$_REQUEST['search_time_year'] = date('Y', time());
		}
		if(!$_REQUEST['search_time_month']){
			$_REQUEST['search_time_month'] = date('m', time());
		}
		if(!$_REQUEST['search_time_week']){
			$_REQUEST['search_time_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$current_year = $_REQUEST['search_time_year'];
		$current_month = $_REQUEST['search_time_month'];
		$current_week = $_REQUEST['search_time_week'];
		$year_arr = getSystemYearArr();
		$month_arr = getSystemMonthArr();
		$week_arr = getMonthWeekArr($current_year, $current_month);
		
		Tpl::output('current_year', $current_year);
		Tpl::output('current_month', $current_month);
		Tpl::output('current_week', $current_week);
		Tpl::output('year_arr', $year_arr);
		Tpl::output('month_arr', $month_arr);
		Tpl::output('week_arr', $week_arr);
		if($_REQUEST['search_type'] == 'day'){
			$stime = $search_time;//昨天0点
			$etime = $search_time + 86400 - 1;//今天24点
			Tpl::output('actionurl','index.php?act=stat_trade&op=class_sale&search_type=day&search_time='.date('Y-m-d',$search_time));
		}
		if($_REQUEST['search_type'] == 'week'){
			$current_weekarr = explode('|', $current_week);
			$stime = strtotime($current_weekarr[0])-86400*7;
			$etime = strtotime($current_weekarr[1])+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=class_sale&search_type=week&search_time_year='.$current_year.'&search_time_month='.$current_month.'&search_time_week='.$current_week);
		}
		if($_REQUEST['search_type'] == 'month'){
			$stime = strtotime($current_year.'-'.$current_month."-01 0 month");
			$etime = getMonthLastDay($current_year,$current_month)+86400-1;
			Tpl::output('actionurl','index.php?act=stat_trade&op=class_sale&search_type=month&search_time_year='.$current_year.'&search_time_month='.$current_month);
		}
		//获取销售排名数据Top10
		$condition = array();
		$condition['order.add_time'] = array('between',array($stime,$etime));
		$condition['order.order_state'] = array('neq',ORDER_STATE_NEW);//去除未支付订单
		$condition['order.refund_state'] = array('exp',"!(order_state = '".ORDER_STATE_CANCEL."' and refund_state = 0)");//没有参与退款的取消订单，不记录到统计中
		$condition['order.payment_code'] = array('exp',"!(order.payment_code='offline' and order.order_state <> '".ORDER_STATE_SUCCESS."')");//货到付款订单，订单成功之后才计入统计
		
		if(trim($_GET['class_type']) == '' || trim($_GET['class_type']) == 'goods_class'){
			$type = 'goods';
			if(intval($_GET['cate_id']) > 0){
				$condition['goods.gc_id'] = intval($_GET['cate_id']);
			}
			Tpl::output('chart_tip','商品类目销售排名Top10');
		}else{
			$type = 'store';
			if(intval($_GET['store_class']) > 0){
				$condition['store.sc_id'] = intval($_GET['store_class']);
			}
			Tpl::output('chart_tip','店铺类目销售排名Top10');
		}
		$model = Model('stat');
		//导出Excel
        if ($_GET['exporttype'] == 'excel'){
        	//获取数据
        	$data_list = $model->getStoreTradeList($condition,$type);
        	//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
		    if(trim($_GET['class_type']) == '' || trim($_GET['class_type']) == 'goods_class'){
		    	$excel_data[0][] = array('styleid'=>'s_title','data'=>'商品名称');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'所属分类');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺名称');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单单量');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单商品件数');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单金额');
		    }else{
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺名称');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'所属分类');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'店主账号');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单单量');
				$excel_data[0][] = array('styleid'=>'s_title','data'=>'下单金额');
		    }
			//data
			foreach ($data_list as $k=>$v){
				if(trim($_GET['class_type']) == '' || trim($_GET['class_type']) == 'goods_class'){
					$excel_data[$k+1][] = array('data'=>$v['goods_name']);
					$excel_data[$k+1][] = array('data'=>$v['gc_name']);
					$excel_data[$k+1][] = array('data'=>$v['store_name']);
					$excel_data[$k+1][] = array('data'=>$v['onum']);
					$excel_data[$k+1][] = array('data'=>$v['gnum']);
					$excel_data[$k+1][] = array('data'=>$v['pnum']);
				}else{
					$excel_data[$k+1][] = array('data'=>$v['store_name']);
					$excel_data[$k+1][] = array('data'=>$v['sc_name']);
					$excel_data[$k+1][] = array('data'=>$v['member_name']);
					$excel_data[$k+1][] = array('data'=>$v['onum']);
					$excel_data[$k+1][] = array('data'=>$v['pnum']);
				}
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
		    $excel_obj->addWorksheet($excel_obj->charset(trim($_GET['class_type']) == 'store_class'?'店铺类目销售排名':'商品类目销售排名',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset(trim($_GET['class_type']) == 'store_class'?'店铺类目销售排名':'商品类目销售排名',CHARSET).date('Y-m-d-H',time()));
			exit();
        }else{
        	$data_list = $model->getStoreTradeList($condition,$type,10);
			Tpl::output('data_list',$data_list);
			$stat_arr['title'] = trim($_GET['class_type']) == 'store_class'?'店铺类目销售排名':'商品类目销售排名';
            $stat_arr['yAxis'] = '下单金额';
			$stat_arr['series'][0]['name'] = '下单金额';
			$stat_arr['series'][0]['data'] = array();
			for ($i = 0; $i < 15; $i++){
			    $stat_arr['series'][0]['data'][] = array('name'=>strval($data_list[$i][trim($_GET['class_type']) == 'store_class'?'store_name':'goods_name']),'y'=>floatval($data_list[$i]['pnum']));
			}
        	//构造横轴数据
			for($i=1; $i<=15; $i++){
				//横轴
				$stat_arr['xAxis']['categories'][] = $i;
			}
			$stat_arr['legend']['enabled'] = false;
    		$stat_json = getStatData_Column2D($stat_arr);
    		Tpl::output('stat_json',$stat_json);
			//店铺分类
			$model_store_class = Model('store_class');
			$parent_list = $model_store_class->getTreeClassList(2);
			if (is_array($parent_list)){
				foreach ($parent_list as $k => $v){
					$parent_list[$k]['sc_name'] = str_repeat("&nbsp;",$v['deep']*2).$v['sc_name'];
				}
			}
			Tpl::output('class_list',$parent_list);
			Tpl::output('goods_class',Model('goods_class')->getTreeClassList(1));
	    	Tpl::output('top_link',$this->sublink($this->links, 'class_sale'));
			Tpl::showpage('stat.classsale');
        }
    }
	/**
	 * 订单统计
	 */
    public function saleOp(){
    	$where = array();
    	if(trim($_GET['order_type']) != ''){
    		$where['order_state'] = trim($_GET['order_type']);
    	}
    	if(trim($_GET['stat_type']) == 'sale'){
    		$field = ' sum(order_amount) as allnum ';
    	}else{
    		$field = ' count(*) as allnum ';
    	}
		if(!$_REQUEST['search_type']){
			$_REQUEST['search_type'] = 'day';
		}
		if(trim($_GET['store_name']) != ''){
			$where['store_name'] = trim($_GET['store_name']);
			$store_name = trim($_GET['store_name']);
		}
		//初始化时间
		//天
		if(!$_REQUEST['search_time']){
			$_REQUEST['search_time'] = date('Y-m-d', time());
		}
		$search_time = strtotime($_REQUEST['search_time']);//搜索的时间
		Tpl::output('search_time',$_REQUEST['search_time']);
		//周
		if(!$_REQUEST['search_time_year']){
			$_REQUEST['search_time_year'] = date('Y', time());
		}
		if(!$_REQUEST['search_time_month']){
			$_REQUEST['search_time_month'] = date('m', time());
		}
		if(!$_REQUEST['search_time_week']){
			$_REQUEST['search_time_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$current_year = $_REQUEST['search_time_year'];
		$current_month = $_REQUEST['search_time_month'];
		$current_week = $_REQUEST['search_time_week'];
		$year_arr = getSystemYearArr();
		$month_arr = getSystemMonthArr();
		$week_arr = getMonthWeekArr($current_year, $current_month);
		
		Tpl::output('current_year', $current_year);
		Tpl::output('current_month', $current_month);
		Tpl::output('current_week', $current_week);
		Tpl::output('year_arr', $year_arr);
		Tpl::output('month_arr', $month_arr);
		Tpl::output('week_arr', $week_arr);
		
    	$model = Model('stat');
		$statlist = array();//统计数据列表
		$sum_num = 0;//总数统计
		if($_REQUEST['search_type'] == 'day'){
			//构造横轴数据
			for($i=0; $i<24; $i++){
				//统计图数据
				$curr_arr[$i] = 0;//今天
				$up_arr[$i] = 0;//昨天
				//统计表数据
				$uplist_arr[$i]['timetext'] = $i;
				$currlist_arr[$i]['timetext'] = $i;
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = "$i";
			}
			$stime = $search_time - 86400;//昨天0点
			$etime = $search_time + 86400 - 1;//今天24点
			
			$today_day = @date('d', $search_time);//今天日期
			$yesterday_day = @date('d', $stime);//昨天日期
			
			$where['add_time'] = array('between',array($stime,$etime));
			$field .= ' ,DAY(FROM_UNIXTIME(add_time)) as dayval,HOUR(FROM_UNIXTIME(add_time)) as hourval ';
			$memberlist = $model->getStoreSaleStatList($where, $field, 0, '', 0, 'dayval,hourval');
			if($memberlist){
				foreach($memberlist as $k => $v){
					if($today_day == $v['dayval']){
						$curr_arr[$v['hourval']] = intval($v['allnum']);
						$currlist_arr[$v['hourval']]['val'] = intval($v['allnum']);
					}
					if($yesterday_day == $v['dayval']){
						$up_arr[$v['hourval']] = intval($v['allnum']);
						$uplist_arr[$v['hourval']]['val'] = intval($v['allnum']);
					}
				}
			}elseif(trim($_GET['store_name']) != ''){
				Tpl::output('data_null','yes');
			}
			$stat_arr['series'][0]['name'] = '昨天';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '今天';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
	
			Tpl::output('actionurl','index.php?act=stat_trade&op=sale&search_type=day&search_time='.date('Y-m-d',$search_time));
		}
		
		if($_REQUEST['search_type'] == 'week'){
			$current_weekarr = explode('|', $current_week);
			$stime = strtotime($current_weekarr[0])-86400*7;
			$etime = strtotime($current_weekarr[1])+86400-1;
			$up_week = @date('W', $stime);//上周
			$curr_week = @date('W', $etime);//本周
			//构造横轴数据
			for($i=1; $i<=7; $i++){
				//统计图数据
				$up_arr[$i] = 0;
				$curr_arr[$i] = 0;
				$tmp_weekarr = getSystemWeekArr();
				//统计表数据
				$uplist_arr[$i]['timetext'] = $tmp_weekarr[$i];
				$currlist_arr[$i]['timetext'] = $tmp_weekarr[$i];
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = $tmp_weekarr[$i];
				unset($tmp_weekarr);
			}
			$where['add_time'] = array('between', array($stime,$etime));
			$field .= ',WEEKOFYEAR(FROM_UNIXTIME(add_time)) as weekval,DAYOFWEEK(FROM_UNIXTIME(add_time)) as dayofweekval ';
			$memberlist = $model->getStoreSaleStatList($where, $field, 0, '', 0, 'weekval,dayofweekval');
			if($memberlist){
				foreach($memberlist as $k=>$v){
					if ($up_week == $v['weekval']){
						$up_arr[$v['dayofweekval']] = intval($v['allnum']);
						$uplist_arr[$v['dayofweekval']]['val'] = intval($v['allnum']);
					}
					if ($curr_week == $v['weekval']){
						$curr_arr[$v['dayofweekval']] = intval($v['allnum']);
						$currlist_arr[$v['dayofweekval']]['val'] = intval($v['allnum']);
					}
				}
			}elseif(trim($_GET['store_name']) != ''){
				Tpl::output('data_null','yes');
			}
			$stat_arr['series'][0]['name'] = '上周';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '本周';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
			
			Tpl::output('actionurl','index.php?act=stat_trade&op=sale&search_type=week&search_time_year='.$current_year.'&search_time_month='.$current_month.'&search_time_week='.$current_week);
		}
		
		if($_REQUEST['search_type'] == 'month'){
			$stime = strtotime($current_year.'-'.$current_month."-01 -1 month");
			$etime = getMonthLastDay($current_year,$current_month)+86400-1;
			
			$up_month = date('m',$stime);
			$curr_month = date('m',$etime);
			//计算横轴的最大量（由于每个月的天数不同）
			$up_dayofmonth = date('t',$stime);
			$curr_dayofmonth = date('t',$etime);
			$x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;
			
		    //构造横轴数据
			for($i=1; $i<=$x_max; $i++){
				//统计图数据
				$up_arr[$i] = 0;
				$curr_arr[$i] = 0;
				//统计表数据
				$uplist_arr[$i]['timetext'] = $i;
				$currlist_arr[$i]['timetext'] = $i;
				$uplist_arr[$i]['val'] = 0;
				$currlist_arr[$i]['val'] = 0;
				//横轴
				$stat_arr['xAxis']['categories'][] = $i;
			}
			$where['add_time'] = array('between', array($stime,$etime));
			$field .= ',MONTH(FROM_UNIXTIME(add_time)) as monthval,day(FROM_UNIXTIME(add_time)) as dayval ';
			$memberlist = $model->getStoreSaleStatList($where, $field, 0, '', 0, 'monthval,dayval');
		    if($memberlist){
				foreach($memberlist as $k=>$v){
					if ($up_month == $v['monthval']){
						$up_arr[$v['dayval']] = intval($v['allnum']);
						$uplist_arr[$v['dayval']]['val'] = intval($v['allnum']);
					}
					if ($curr_month == $v['monthval']){
						$curr_arr[$v['dayval']] = intval($v['allnum']);
						$currlist_arr[$v['dayval']]['val'] = intval($v['allnum']);
					}
				}
			}elseif(trim($_GET['store_name']) != ''){
				Tpl::output('data_null','yes');
			}
			$stat_arr['series'][0]['name'] = '上月';
			$stat_arr['series'][0]['data'] = array_values($up_arr);
			$stat_arr['series'][1]['name'] = '本月';
			$stat_arr['series'][1]['data'] = array_values($curr_arr);
			
			Tpl::output('actionurl','index.php?act=stat_trade&op=sale&search_type=month&search_time_year='.$current_year.'&search_time_month='.$current_month);
		}
		//统计数据标题
		$statlist['headertitle'] = array('订单号','买家','店铺名称','下单时间','订单总额','订单状态');
		//导出Excel
        if ($_GET['exporttype'] == 'excel'){
			//导出Excel
			import('libraries.excel');
		    $excel_obj = new Excel();
		    $excel_data = array();
		    //设置样式
		    $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
			//header
			foreach ($statlist['headertitle'] as $v){
			    $excel_data[0][] = array('styleid'=>'s_title','data'=>$v);
			}
			$order_all_list = $model->getStoreOrderList($where,false);
			//data
			foreach ($order_all_list as $k=>$v){
				$excel_data[$k+1][] = array('data'=>$v['order_sn']);
				$excel_data[$k+1][] = array('data'=>$v['buyer_name']);
				$excel_data[$k+1][] = array('data'=>$v['store_name']);
				$excel_data[$k+1][] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
				$excel_data[$k+1][] = array('data'=>number_format(($v['order_amount']),2));
				switch ($v['order_state']){
		        	case ORDER_STATE_CANCEL:
		        		$excel_data[$k+1][] = array('data'=>'已取消');
		        		break;
		        	case ORDER_STATE_NEW:
		        		$excel_data[$k+1][] = array('data'=>'待付款');
		        		break;
		        	case ORDER_STATE_PAY:
		        		$excel_data[$k+1][] = array('data'=>'待发货');
		        		break;
		        	case ORDER_STATE_SEND:
		        		$excel_data[$k+1][] = array('data'=>'待收货');
		        		break;
		        	case ORDER_STATE_SUCCESS:
		        		$excel_data[$k+1][] = array('data'=>'交易完成');
		        		break;
		        }
			}
			$excel_data = $excel_obj->charset($excel_data,CHARSET);
			$excel_obj->addArray($excel_data);
			$excel_obj->addWorksheet($excel_obj->charset('订单统计',CHARSET));
		    $excel_obj->generateXML($excel_obj->charset('订单统计',CHARSET).date('Y-m-d-H',time()));
			exit();
		} else {
			$order_list = $model->getStoreOrderList($where);
			//得到统计图数据
			if(trim($_GET['stat_type']) == 'sale'){
				$stat_arr['title'] = '订单销售额统计';
            	$stat_arr['yAxis'] = '订单销售额';
			}else{
				$stat_arr['title'] = '订单量统计';
            	$stat_arr['yAxis'] = '订单量';
			}
    		$stat_json = getStatData_LineLabels($stat_arr);
    		//总数统计
    		$amount = $model->getStoreSaleStatList($where,' count(*) as allnum ');
    		$sale = $model->getStoreSaleStatList($where,' sum(order_amount) as allnum ');
    		Tpl::output('sum_data',array($amount[0]['allnum'],$sale[0]['allnum']));
    		Tpl::output('stat_json',$stat_json);
    		Tpl::output('statlist',$statlist);
    		Tpl::output('order_list',$order_list);
    		Tpl::output('show_page',$model->showpage());
    		Tpl::output('top_link',$this->sublink($this->links, 'sale'));
			Tpl::showpage('stat.sale');
		}
    }
}