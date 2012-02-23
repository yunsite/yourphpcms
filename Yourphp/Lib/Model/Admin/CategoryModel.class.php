<?php
class CategoryModel extends Model {
	/*
	 * 表单验证
	 */
	protected  $_validate = array(	
		array('catname','require','{%catname_is_empty}',1,'regex',3),		 
	);
}
?>