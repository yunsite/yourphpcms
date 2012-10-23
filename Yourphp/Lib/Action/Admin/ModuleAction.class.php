<?php
/**
 * 
 * Module(模型管理)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-10-08 yourphp.cn $
 */
if(!defined("Yourphp")) exit("Access Denied");
class ModuleAction extends AdminbaseAction {

	protected $dao;
    function _initialize()
    {	
		parent::_initialize();
		$this->dao=D('Admin/module');
		
		$this->Role = F('Role');
		$this->assign('Role',$this->Role);
    }

 
 	function _before_index(){
		if($_REQUEST ['type']){ 
			$_REQUEST['where'] = 'type='.intval($_REQUEST ['type']);
		}else{
			$_REQUEST['where'] = 'type=1';
		}
	
	}

	function _before_add(){
		if(empty($_GET['type'])) $_GET['type']=1;
	
	}

	function _before_update(){
		$_POST['postgroup'] = implode(',',$_POST['postgroup']);	
	}

	

	function insert(){	

		if($_GET['isajax']){
			$name=$_GET['name'];
			$tablename = C('DB_PREFIX').$name; 
			$db=D('');
			$db =   DB::getInstance();
			$tables = $db->getTables();
			if(in_array($tablename,$tables)){
				echo 'false';
			}else{
				echo 'true';
			}
			exit;
		}
		$db=D('');
		$db =   DB::getInstance();

		$tablename = C('DB_PREFIX').$_POST['name'];
		$_POST['name'] = ucfirst($_POST['name']);
		$model = $this->dao;
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$moduleid = $model->add();
		if(empty($moduleid)) $this->error (L('add_error').': '.$model->getDbError());

		if(empty($_POST['emptytable'])){
			$db->execute("CREATE TABLE `".$tablename."` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
			  `userid` int(8) unsigned NOT NULL DEFAULT '0',
			  `username` varchar(40) NOT NULL DEFAULT '',
			  `title` varchar(120) NOT NULL DEFAULT '',
			  `title_style` varchar(40) NOT NULL DEFAULT '',
			  `thumb` varchar(100) NOT NULL DEFAULT '',
			  `keywords` varchar(120) NOT NULL DEFAULT '',
			  `description` mediumtext NOT NULL,
			  `content` mediumtext NOT NULL,
			  `url` varchar(60) NOT NULL DEFAULT '',
			  `template` varchar(40) NOT NULL DEFAULT '', 
			  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `readgroup` varchar(100) NOT NULL DEFAULT '',
			  `readpoint` smallint(5) NOT NULL DEFAULT '0',
			  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
			  `hits` int(11) unsigned NOT NULL DEFAULT '0',
			  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
			  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
			  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `status` (`id`,`status`,`listorder`),
			  KEY `catid` (`id`,`catid`,`status`),
			  KEY `listorder` (`id`,`catid`,`status`,`listorder`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8");
					
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'catid', '".L('catid')."', '', '1', '1', '6', '', '".L('catid_error')."', '', 'catid', '','1','', '0', '1', '1')");	
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'title', '".L('title')."', '', '1', '1', '80', '', '".L('title_error')."', '', 'title', 'array (\n  \'thumb\' => \'1\',\n  \'style\' => \'1\',\n  \'size\' => \'55\',\n)','1','',  '0', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'keywords', '".L('keywords')."', '', '0', '0', '80', '', '', '', 'text', 'array (\n  \'size\' => \'55\',\n  \'default\' => \'\',\n  \'ispassword\' => \'0\',\n  \'fieldtype\' => \'varchar\',\n)','1','',  '2', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'description', '".L('description')."', '', '0', '0', '0', '', '', '', 'textarea', 'array (\n  \'fieldtype\' => \'mediumtext\',\n  \'rows\' => \'4\',\n  \'cols\' => \'55\',\n  \'default\' => \'\',\n)','1','',  '3', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'content', '".L('content')."', '', '0', '0', '0', '', '', '', 'editor', 'array (\n  \'toolbar\' => \'full\',\n  \'default\' => \'\',\n  \'height\' => \'\',\n  \'showpage\' => \'1\',\n  \'enablekeylink\' => \'0\',\n  \'replacenum\' => \'\',\n  \'enablesaveimage\' => \'0\',\n  \'flashupload\' => \'1\',\n  \'alowuploadexts\' => \'\',\n)','1','',  '10', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'createtime', '".L('createtime')."', '', '0', '0', '0', '', '', '', 'datetime', '','1','3,4',  '93', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'recommend', '".L('recommend')."', '', '0', '0', '1', '', '', '', 'radio', 'array (\n  \'options\' => \'".L('recommend')."|1\r\n".L('norecommend')."|0\',\n  \'fieldtype\' => \'tinyint\',\n  \'numbertype\' => \'1\',\n  \'labelwidth\' => \'\',\n  \'default\' => \'\',\n)','1','3,4', '93', '0', '0')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'readpoint', '".L('readpoint')."', '', '0', '0', '5', '', '', '', 'number', 'array (\n  \'size\' => \'5\',\n  \'numbertype\' => \'1\',\n  \'decimaldigits\' => \'0\',\n  \'default\' => \'0\',\n)','1','3,4', '93', '0', '0')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'hits', '".L('hits')."', '', '0', '0', '8', '', '', '', 'number', 'array (\n  \'size\' => \'10\',\n  \'numbertype\' => \'1\',\n  \'decimaldigits\' => \'0\',\n  \'default\' => \'0\',\n)','1','3,4',  '93', '0', '0')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'readgroup', '".L('readgroup')."', '', '0', '0', '0', '', '', '', 'groupid', 'array (\n  \'inputtype\' => \'checkbox\',\n  \'fieldtype\' => \'tinyint\',\n  \'labelwidth\' => \'85\',\n  \'default\' => \'\',\n)','1','3,4', '96', '0', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'posid', '".L('posid')."', '', '0', '0', '0', '', '', '', 'posid', '','1','', '97', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'template', '".L('template')."', '', '0', '0', '0', '', '', '', 'template', '','1','3,4', '98', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'status', '".L('status')."', '', '0', '0', '0', '', '', '', 'radio', 'array (\n  \'options\' => \'".L('fabu')."|1\r\n".L('dingshi')."|0\',\n  \'fieldtype\' => \'tinyint\',\n  \'numbertype\' => \'1\',\n  \'labelwidth\' => \'75\',\n  \'default\' => \'1\',\n)','1','3,4','99', '1', '1')");
		}else{
			$db->execute("CREATE TABLE `".$tablename."` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `userid` int(8) unsigned NOT NULL DEFAULT '0',
			  `username` varchar(40) NOT NULL DEFAULT '',
			  `url` varchar(60) NOT NULL DEFAULT '',
			  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
			  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
			  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
			  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'createtime', '".L('createtime')."', '', '1', '0', '0', '', '', '', 'datetime', '','0','3,4',  '93', '1', '1')");
			$db->execute("INSERT INTO `".C('DB_PREFIX')."field` VALUES ('', '".$moduleid."', 'status', '".L('status')."', '', '0', '0', '0', '', '', '', 'radio', 'array (\n  \'options\' => \'".L('isshenhe')."|1\r\n".L('noshenhe')."|0\',\n  \'fieldtype\' => \'tinyint\',\n  \'numbertype\' => \'1\',\n  \'labelwidth\' => \'75\',\n  \'default\' => \'1\',\n)','0','3,4', '99', '1', '1')");
		}

		if ($moduleid  !==false) {
			
			$menu=M('Menu');
			$data=array();
			$data['parentid'] =  $_POST['type']==2 ? 4 : 3;
			$data['model'] = $_POST['name'];
			$data['action'] = 'index';
			$data['type'] = 1;
			$data['status'] = 1;
			$data['name'] =  $_POST['title'];
			$data['listorder']=9;
			$pmenuid = $menu->data($data)->add();
			$data['parentid'] = $pmenuid;
			$data['action'] ='add';
			$data['name'] = L('module_addmenu');
			$menu->data($data)->add();
			savecache('Module');
			savecache('Menu');
			savecache('Field',$moduleid);
			$this->assign ( 'jumpUrl', U(MODULE_NAME.'/index') );
			$this->success (L('add_ok'));
		} else {
			$this->error (L('add_error').': '.$model->getDbError());
		}
	}

	function delete() {	
		$id =intval($_GET['id']);
		$r = $this->dao->find($id);
		if(empty($r)) $this->error (L('do_empty'));
		
		$tablename = C('DB_PREFIX').$this->module[$id]['name']; 
		$m = $this->dao->delete($id);
		if($m){
			$t =$this->dao->execute("DROP TABLE IF EXISTS `".$tablename."`");
			$module = ucfirst($this->module[$id]['name']);
			$menu = M('Menu');
			$menu->where("model='".$module."'")->delete();
			$module = M('Field');
			$f = $module->where('moduleid='.$id)->delete();

			savecache('Module');
			savecache('Menu');
			@unlink(RUNTIME_PATH.'Data/'.$id.'_Field.php');
			$this->success (L('do_ok'));
		}
	}

}
?>