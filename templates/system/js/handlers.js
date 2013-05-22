/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function preLoad() {
	if (!this.support.loading) {
		alert("You need the Flash Player 9.028 or above to use SWFUpload.");
		return false;
	}
}
function loadFailed() {
	alert("Something went wrong while loading SWFUpload. If this were a real application we'd clean up and then give you an alternative");
}

function fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("请稍等...");
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

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
		
		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
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
		progress.setStatus("正在上传...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		if (percent < 100) {
			canSave = false;
		} else {
			canSave = true;
		}
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		//progress.setStatus(percent+"%<br/>正在上传...");
		progress.setStatus('<div>'+percent+'%</div><div style="width:'+(bytesLoaded / bytesTotal)*350+'px;background:#FF0000;height:4px;"></div>');
		if (percent > 0) {
			this.settings.swf_upload_hidetime = 10;
			jQuery('#fsUploadProgress_'+this.settings.swfid).show('fast');
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function toHideField(get_settings) {
	//alert('to Hide time:' + get_settings.swf_upload_hidetime);
	if (get_settings.swf_upload_hidetime > 0) {
		get_settings.swf_is_hide_field_run = true;
		setTimeout(function(){
			get_settings.swf_upload_hidetime --;
			toHideField(get_settings);
		}, 1000);
	} else {
		get_settings.swf_is_hide_field_run = false;
		jQuery('#fsUploadProgress_'+get_settings.swfid).hide('slow');
	}
}

function uploadSuccess(file, serverData) {
	try {
		var json = eval('('+serverData+')');
		if (json.state == 200) {
			var base_folder = file_upload_baseurl;
			var get_file_input_id = eval("file_input_id_"+this.settings.swfid);
			var get_file_upload_folder = eval("file_upload_folder_"+this.settings.swfid);
			var get_file_view_id = eval("file_view_id_"+this.settings.swfid);
			if (get_file_input_id && document.getElementById(get_file_input_id) != undefined) {
				document.getElementById(get_file_input_id).value = base_folder+'/'+(get_file_upload_folder ? get_file_upload_folder : 'img')+'/'+json.filename;
			} else if (document.getElementById('filename') != undefined) {
				document.getElementById('filename').value = base_folder+'/'+(get_file_upload_folder ? get_file_upload_folder : 'img')+'/'+json.filename;
			}
			
			if (get_file_view_id && document.getElementById(get_file_view_id) != undefined) {
				document.getElementById(get_file_view_id).setAttribute('src','../'+base_folder+'/'+(get_file_upload_folder ? get_file_upload_folder : 'img')+'/'+json.filename);
			}
			document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setStatus("上传成功："+json.filename+"！");
			progress.toggleCancel(false);
		} else {
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setStatus("上传失败："+json.mess+"！");
			progress.toggleCancel(false);
		}
		
		if (this.settings.swf_is_hide_field_run == false) {
			toHideField(this.settings);
		}
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
			progress.setStatus("上传出错：" + message);
			this.debug("错误提示：HTTP错误，文件名: " + file.name + "，信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("上传失败！");
			this.debug("错误提示: 上传失败，文件名：" + file.name + "，文件大小：" + file.size + "，信息：" + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("服务器(IO)异常");
			this.debug("错误提示: IO异常，文件名：" + file.name + "，信息：" + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("错误提示: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("错误提示: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("已经取消");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
		
		if (this.settings.swf_is_hide_field_run == false) {
			toHideField(this.settings);
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	//var status = document.getElementById("divStatus");
	//status.innerHTML = numFilesUploaded + " 个文件已经上传";
	
	var get_uploaded_view_allimgs = eval("uploaded_view_allimgs_"+this.settings.swfid);
	if (get_uploaded_view_allimgs != null && get_uploaded_view_allimgs != "") {
		chngImage('nothing');
	}
}
