<?php
/**
 * 
 * Alipay.php (模型表单生成)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2011-03-01 yourphp.cn $
 */
class Chinabank extends Think {
	public $config = array()  ;

    public function __construct($config=array()) {
         $this->config = $config;
 
		$this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = return_url('chinabank',1);
		$this->config['return_url'] = return_url('chinabank');

    }
	public function setup(){

		$modules['pay_name']    =  L('Chinabank_pay_name');   
		$modules['pay_code']    = 'Chinabank';
		$modules['pay_desc']    =  L('Chinabank_pay_desc');   
		$modules['is_cod']  = '0';
		$modules['is_online']  = '1';
		$modules['author']  = 'Yourphp';
		$modules['website'] = 'http://www.chinabank.com.cn';
		$modules['version'] = '1.0.0';
		$modules['config']  = array(
			 array('name' => 'chinabank_account', 'type' => 'text', 'value' => ''),
			 array('name' => 'chinabank_key',     'type' => 'text', 'value' => ''),
		);
		return $modules;
	}

	public function get_code($info,$value){
 
		
		$data_vid           = trim($this->config['chinabank_account']);
        $data_orderid       = $this->config['order_sn'];
        $data_vamount       = $this->config['order_amount'];
        $data_vmoneytype    = 'CNY';
        $data_vpaykey       = trim($this->config['chinabank_key']);

        $data_vreturnurl    = $this->config['return_url'];
		$remark1			= $this->config['body'];
        

        $MD5KEY =$data_vamount.$data_vmoneytype.$data_orderid.$data_vid.$data_vreturnurl.$data_vpaykey;
        $MD5KEY = strtoupper(md5($MD5KEY));

        $def_url  = '<span style="clean:both;"><form  method=post action="https://pay3.chinabank.com.cn/PayGate" target="_blank">';
        $def_url .= "<input type=HIDDEN name='v_mid' value='".$data_vid."'>";
        $def_url .= "<input type=HIDDEN name='v_oid' value='".$data_orderid."'>";
        $def_url .= "<input type=HIDDEN name='v_amount' value='".$data_vamount."'>";
        $def_url .= "<input type=HIDDEN name='v_moneytype'  value='".$data_vmoneytype."'>";
        $def_url .= "<input type=HIDDEN name='v_url'  value='".$data_vreturnurl."'>";
        $def_url .= "<input type=HIDDEN name='v_md5info' value='".$MD5KEY."'>";
        $def_url .= "<input type=HIDDEN name='remark1' value='".$remark1."'>";
        $def_url .= "<input type=submit class='button' value='" .L('PAY_NOW'). "'>";
        $def_url .= "</form></span>";

        return $def_url;

	}

	public function respond()
    {
        $v_oid          = trim($_POST['v_oid']); //订单编号
        $v_pmode        = trim($_POST['v_pmode']); //支付方式
        $v_pstatus      = trim($_POST['v_pstatus']); //支付状态 20（表示支付成功）30（表示支付失败）
        $v_pstring      = trim($_POST['v_pstring']); //支付结果信息
        $v_amount       = trim($_POST['v_amount']); //订单总金额
        $v_moneytype    = trim($_POST['v_moneytype']); //币种
        $remark1        = trim($_POST['remark1' ]); //备注字段1
        $remark2        = trim($_POST['remark2' ]); //备注字段2
        $v_md5str       = trim($_POST['v_md5str' ]); //订单MD5校验码

        /**
         * 重新计算md5的值
         */
        $key            = $this->config['chinabank_key'];

        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

        /* 检查秘钥是否正确 */
        if ($v_md5str==$md5string)
        {
            if ($v_pstatus == '20')
            {
				order_pay_status($v_oid,'2');
                return true;
            }
        }
        else
        {
            return false;
        } 
	}	
}
?>