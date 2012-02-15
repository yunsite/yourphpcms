<?php
class FieldModel extends  Model {
	/*
	 * 表单验证
	 */
	protected  $_validate = array(
		array('type','require','{%field_empty_type}',1,'regex',1),
		array('name','require','{%field_empty_name}',1,'regex',3),
		array('field','require','{%field_empty_field}',1,'regex',1),
	);

	/*
	 * 自动完成
	 */
	protected $_auto=array(	
		//array('setup','array2string',3,'function'),
	);
 
}
?>