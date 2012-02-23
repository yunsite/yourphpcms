<?php
// +----------------------------------------------------------------------
// | Yourphp Cxml Library, by liuxun , version 1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.yourphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: liuxun <147613338@qq.com>
// +----------------------------------------------------------------------

class Cxml extends Think {

	public $root='rss';
	public $root_attributes=array();
    public $charset='utf-8';
	public $NodeName= 'item';
	public $dom;

    private function __constract() {
    }
    
    public function outHeader() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        header("Content-type: text/xml; charset=".$this->charset);
    }
    
    public function Cxml($data=null,$file='') {

        if (!is_array($data) || count($data) == 0) return false;
        $dom = new DOMDocument('1.0',$this->charset);
        
        //添加DOM根元素
        $resultElement = $dom->createElement($this->root);
		//设置DOM根元素属性
		$this->Attribute($this->root_attributes,$dom,$resultElement);
        //将数组转换为xml添加到根元素
        $this->Array2Xml($dom, $data, $resultElement);        
        //加入DOM对象
        $dom->appendChild($resultElement);
		if($file) {
			//生成xml文件
			$r = $dom->save($file);
			return $r ;
		}else{
			//输出XML显示
			$this->outHeader();
			return $dom->saveXML();
		}
	}
	public function Xml2Array($file=''){
		if(!is_file($file)) return false;
		//$dom = new DOMDocument('1.0',$this->charset);		
		//$array=$this->xml_to_array(simplexml_load_file($file));
		$array=$this->simplexml2array(simplexml_load_file($file));		
		return $array;
	}
	public function Array2Xml($dom, $data, $result='') {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {					
                    $NodeName = $value['NodeName']['value'];
					if($value['NodeName']['attributes'])$value['attributes'] =  $value['NodeName']['attributes'];
					unset($value['NodeName']);
                } else {
                    $NodeName = $key;
                }
                if (!isset($value['value'])) {
                    $key_Element = $dom->createElement($NodeName);
                    $result->appendChild($key_Element);
					if($value['attributes']){
						$this->Attribute($value['attributes'],$dom,$key_Element);
						unset($value['attributes']);
					}
                    $this->Array2Xml($dom, $value, $key_Element);
                } else {
                	$key_Element = $dom->createElement($NodeName);
					 
					if($value['ishtml']){
						$key_Element->appendChild($dom->createCDATASection($value['value']));
					}else{
						$key_Element->appendChild($dom->createTextNode($value['value']));
					}
					if($value['attributes']){
						$this->Attribute($value['attributes'],$dom,$key_Element);						
					}
                	$result->appendChild($key_Element);
                }
            }
            return $result;
        }
	}
	public function Attribute($att,$dom,$key_Element){	 
		$attributes_element='';
		foreach ($att as $key =>$rs){				
				$attributes_element = $dom->createAttribute($key);
				$attributes_element->appendChild($dom->createTextNode($rs));
				$key_Element->appendChild($attributes_element);
		}
	}

	public function simplexml_to_array($xml) { 
	   $ar = array(); 
	   foreach($xml->children() as $k => $v) { 

		   $child = simplexml_to_array($v); 
		   if( count($child) == 0 ) { 
			   $child = (string)$v;
		   } 
		   foreach( $v->attributes() as $ak => $av ) { 
			   if( !is_array( $child ) ) { 
				   $child = array( "value" => $child ); 
			   } 
			   $child[$ak] = (string)$av; 
		   } 
		   if (!in_array($k,array_keys($ar))) { 
			   $ar[$k] = $child;
		   }else{
				if($ar[$k][0]){
					$ar[$k][] = $child; 
				}else{
					$ar[$k] = array($ar[$k]); 
					$ar[$k][] = $child;
				}
		   } 

	   }
	   return $ar; 
	}

	public function simplexml2array($xml) { 
		$arXML=array(); 
		$arXML['name']=trim($xml->getName()); 
		$arXML['value']=trim((string)$xml); 
		$t=array(); 
		foreach($xml->attributes() as $name => $value){ 
			$t[$name]=trim($value); 
		} 
		$arXML['attr']=$t; 
		$t=array(); 
		foreach($xml->children() as $name => $xmlchild) { 

			if (!in_array($name,array_keys($t))) { 
			   $t[$name] = $this->simplexml2array($xmlchild);
			}else{
				if(!$t[$name][0]){
					$t[$name] = array($t[$name]);
				}
				$t[$name][]= $this->simplexml2array($xmlchild);
		   }
		}
		$arXML['children']=$t; 
		return($arXML); 
	} 




}
?>