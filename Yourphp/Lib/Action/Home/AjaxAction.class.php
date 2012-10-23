<?php
/**
 * 
 * AreaAction.class.php (ajax 获取地址)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class AjaxAction extends BaseAction
{
    public function index()
    {
	 exit;
    }
    public function area()
    {
		$module = M('Area');
		$id = intval($_REQUEST['id']);
		$level= intval($_REQUEST['level']);
		$provinceid= intval($_REQUEST['provinceid']);
		$cityid= intval($_REQUEST['cityid']);
		$areaid= intval($_REQUEST['areaid']);
		
 		
		$province_str='<option value="0">请选择省份...</option>';
		$city_str='<option value="0">请选择城市...</option>';
		$area_str='<option value="0">请选择区域...</option>';
		$str ='';

		$r = $module->where("parentid=".$id)->select();	 		
		foreach($r as $key=>$pro){
			$selected = ( $pro['id']==$provinceid) ? ' selected="selected" ' : '';
			$str .='<option value="'.$pro['id'].'"'.$selected.'>'.$pro['name'].'</option>';
		}
		if($level==0){
			$province_str .=$str;
		}elseif($level==1){
			$city_str .=$str;
		}elseif($level==2){
			$area_str .=$str;
		}
		$str='';
		if($provinceid){
			
			$rr = $module->where("parentid=".$provinceid)->select();	 		
			foreach($rr as $key=>$pro){
				$selected = ($pro['id']==$cityid) ? ' selected="selected" ' : '';
				$str .='<option value="'.$pro['id'].'"'.$selected.'>'.$pro['name'].'</option>';
			}
			$city_str .=$str;
		}
		$str='';
		if($cityid){
			$rrr = $module->where("parentid=".$cityid)->select();	 		
			foreach($rrr as $key=>$pro){
				$selected = ($pro['id']==$areaid) ? ' selected="selected" ' : '';
				$str .='<option value="'.$pro['id'].'"'.$selected.'>'.$pro['name'].'</option>';
			}
			$area_str .=$str;
		}
		
		$res=array();
		$res['data']= $rs ? 1 : 0 ;
		$res['province'] =$province_str;
		$res['city'] =$city_str;
		$res['area'] =$area_str;
		echo json_encode($res); exit;
	 exit;
    }

	public function address(){
		$do=get_safe_replace($_REQUEST['do']);
		$model = M('User_address');
		$id = intval($_REQUEST['id']);
		
		$provinceid= intval($_REQUEST['province']);
		$cityid= intval($_REQUEST['city']);
		$areaid= intval($_REQUEST['area']);
		
		$userid = $_POST['userid'] = $this->_userid;
		if($do=='save'){
			$id= intval($_POST['id']);
			$_POST['isdefault']=1;
			if($userid){				
				$model->where("userid=".$userid)->save(array('isdefault'=>0));				
				if($id){
					$r = $model->save($_POST);
					if($model->getDbError())die(json_encode(array('id'=>0)));
					$_POST['edit'] =1;				
				}else{
					$where['province'] = array('eq',$provinceid);
					$where['city'] = array('eq',$cityid);
					$where['area'] = array('eq',$areaid);
					$where['consignee'] = array('eq',$_POST['consignee']);
					$where['address'] = array('eq',$_POST['address']);
					$ir = $model->where($where)->find();
					if($ir){
						echo json_encode(array('error'=>'收货信息已经存在！'));exit;
					}
					$id=$model->add ($_POST);
				}
			}else{
					$_POST['id']=1;
					$data = serialize($_POST);
					cookie('guest_address',$data,315360000);
					$id=1;
					$_POST['edit'] =1;
			}
			if($id){
				$_POST['id'] =$id;
				die(json_encode($_POST));
			}else{
				die(json_encode(array('id'=>0)));
			}
			 
		}elseif($do=='get'){
			if($userid){	
				$data=$model->find($id);
			}else{
				$data = unserialize( cookie('guest_address'));
			}
			if($data){
				die(json_encode($data));
			}else{
				die(json_encode(array('id'=>0)));
			}
			exit;
		}
	
	}

	public function shipping(){
		$do=get_safe_replace($_REQUEST['do']);
		$model = M('Shipping');
		$id = intval($_REQUEST['id']); 
 
		if($do=='get'){
			$data=$model->find($id);
			if($data){
				echo json_encode($data);
			}else{
				echo json_encode(array('id'=>0));
			}
			exit;
		}
	
	}
 
}
?>