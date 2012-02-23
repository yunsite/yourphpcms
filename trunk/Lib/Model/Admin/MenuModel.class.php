<?php
class MenuModel extends Model {
	/*
	 * 表单验证
	 */
	protected  $_validate = array(	
		array('name','require','{%menu_user_is_empty}',1,'regex',3),
		array('model','require','{%menu_model_is_empty}',1,'regex',3),
	);

}
?>