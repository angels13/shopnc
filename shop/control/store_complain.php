<?php
/**
 * 投诉
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
class store_complainControl extends BaseSellerControl{
    //定义投诉状态常量
    const STATE_NEW = 10;
    const STATE_APPEAL = 20;
    const STATE_TALK = 30;
    const STATE_HANDLE = 40;
    const STATE_FINISH = 99;
    const STATE_UNACTIVE = 1;
    const STATE_ACTIVE = 2;

    public function __construct() {
        parent::__construct() ;
        Language::read('member_layout,member_complain');
    }

    /*
     * 被投诉列表
     */
    public function indexOp() {
        $page = new Page() ;
        $page->setEachNum(10);
        $page->setStyle('admin') ;
        $model_complain = Model('complain') ;
        $condition = array();
        $condition['order']        = 'complain_state asc,complain_id desc';
        $condition['accused_id'] = $_SESSION['store_id'];
        switch(intval($_GET['select_complain_state'])) {
            case 1:
                $condition['accused_progressing'] = 'true';
                break;
            case 2:
                $condition['accused_finish'] = 'true';
                break;
            default :
                $condition['accused_all'] = 'true';
        }
        $list = $model_complain->getComplain($condition, $page) ;
        $this->profile_menu('complain_accused_list');
        Tpl::output('list', $list) ;
        Tpl::output('show_page', $page->show()) ;
        Tpl::showpage('complain.list');
    }

    /*
     * 处理投诉请求
     */
    public function complain_showOp() {
        $complain_id = intval($_GET['complain_id']);
        //获取投诉详细信息
        $complain_info = $this->get_complain_info($complain_id);
        //获取订单详细信息
        $model_order = Model('order');
        $order_info = $model_order->getOrderInfo(array('order_id' => $complain_info['order_id']));
        if($order_info['store_id'] != $_SESSION['store_id']) {
            showMessage(Language::get('para_error'),'','html','error');
        }
        $order_info['order_state_text'] = orderState($order_info);
        //获取投诉的商品列表
        $model_complain_goods = Model('complain_goods');
        $param = array();
        $param['complain_id'] = $complain_id;
        $complain_goods_list = $model_complain_goods->getComplainGoods($param);
        $page_name = '';
        switch(intval($complain_info['complain_state'])) {
            case self::STATE_NEW:
                $page_name = 'complain.info';
                break;
            case self::STATE_APPEAL:
                $page_name = 'complain.appeal';
                break;
            case self::STATE_TALK:
                $page_name = 'complain.talk';
                break;
            case self::STATE_HANDLE:
                $page_name = 'complain.talk';
                break;
            case self::STATE_FINISH:
                $page_name = 'complain.info';
                break;
            default:
                showMessage(Language::get('para_error'),'','html','error');
        }
        Tpl::output('order_info',$order_info);
        Tpl::output('complain_info',$complain_info);
        Tpl::output('complain_goods_list',$complain_goods_list);
        Tpl::output('left_show','order_view');
        Tpl::showpage($page_name);
    }

    /*
     * 保存申诉
     */
    public function appeal_saveOp() {
        $complain_id = intval($_POST['input_complain_id']);
        //获取投诉详细信息
        $complain_info = $this->get_complain_info($complain_id);
        //检查当前是不是投诉状态
        if(intval($complain_info['complain_state']) !== self::STATE_APPEAL) {
            showDialog(Language::get('para_error'),'reload');
        }
        $input = array();
        $input['appeal_message'] = trim($_POST['input_appeal_message']);
        //验证输入的信息
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$input['appeal_message'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"255","message"=>Language::get('appeal_message_error')),
        );
        $error = $obj_validate->validate();
        if ($error != ''){
        	showValidateError($error);
        }
        //上传图片
        $appeal_pic = array();
        $appeal_pic[1] = 'input_appeal_pic1';
        $appeal_pic[2] = 'input_appeal_pic2';
        $appeal_pic[3] = 'input_appeal_pic3';
        $pic_name = array();
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'complain'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        $count = 1;
        foreach($appeal_pic as $pic) {
            if (!empty($_FILES[$pic]['name'])){
                $result = $upload->upfile($pic);
                if ($result){
                    $pic_name[$count] = $upload->file_name;
                    $upload->file_name = '';
                } else {
                    $pic_name[$count] = '';
                }
            }
            $count++;
        }
        $input['appeal_pic1'] = $pic_name[1];
        $input['appeal_pic2'] = $pic_name[2];
        $input['appeal_pic3'] = $pic_name[3];
        $input['appeal_datetime'] = time();
        $input['complain_state'] = self::STATE_TALK;
        $where_array = array();
        $where_array['complain_id'] = $complain_id;
        //保存申诉信息
        $model_complain = Model('complain');
        $complain_id = $model_complain->updateComplain($input,$where_array);
        $this->recordSellerLog('投诉申诉处理，投诉编号：'.$complain_id);
        showDialog(Language::get('appeal_submit_success'),'index.php?act=store_complain','succ');
    }

    /*
     * 申请仲裁
     */
    public function apply_handleOp() {
        $complain_id = intval($_POST['input_complain_id']);
        //获取投诉详细信息
        $complain_info = $this->get_complain_info($complain_id);
        $complain_state = intval($complain_info['complain_state']);
        //检查当前是不是投诉状态
        if($complain_state < self::STATE_TALK || $complain_state === 99) {
            showMessage(Language::get('para_error'),'','html','error');
        }
        $update_array = array();
        $update_array['complain_state'] = self::STATE_HANDLE;
        $where_array = array();
        $where_array['complain_id'] = $complain_id;
        //保存投诉信息
        $model_complain = Model('complain');
        $complain_id = $model_complain->updateComplain($update_array,$where_array);
        $this->recordSellerLog('投诉申请仲裁，投诉编号：'.$complain_id);
        showMessage(Language::get('handle_submit_success'),'index.php?act=store_complain');
    }

    /*
     * 根据投诉id获取投诉对话
     */
    public function get_complain_talkOp() {
        $complain_id = intval($_POST['complain_id']);
        $complain_info = $this->get_complain_info($complain_id);
        $model_complain_talk = Model('complain_talk');
        $param = array();
        $param['complain_id'] = $complain_id;
        $complain_talk_list = $model_complain_talk->getComplainTalk($param);
        $talk_list = array();
        $i=0;
        if(!empty($complain_talk_list)) {
            foreach($complain_talk_list as $talk) {
                $talk_list[$i]['css'] = $talk['talk_member_type'];
                $talk_list[$i]['talk'] = date("Y-m-d H:i:s",$talk['talk_datetime']);
                switch($talk['talk_member_type']){
                    case 'accuser':
                        $talk_list[$i]['talk'] .= Language::get('complain_accuser');
                        break;
                    case 'accused':
                        $talk_list[$i]['talk'] .= Language::get('complain_accused');
                        break;
                    case 'admin':
                        $talk_list[$i]['talk'] .= Language::get('complain_admin');
                        break;
                    default:
                        $talk_list[$i]['talk'] .= Language::get('complain_unknow');
                }
                if(intval($talk['talk_state']) === 2) {
                    $talk['talk_content'] = Language::get('talk_forbit_message');
                }
                $talk_list[$i]['talk'].= '('.$talk['talk_member_name'].')'.Language::get('complain_text_say').':'.$talk['talk_content'];
                $i++;
            }
        }
        if (strtoupper(CHARSET) == 'GBK') {
            $talk_list = Language::getUTF8($talk_list);
        }
        echo json_encode($talk_list);
    }

    /*
     * 根据发布投诉对话
     */
    public function publish_complain_talkOp() {
        $complain_id = intval($_POST['complain_id']);
        $complain_talk = trim($_POST['complain_talk']);
        $talk_len = strlen($complain_talk);
        if($talk_len > 0 && $talk_len < 255) {
            $complain_info = $this->get_complain_info($complain_id);
            $complain_state = intval($complain_info['complain_state']);
            //检查投诉是否是可发布对话状态
            if($complain_state > self::STATE_APPEAL && $complain_state < self::STATE_FINISH) {
                $model_complain_talk = Model('complain_talk');
                $param = array();
                $param['complain_id'] = $complain_id;
                $param['talk_member_id'] = $complain_info['accused_id'];
                $param['talk_member_name'] = $complain_info['accused_name'];
                $param['talk_member_type'] = $complain_info['member_status'];
                if (strtoupper(CHARSET) == 'GBK') {
                    $complain_talk = Language::getGBK($complain_talk);
                }
                $param['talk_content'] = $complain_talk;
                $param['talk_state'] =1;
                $param['talk_admin'] = 0;
                $param['talk_datetime'] = time();
                if($model_complain_talk->saveComplainTalk($param)) {
                    echo json_encode('success');
                } else {
                    echo json_encode('error2');
                }
            } else {
                echo json_encode('error');
            }
        } else {
            echo json_encode('error1');
        }
    }

    /*
     * 获取投诉信息
     */
    private function get_complain_info($complain_id) {
        if(empty($complain_id)) {
            showMessage(Language::get('para_error'),'','html','error');
        }
        $model_complain = Model('complain');
        $complain_info = $model_complain->getoneComplain($complain_id);
        if($complain_info['accused_id'] != $_SESSION['store_id']) {
            showMessage(Language::get('para_error'),'','html','error');
        }
        $complain_info['member_status'] = 'accused';
        $complain_info['complain_state_text'] = $this->get_complain_state_text($complain_info['complain_state']);
        return $complain_info;
    }

    /*
     * 获得投诉状态文本
     */
    private function get_complain_state_text($complain_state) {
        switch(intval($complain_state)) {
            case self::STATE_NEW:
                return Language::get('complain_state_new');
                break;
            case self::STATE_APPEAL:
                return Language::get('complain_state_appeal');
                break;
            case self::STATE_TALK:
                return Language::get('complain_state_talk');
                break;
            case self::STATE_HANDLE:
                return Language::get('complain_state_handle');
                break;
            case self::STATE_FINISH:
                return Language::get('complain_state_finish');
                break;
            default:
                showMessage(Language::get('para_error'),'','html','error');
        }
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string	$menu_type	导航类型
     * @param string 	$menu_key	当前导航的menu_key
     * @param array 	$array		附加菜单
     * @return
     */
    private function profile_menu($menu_key='') {
        $menu_array = array(
            array('menu_key'=>'complain_accused_list','menu_name'=>Language::get('complain_manage_title'),'menu_url'=>'index.php?act=store_complain')
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
