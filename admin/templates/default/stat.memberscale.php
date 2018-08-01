<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>会员统计</h3>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div class="fixed-empty"></div>
  
  <form method="get" action="index.php" name="formSearch" id="formSearch">
    <input type="hidden" name="act" value="stat_member" />
    <input type="hidden" name="op" value="scale" />
    <input type="hidden" id="exporttype" name="exporttype" value=""/>
    <input type="hidden" id="orderby" name="orderby" value="<?php echo $output['search_arr']['orderby']?$output['search_arr']['orderby']:'orderamount desc';?>"/>
    <div class="w100pre" style="width: 100%;">
        <table class="tb-type1 noborder search left">
          <tbody>
            <tr>
              <td>
              	<select name="search_type" id="search_type" class="querySelect">
                  <option value="day" <?php echo $output['search_arr']['search_type']=='day'?'selected':''; ?>>按照天统计</option>
                  <option value="week" <?php echo $output['search_arr']['search_type']=='week'?'selected':''; ?>>按照周统计</option>
                  <option value="month" <?php echo $output['search_arr']['search_type']=='month'?'selected':''; ?>>按照月统计</option>
                </select></td>
              <td id="searchtype_day" style="display:none;">
              	<input class="txt date" type="text" value="<?php echo @date('Y-m-d',$output['search_arr']['day']['search_time']);?>" id="search_time" name="search_time">
              </td>
              <td id="searchtype_week" style="display:none;">
              	<select name="searchweek_year" class="querySelect">
              		<?php foreach ($output['year_arr'] as $k=>$v){?>
              		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
              		<?php } ?>
                </select>
                <select name="searchweek_month" class="querySelect">
                	<?php foreach ($output['month_arr'] as $k=>$v){?>
              		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
              		<?php } ?>
                </select>
                <select name="searchweek_week" class="querySelect">
                	<?php foreach ($output['week_arr'] as $k=>$v){?>
              		<option value="<?php echo $v['key'];?>" <?php echo $output['search_arr']['week']['current_week'] == $v['key']?'selected':'';?>><?php echo $v['val']; ?></option>
              		<?php } ?>
                </select>
              </td>
              <td id="searchtype_month" style="display:none;">
              	<select name="searchmonth_year" class="querySelect">
              		<?php foreach ($output['year_arr'] as $k=>$v){?>
              		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
              		<?php } ?>
                </select>
                <select name="searchmonth_month" class="querySelect">
                	<?php foreach ($output['month_arr'] as $k=>$v){?>
              		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
              		<?php } ?>
                </select>
              </td>
              <td>
              	会员名称：<input type="text" name="membername" value="<?php echo $output['search_arr']['membername'];?>"/>
              </td>
              <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search tooltip" title="<?php echo $lang['nc_query'];?>">&nbsp;</a></td>
            </tr>
          </tbody>
        </table>
        <span class="right" style="margin:12px 0px 6px 4px;">
        	
        </span>
    </div>
  </form>
  
  <div class="w100pre close_float" style="text-align:right;">
  	<!-- <input type="hidden" id="export_type" name="export_type" data-param='{"url":"<?php echo $output['actionurl'];?>&exporttype=excel"}' value="excel"/> -->
  	<a class="btns" href="javascript:void(0);" id="export_btn"><span>导出Excel</span></a>
  </div>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead sortbar-array">
        <th class="align-center">会员名称</th>
        <th class="align-center"><a nc_type="orderitem" data-param='{"orderby":"orderamount"}' class="<?php echo (!$output['search_arr']['orderby'] || $output['search_arr']['orderby']=='orderamount desc')?'selected desc':''; echo $output['search_arr']['orderby']=='orderamount asc'?'selected asc':''; ?>">下单金额<i></i></a></th>
        <th class="align-center"><a nc_type="orderitem" data-param='{"orderby":"predincrease"}' class="<?php echo ($output['search_arr']['orderby']=='predincrease desc')?'selected desc':''; echo $output['search_arr']['orderby']=='predincrease asc'?'selected asc':''; ?>">增预存款<i></i></a></th>
        <th class="align-center"><a nc_type="orderitem" data-param='{"orderby":"predreduce"}' class="<?php echo ($output['search_arr']['orderby']=='predreduce desc')?'selected desc':''; echo $output['search_arr']['orderby']=='predreduce asc'?'selected asc':''; ?>">减预存款<i></i></a></th>
        <th class="align-center"><a nc_type="orderitem" data-param='{"orderby":"pointsincrease"}' class="<?php echo ($output['search_arr']['orderby']=='pointsincrease desc')?'selected desc':''; echo $output['search_arr']['orderby']=='pointsincrease asc'?'selected asc':''; ?>">增积分<i></i></a></th>
        <th class="align-center"><a nc_type="orderitem" data-param='{"orderby":"pointsreduce"}' class="<?php echo ($output['search_arr']['orderby']=='pointsreduce desc')?'selected desc':''; echo $output['search_arr']['orderby']=='pointsreduce asc'?'selected asc':''; ?>">减积分<i></i></a></th>
      </tr>
    </thead>
    <tbody id="datatable">
    <?php if(!empty($output['statlist'])){ ?>
        <?php foreach ($output['statlist'] as $k=>$v){?>
          <tr class="hover">
            <td class="align-center"><?php echo $v['statm_membername'];?></td>
            <td class="align-center"><?php echo $v['orderamount'];?></td>
            <td class="align-center"><?php echo $v['predincrease'];?></td>
            <td class="align-center"><?php echo $v['predreduce'];?></td>
            <td class="align-center"><?php echo $v['pointsincrease'];?></td>
            <td class="align-center"><?php echo $v['pointsreduce'];?></td>
          </tr>
        <?php } ?>
    <?php } else { ?>
        <tr class="no_data">
        	<td colspan="11"><?php echo $lang['no_record']; ?></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15" id="dataFuncs"><div class="pagination"> <?php echo $output['show_page'];?> </div></td>
      </tr>
    </tfoot>
  </table>
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/statistics.js"></script>
</div>
<script>
$(function () {
	//统计数据类型
	var s_type = $("#search_type").val();
	$('#search_time').datepicker({dateFormat: 'yy-mm-dd'});

	show_searchtime();
	$("#search_type").change(function(){
		show_searchtime();
	});
	//展示搜索时间框
	function show_searchtime(){
		s_type = $("#search_type").val();
		$("[id^='searchtype_']").hide();
		$("#searchtype_"+s_type).show();
	}
	
	//更新周数组
	$("[name='searchweek_month']").change(function(){
		var year = $("[name='searchweek_year']").val();
		var month = $("[name='searchweek_month']").val();
		$("[name='searchweek_week']").html('');
		$.getJSON('index.php?act=common&op=getweekofmonth',{y:year,m:month},function(data){
	        if(data != null){
	        	for(var i = 0; i < data.length; i++) {
	        		$("[name='searchweek_week']").append('<option value="'+data[i].key+'">'+data[i].val+'</option>');
			    }
	        }
	    });
	});
	
	$('#ncsubmit').click(function(){
		$("#exporttype").val('');
		$("#orderby").val('');
    	$('#formSearch').submit();
    });

	//导出图表
    $("#export_btn").click(function(){
    	$("#exporttype").val('excel');
    	$('#formSearch').submit();
    });

    $("[nc_type='orderitem']").click(function(){
    	$("#exporttype").val('');
    	var data_str = $(this).attr('data-param');
	    eval( "data_str = "+data_str);
        if($(this).hasClass('desc')){
        	$("#orderby").val(data_str.orderby + ' asc');
        } else {
        	$("#orderby").val(data_str.orderby + ' desc');
        }
        $('#formSearch').submit();
    });
});
</script>