<?php
/**
 * 
 * OrderAction.class.php (前台购物订单)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class OrderAction extends BaseAction
{
	protected   $dao , $sessionid;
	function _initialize()
    {
		parent::_initialize();
		$this->dao=M('Cart');
		$this->sessionid =  cookie('onlineid');
    }


    public function index()
    {	
		$buy = intval($_REQUEST['buy']);
		$cart = $this->dao->where("sessionid='{$this->sessionid}'")->select();
		$this->assign('cart',$cart);
        $this->display();
    }

	public function checkout(){

		if($this->Config['isuserbuy'] && empty($this->_userid))$this->error ( L('do_empty'));

		$data= M()->query("SELECT DISTINCT o.sessionid FROM ".C('DB_PREFIX')."cart as c , ".C('DB_PREFIX')."online as o where c.sessionid=o.sessionid ");
		if($data){
			foreach($data as $key=>$id)$ids[]=$id['sessionid'];
			M('Cart')->where(" sessionid NOT in('".implode("','",$ids)."') ")->delete();
		}

		$cart = $this->dao->where("sessionid='{$this->sessionid}'")->select();
		$amount=0;
		foreach($cart as $key=>$r){
			$amount = $amount+$r['price'];
		}

		$this->assign('cart',$cart);
		$this->assign('buy',1);

		if($this->_userid)
			$user_address = M('User_address')->where("userid='{$this->_userid}'")->select();
		else
			if( cookie('guest_address'))$default_address = unserialize( cookie('guest_address'));

		$Area = M('Area')->getField('id,name');
		$shipping = M('Shipping')->where("status=1")->select();
		$payment = M('Payment')->field('id,pay_code,pay_name,pay_fee,pay_fee_type,pay_desc,is_cod,is_online')->where("status=1")->select();

		foreach($user_address as $key=>$r){
			if($r['isdefault'])$default_address=$r;
		}
		$this->assign('default_address',$default_address);
		$this->assign('payment',$payment);
		$this->assign('user_address',$user_address);
		$this->assign('Area',$Area);
		$this->assign('shipping',$shipping);

		if($_REQUEST['do']){
			$this->assign('buy',2);
		}
	    $this->display();
	}

	public function _before_insert(){
		$_POST['ip'] = get_client_ip();
	}

	public function ajax(){ 
		$id = intval($_REQUEST['id']);
		$num = intval($_REQUEST['num']);
		$moduleid = intval($_REQUEST['moduleid']);
		$module =  $this->module[$moduleid]['name'];
		$do = $_REQUEST['do']; 
		
		if($do=='add'){
			if(!$module){$res['msg']='error';echo json_encode($res); exit;}	
			$session_info = M('Online')->find($this->sessionid);
			$r=M($module)->find($id);
			$cart = $this->dao->where("product_id='{$id}' and sessionid='{$this->sessionid}'")->find();
			if($cart){
				$cart['number']=$cart['number']+$num;
				$cart['price'] = $cart['product_price']*$cart['number'];
				$rs = $this->dao->save($cart);
			}else{
				$data=array();
				$data['userid']=$session_info['userid'];
				$data['sessionid']=$session_info['sessionid'];
				$data['product_id']=$r['id'];
				$data['product_thumb']=$r['thumb'];
				$data['product_url']=$r['url'];
				$data['product_name']=$r['title'];
				$data['product_price'] =$r['price'];
				$data['moduleid']=$moduleid;	
				$data['number']=$num;
				$data['price']= $data['product_price']*$data['number'];
				$rs = $this->dao->add($data);
			}
			
			$res['data']= $rs ? 1 : 0 ;
			echo json_encode($res); exit;		
		}elseif($do=='update'){
			$data = $this->dao->find($id);
			$data['number']=$num;
			$data['price']=$data['product_price']*$data['number'];
			$rs = $this->dao->save($data);
			$res['data']= $rs ? 1 : 0 ;
			echo json_encode($res); exit;
		}elseif($do=='del'){
			$rs = $this->dao->delete($id);
			$res['data']= $rs ? 1 : 0 ;
			echo json_encode($res); exit;
		} 
 
	}
	public function clear(){
		$this->dao->where("sessionid = '$this->sessionid'")->delete();
		$this->error ( L('do_ok'));
	}

	public function done(){
	

		if($this->Config['isuserbuy'] && empty($this->_userid))$this->error ( L('NOLOGIN'));

		$model = M('Order');
		$userid = intval($this->_userid) ;
		if($userid){			
			$user = M('User')->find($userid);
			if (!$user){ $this->assign('jumpUrl',URL('User-Login/index'));$this->error ( L('NOLOGIN'));}
		}


		/* 检查购物车中是否有商品 */
		$cart_count = $this->dao->where("sessionid = '$this->sessionid'")->count();
		if ($cart_count == 0) $this->error ( L('ORDER_NO_PRODUCT'));

		 /* 检查收货人信息是否完整 */
		if($this->Config['use_address']){
			if($userid){
				$address = M('User_address')->where("userid='$this->_userid' AND isdefault='1' ")->find();
			}else{
				$address = unserialize( cookie('guest_address'));
			}
			if(!$address['province'] || !$address['city'] || !$address['area'] || !$address['address'] || !$address['consignee'] || !$address['mobile']){
				$this->assign('jumpUrl',URL('Home-Order/checkout'));
				$this->error ( L('SHIPPING_ADDRESS_NO_FULL'));
			}
		}else{
			$address=$_POST;
		}

		$order=array();
		/*商品金额*/
		$cart = $this->dao->where("sessionid='{$this->sessionid}'")->select();
		$amount=0;
		foreach($cart as $key=>$r)	$amount = $amount+$r['price'];

		/*配送方式*/
		$shippingid= intval($_POST['shipping_id']);
		$Shipping = M('Shipping')->find($shippingid);
		
		/*保价*/
		if(intval($_POST['isinsure'])){ 
			$insure_fee = $amount*$Shipping['insure_fee']/100;
			$insure_fee =  number_format($insure_fee,2);
			if($insure_fee<=$Shipping['insure_low_price']) $insure_fee=$Shipping['insure_low_price']; 
			$order['insure_fee'] = $insure_fee;
		}

		/*支付方式*/
		$paymentid= intval($_POST['payment']);
		$Payment = M('Payment')->find($paymentid);

		/*发票*/
		$order['invoice'] = intval($_POST['invoice']);
		if($order['invoice']){			
			$order['invoice_title']= htmlspecialchars($_POST['invoice_title']);
			$order['invoice_fee'] = $amount*$_POST['invoice_fee']/100;
			$order['invoice_fee'] =  number_format($order['invoice_fee'],2);
		}
		
		$order['amount'] = $amount;
	
		$order['shipping_fee'] = number_format($Shipping['first_price'],2);	
		$order['order_amount'] = $order['amount']+$order['invoice_fee']+$order['insure_fee']+$order['shipping_fee'];
		
		/*发票*/
		if($Payment['pay_fee']){
			$order['pay_fee'] = $Payment['pay_fee_type'] ?  $Payment['pay_fee'] : $order['order_amount']*$Payment['pay_fee']/100;
			$order['pay_fee'] = number_format($order['pay_fee'],2);
		}
		$order['order_amount'] = $order['order_amount']+$order['pay_fee'];
	
		$order['userid'] = intval($userid);
		$order['status'] = 0;
		$order['pay_status']= 0;
		$order['shipping_status']= 0;

		$order['consignee'] = $address['consignee'];
		$order['country'] =  intval($address['country']);
		$order['province']  =  intval($address['province']);
		$order['city'] =  intval($address['city']);
		$order['area'] =  intval($address['area']);
		$order['address'] =  $address['address'];
		$order['zipcode'] =  $address['zipcode'];
		$order['tel'] =  $address['tel'];
		$order['mobile'] =  $address['mobile'];
		$order['email'] =  $address['email'];

		$order['shipping_id'] =  intval($Shipping['id']);
		$order['shipping_name'] =  $Shipping['name'] ?  $Shipping['name'] : '';
 
		$order['pay_id'] =  intval($Payment['id']);
		$order['pay_name'] =  $Payment['pay_name'] ? $Payment['pay_name'] : '';
		$order['pay_code'] =  $Payment['pay_code'] ? $Payment['pay_code'] : '';
		$order['postmessage'] =  htmlspecialchars($_POST['postmessage']);
		

		$order['add_time'] =  time();
		foreach($order as $key=>$r){if($r==null)$order[$key]='';}
		$orderid= $model->add($order);
		if($orderid){
			$order['sn'] = date("Ymd"). sprintf('%06d',$orderid); 
			$model->save(array('id'=>$orderid,'sn'=>$order['sn']));
			foreach($cart as $key=>$r){
				$cart[$key]['order_id']=$orderid;
				$cart[$key]['userid']=$userid;
				M('Order_data')->add($cart[$key]);
			}
			$this->dao->where("sessionid = '$this->sessionid'")->delete();
			
			if($order['pay_id']){
				if($order['pay_code']=='Balance'){				
					if( $order['order_amount']>0 && $order['order_amount'] <= $user['amount']){
						//减用户余额
						$r =M('User')->where("userid = '$userid'")->setDec('amount',$order['order_amount']);
						if($r){
							$orderup['id'] = $orderid;
							$orderup['status'] = 1;
							$orderup['pay_status'] = 2;
							$orderup['pay_time'] =time();
							$model->save($orderup);
						}else{
							$this->error ( L('do_error'));
						}
					}else{					
						$paybutton='<span><input type="button"  class="button" onclick="window.location.href =\''.URL("User-Pay/Recharge").'\'" value="'.L('Recharge').'" /></span>';
						$this->assign('paybutton',$paybutton);
					}
				}else{
					$pay_code = $order['pay_code'];
					$aliapy_config = unserialize($Payment['pay_config']);
					$aliapy_config['order_sn']= $order['sn'];
					$aliapy_config['order_amount']= $order['order_amount'];
					$aliapy_config['body'] = $order['consignee'].' '.$order['postmessage'];
					import("@.Pay.".$pay_code);
					$pay=new $pay_code($aliapy_config);
					$paybutton = $pay->get_code();
					$this->assign('paybutton',$paybutton);
				}
			}
		}

		$this->assign('order',$order);
		$this->assign('cart',$cart);
		$this->display();
	}

}


?>