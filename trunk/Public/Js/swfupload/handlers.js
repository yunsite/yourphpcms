var ids =new Array();
function add_uploadok(data)
{ 
	var id = data.aid;
	var src = data.filepath;
	var ext =  data.isimage;
	var name = data.filename;
	var filesize = data.filesize;

	if(ext == 1) {
		var img = '<a href="javascript:void(0);" onclick="javascript:add_file(this,'+id+')" id="on_'+id+'" class="on"><div class="icon"></div><img src="'+src+'" width="80" alt="'+name+'" imgid="'+id+'" path="'+src+'"/></a>';
	} else {
		var img = '<a href="javascript:void(0);" onclick="javascript:add_file(this,'+id+')" id="on_'+id+'" class="on"><div class="icon"></div><img src="Public/images/ext/'+ext+'.png" width="80" alt="'+name+'" imgid="'+id+'" path="'+src+'"/></a>';
	}
	$('#thumbnails').append('<li><div id="attachment_'+id+'" class="img"></div></li>');
	$('#attachment_'+id).html(img);	
	var datas='<div id="uplist_'+id+'"><input type="hidden" name="status" id="status" value="0"><input type="hidden"  id="aids" name="aids[]"  value="'+id+'"  /><input type="text"  id="filedata" name="filedata[]" value="'+src+'"  /> <input type="text" id="namedata" name="namedata[]" value="'+name+'"  /> &nbsp;<a href="javascript:remove_this(\'uplist_'+id+'\');">移除</a> </div>';
	$('#myuploadform').append(datas);
	ids.push(id);	
}




function add_file(obj,id,status){
 	var src = $(obj).children("img").attr("path");
	var name = $(obj).children("img").attr("alt");
	var filesize =  $(obj).children("img").attr("imgsize"); 
	if($(obj).hasClass('on')){
		$(obj).removeClass("on");
		$('#myuploadform #uplist_'+id ).remove();
		for(var i=0;i<ids.length;i++){ if(ids[i]==id)ids.splice(i,1)}
	} else {
		var num = $('#myuploadform > div').length;
		if(num < file_limit){
			$(obj).addClass("on");
			var datas='<div id="uplist_'+id+'"><input type="hidden" name="status" id="status" value="'+status+'"><input type="hidden"  id="aids" name="aids[]"  value="'+id+'"  /><input type="text"  id="filedata" name="filedata[]" value="'+src+'"  /> <input type="text" id="namedata" name="namedata[]" value="'+name+'"  /> &nbsp;<a href="javascript:remove_this(\'uplist_'+id+'\');">移除</a> </div>';
			$('#myuploadform').append(datas);		
			ids.push(id);
		}else{
		alert('已经达到附件限制数');
		}
	}
}


function uploadSuccess(file, serverData) {
	try {
		
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		serverData = eval('('+serverData+')');

		if (serverData.status==1)
        { 			
			progress.setStatus(serverData.info);
			add_uploadok(serverData.data);

        }else{
			progress.setStatus(serverData.info);
        }
		//progress.setStatus("Complete.");
		progress.toggleCancel(false);
		

	} catch (ex) {
		this.debug(ex);
	}
}

function fileQueued(file) {
	try {
		//this.customSettings.tdFilesQueued.innerHTML = this.getStats().files_queued;
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("等待上传...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}
}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadStart(file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("正在上传请稍后...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("正在上传请稍后...");
	} catch (ex) {
		this.debug(ex);
	}
}


function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("上传错误: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("上传错误.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("服务器 I/O 错误");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("服务器安全认证错误");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("队列文件数量超过设定值.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("附件安全检测失败，上传终止.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			progress.setStatus("己取消上传");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("上传终止");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}

function uploadComplete(file) {
	if (this.getStats().files_queued > 0)
	{
		 this.startUpload();
	}

	if (this.getStats().files_queued === 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
	//this.customSettings.tdFilesQueued.innerHTML = this.getStats().files_queued-1;
	this.customSettings.tdFilesUploaded.innerHTML = this.getStats().successful_uploads+1;
	this.customSettings.tdErrors.innerHTML = this.getStats().upload_errors;
}



function fileDialogComplete(numFilesSelected, numFilesQueued) {
	this.customSettings.tdFilesQueued.innerHTML = this.getStats().files_queued; 
	try {
		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
		
		/* I want auto start the upload and I can do that here */
		//this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function preLoad() {
	if (!this.support.loading) {
		alert("You need the Flash Player 9.028 or above to use SWFUpload.");
		return false;
	}
}
function loadFailed() {
	alert("Something went wrong while loading SWFUpload. If this were a real application we'd clean up and then give you an alternative");
}