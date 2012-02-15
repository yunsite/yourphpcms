<?php
class AccessModel extends RelationModel {
	//权限
	protected $_link = array(
		"Role"=>array(
			"mapping_type"=>BELONGS_TO,
			"class_name"=>'Role',
			"foreign_key"=>'role_id',
			"mapping_name"=>'role',
			"as_fields"=>'name',
		),
		"Node"=>array(
			"mapping_type"=>BELONGS_TO,
			"class_name"=>'Node',
			"foreign_key"=>'node_id',
			"mapping_name"=>'node',
			"as_fields"=>'name:node_name,remark:node_remark',
		),
	);
}
?>