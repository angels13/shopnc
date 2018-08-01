<?php
/**
 * 团购管理
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
class groupbuyControl extends SystemControl{

    public function __construct(){
        parent::__construct();
        Language::read('groupbuy');

		//如果是执行开启团购操作，直接返回
		if ($_GET['groupbuy_open'] == 1) return true;        
        
        //检查团购功能是否开启
        if (C('groupbuy_allow') != 1){
 			$url = array(
 				array(
					'url'=>'index.php?act=dashboard&op=welcome',
					'msg'=>Language::get('close'),
				),			
				array(
					'url'=>'index.php?act=groupbuy&op=groupbuy_template_list&groupbuy_open=1',
					'msg'=>Language::get('open'),
				),
			);
			showMessage(Language::get('admin_groupbuy_unavailable'),$url,'html','succ',1,6000);
        }
    }

    public function indexOp() {
        $this->groupbuy_listOp();
    }

    /**
     * 进行中团购列表，只可推荐
     *
     */
    public function groupbuy_listOp(){
        $model_groupbuy = Model('groupbuy');

        $condition = array();
        if(!empty($_GET['groupbuy_name'])) {
            $condition['groupbuy_name'] = array('like', '%'.$_GET['groupbuy_name'].'%');
        }
        if(!empty($_GET['store_name'])) {
            $condition['store_name'] = array('like', '%'.$_GET['store_name'].'%');
        }
        if(!empty($_GET['groupbuy_state'])) {
            $condition['state'] = $_GET['groupbuy_state'];
        }
        $groupbuy_list = $model_groupbuy->getGroupbuyList($condition, 10);
        Tpl::output('groupbuy_list',$groupbuy_list);
        Tpl::output('show_page',$model_groupbuy->showpage());
        Tpl::output('groupbuy_state_array', $model_groupbuy->getGroupbuyStateArray());

        $this->show_menu('groupbuy_list');
        Tpl::showpage('groupbuy.list');
    }

    /**
     * 审核通过
     */
    public function groupbuy_review_passOp(){
        $groupbuy_id = intval($_POST['groupbuy_id']);

        $model_groupbuy = Model('groupbuy');
        $result = $model_groupbuy->reviewPassGroupbuy($groupbuy_id);
        if($result) {
        	$this->log('通过团购活动申请，团购编号'.$groupbuy_id,null);
            showMessage(L('nc_common_op_succ'), '');
        } else {
            showMessage(L('nc_common_op_fail'), '');
        }
    }

    /**
     * 审核失败
     */
    public function groupbuy_review_failOp(){
        $groupbuy_id = intval($_POST['groupbuy_id']);

        $model_groupbuy = Model('groupbuy');
        $result = $model_groupbuy->reviewFailGroupbuy($groupbuy_id);
        if($result) {
        	$this->log('拒绝团购活动申请，团购编号'.$groupbuy_id,null);
            showMessage(L('nc_common_op_succ'), '');
        } else {
            showMessage(L('nc_common_op_fail'), '');
        }
    }

    /**
     * 取消
     */
    public function groupbuy_cancelOp() {
        $groupbuy_id = intval($_POST['groupbuy_id']);

        $model_groupbuy = Model('groupbuy');
        $result = $model_groupbuy->cancelGroupbuy($groupbuy_id);
        if($result) {
        	$this->log('取消团购活动，团购编号'.$groupbuy_id,null);
            showMessage(L('nc_common_op_succ'), '');
        } else {
            showMessage(L('nc_common_op_fail'), '');
        }
    }

    /**
     * 删除
     */
    public function groupbuy_delOp(){
        $groupbuy_id = intval($_POST['groupbuy_id']);

        $model_groupbuy = Model('groupbuy');
        $result = $model_groupbuy->delGroupbuy(array('groupbuy_id' => $groupbuy_id));
        if($result) {
        	$this->log('删除团购活动，团购编号'.$groupbuy_id,null);
            showMessage(L('nc_common_op_succ'), '');
        } else {
            showMessage(L('nc_common_op_fail'), '');
        }
    }

    /**
     * ajax修改团购信息
     */
    public function ajaxOp(){

        $result = true;
        $update_array = array();
        $where_array = array();

        switch ($_GET['branch']){
        case 'class_sort':
            $model= Model('groupbuy_class');
            $update_array['sort'] = $_GET['value'];
            $where_array['class_id'] = $_GET['id'];
            $result = $model->update($update_array,$where_array);
            break;
        case 'class_name':
            $model= Model('groupbuy_class');
            $update_array['class_name'] = $_GET['value'];
            $where_array['class_id'] = $_GET['id'];
            $result = $model->update($update_array,$where_array);
            break;
        case 'area_sort':
            $model= Model('groupbuy_area');
            $update_array['area_sort'] = $_GET['value'];
            $where_array['area_id'] = $_GET['id'];
            $result = $model->update($update_array,$where_array);
            break;
        case 'area_name':
            $model= Model('groupbuy_area');
            $update_array['area_name'] = $_GET['value'];
            $where_array['area_id'] = $_GET['id'];
            $result = $model->update($update_array,$where_array);
            break;
         case 'recommended':
            $model= Model('groupbuy');
            $update_array['recommended'] = $_GET['value'];
            $where_array['groupbuy_id'] = $_GET['id'];
            $result = $model->editGroupbuy($update_array, $where_array);
            break;
        }
        if($result) {
        	H('groupbuy',null);
            echo 'true';exit;
        }
        else {
            echo 'false';exit;
        }

    }

    /**
     * 套餐管理
     **/
    public function groupbuy_quotaOp() {
        $model_groupbuy_quota = Model('groupbuy_quota');

        $condition = array();
        $condition['store_name'] = array('like', '%'.$_GET['store_name'].'%');
        $list = $model_groupbuy_quota->getGroupbuyQuotaList($condition, 10, 'end_time desc');
        Tpl::output('list',$list);
        Tpl::output('show_page',$model_groupbuy_quota->showpage());

        $this->show_menu('groupbuy_quota');
        Tpl::showpage('groupbuy_quota.list');
    }

    /**
     * 团购类别列表 
     */
    public function class_listOp() {

        $model_groupbuy_class = Model('groupbuy_class');
        $param = array();
        $param['order'] = 'sort asc';
        $groupbuy_class_list = $model_groupbuy_class->getTreeList($param);

        $this->show_menu('class_list');
        Tpl::output('list',$groupbuy_class_list);
        Tpl::showpage('groupbuy_class.list');
    }

    /**
     * 添加团购分类页面 
     */
    public function class_addOp() {

        $model_groupbuy_class = Model('groupbuy_class');
        $param = array();
        $param['order'] = 'sort asc';
        $param['class_parent_id'] = 0;
        $groupbuy_class_list = $model_groupbuy_class->getList($param);
        Tpl::output('list',$groupbuy_class_list);

        $this->show_menu('class_add');
        Tpl::output('parent_id',$_GET['parent_id']);
        Tpl::showpage('groupbuy_class.add');

    }

    /**
     * 保存添加的团购类别 
     */
    public function class_saveOp() {

        $class_id = intval($_POST['class_id']);
        $param = array();
        $param['class_name'] = trim($_POST['input_class_name']);
        if(empty($param['class_name'])) {
            showMessage(Language::get('class_name_error'),'');
        }
        $param['sort'] = intval($_POST['input_sort']);
        $param['class_parent_id'] = intval($_POST['input_parent_id']);

        $model_groupbuy_class = Model('groupbuy_class');
        
        if(empty($class_id)) {
            //新增
            if($model_groupbuy_class->save($param)) {
            	H('groupbuy',null);
            	$this->log(L('nc_del,groupbuy_class_add_success').'['.$_POST['input_class_name'].']',null);
                showMessage(Language::get('groupbuy_class_add_success'),'index.php?act=groupbuy&op=class_list');
            }
            else {
                showMessage(Language::get('groupbuy_class_add_fail'),'index.php?act=groupbuy&op=class_list');
            }
        }
        else {
            //编辑
            if($model_groupbuy_class->update($param,array('class_id'=>$class_id))) {
            	H('groupbuy',null);
            	$this->log(L('nc_del,groupbuy_class_edit_success').'['.$_POST['input_class_name'].']',null);
                showMessage(Language::get('groupbuy_class_edit_success'),'index.php?act=groupbuy&op=class_list');
            }
            else {
                showMessage(Language::get('groupbuy_class_edit_fail'),'index.php?act=groupbuy&op=class_list');
            }
        }

    }

    /**
     * 删除团购类别 
     */
    public function class_dropOp() {

        $class_id = trim($_POST['class_id']);
        if(empty($class_id)) {
            showMessage(Language::get('param_error'),'');
        }

        $model_groupbuy_class = Model('groupbuy_class');
        //获得所有下级类别编号
        $all_class_id = $model_groupbuy_class->getAllClassId(explode(',',$class_id));
        $param = array();
        $param['in_class_id'] = implode(',',$all_class_id);
        if($model_groupbuy_class->drop($param)) {
        	H('groupbuy',null);
        	$this->log(L('groupbuy_class_drop_success').'[ID:'.$param['in_class_id'].']',null);
            showMessage(Language::get('groupbuy_class_drop_success'),'');
        }
        else {
            showMessage(Language::get('groupbuy_class_drop_fail'),'');
        }

    }

    /**
     * 团购地区列表 
     */
    public function area_listOp() {

        $model_groupbuy_area = Model('groupbuy_area');
        $param = array();
        $param['order'] = 'area_sort asc';
        $groupbuy_area_list = $model_groupbuy_area->getTreeList($param,'',2);
        Tpl::output('list',$groupbuy_area_list);

        $this->show_menu('area_list');
        Tpl::showpage('groupbuy_area.list');
    }

    /**
     * 添加团购地区页面 
     */
    public function area_addOp() {

        $model_groupbuy_area = Model('groupbuy_area');
        $param = array();
        $param['order'] = 'area_sort asc';
        $param['area_parent_id'] = 0;
        $groupbuy_area_list = $model_groupbuy_area->getTreeList($param,'',1);
        Tpl::output('list',$groupbuy_area_list);

        $this->show_menu('area_add');
        Tpl::output('parent_id',$_GET['parent_id']);
        Tpl::showpage('groupbuy_area.add');

    }

    /**
     * 保存添加的团购地区 
     */
    public function area_saveOp() {

        $area_id = intval($_POST['area_id']);
        $param = array();
        $param['area_name'] = trim($_POST['input_area_name']);
        if(empty($param['area_name'])) {
            showMessage(Language::get('area_name_error'),'');
        }
        $param['area_sort'] = intval($_POST['input_area_sort']);
        $param['area_parent_id'] = intval($_POST['input_parent_id']);

        $model_groupbuy_area = Model('groupbuy_area');
        
        if(empty($area_id)) {
            //新增
            if($model_groupbuy_area->save($param)) {
            	H('groupbuy',null);
            	$this->log(L('groupbuy_area_add_success').'[ID:'.$_POST['input_area_name'].']',null);
                showMessage(Language::get('groupbuy_area_add_success'),'index.php?act=groupbuy&op=area_list');
            }
            else {
                showMessage(Language::get('groupbuy_area_add_fail'),'index.php?act=groupbuy&op=area_list');
            }
        }
        else {
            //编辑
            if($model_groupbuy_area->update($param,array('area_id'=>$area_id))) {
            	H('groupbuy',null);
            	$this->log(L('groupbuy_area_edit_success').'[ID:'.$_POST['input_area_name'].']',null);
                showMessage(Language::get('groupbuy_area_edit_success'),'index.php?act=groupbuy&op=area_list');
            }
            else {
                showMessage(Language::get('groupbuy_area_edit_fail'),'index.php?act=groupbuy&op=area_list');
            }
        }

    }

    /**
     * 删除团购地区 
     */
    public function area_dropOp() {

        $area_id = trim($_POST['area_id']);
        if(empty($area_id)) {
            showMessage(Language::get('param_error'),'');
        }

        $model_groupbuy_area = Model('groupbuy_area');
        //获得所有下级类别编号
        $all_area_id = $model_groupbuy_area->getAllAreaId(explode(',',$area_id));
        $param = array();
        $param['in_area_id'] = implode(',',$all_area_id);
        if($model_groupbuy_area->drop($param)) {
        	H('groupbuy',null);
        	$this->log(L('groupbuy_area_drop_success').'[ID:'.$area_id.']',null);
            showMessage(Language::get('groupbuy_area_drop_success'),'');
        }
        else {
            showMessage(Language::get('groupbuy_area_drop_fail'),'');
        }

    }

    /**
     * 团购价格区间列表 
     */
    public function price_listOp() {

        $model= Model('groupbuy_price_range');
        $groupbuy_price_list = $model->getList();
        Tpl::output('list',$groupbuy_price_list);

        $this->show_menu('price_list');
        Tpl::showpage('groupbuy_price.list');
    }

    /**
     * 添加团购价格区间页面 
     */
    public function price_addOp() {

        $this->show_menu('price_add');
        Tpl::showpage('groupbuy_price.add');

    }

    /**
     * 编辑团购价格区间页面 
     */
    public function price_editOp() {

        $range_id = intval($_GET['range_id']);
        if(empty($range_id)) {
            showMessage(Language::get('param_error'),'');
        }

        $model = Model('groupbuy_price_range');

        $price_info = $model->getOne($range_id);
        if(empty($price_info)) {
            showMessage(Language::get('param_error'),'');
        }
        Tpl::output('price_info',$price_info);

        $this->show_menu('price_edit');
        Tpl::showpage('groupbuy_price.add');

    }

    /**
     * 保存添加的团购价格区间
     */
    public function price_saveOp() {

        $range_id = intval($_POST['range_id']);
        $param = array();
        $param['range_name'] = trim($_POST['range_name']);
        if(empty($param['range_name'])) {
            showMessage(Language::get('range_name_error'),'');
        }
        $param['range_start'] = intval($_POST['range_start']);
        $param['range_end'] = intval($_POST['range_end']);

        $model = Model('groupbuy_price_range');
        
        if(empty($range_id)) {
            //新增
            if($model->save($param)) {
            	H('groupbuy',null);
            	$this->log(L('groupbuy_price_range_add_success').'['.$_POST['range_name'].']',null);
                showMessage(Language::get('groupbuy_price_range_add_success'),'index.php?act=groupbuy&op=price_list');
            }
            else {
                showMessage(Language::get('groupbuy_price_range_add_fail'),'index.php?act=groupbuy&op=price_list');
            }
        }
        else {
            //编辑
            if($model->update($param,array('range_id'=>$range_id))) {
            	H('groupbuy',null);
            	$this->log(L('groupbuy_price_range_edit_success').'['.$_POST['range_name'].']',null);
                showMessage(Language::get('groupbuy_price_range_edit_success'),'index.php?act=groupbuy&op=price_list');
            }
            else {
                showMessage(Language::get('groupbuy_price_range_edit_fail'),'index.php?act=groupbuy&op=price_list');
            }
        }

    }

    /**
     * 删除团购价格区间
     */
    public function price_dropOp() {

        $range_id = trim($_POST['range_id']);
        if(empty($range_id)) {
            showMessage(Language::get('param_error'),'');
        }

        $model = Model('groupbuy_price_range');
        $param = array();
        $param['in_range_id'] = "'".implode("','", explode(',', $range_id))."'";
        if($model->drop($param)) {
        	H('groupbuy',null);
        	$this->log(L('groupbuy_price_range_drop_success').'[ID:'.$range_id.']',null);
            showMessage(Language::get('groupbuy_price_range_drop_success'),'');
        }
        else {
            showMessage(Language::get('groupbuy_price_range_drop_fail'),'');
        }
    }

    /**
     * 设置
     **/
    public function groupbuy_settingOp() {

        $model_setting = Model('setting');
        $setting = $model_setting->GetListSetting();
        Tpl::output('setting',$setting);

        $this->show_menu('groupbuy_setting');
        Tpl::showpage('groupbuy.setting');
    }

    public function groupbuy_setting_saveOp() {
        $groupbuy_price = intval($_POST['groupbuy_price']);
        if($groupbuy_price < 0) {
            $groupbuy_price = 0;
        }

        $groupbuy_review_day = intval($_POST['groupbuy_review_day']);
        if($groupbuy_review_day< 0) {
            $groupbuy_review_day = 0;
        }

        $model_setting = Model('setting');
        $update_array = array();
        $update_array['groupbuy_price'] = $groupbuy_price;
        $update_array['groupbuy_review_day'] = $groupbuy_review_day;
        $result = $model_setting->updateSetting($update_array);
        if ($result){
            $this->log('修改团购套餐价格为'.$groupbuy_price.'元');
            showMessage(Language::get('nc_common_op_succ'),'');
        }else {
            showMessage(Language::get('nc_common_op_fail'),'');
        }
    }

    /**
     * 页面内导航菜单
     *
     * @param string 	$menu_key	当前导航的menu_key
     * @param array 	$array		附加菜单
     * @return 
     */
    private function show_menu($menu_key) {
        $menu_array = array(
            'groupbuy_list'=>array('menu_type'=>'link','menu_name'=>'团购活动','menu_url'=>'index.php?act=groupbuy&op=groupbuy_list'),
            'groupbuy_quota'=>array('menu_type'=>'link','menu_name'=>'套餐管理','menu_url'=>'index.php?act=groupbuy&op=groupbuy_quota'),
            'class_list'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_class_list'),'menu_url'=>'index.php?act=groupbuy&op=class_list'),
            'class_add'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_class_add'),'menu_url'=>'index.php?act=groupbuy&op=class_add'),
            'area_list'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_area_list'),'menu_url'=>'index.php?act=groupbuy&op=area_list'),
            'area_add'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_area_add'),'menu_url'=>'index.php?act=groupbuy&op=area_add'),
            'price_list'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_price_list'),'menu_url'=>'index.php?act=groupbuy&op=price_list'),
            'price_add'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_price_add'),'menu_url'=>'index.php?act=groupbuy&op=price_add'),
            'price_edit'=>array('menu_type'=>'link','menu_name'=>Language::get('groupbuy_price_edit'),'menu_url'=>'index.php?act=groupbuy&op=price_edit'),
            'groupbuy_setting'=>array('menu_type'=>'link','menu_name'=>'设置','menu_url'=>urlAdmin('groupbuy', 'groupbuy_setting')),
        );
        switch ($menu_key) {
            case 'class_add':
                unset($menu_array['area_add']);
                unset($menu_array['price_add']);
                unset($menu_array['price_edit']);
                break;
            case 'area_add':
                unset($menu_array['class_add']);
                unset($menu_array['price_add']);
                unset($menu_array['price_edit']);
                break;
            case 'price_add':
                unset($menu_array['area_add']);
                unset($menu_array['class_add']);
                unset($menu_array['price_edit']);
                break;
            case 'price_edit':
                unset($menu_array['area_add']);
                unset($menu_array['class_add']);
                unset($menu_array['price_add']);
                break;
            default:
                unset($menu_array['area_add']);
                unset($menu_array['class_add']);
                unset($menu_array['price_add']);
                unset($menu_array['price_edit']);
                break;
        }
        $menu_array[$menu_key]['menu_type'] = 'text';
        Tpl::output('menu',$menu_array);
    }	
}
