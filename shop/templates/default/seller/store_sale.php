<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/highcharts.js" charset="utf-8"></script>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column'
            },
            title: {
                text: '<?php echo $output['main_title']; ?>'
            },
            subtitle: {
                text: '<?php echo $output['sub_title']; ?>'
            },
            xAxis: {
                categories: [
                    <?php echo $output['result_date_str']; ?>
                ]
                <?php if($output['labellean'] == 'yes'){?>
                ,labels: {
    				rotation: -45,
    				align: 'right',
    				style: {
    					font: 'normal 12px Verdana, sans-serif'
    				}
    			},
    			<?php } ?>
    			<?php if($output['usextip'] == 'yes'){?>
    			,title: {
    				text: '<?php echo $lang['stat_unit_tip']; ?><?php echo $output['xtip']; ?>',
    				align: 'high'
    			},
    			<?php } ?>
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?php echo $lang['stat_store_sale_tip']; ?>'
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +'<?php echo $output['xtip']; ?>: '+ this.y +' <?php echo $lang['stat_unit']; ?>';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
                series: [{
                name: '<?php echo $lang['stat_sale']; ?>',
                data: [<?php echo $output['result_clicknum_str']; ?>]

            }]
        });
    });

});
</script>

  <div class="tabmenu">
    <?php include template('layout/submenu');?>
    </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name="act" value="statistics_sale" />
      <input type="hidden" name="op" value="sale_statistics" />
      <tr>
        <td><a href="javascript:void(0);" id="week_flow" class="ncsc-btn-mini"><?php echo $lang['stat_week_sale']; ?></a><a href="javascript:void(0);" id="month_flow" class="ncsc-btn-mini"><?php echo $lang['stat_month_sale']; ?></a><a href="javascript:void(0);"id="year_flow" class="ncsc-btn-mini"><?php echo $lang['stat_year_sale']; ?></a></td>
        <th><?php echo $lang['stat_time_search'];?></th>
        <td class="w240"><input type="text" class="text w70" name="add_time_from" id="add_time_from" value="<?php echo $_GET['add_time_from']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input type="text" class="text w70" id="add_time_to" name="add_time_to" value="<?php echo $_GET['add_time_to']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
        <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
      </tr>
    </table>
  </form>
  <!-- JS统计图表 -->
  <div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<script type="text/javascript">
	$(function(){
	    $('#add_time_from').datepicker({dateFormat: 'yymmdd'});
	    $('#add_time_to').datepicker({dateFormat: 'yymmdd'});
	    $('#week_flow').click(function(){
	    	window.location.href = 'index.php?act=statistics_sale&op=sale_statistics';
		})
		$('#month_flow').click(function(){
	    	window.location.href = 'index.php?act=statistics_sale&op=sale_statistics&type=month';
		})
		$('#year_flow').click(function(){
	    	window.location.href = 'index.php?act=statistics_sale&op=sale_statistics&type=year';
		})
	});
</script>
