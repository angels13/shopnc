<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store&op=store" ><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a href="index.php?act=store&op=store_audit" ><span><?php echo $lang['pending'];?></span></a></li>
        <li><a href="index.php?act=store&op=store_auth" ><span><?php echo $lang['store_auth_verify'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="step" value="one" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="member_name"><?php echo $lang['user_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="member_name" name="member_name" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['enter_shop_owner_info'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="member_passwd"><?php echo $lang['pwd'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="member_passwd" name="member_passwd" class="txt"></td>
          <td class="vatop tips"><input type="checkbox" checked="checked" id="checkbox" value="1" name="need_password"> <?php echo $lang['need_verify_pwd'];?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2"><a href="javascript:document.form1.submit();" class="btn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
