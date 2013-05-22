/**
 * @version		$Id: validate.js 19871 2010-12-14 01:53:28Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

Object.append(Browser.Features, {
	inputemail: (function() {
		var i = document.createElement("input");
		i.setAttribute("type", "email");
		return i.type !== "text";
	})()
});

/**
 * Unobtrusive Form Validation library
 *
 * Inspired by: Chris Campbell <www.particletree.com>
 *
 * @package		Joomla.Framework
 * @subpackage	Forms
 * @since		1.5
 */
var JFormValidator = new Class({
	initialize: function()
	{
		// Initialize variables
		this.handlers	= Object();
		this.custom		= Object();

		// Default handlers
		this.setHandler('username',
			function (value) {
				regex = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&]", "i");
				return !regex.test(value);
			}
		);

		this.setHandler('password',
			function (value) {
				regex=/^\S[\S ]{2,98}\S$/;
				return regex.test(value);
			}
		);

		this.setHandler('numeric',
			function (value) {
				regex=/^(\d|-)?(\d|,)*\.?\d*$/;
				return regex.test(value);
			}
		);

		this.setHandler('email',
			function (value) {
				regex=/^[a-zA-Z0-9._-]+(\+[a-zA-Z0-9._-]+)*@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
				return regex.test(value);
			}
		);

		// Attach to forms with class 'form-validate'
		var forms = $$('form.form-validate');
		forms.each(function(form){ this.attachToForm(form); }, this);
	},

	setHandler: function(name, fn, en)
	{
		en = (en == '') ? true : en;
		this.handlers[name] = { enabled: en, exec: fn };
	},

	attachToForm: function(form)
	{
		// Iterate through the form object and attach the validate method to all input fields.
		form.getElements('input,textarea,select,button').each(function(el){
			if (el.hasClass('required')) {
				el.set('aria-required', 'true');
				el.set('required', 'required');
			}
			if ((document.id(el).get('tag') == 'input' || document.id(el).get('tag') == 'button') && document.id(el).get('type') == 'submit') {
				if (el.hasClass('validate')) {
					el.onclick = function(){
						var check_result = document.formvalidator.isValid(this.form);
						if (check_result == false) {
							alert(language.check_form_fail);
						}
						return check_result;
					};
				}
			} else {
				el.addEvent('blur', function(){return document.formvalidator.validate(this);});
				if (el.hasClass('validate-email') && Browser.Features.inputemail) {
					el.type = 'email';
				}
			}
		});
	},

	validate: function(el)
	{
		if (el.get == null) return true;
		el = document.id(el);

		// Ignore the element if its currently disabled, because are not submitted for the http-request. For those case return always true.
		if(el.get('disabled')) {
			this.handleResponse(true, el);
			return true;
		}

		// If the field is required make sure it has a value
		if (el.hasClass('required')) {
			if (el.get('tag')=='fieldset' && (el.hasClass('radio') || el.hasClass('checkboxes'))) {
				for(var i=0;;i++) {
					if (document.id(el.get('id')+i)) {
						if (document.id(el.get('id')+i).checked) {
							break;
						}
					}
					else {
						this.handleResponse(false, el);
						return false;
					}
				}
			}
			else if (!(el.get('value'))) {
				this.handleResponse(false, el);
				return false;
			}
		}

		// Only validate the field if the validate class is set
		var handler = (el.className && el.className.search(/validate-([a-zA-Z0-9\_\-]+)/) != -1) ? el.className.match(/validate-([a-zA-Z0-9\_\-]+)/)[1] : "";
		if (handler == '') {
			this.handleResponse(true, el);
			return true;
		}

		// Check the additional validation types
		if ((handler) && (handler != 'none') && (this.handlers[handler]) && el.get('value')) {
			// Execute the validation handler and return result
			if (this.handlers[handler].exec(el.get('value')) != true) {
				this.handleResponse(false, el);
				return false;
			}
		}

		// Return validation state
		this.handleResponse(true, el);
		return true;
	},

	isValid: function(form)
	{
		var valid = true;

		// Validate form fields
		var elements = form.getElements('fieldset').concat(Array.from(form.elements));
		for (var i=0;i < elements.length; i++) {
			if (this.validate(elements[i]) == false) {
				valid = false;
			}
		}

		// Run custom form validators if present
		new Hash(this.custom).each(function(validator){
			if (validator.exec() != true) {
				valid = false;
			}
		});

		return valid;
	},

	handleResponse: function(state, el)
	{
		// Find the label object for the given field if it exists
		if (!(el.labelref)) {
			var labels = $$('label');
			labels.each(function(label){
				if (label.get('for') == el.get('id')) {
					el.labelref = label;
				}
			});
		}

		// Set the element and its label (if exists) invalid state
		if (state == false) {
			el.addClass('invalid');
			el.set('aria-invalid', 'true');
			if (el.labelref) {
				document.id(el.labelref).addClass('invalid');
				document.id(el.labelref).set('aria-invalid', 'true');
			}
		} else {
			el.removeClass('invalid');
			el.set('aria-invalid', 'false');
			if (el.labelref) {
				document.id(el.labelref).removeClass('invalid');
				document.id(el.labelref).set('aria-invalid', 'false');
			}
		}
	},
	
	removeItem: function(ele)
	{
		if (document.getElementById('productsif') != undefined) {
			var cb = eval('document.getElementById(\'productsif\').contentWindow.document.adminForm.'+jQuery(ele).parent().attr('cb'));
			if(cb){
				cb.checked=false;
			}
		}
		jQuery(ele).parent().remove();
	},
	
	initProPage: function() 
	{
		var pids = document.formvalidator.getParentIds();
		$('.adminlist tbody tr').each(function(){
			var box = $(this).find('td:eq(0) input');
			if(document.formvalidator.inarray(box.val(),pids)){
				box.attr('checked','checked');
				$('#div'+box.val(),parent.document).attr('cb',box.attr('id'));
			}
			box.click(function(){
				document.formvalidator.addParent(box,box.attr('checked'));
			});
		});
		$('input[type=checkbox][name=checkall-toggle]').click(function(){
			var checked = $(this).attr('checked');
			$('.adminlist tbody tr').each(function(){
				var box = $(this).find('td:eq(0) input');
				document.formvalidator.addParent(box,checked);
			});
		});
	},
	
	addParent: function(box,checked)
	{
		if(checked){
			if($('#div'+box.val(),parent.document).length==0){
				$('.choise_pros',parent.document).prepend('<div class="pitem" id="div'+box.val()+'" cb="'+box.attr('id')+'">'+box.parent().next().text()+'('+box.val()+')--<a class="cartdelete" href="javascript:;" onclick="document.formvalidator.removeItem(this);">删除</a><input type="hidden" value="'+box.val()+'" name="p[]"/></div>');
			}
		}else{
			$('#div'+box.val(),parent.document).remove();
		}
	},
	
	inarray: function(v,a) 
	{
		var l = a.length;
		for(i = 0;i<l;i++){
			if(a[i]==v)return true;
		}
		return false;
	},
	
	getParentIds: function()
	{
		var pids = new Array();
		$('.choise_pros input',parent.document).each(function(){
			pids[pids.length] = $(this).val();
		});
		return pids;
	}
});

document.formvalidator = null;
window.addEvent('domready', function(){
	document.formvalidator = new JFormValidator();
});
