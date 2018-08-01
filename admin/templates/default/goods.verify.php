<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo ADMIN_TEMPLATES_URL;?>/css/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo ADMIN_TEMPLATES_URL;?>/css/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['goods_index_goods'];?></h3>
      <ul class="tab-base">
        <li><a href="<?php echo urlAdmin('goods', 'goods');?>" ><span><?php echo $lang['goods_index_all_goods'];?></span></a></li>
        <li><a href="<?php echo urlAdmin('goods', 'goods', array('type' => 'lockup'));?>"><span><?php echo $lang['goods_index_lock_goods'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span>等待审核</span></a></li>
        <li><a href="<?php echo urlAdmin('goods', 'goods_set');?>"><span><?php echo $lang['nc_goods_set'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
    <input type="hidden" name="act" value="goods" />
    <input type="hidden" name="op" value="goods" />
    <input type="hidden" name="type" value="waitverify" />
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th> <label for="search_goods_name"><?php echo $lang['goods_index_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['search']['search_goods_name'];?>" name="search_goods_name" id="search_goods_name" class="txt"></td>
          <th>审核状态</th>
          <td><select name="search_verify">
            <option><?php echo $lang['nc_please_choose'];?>...</option>
            <option value="10" <?php if ($output['search']['search_verify'] != '' && $output['search']['search_verify'] == 10) {?>selected="selected"<?php }?>>等待审核</option>
            <option value="0" <?php if ($output['search']['search_verify'] != '' && $output['search']['search_verify'] == 0) {?>selected="selected"<?php }?>>未通过</option>
          </select></td>
          <th><label><?php echo $lang['goods_index_class_name'];?></label></th>
          <td id="gcategory"><input type="hidden" id="cate_id" name="cate_id" value="" class="mls_id" />
            <input type="hidden" id="cate_name" name="cate_name" value="" class="mls_names" />
            <select>
              <option><?php echo $lang['nc_please_choose'];?>...</option>
              <?php foreach($output['goods_class'] as $val) { ?>
              <option value="<?php echo $val['gc_id']; ?>" <?php if($output['search']['cate_id'] == $val['gc_id']){?>selected<?php }?>><?php echo $val['gc_name']; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <th><label for="search_store_name"><?php echo $lang['goods_index_store_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['search']['search_store_name'];?>" name="search_store_name" id="search_store_name" class="txt"></td>
          <th><label><?php echo $lang['goods_index_brand'];?></label></th>
          <td><select name="search_brand_id">
              <option value=""><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(is_array($output['brand_list'])){ ?>
              <?php foreach($output['brand_list'] as $k => $v){ ?>
              <option value="<?php echo $v['brand_id'];?>" <?php if($output['search']['search_brand_id'] == $v['brand_id']){?>selected<?php }?>><?php echo $v['brand_name'];?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
            
            </td>
        </tr>
      </tbody>
    </table>
  </form>
  <form method='post' id="form_goods" action="<?php echo urlAdmin('goods', 'goods_del');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15"><?php echo $lang['nc_list'];?></th>
        </tr>
        <tr class="thead">
          <th></th>
          <th class="w24"></th>
          <th>平台货号</th>
          <th colspan="2"><?php echo $lang['goods_index_name'];?></th>
          <th><?php echo $lang['goods_index_brand'];?>&<?php echo $lang['goods_index_class_name'];?></th>
          <th class="align-center">价格</th>
          <th class="align-center">库存</th>
          <th class="align-center">审核状态</th>
          <th class="align-center"><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
        <?php foreach($output['goods_list'] as $k => $v){  ?>
        <tr class="hover edit">
          <td class="w24"><input type="checkbox" name="id[]" value="<?php echo $v['goods_commonid'];?>" class="checkitem"></td>
          <td><i class="icon-plus-sign" nctype="ajaxGoodsList" data-comminid="<?php echo $v['goods_commonid'];?>" style="cursor: pointer;"></i></td>
          <td><?php echo $v['goods_commonid'];?></td>
          <td class="w60"><div class="goods-picture"><span class="thumb size-goods"><i></i><img src="<?php echo thumb($v, 60);?>" onload="javascript:DrawImage(this,56,56);"/></span></div></td>
          <td class="goods-name w270"><p><span><?php echo $v['goods_name'];?></span></p>
            <p class="store"><?php echo $lang['goods_index_store_name'];?>:<?php echo $v['store_name'];?></p></td>
          <td class="w200">
            <p><?php echo $v['brand_name'];?></p>
            <p><?php echo $v['gc_name'];?></p>
          </td>
          <td class="align-center"><?php echo $lang['currency'].$v['goods_price']?></td>
          <td class="align-center"><?php echo $output['storage_array'][$v['goods_commonid']]['sum']?></td>
          <td class="align-center">
            <p><?php echo $output['verify'][$v['goods_verify']];?></p>
            <?php if ($v['goods_verify'] == 0) {?><p><?php echo $v['goods_verifyremark'];?></p><?php }?>
          </td>
          <td class="w48 align-center">
            <p><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['storage_array'][$v['goods_commonid']]['goods_id']));?>" target="_blank"><?php echo $lang['nc_view'];?></a></p>
            <p><a href="javascript:void(0);" onclick="goods_verify(<?php echo $v['goods_commonid'];?>);">审核</a></p>
          </td>
        </tr>
        <tr style="display:none;"><td colspan="20"><div class="ncsc-goods-sku ps-container"></div></td></tr>
        <?php } ?>
        <?php } else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp;<a href="javascript:void(0);" class="btn" nctype="verify_batch"><span>审核</span></a>
            <a href="JavaScript:void(0);" class="btn" nctype="del_batch"><span><?php echo $lang['nc_del'];?></span></a>
            <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(function(){
    gcategoryInit("gcategory");
    $('#ncsubmit').click(function(){
        $('input[name="op"]').val('goods');$('#formSearch').submit();
    });
    
    // 批量删除
    $('a[nctype="del_batch"]').click(function(){
        ajaxpost('form_goods', '', '', 'onerror');
    });

    // 审核批量处理
    $('a[nctype="verify_batch"]').click(function(){
        str = getId();
        if (str) {
            goods_verify(str);
        }
    });
    
    // ajax获取商品列表
    $('i[nctype="ajaxGoodsList"]').toggle(
        function(){
            $(this).removeClass('icon-plus-sign').addClass('icon-minus-sign');
            var _parenttr = $(this).parents('tr');
            var _commonid = $(this).attr('data-comminid');
            var _div = _parenttr.next().find('.ncsc-goods-sku');
            if (_div.html() == '') {
                $.getJSON('index.php?act=goods&op=get_goods_list_ajax' , {commonid : _commonid}, function(date){
                    if (date != 'false') {
                        var _ul = $('<ul class="ncsc-goods-sku-list"></ul>');
                        $.each(date, function(i, o){
                            $('<li><div class="goods-thumb" title="商家货号：' + o.goods_serial + '"><a href="' + o.url + '" target="_blank"><image src="' + o.goods_image + '" ></a></div>' + o.goods_spec + '<div class="goods-price">价格：<em title="￥' + o.goods_price + '">￥' + o.goods_price + '</em></div><div class="goods-storage">库存：<em title="' + o.goods_storage + '">' + o.goods_storage + '</em></div><a href="' + o.url + '" target="_blank" class="ncsc-btn-mini">查看商品详情</a></li>').appendTo(_ul);
                        });
                        _ul.appendTo(_div);
                        _parenttr.next().show();
                        // 计算div的宽度
                        _div.css('width', document.body.clientWidth-54);
                        _div.perfectScrollbar();
                    }
                });
            } else {
            	_parenttr.next().show()
            }
        },
        function(){
            $(this).removeClass('icon-minus-sign').addClass('icon-plus-sign');
            $(this).parents('tr').next().hide();
        }
    );
});

// 获得选中哎
function getId() {
    var str = '';
    $('#form_goods').find('input[name="id[]"]:checked').each(function(){
        id = parseInt($(this).val());
        if (!isNaN(id)) {
            str += id + ',';
        }
    });
    if (str == '') {
        return false;
    }
    str = str.substr(0, (str.length - 1));
    return str;
}

// 商品下架
function goods_verify(ids) {
    _uri = "<?php echo ADMIN_SITE_URL;?>/index.php?act=goods&op=goods_verify&id=" + ids;
    CUR_DIALOG = ajax_form('goods_verify', '审核商品', _uri, 350);
}
</script>