$(document).ready(function(){
	if(document.getElementById("system-message"))
	{
		var message = $("#system-message dd ul li").html();
		alert( message );
	}
});