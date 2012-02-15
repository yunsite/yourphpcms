<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

class Page extends Think {
    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
	// 分页url定制
	protected $urlrule;
	// 分页显示定制
    protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$p='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = C('PAGE_ROLLPAGE');
        $this->listRows = !empty($listRows)?$listRows:C('PAGE_LISTROWS');
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
		if (!define('PAGESTOTAL')) define('PAGESTOTAL', $this->totalPages);
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
		if($p){
			$this->nowPage =$p;
			}else{
			$this->nowPage  = !empty($_GET[C('VAR_PAGE')])?intval($_GET[C('VAR_PAGE')]):1;
		}
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

 	public function show() {	
				
		$urlrule =  str_replace('%7B%24page%7D','{$page}',$this->urlrule); //urldecode	
		if(!$urlrule){		
			$p = C('VAR_PAGE');			
			$nowCoolPage      = ceil($this->nowPage/$this->rollPage);
			$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
			$parse = parse_url($url);
			if(isset($parse['query'])) {
				parse_str($parse['query'],$params);
				unset($params[$p]);
				$urlrule   =  $parse['path'].'?'.http_build_query($params);
			}
			$urlrule = $urlrule."&".$p.'={$page}';
		}
		$array = $this->parameter;
		$setpages =  $this->rollPage;

		$num =$this->totalRows;
		$perpage = $this->listRows;		
		$curr_page = $this->nowPage;

		$pageStr = '';
		if($num > $perpage) {
			$page = $setpages+1;
			$offset = ceil($setpages/2-1);
			$pages = ceil($num / $perpage);
			$from = $curr_page - $offset;
			$to = $curr_page + $offset;
			$more = 0;
			if($page >= $pages) {
				$from = 2;
				$to = $pages-1;
			} else {
				if($from <= 1) {
					$to = $page-1;
					$from = 2;
				}  elseif($to >= $pages) { 
					$from = $pages-($page-2);  
					$to = $pages-1;  
				}
				$more = 1;
			} 
			$pageStr .= '<a class="a1">'.$num.L('page_item').'</a>';
			if($curr_page>0) {
				$prepage=max(1,$curr_page-1);
				$pageStr .= ' <a href="'.$this->pageurl($urlrule, $prepage, $array).'" class="a1">'.L('previous').'</a>';
				if($curr_page==1) {
					$pageStr .= ' <span>1</span>';
				} elseif($curr_page>6 && $more) {
					$pageStr .= ' <a href="'.$this->pageurl($urlrule, 1, $array).'">1</a>..';
				} else {
					$pageStr .= ' <a href="'.$this->pageurl($urlrule, 1, $array).'">1</a>';
				}
			}
			for($i = $from; $i <= $to; $i++) { 
				if($i != $curr_page) { 
					$pageStr .= ' <a href="'.$this->pageurl($urlrule, $i, $array).'">'.$i.'</a>'; 
				} else { 
					$pageStr .= ' <span>'.$i.'</span>'; 
				} 
			} 
			if($curr_page<$pages) {
				if($curr_page<$pages-5 && $more) {
					$pageStr .= ' .. <a href="'.$this->pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$this->pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
				} else {
					$pageStr .= ' <a href="'.$this->pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$this->pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
				}
			} elseif($curr_page==$pages) {
				$pageStr .= ' <span>'.$pages.'</span> <a href="'.$this->pageurl($urlrule, $curr_page, $array).'" class="a1">'.L('next').'</a>';
			} else {
				$pageStr .= ' <a href="'.$this->pageurl($urlrule, $pages, $array).'">'.$pages.'</a> <a href="'.$this->pageurl($urlrule, $curr_page+1, $array).'" class="a1">'.L('next').'</a>';
			}
		}
		return $pageStr;
	}

	public function pageurl($urlrule, $page, $array = array())
	{
		@extract($array, EXTR_SKIP);
		if(is_array($urlrule))
		{
			//$urlrules = explode('|', $urlrule);
			$urlrule = $page < 2 ? $urlrule[0] : $urlrule[1];
		}
		$url = str_replace('{$page}', $page, $urlrule);
		return $url;
	}

}
?>