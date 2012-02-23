/*
hdPic:高清组图专用脚本
@版本:hd2011_v2.7.2
@作者:tomiezhang#tencent.com
@时间:2011-3-18
*/
/*json文件开始*/
JSON=new function(){
	this.decode=function(){var filter,result,self,tmp;if($$("toString")){switch(arguments.length){case 2:self=arguments[0];filter=arguments[1];break;case 1:if($[typeof arguments[0]](arguments[0])===Function){self=this;filter=arguments[0]}else self=arguments[0];break;default:self=this;break};if(rc.test(self)){try{result=e("(".concat(self,")"));if(filter&&result!==null&&(tmp=$[typeof result](result))&&(tmp===Array||tmp===Object)){for(self in result)result[self]=v(self,result)?filter(self,result[self]):result[self]}}catch(z){}}else{throw new JSONError("bad data");}};return result};
	this.encode=function(){var self=arguments.length?arguments[0]:this,result,tmp;if(self===null)result="null";else if(self!==undefined&&(tmp=$[typeof self](self))){switch(tmp){case Array:result=[];for(var i=0,j=0,k=self.length;j<k;j++){if(self[j]!==undefined&&(tmp=JSON.encode(self[j])))result[i++]=tmp};result="[".concat(result.join(","),"]");break;case Boolean:result=String(self);break;case Date:result='"'.concat(self.getFullYear(),'-',d(self.getMonth()+1),'-',d(self.getDate()),'T',d(self.getHours()),':',d(self.getMinutes()),':',d(self.getSeconds()),'"');break;case Function:break;case Number:result=isFinite(self)?String(self):"null";break;case String:result='"'.concat(self.replace(rs,s).replace(ru,u),'"');break;default:var i=0,key;result=[];for(key in self){if(self[key]!==undefined&&(tmp=JSON.encode(self[key])))result[i++]='"'.concat(key.replace(rs,s).replace(ru,u),'":',tmp)};result="{".concat(result.join(","),"}");break}};return result};
	this.toDate=function(){var self=arguments.length?arguments[0]:this,result;if(rd.test(self)){result=new Date;result.setHours(i(self,11,2));result.setMinutes(i(self,14,2));result.setSeconds(i(self,17,2));result.setMonth(i(self,5,2)-1);result.setDate(i(self,8,2));result.setFullYear(i(self,0,4))}else if(rt.test(self))result=new Date(self*1000);return result};
	var c={"\b":"b","\t":"t","\n":"n","\f":"f","\r":"r",'"':'"',"\\":"\\","/":"/"},d=function(n){return n<10?"0".concat(n):n},e=function(c,f,e){e=eval;delete eval;if(typeof eval==="undefined")eval=e;f=eval(""+c);eval=e;return f},i=function(e,p,l){return 1*e.substr(p,l)},p=["","000","00","0",""],rc=null,rd=/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/,rs=/(\x5c|\x2F|\x22|[\x0c-\x0d]|[\x08-\x0a])/g,rt=/^([0-9]+|[0-9]+[,\.][0-9]{1,3})$/,ru=/([\x00-\x07]|\x0b|[\x0e-\x1f])/g,s=function(i,d){return"\\".concat(c[d])},u=function(i,d){var n=d.charCodeAt(0).toString(16);return"\\u".concat(p[n.length],n)},v=function(k,v){return $[typeof result](result)!==Function&&(v.hasOwnProperty?v.hasOwnProperty(k):v.constructor.prototype[k]!==v[k])},$={"boolean":function(){return Boolean},"function":function(){return Function},"number":function(){return Number},"object":function(o){return o instanceof o.constructor?o.constructor:null},"string":function(){return String},"undefined":function(){return null}},$$=function(m){function $(c,t){t=c[m];delete c[m];try{e(c)}catch(z){c[m]=t;return 1}};return $(Array)&&$(Object)};try{rc=new RegExp('^("(\\\\.|[^"\\\\\\n\\r])*?"|[,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t])+?$')}catch(z){rc=/^(true|false|null|\[.*\]|\{.*\}|".*"|\d+|\d+\.\d+)$/}
	};

