<?php
/**
 * 
 * OrderAction.class.php (订单管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("YOURPHP")) exit("Access Denied");
class OrderAction extends BaseAction
{

	function _initialize()
    {	
		parent::_initialize();

		$this->dao = M('Order');
		$this->assign('bcid',0);
		$user =  M('User')->find($this->_userid);
		$this->assign('vo',$user);
    }

    public function index()
    {

		if($_REQUEST['sn']){
		$sn = $_REQUEST['sn'];
		unset($_REQUEST['sn']);
		}
		import('@.Action.Adminbase');
		$c=new AdminbaseAction();
		if($this->_userid || $sn){ 
			$map['userid']=intval($this->_userid);
			if($sn)$map['sn'] = $sn;
			$c->_list(MODULE_NAME,$map);
		}
        $this->display();
    }

	public function show()
    {
		$sn = intval($_REQUEST['sn']);
		$id= intval($_REQUEST['id']);
		$order = $id ? $this->dao->find($id) : $this->dao->getBySn($sn) ;
		if(!$order && $order['userid']!=$this->_userid) $this->success (L('do_empty'));

		$order_data = M('Order_data')->where("order_id='{$order[id]}'")->select();
		$amount=0;
		foreach($order_data as $key=>$r){
			$amount = $amount+$r['price'];
		}
	 

		$Payment = M('Payment')->find($order['pay_id']);
		$Shipping = M('Shipping')->find($shippingid);
		$Area = M('Area')->getField('id,name');
		$this->assign('Area',$Area);
		$this->assign('Payment',$Payment);
		$this->assign('Shipping',$Shipping);


		if($order['pay_code'] && $order['status']<2 && $order['pay_status']<2){
			
			$aliapy_config = unserialize($Payment['pay_config']);
			$aliapy_config['order_sn']= $order['sn'];
			$aliapy_config['order_amount']= $order['order_amount'];
			$aliapy_config['body'] = $order['consignee'].' '.$order['postmessage'];
			import("@.Pay.".$order['pay_code']);
			$pay=new $order['pay_code']($aliapy_config);
			$paybutton = $pay->get_code();
			$this->assign('paybutton',$paybutton);
		}
		$this->assign('order',$order);
		$this->assign('order_data',$order_data);
		$this->assign('amount',$amount); 
		$this->display();
    }



	function ajax(){
		
		$model= M('Order');
		$order = $model->find($_POST['id']);
		if($order['userid']!=$this->_userid)die(json_encode(array('msg'=>L('do_empty'))));
		if($_GET['do']=='saveaddress'){
			$r = $model->save($_POST);
			die(json_encode(array('id'=>1)));
		}elseif($_GET['do'] =='order_status'){
			$_POST['status']=3;
			$_POST['confirm_time']=time();
			$r = $model->save($_POST);
			die(json_encode(array('id'=>1)));
		}elseif($_GET['do'] =='pay_status'){
			$_POST['pay_status']=3;
			$r = $model->save($_POST);
			die(json_encode(array('id'=>1)));
		}elseif($_GET['do'] =='shipping_status'){
			$_POST['shipping_status']=$_POST['num'];
			unset($_POST['num']);
			$_POST['accept_time']= $_POST['shipping_status']==2 ? time() : '';
			$r = $model->save($_POST);
			die(json_encode(array('id'=>1)));
		}
	}



	
}
?>