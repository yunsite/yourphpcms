<?php
/**
 * 
 * Order (后台订单管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class OrderAction extends AdminbaseAction
{

	protected $dao;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao=M('Order');	
    }

    public function index()
    {
	    $this->_list(MODULE_NAME);
        $this->display ();
    }
 
 	public function show(){
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

			$this->assign('order',$order);
			$this->assign('order_data',$order_data);
			$this->assign('amount',$amount); 
		$this->display();		
	}
	public function edit()
    {
		$id= intval($_REQUEST['id']);
		$order = $id ? $this->dao->find($id) : '';
		$do = $_REQUEST['do']; 
		$this->assign('do',$do);
		$this->assign('id',$id);

		if($order['shipping_status'] && $do!='status'){
				$this->assign('dialog','1');
				$this->assign ( 'waitSecond', 2);
				$this->assign ( 'jumpUrl',1);
				$this->error (L('order_shippinged_no_edit'));
		}

		if($_REQUEST['dosubmit']){
			
			switch($do) {
				case 'data':
					$modle = M('Order_data');
					if($_GET['delete']){
						$data_id = intval($_GET['data_id']);
						$modle->delete($data_id);
					}else{
						foreach($_POST['data_id'] as $key=>$r){
							$data=array();
							$data['id'] = $r;
							$data['product_price'] = $_POST['product_price'][$key];
							$data['number'] =  $_POST['number'][$key];
							$data['price'] = $data['product_price']*$data['number'];
							$modle->save($data); 
						}
					}
					$_POST = order_count($order); 

				case 'money':
					$order['discount'] = $_POST['discount'];
					$_POST  = order_count($order);
				break;

				case 'payment':
					$order['pay_id'] = $_POST['pay_id'];
					$_POST  = order_count($order);
				break;

				case 'shipping':					
					$order['shipping_id'] = $_POST['shipping_id'];
					$order['insure'] =  $_POST['insure_'.$order['shipping_id']] ? 1 : 0;
					$_POST  = order_count($order);
				break;

				case 'status':					
					$order[$_POST['type']] = $_POST['value'];
 
					if($_POST['type'] == 'status' && $_POST['value']==2){
						$order['confirm_time'] =time();
					}elseif($_POST['type'] == 'shipping_status' && $_POST['value']==1){
						$order['shipping_time'] =time();
					}elseif($_POST['type'] == 'pay_status' && $_POST['value']==2){
						$order['pay_time'] =time();
					}elseif($_POST['type'] == 'shipping_status' && $_POST['value']==2){
						$order['accept_time']=time();
					}

					if (false!==$this->dao->save($order)) {
						die(json_encode(array('msg'=>L('do_ok'))));
					}else{
						die(json_encode(array('msg'=>L('do_error'))));
					}
				break;				
			}

			if (false === $this->dao->create ())  $this->error ( $this->dao->getError () ); 
			if (false!==$this->dao->save()) {
				$this->assign('dialog','1');
				$jumpUrl = U(MODULE_NAME.'/show?id='.$_REQUEST['id']);
				$this->assign ( 'jumpUrl', $jumpUrl);
				$this->success (L('edit_ok'));
			}else{
				$this->error (L('do_error'));
			}

			exit;
		}

		switch($do) {
				case 'address':
					$Area = M('Area')->getField('id,name');
					$this->assign('Area',$Area);
				break;

				case 'payment':
					$payment = M('Payment')->field('id,pay_code,pay_name,pay_fee,pay_fee_type,pay_desc,is_cod,is_online')->where("status=1")->select();
					$this->assign('payment',$payment);
				break;

				case 'data':
					$order_data = M('Order_data')->where("order_id='{$order[id]}'")->select();
					$this->assign('order_data',$order_data);
				break;
				case 'shipping':
					$shipping = M('Shipping')->where("status=1")->select();
					$this->assign('shipping',$shipping);
				break;
		}

		$this->assign('order',$order);
		$this->display();
    }

	function orderlist(){
	
		exit;
		$this->display();
	}

}

function order_count($order){
	$order['amount'] = M('Order_data')->where("order_id='{$order[id]}'")->sum('price'); //商品总价
	$order['invoice_fee'] =  $order['invoice'] ? $order['amount']*0.05 : 0; //税金
	$order['invoice_fee'] =  number_format($order['invoice_fee'],2);

	if($order['shipping_id'])$Shipping = M('Shipping')->find($order['shipping_id']);
	if($order['pay_id'])$Payment  = M('Payment')->find($order['pay_id']);
	$order['pay_name'] = $Payment['pay_name'];
	$order['pay_code'] = $Payment['pay_code'];

	if($order['insure']){ //保价
		$insure_fee =$order['amount']*$Shipping['insure_fee']/100;
		$order['insure_fee'] = $insure_fee >=$Shipping['insure_low_price'] ? number_format($insure_fee,2) : $Shipping['insure_low_price'];
	}else{
		$order['insure_fee'] =0;
	}
	$order['shipping_name']  = $Shipping['name']; //运费
	$order['shipping_fee'] = $Shipping['first_price']; //运费
	$order['order_amount'] = $order['amount']+$order['invoice_fee']+$order['insure_fee']+$order['shipping_fee']-$order['promotions']-$order['discount'];	
	$order['pay_fee'] =  $Payment['pay_fee_type'] ?  $Payment['pay_fee'] : $order['order_amount']*$Payment['pay_fee']/100; 
	$order['pay_fee'] =   number_format($order['pay_fee'],2);

	$order['order_amount'] = $order['order_amount']+$order['pay_fee'];
	return $order;  
}

?>