/*json文件结束*/
	var indexPic = 0;
	var loadingProcess= {//全屏播放用
		isJsReady : false,
		isSwfReady : false,
		divName : 'fullSwf',
		swfUrl : '/Yourphp/Tpl/default/Public/images/picViewsFullScreenv1.0.0.1.0.swf',
		sitePicUrl : '#',
		lastUrl : '#',
		datas : null,
		flashNub:0,
		isFlashReady: function() {
			loadingProcess.isSwfReady = true;
			return loadingProcess.isJsReady;
		},
		setPicHandler : function () {
			var numargs = arguments.length;
			if (typeof window.document.setSoScreen.loadFullScreen != 'undefined') {
				if (numargs >= 1) {
					indexPic = arguments[0];
				}
				window.document.setSoScreen.loadFullScreen(loadingProcess.datas, indexPic);
			} else {
				setTimeout("loadingProcess.setPicHandler()", 300);
			}
		},
		addSwfHandler : function () {
			loadingProcess.swfUrl = ROOT+loadingProcess.swfUrl;
			var flashvars = { };
			var params = { };
			var attributes = { };
			flashvars.fristTips = "第一张";	
			flashvars.lastTips = "最后一张";	
			flashvars.gotoUrl = loadingProcess.sitePicUrl;	
			flashvars.picUrl = loadingProcess.lastUrl;	
			params.wmode = "window";
			params.allowFullScreen = "true";
			params.allowScriptAccess = "always";
			params.allowNetworking = "all"
			attributes.id = "setSoScreen";
			swfobject.embedSWF(loadingProcess.swfUrl, loadingProcess.divName, "48px", "12px", "9.0.0", "#000000", flashvars, params, attributes);			 
		},
		setTitle : function() {
			var title = document.title.replace(/#p.\d/i,"");
			document.title = title;
		},
		callByFullScreen : function (indexId, isExiting) {
			var deDatas = JSON.decode(loadingProcess.datas);
			//hdPic.fn._falshInt(deDatas);
			hdPic.fn._showBig(deDatas,indexId);
			
		},
		setFullScreenDatas:function (data) {
			loadingProcess.datas = JSON.encode(data);
			
		},
		initSystems : function (){
			loadingProcess.addSwfHandler();
		}
	};
	var hdPic = window.hdPic = function(p){
		return hdPic.fn.init(p);
	};
hdPic.fn=hdPic.prototype = {

			_tmpArray:[],
			_lastUrl:"",
			_lastTitle:"",
			_isgoOn:false,
			_coentArray:"",
			_pageNow:0,
			_isMove:false,
			_dragx:0,
			_isAuto:false,
			_autoTimer:null,
			_nowSrc:new Image(),
			_preloadN:new Image(),
			_preloadP:new Image(),
			_sourName:"",
			_sourUrl:"",
			_pubTime:"",
			_siteName:"",
			_siteLink:"",
			_isPic:true,
			_isCiment:false,
			_aid:0,
		    _siteEname:"news",
			_auth:"",

			_maxdragwidth:0,
			_playshownum: 4,
			_scrollButton: ".scrollButton",
			_playbox: "#playbox",
			_smallimgwidth: 0,
			_imgmaxwidth:0,
			_imgmaxheight:0,

		_getReady:function(){//大图首次载入ready，初始化播放器区域高度，图片切换效果、hover效果
					$(".pageLeft-bg").show();
					$(".pageRight-bg").show();
					$(".pageLeft").height($(hdPic.fn._playbox).height());
					$(".pageLeft span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageLeft-bg").height($(hdPic.fn._playbox).height());
					$(".pageRight").height($(hdPic.fn._playbox).height());
					$(".pageRight span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageRight-bg").height($(hdPic.fn._playbox).height());
					$("#mouseOverleft").height($(hdPic.fn._playbox).height());
					$("#mouseOverleft").width(parseInt($(hdPic.fn._playbox).width()/2));
					$("#mouseOverright").height($(hdPic.fn._playbox).height());
					$("#mouseOverright").width(parseInt($(hdPic.fn._playbox).width()/2));
					$("#mouseOverleft").hover(function(){
						$(".pageLeft").fadeIn("fast");
					},function(){
						$(".pageLeft").fadeOut("fast");
					});
					$("#mouseOverright").hover(function(){
						$(".pageRight").fadeIn("fast");
					},function(){
						$(".pageRight").fadeOut("fast");
					}); 
		},
		_getLast:function(data){//末页推荐
			$("#end").css("left",parseInt(($(hdPic.fn._playbox).width()/2-$("#end").width()/2))+"px");
			$(hdPic.fn._playbox).height($(hdPic.fn._playbox).height());
			$("#end").animate({top:"114px"},"slow",function(){
					$(".pageLeft").height($(hdPic.fn._playbox).height());
					$(".pageLeft span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageLeft-bg").height($(hdPic.fn._playbox).height());
					$(".pageRight").height($(hdPic.fn._playbox).height());
					$(".pageRight span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageRight-bg").height($(hdPic.fn._playbox).height());
					$("#mouseOverleft").height($(hdPic.fn._playbox).height());
					$("#mouseOverright").height($(hdPic.fn._playbox).height());
			});
		  $(".firstImg").html("<img src='"+data[0].smallpic+"' width=86 height=56/>");
		  $("h2").html($("h1").html());
		  $("#replayPic").bind("click",function(){
				hdPic.fn._hideLast();
				hdPic.fn._pageNow = 0;
				hdPic.fn._showBig(data,hdPic.fn._pageNow);
		  });
		  $("a.close").bind("click",function(){
				hdPic.fn._hideLast();
				hdPic.fn._showBig(data,hdPic.fn._pageNow);
		  });
		},
		_hideLast:function(){//隐藏末页推荐
			$("#end").animate({top:"-1828px"},"slow",function(){
					$(".pageLeft").height($(hdPic.fn._playbox).height());
					$(".pageLeft span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageLeft-bg").height($(hdPic.fn._playbox).height());
					$(".pageRight").height($(hdPic.fn._playbox).height());
					$(".pageRight span").css("marginTop",parseInt(($(hdPic.fn._playbox).height()-95)/2));
					$(".pageRight-bg").height($(hdPic.fn._playbox).height());
					$("#mouseOverleft").height($(hdPic.fn._playbox).height());
					$("#mouseOverright").height($(hdPic.fn._playbox).height());
			});
		},
		_clickleft:function(data){//向前点
			if(hdPic.fn._pageNow>0){
						hdPic.fn._pageNow--;
					}else{
						hdPic.fn._pageNow = 0;
					}
				hdPic.fn._showBig(data,hdPic.fn._pageNow);
		},
		_clickright:function(data){//向后点
			if(hdPic.fn._pageNow<data.length-1){
						hdPic.fn._pageNow++;
						hdPic.fn._showBig(data,hdPic.fn._pageNow);
					}else{
						this._getLast(data);
					}
		},
		_bindClick:function(data){//为各种按钮绑定事件、拖拽浏览、快捷键、页面初始焦点
			$("#Smailllist li").each(function(i){
				$(this).click(function(){
					hdPic.fn._stopAuto();
					hdPic.fn._showBig(data,i);
				})
			});
			$("#mouseOverright").bind('click',function(){
				hdPic.fn._stopAuto();
				hdPic.fn._clickright(data);
			});
			$("#goright").bind('click',function(){
				hdPic.fn._stopAuto();
				hdPic.fn._clickright(data);
			});
			$("#mouseOverleft").bind('click',function(){
				hdPic.fn._stopAuto();
				hdPic.fn._clickleft(data);
			});
			$("#goleft").bind('click',function(){
				hdPic.fn._stopAuto();
				hdPic.fn._clickleft(data);	
			});
			//拖拽浏览
			if(hdPic.fn._tmpArray.length>hdPic.fn._playshownum){
				$(hdPic.fn._scrollButton).bind("selectstart",function(){return false;})
				$(hdPic.fn._scrollButton).click(function(){}).mousedown(function(e){
						hdPic.fn._stopAuto();
					  //设置捕获范围
					  if($(hdPic.fn._scrollButton).setCapture){
						  $(hdPic.fn._scrollButton).setCapture();
					  }else if(window.captureEvents){
						  window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
					  }
					hdPic.fn._isMove = true;
					hdPic.fn._dragx = e.pageX-parseInt($(hdPic.fn._scrollButton).css("left"));
					$(hdPic.fn._scrollButton).fadeTo(20, 0.5);
					$("a.mask").hide();
				});
				 $(document).mousemove(function(e){
					if(hdPic.fn._isMove){
						var x=Math.max(0, Math.min(e.pageX-hdPic.fn._dragx,hdPic.fn._maxdragwidth));
						 $(hdPic.fn._scrollButton).css({left:x});
						hdPic.fn._dragmov();
					}
				 }).mouseup(function(){
					hdPic.fn._isMove=false;
					//取消捕获范围
					 if($(hdPic.fn._scrollButton).releaseCapture){
						  $(hdPic.fn._scrollButton).releaseCapture();
					   }else if(window.captureEvents){
						  window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
					  }
					$(hdPic.fn._scrollButton).fadeTo("fast", 1);
					if(parseInt($("#Smailllist").css("left"))%hdPic.fn._smallimgwidth!==0){
						var argleft = parseInt($("#Smailllist").css("left"));
						$("#Smailllist").animate({left:argleft+(Math.abs(parseInt($("#Smailllist").css("left"))%hdPic.fn._smallimgwidth))+"px"},"fast");
					};
				 })
			};
			//自动播放
			$(".play").click(function(){
				if(!hdPic.fn._isAuto){
					hdPic.fn._autoplay(data);
				}else{
					hdPic.fn._stopAuto();
				}
			});
			//快捷键
			$(document).bind("keydown",function(e){
				e = window.event || e;
				hdPic.fn._stopAuto();
				e.keyCode == 37 && hdPic.fn._clickleft(data);
				e.keyCode == 39 && hdPic.fn._clickright(data);
				e.keyCode == 38 && hdPic.fn._clickleft(data);
				e.keyCode == 40 && hdPic.fn._clickright(data);
			});
			//焦点
			var scrollPos; 
			if(typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') { 
				scrollPos = document.documentElement; 
			} 
			else if (typeof document.body != 'undefined') { 
				scrollPos = document.body; 
			} 
			var _topnav = $("#toolBar").offset().top;
			$(scrollPos).animate({scrollTop:_topnav - 10}, 1000);
		},
		_stopAuto:function(){//停止自动播放
			$(".play").html("幻灯播放");
			$(".play").removeClass("stop");
			hdPic.fn._isAuto = false;
			window.clearInterval(hdPic.fn._autoTimer);
		},
		_autoplay:function(data){//自动播放
			$(".play").html("停止播放");
			$(".play").addClass("stop");
			hdPic.fn._isAuto = true;
				this._autoTimer = window.setInterval(function(){
					if(hdPic.fn._pageNow<data.length-1){
						hdPic.fn._pageNow++;
						hdPic.fn._showBig(data,hdPic.fn._pageNow);
					}else{
						hdPic.fn._stopAuto();
						hdPic.fn._getLast(data);
					}
					
				},5000)
		},
		_dragmov:function(){//拖拽浏览用函数
			 var a = parseInt(hdPic.fn._smallimgwidth * (parseInt($(hdPic.fn._scrollButton).css("left"))/hdPic.fn._maxdragwidth)*(this._tmpArray.length - hdPic.fn._playshownum));
			 $("#Smailllist").css({left:-a+"px"});
		},
		_creatUrl:function(n){//创建组图浏览url标识
			var _org = /\#p\=/i.test(window.location.href);
			if(!_org){
				window.location.href = window.location.href+"#p=1";
			}else{
				window.location.href = window.location.href.split("#p=")[0] + "#p="+parseInt(n+1);
			}
			//hdPic.fn._countPV(parseInt(n+1));//统计PGV
		},
		_getUrl:function(){//获得组图url标识
			var str = window.location.href.toString(),pos = str.indexOf("#p=");
			var nub = 1;
			if(pos!==-1){
					nub=str.match(/\#p\=(\d{1,})/i)[1];
				}
			return nub;
		},
		_Pload:function(data,n){//预加载前后
			if(data.length>3){//大于3张 才预加载
				if (n != Number(data.length - 1)) {
					this._preloadN.src = data[n + 1].bigpic
				}
			}
		},
		_showBig:function(data,n){//显示大图、显示成功后设置索引值对应的图注、url、组图当前索引值改写、小图位置、统计
			indexPic = n;

			$("#orgPic").attr("href",data[n].bigpic);
			$("#PicSrc").attr("src","http://demo.yourphp.cn/Public/images/msg_loading.gif");
			hdPic.fn._Pload(data,n); //预加载

			$("#PicSrc").load(function(){
				hdPic.fn._autoSca($(this),data[n].bigpic);
				//if($.browser.msie && $.browser.version < 7){
					//ow = $(this).width();
					//oh = $(this).height(); 
					//if(ow >=hdPic.fn._imgmaxwidth){$(this).width(hdPic.fn._imgmaxwidth);}					
				//}
				$(this).height()>hdPic.fn._imgmaxheight? $(hdPic.fn._playbox).height($(this).height()):$(hdPic.fn._playbox).height(hdPic.fn._imgmaxheight);
				$(this).css("margin-top",parseInt($(hdPic.fn._playbox).height()-$(this).height())/2+"px");
			});
			$("#PicSrc").fadeTo("fast", 0, function(){
				
					hdPic.fn._pageNow = n;
					hdPic.fn._showSmall(n);
					hdPic.fn._creatUrl(n);
					$("#PicSrc").attr("src",data[n].bigpic);
					$("#PicSrc").fadeTo("fast",1);
					
			 });

		},
		_autoSca:function($this,src){
			var img = new Image();
			img.src = src;
			if (img.width > 0 && img.height > 0) {//都大于0
				if(img.width > hdPic.fn._imgmaxwidth){
				$this.width(hdPic.fn._imgmaxwidth)
				}else{
				$this.width(img.width)
				}
			}
		},
		_showSmall:function(n){//小图移动切换逻辑
			$("a.mask").show();
			if(hdPic.fn._tmpArray.length<=hdPic.fn._playshownum){
				$("a.mask").animate({left: (hdPic.fn._smallimgwidth*n)+"px"},"slow");
				return false;
			}
			var _left,_latsindex=hdPic.fn._playshownum;
			$("#Smailllist li").eq(n).addClass("on").siblings().removeClass("on");;
			if(n>=3 && n<(hdPic.fn._tmpArray.length)-3){//大于3小于倒数3
				$("#Smailllist").animate({left:-hdPic.fn._smallimgwidth*(n-3)+"px"},"slow",function(){
						_left = (hdPic.fn._smallimgwidth*3)+"px";
						$("a.mask").animate({left:_left},"fast");
						$(hdPic.fn._scrollButton).animate({left:_left},"fast");
				});
			}else{
				if(n>=(hdPic.fn._tmpArray.length)-3){
					_left = (hdPic.fn._smallimgwidth*(_latsindex-(hdPic.fn._tmpArray.length-n)))+"px";
					$("#Smailllist").animate({left:-(hdPic.fn._tmpArray.length-hdPic.fn._playshownum)*hdPic.fn._smallimgwidth+"px"},"slow");
					$("a.mask").animate({left:_left},"slow");
					$(hdPic.fn._scrollButton).animate({left:_left},"fast");
				}else{
					if(n<3){
						$("#Smailllist").animate({left:"0px"},"slow");
					}
						_left = (hdPic.fn._smallimgwidth*n)+"px";
						$("a.mask").animate({left:_left},"slow");
					
					$(hdPic.fn._scrollButton).animate({left:_left},"fast");
				}				
			}
			
		},
 
		_getData:function(data){//第一次加载后，初始化大图、小图、绑定事件、统计等
			if(data.length>0){
				/*成功*/
				$(hdPic.fn._playbox).append("<img src="+data[parseInt(hdPic.fn._getUrl()-1)].bigpic+" id='PicSrc' style='display:none;'/>");
				this._getReady();//大图ready事件
				this._small(data);//装载小图
				this._pageNow = parseInt(hdPic.fn._getUrl()-1);
				this._bindClick(data);
				$("#orgPic").attr("href",data[parseInt(hdPic.fn._getUrl()-1)].bigpic);
				this._showBig(data,parseInt(hdPic.fn._getUrl()-1));
			}
		},
		_falshInt:function(data){
			loadingProcess.initSystems();//初始化全屏按钮
			loadingProcess.isJsReady = true;
			loadingProcess.setFullScreenDatas(data);//全屏数据传递
		},
		_small:function(data){//第一次加载后初始化小图
			var _tmp="",ulLength=hdPic.fn._smallimgwidth*data.length;
			$.each(data,function(i){
					if(i==0){
					_tmp+='<li><span><a href="javascript:void(0);" class="select"  onfocus="this.blur()"><img src="'+data[i].smallpic+'"/><em>'+(i+1)+"/"+data.length+'</em></a></span></li>';
					}else{
					_tmp+='<li><span><a href="javascript:void(0);"  onfocus="this.blur()"><img src="'+data[i].smallpic+'"/><em>'+(i+1)+"/"+data.length+'</em></a></span></li>';
					}
			});
			hdPic.fn._maxdragwidth = $(hdPic.fn._scrollButton).parent().width()-$(hdPic.fn._scrollButton).width();
			$("#Smailllist").width(ulLength);
			$("#Smailllist").html(_tmp);
		},
		_init:function(){//第一次加载,数据格式化为数组 
			 var org = window.location.href;
			//alert(eval("(" + arguments[0] + ")"););
			 var length = $("#Smailllist").find('li').length;
			 hdPic.fn._smallimgwidth = $("#Smailllist li").width();
			 $("#Smailllist li img ").each(function(i){
				smallpic = $(this).attr("src");
				bigpic = $(this).attr("rel");
				showtit = $(this).attr("alt");
				hdPic.fn._tmpArray.push({'showtit':showtit,'showtxt': showtit,'smallpic': smallpic,'bigpic':bigpic});			 
			 });			
			 hdPic.fn._getData(hdPic.fn._tmpArray);
			 hdPic.fn._falshInt(hdPic.fn._tmpArray);
		},
		init:function(p){
			hdPic.fn.title = p.title;//标题;
			hdPic.fn.url = p.url;//Url;
			hdPic.fn.createtime = p.createtime;//发布时间;
			hdPic.fn.site_name = p.site_name;//站点名称
			hdPic.fn.site_url = p.site_url;//站点链接
			hdPic.fn._playshownum = 4;
			hdPic.fn._imgmaxwidth = 880,
			hdPic.fn._imgmaxheight = 600,
			hdPic.fn._scrollButton = ".scrollButton",
			hdPic.fn._playbox = "#playbox",
			hdPic.fn._init();
		}
	} 
	hdPic.fn.init.prototype = hdPic.fn;