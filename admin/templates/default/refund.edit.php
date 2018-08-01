<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['refund_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=refund&op=refund_manage"><span><?php echo '待处理';?></span></a></li>
        <li><a href="index.php?act=refund&op=refund_all"><span><?php echo '所有记录';?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo '审核';?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="post_form" method="post" action="index.php?act=refund&op=edit&refund_id=<?php echo $output['refund']['refund_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2"><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo ncPriceFormat($output['refund']['refund_amount']); ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2"><?php echo '商品名称'.$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['goods_name']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2"><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['buyer_message']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2"><?php echo $lang['refund_seller_message'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['seller_message']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation"><?php echo $lang['refund_message'].$lang['nc_colon'];?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
        	<textarea id="admin_message" name="admin_message" class="tarea"></textarea></td>
          <td class="vatop tips"></td>
        </tr>

      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){
	$("#submitBtn").click(function(){
    if($("#post_form").valid()){
     $("#post_form").submit();
		}
	});
    $('#post_form').validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            admin_message : {
                required   : true
            }
        },
        messages : {
            admin_message  : {
                required   : '<?php echo $lang['refund_message_null'];?>'
            }
        }
    });
});
</script>