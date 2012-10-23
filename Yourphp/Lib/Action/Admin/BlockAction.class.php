<?php
/**
 * 
 * Posid (推荐位管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class BlockAction extends AdminbaseAction {

	protected $dao,$Type;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao = M(MODULE_NAME);
		$this->Type=F('Type');

    }

	public function index() {  
		if(APP_LANG)$map['lang']=array('eq',LANG_ID);
		$this->_list(MODULE_NAME, $map);
        $this->display();
    }

	public function _before_insert()
    {
		 if(APP_LANG)$_POST['lang']=LANG_ID;
	}
	public function edit() {
		$pos=strip_tags($_REQUEST['pos']);
		$name = MODULE_NAME;
		$model = M ( $name );
		$pk=ucfirst($model->getPk ());
		$id = $_REQUEST [$model->getPk ()];
		if(empty($id))   $this->error(L('do_empty'));
		if($pos){
			$map['pos']=array('eq',$pos);
			if(APP_LANG)$map['lang']=array('eq',LANG_ID);
			$vo = $model->where($map)->find();
		}else{
			$do='getBy'.$pk;
			$vo = $model->$do ( $id );
		}
		if($vo['setup']) $vo['setup']=string2array($vo['setup']);
		$this->assign ( 'vo', $vo );
		$this->display ();
	}

	function delete(){
		$name = MODULE_NAME;
		$model = M ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		if (isset ( $id )) {
			if(false!==$model->delete($id)){
				delattach(array('moduleid'=>'231','id'=>$id));
				$this->success(L('delete_ok'));
			}else{
				$this->error(L('delete_error').': '.$model->getDbError());
			}
		}else{
			$this->error (L('do_empty'));
		}
	}
}
?>