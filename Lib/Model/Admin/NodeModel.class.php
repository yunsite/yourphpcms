<?php
class NodeModel extends Model {
	/*
	 * 表单验证
	 */
	protected  $_validate = array(	
		array('name','require','{%node_name_empty}',1,'regex',3),
		array('title','require','{%node_title_empty}',1,'regex',3),
	);

}
?>