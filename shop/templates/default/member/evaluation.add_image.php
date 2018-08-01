<?php defined('InShopNC') or exit('Access Invalid!');?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/jquery.raty.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.raty').raty({
            path: "<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/img",
            readOnly: true,
            score: function() {
                return $(this).attr('data-score');
            }
        });

        //图片上传
        $(".input-file").fileupload({
            dataType: 'json',
            url: "<?php echo urlShop('sns_album', 'swfupload');?>",
            formData: "",
            add: function(e, data) {
                data.formData = {category_id:<?php echo $output['ac_id'];?>}
                data.submit();
            },
            done: function (e, data) {
                if(data.result.state) {
                    $item = $(this).parents('li');
                    $item.find('img').attr('src', data.result.file_url);
                    $item.find('[nctype="input_image"]').val(data.result.file_name);
                    var file_id = $item.find('[nctype="del"]').attr('data-file-id');
                    album_pic_del(file_id);
                    $item.find('[nctype="del"]').attr('data-file-id', data.result.file_id);
                    $item.find('[nctype="image_item"]').show();
                } else {
                    showError(data.result.message);
                }
            }
        });

        $('[nctype="del"]').on('click', function() {
            album_pic_del($(this).attr('data-file-id'));
            $item = $(this).parent();
            $item.find('[nctype="input_image"]').val('');
            $item.hide();
        });

        function album_pic_del(file_id) {
            var del_url = "<?php echo urlShop('sns_album', 'album_pic_del');?>";
            del_url += '&id=' + file_id;
            $.get(del_url);
        }

        $('#btn_submit').on('click', function() {
			ajaxpost('add_form', '', '', 'onerror')
        });
    });
</script>

<form id="add_form" action="<?php echo urlShop('member_evaluate', 'add_image_save');?>" method="post">
  <input type="hidden" name="geval_id" value="<?php echo $output['geval_info']['geval_id'];?>">
  <div class="wrap-shadow">
    <div class="wrap-all ncu-order-view">
      <h2>对商品进行晒单</h2>
      <h3 class="mt20">评价信息</h3>
      <table class="ncu-table-style">
        <thead>
          <tr>
            <th class="w70"></th>
            <th class="w400 tl">商品信息</th>
            <th class="w100">评分</th>
            <th>评价内容</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><div class="goods-pic-small">
<span class="thumb size60">
<i></i>
<a target="_blank" href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['geval_info']['geval_goodsid']));?>">
<img src="<?php echo cthumb($output['goods_info']['goods_image'], 60);?>">
</a>
</span>
</div></td>
            <td class="tl"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['geval_info']['geval_goodsid']));?>" target="_blank" class="ml20"><?php echo $output['geval_info']['geval_goodsname'];?></a></td>
            <td><div class="raty" style="display:inline-block;" data-score="<?php echo $output['geval_info']['geval_scores'];?>"></div></td>
            <td><?php echo $output['geval_info']['geval_content'];?></td>
            
          </tr>
        </tbody>
      </table>
      <h3 class="mt20">上传晒单图片</h3>
      <div class="evaluation-image">
        <ul>
          <?php for ($i = 0; $i < 5; $i++) {?>
          <li>
          <div class="upload-thumb">
              <div nctype="image_item" style="display:none;">
                  <img src="">
                  <input type="hidden" nctype="input_image" name="evaluate_image[]" value="" >
                  <a href="javascript:;" nctype="del" class="del" title="移除">X</a>
              </div>
          </div>
          <div class="upload-btn"> <a href="javascript:void(0);"> <span>
                      <input type="file" hidefocus="true" size="1" class="input-file" name="file">
                  </span>
                  <p>图片上传</p>
          </a> </div>
          </li>
          <?php }?>
      </ul>
      <dl class="help">
          <dt>图片上传要求：</dt>
          <dd>请使用jpg\jpeg\png等格式、单张大小不超过1M的图片，最多可发布5张晒图，上传后的图片也将被保存在个人主页相册中以便其它使用。</dd>
      </dl>
      </div>
      <div class="tc mt20 mb20"> <a id="btn_submit" href="javascript:;" class="ncu-btn5">确定提交</a> <a href="<?php echo urlShop('member_evaluate', 'list');?>" class="ml30">取消返回</a> </div>
    </div>
  </div>
</form>
