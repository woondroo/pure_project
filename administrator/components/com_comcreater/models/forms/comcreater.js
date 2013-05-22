window.addEvent('domready', function() {
	document.formvalidator.setHandler('title',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
	if (parseInt(document.getElementById('jform_id').value) > 0) {
		document.getElementById('jform_title').readOnly = 'readonly';
	}
});
