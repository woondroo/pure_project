<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
	<title>测试日期已经截止</title> 
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"> 
	<style type="text/css"><!--
	th {
	font-family: arial, verdena, sans-serif;
	}
	#divid{
		padding-left:16px;
		padding-top:40px;
		color:#222;
		text-align:left;
		font-weight:normal;
		background:url(expirebg.jpg) no-repeat;
	}
	#divid div{
		width:488px;
		height:141px;
	}
	a{
		color:#0B55C4;
	}
	--> </style> 
</head>
<body> 
<?php 
$url = urldecode($_REQUEST['url']);
?>
	<table width="99%" height="92%"> 
		<tr> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> </tr> 
		<tr> 
			<td height="1%">&nbsp;</td>
			<th width="1%" height="1%">
				<div id="divid">
					<div>
本次网站测试已结束，感谢您的参与。<br/>
如果您已对本测试网站验收确认完毕，请<a href="<?php echo $url; ?>">确认</a>，<br/>
我们会尽快与您联系进行网站上线。<br/>
如果您仍然对本测试网站存在疑虑，请致电82784400<br/>
或致信您的客户经理，我们将会第一时间提供协助或解答。
					</div>
				</div>
			</th>
			<td>&nbsp;</td> 
		</tr> 
		<tr> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> </tr> 
	</table> 
</body> 
</html>  