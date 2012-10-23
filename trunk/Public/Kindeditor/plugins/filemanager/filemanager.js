/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('filemanager', function(K) {
	var self = this, name = 'filemanager',
		fileManagerJson = K.undef(self.fileManagerJson, false),
		//imgPath = self.pluginsPath + name + '/images/',
		lang = self.lang(name + '.'),
		editorid = self.editorid; ;

	self.plugin.filemanagerDialog = function(options) {
		var width = K.undef(options.width, 600),
			height = K.undef(options.height, 440),
			upUrl = options.upUrl;
			clickFn = options.clickFn;
		var html = ['<div class="ke-map" style="width:600px;height:455px;"></div>'].join('');
		var dialog = self.createDialog({
			name : name,
			width : width,
			height : height,
			title : self.lang(name),
			body : html,
			yesBtn : {
				name : self.lang('yes'),
				click : function(e) {
					datadiv = K('#myuploadform div', doc);
					var num = datadiv.length;
					 
						var imgdata='' ,datas='' ,src,status,aid,name;
						datadiv.each(function() {
							src = $(this).find('#filedata').val();
							status =  $(this).find('#status').val();
							aid = $(this).find('#aids').val();							
							name = $(this).find('#namedata').val();
							if(status==0) datas += '<input type="text" name="aid[]" value="'+aid+'"/>';
							if(src) imgdata += '<p><img src="'+src+'" /></p>';
						});
					 
					
					oldaidhtml=K('#'+editorid+'_aid_box').html();
					K('#'+editorid+'_aid_box').html(oldaidhtml+datas);
					//self.insertHtml(imgdata).hideDialog().focus();					
					clickFn.call(this, src, name);
				}
			} 
		}),
		div = dialog.div,win, doc;
		var iframe = K('<iframe class="ke-textarea" frameborder="0" src="' + upUrl + '" style="width:600px;height:455px;border:none;"></iframe>');
		function ready() {
			win = iframe[0].contentWindow;
			doc = K.iframeDoc(iframe);
		}
		iframe.bind('load', function() {
			iframe.unbind('load');
			if (K.IE) {
				ready();
			} else {
				setTimeout(ready, 0);
			}
		});
		K('.ke-map', div).replaceWith(iframe);
		

}
});
