/**
 * @version		$Id: popup-imagemanager.js 20828 2011-02-22 04:22:21Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JImageManager behavior for media component
 *
 * @package		Joomla.Extensions
 * @subpackage	Media
 * @since		1.5
 */

(function() {
var ImageManager = this.ImageManager = {
	initialize: function()
	{
		o = this._getUriObject(window.self.location.href);
		//console.log(o);
		q = new Hash(this._getQueryObject(o.query));
		/**
		 * 2011-xx-xx wengebin 编辑！
		 * 
		 * 新增两个参数，这两个参数分别是：图片插入的路径保存输入框对象、图片预览的图片对象。
		 */
		this.editor = decodeURIComponent(q.get('e_name'));
		this.browse = decodeURIComponent(q.get('e_show'));

		// Setup image manager fields object
		this.fields			= new Object();
		this.fields.url		= document.id("f_url");
		this.fields.alt		= document.id("f_alt");
		this.fields.align	= document.id("f_align");
		this.fields.title	= document.id("f_title");
		this.fields.caption	= document.id("f_caption");

		// Setup image listing objects
		//this.folderlist = document.id('folderlist');

		this.frame		= window.frames['imageframe'];
		this.frameurl	= this.frame.location.href;

		// Setup imave listing frame
		this.imageframe = document.id('imageframe');
		this.imageframe.manager = this;
		this.imageframe.addEvent('load', function(){ ImageManager.onloadimageview(); });

		// Setup folder up button
		/**
		 * 2012-xx-xx wengebin 新增!
		 * 
		 * 为“上级目录”按钮绑定事件进行目录跳转！
		 */
		this.upbutton = document.id('upbutton');
		this.upbutton.removeEvents('click');
		this.upbutton.addEvent('click', function(){ ImageManager.upFolder(); });
	},

	onloadimageview: function()
	{
		// Update the frame url
		this.frameurl = this.frame.location.href;

		var folder = this.getImageFolder();
		document.getElementById('view_current_folder').innerHTML = '/'+folder;
		document.getElementById('current_folder_val').value = folder;
		/**
		 * 2011-xx-xx wengebin 编辑!
		 * 
		 * 这段代码是Media 原始代码,用来遍历下拉框的选择项,然后选择目前所在的文件夹.
		 * 
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if(folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				break;
			}
		}
		*/

		a = this._getUriObject(document.id('uploadForm').getProperty('action'));
		//console.log(a);
		q = new Hash(this._getQueryObject(a.query));
		q.set('folder', folder);
		var query = [];
		q.each(function(v, k){
			if ($chk(v)) {
				this.push(k+'='+v);
			}
		}, query);
		a.query = query.join('&');
		var portString = '';
		if (typeof(a.port) !== 'undefined' && a.port != 80) {
			portString = ':'+a.port;
		}
		document.id('uploadForm').setProperty('action', a.scheme+'://'+a.domain+portString+a.path+'?'+a.query);
	},

	getImageFolder: function()
	{
		var url 	= this.frame.location.search.substring(1);
		var args	= this.parseQuery(url);

		return args['folder'];
	},

	onok: function()
	{
		extra = '';
		// Get the image tag field information
		var url		= this.fields.url.get('value');
		var alt		= this.fields.alt.get('value');
		var align	= this.fields.align.get('value');
		var title	= this.fields.title.get('value');
		var caption	= this.fields.caption.get('value');

		if (url != '') {
			// Set alt attribute
			if (alt != '') {
				extra = extra + 'alt="'+alt+'" ';
			} else {
				extra = extra + 'alt="" ';
			}
			// Set align attribute
			if (align != '') {
				extra = extra + 'align="'+align+'" ';
			}
			// Set align attribute
			if (title != '') {
				extra = extra + 'title="'+title+'" ';
			}
			// Set align attribute
			if (caption != '') {
				extra = extra + 'class="caption" ';
			}

			var tag = "<img src=\""+url+"\" "+extra+"/>";
		}
		
		/**
		 * 2012-04-12 wengebin 编辑!
		 * 
		 * 如果一个页面有多个编辑器，那么我们要将图片插入哪个编辑器呢？
		 * 这个时候我们就需要通过给图片插入方法进行重命名，然后动态调用对应的图片插入方法即可！
		 */
		var editor_str = this.editor.replace(/jform/g,'');
		editor_str = editor_str.replace(/params/g,'');
		editor_str = editor_str.replace(/\[/g,'');
		editor_str = editor_str.replace(/\]/g,'');
		eval('window.parent.jInsertEditorText_'+editor_str+'(tag, this.editor);');
		return false;
	},
	
	/**
	 * 2012-xx-xx wengebin 新增！
	 * 
	 * 当我们需要对使用Media组件上传图片，然后能够进行预览就必须重写图片插入方法，
	 * 与普通图片插入方法不一样的是，此方法会对预览区域的图片路径动态改变，修改成上传的图片路径。
	 */
	onok2: function()
	{
		// Get the image tag field information
		var url = this.fields.url.get('value');
		if(this.editor && this.editor != null) window.parent.document.getElementById(this.editor).value = url;
		if(this.browse && this.browse != null) window.parent.document.getElementById(this.browse).src='../' + url;
		return false;
	},

	setFolder: function(folder,asset,author)
	{
		//this.showMessage('Loading');
		/**
		 * 2011-xx-xx wengebin 编辑!
		 * 
		 * 修改该默认的文件夹选择方法，改为“上级目录”按钮进行文件夹跳转。
		 * 
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if(folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				break;
			}
		}
		*/
		this.frame.location.href='index.php?option=com_media&view=imagesList&tmpl=component&folder=' + folder + '&asset=' + asset + '&author=' + author;
	},

	getFolder: function() {
		//return this.folderlist.get('value');
		return '';
	},

	/**
	 * 2011-xx-xx wengebin 增加！
	 * 
	 * 通过当前URL的路径进行分析，然后跳转到当前路径的上级路径，但是如果需要跳转到的路径已经被锁定则无法跳转。
	 */
	upFolder: function()
	{
		var current_folder = document.getElementById('current_folder_val').value;
		var folders = current_folder.split('/');
		var folder = '';
		
		var lock_folders = lock_folder.split('/');
		if (lock_folder != null && lock_folder != '' && lock_folders.length < folders.length) {
			for(var i = 0; i < folders.length - 1; i++) {
				folder += folders[i];
				folder += '/';
			}
			folder = folder.substring(0, folder.length - 1);
			
			document.getElementById('view_current_folder').innerHTML = '/'+folder;
			document.getElementById('current_folder_val').value = folder;
			this.setFolder(folder);
		} else {
			alert('对不起，您不能访问上级目录！');
		}
	},

	populateFields: function(file)
	{
		document.id("f_url").value = image_base_path+file;
	},

	showMessage: function(text)
	{
		var message  = document.id('message');
		var messages = document.id('messages');

		if(message.firstChild)
			message.removeChild(message.firstChild);

		message.appendChild(document.createTextNode(text));
		messages.style.display = "block";
	},

	parseQuery: function(query)
	{
		var params = new Object();
		if (!query) {
			return params;
		}
		var pairs = query.split(/[;&]/);
		for ( var i = 0; i < pairs.length; i++ )
		{
			var KeyVal = pairs[i].split('=');
			if ( ! KeyVal || KeyVal.length != 2 ) {
				continue;
			}
			var key = unescape( KeyVal[0] );
			var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
			params[key] = val;
	   }
	   return params;
	},

	refreshFrame: function()
	{
		this._setFrameUrl();
	},

	_setFrameUrl: function(url)
	{
		if (url != null) {
			this.frameurl = url;
		}
		this.frame.location.href = this.frameurl;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	}
};
})(document.id);

window.addEvent('domready', function(){
	ImageManager.initialize();
});
