<?php
/**
 * @version		$Id: index.php
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2008 - 2011 Mulan Design, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('mulan.mlhtml');
JHtml::core();

/* The following line loads the MooTools JavaScript Library */
//JHtml::_('behavior.framework', true);

/* The following line gets the application object for things like displaying the site name */
$app = JFactory::getApplication();
$pagelanguage = $app->getCfg('pagelanguage');

$option = JRequest::getVar('option');
$is_no_head_footer = $option == 'com_qlue404' ? true : false;

$itemid = JRequest::getVar('Itemid');
$is_show_content_comps = $itemid == 21 ? false : true;

$base = JURI::base(true);
$root = JURI::root();
$document = JFactory::getDocument();
$document->removeScript($base.'/media/system/js/core.js');
$document->removeScript($base.'/media/system/js/mootools-core.js');
$document->removeScript($base.'/media/system/js/caption.js');

if (MulanHTMLUtil::isIE6()) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
} else {
	echo '<?xml version="1.0" encoding="'.$this->_charset.'"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
';
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<!-- The following JDOC Head tag loads all the header and meta information from your site config and content. -->
		<jdoc:include type="head" />
<?php if (!$is_no_head_footer) { ?>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.mulan.base.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/<?php echo $pagelanguage == 1 ? 'CN.js' : 'EN.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/system/js/system.jquery.js"></script>
		<!--[if lte IE 6]>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/system/js/PNG.js"></script>
		<script type="text/javascript">DD_belatedPNG.fix('#logo a');</script>
		<![endif]-->
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/base.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
<?php } ?>
	</head>
	<body>
<?php if (!$is_no_head_footer) { ?>
		<div id="header">
			<div class="header-container frame960">
				<jdoc:include type="modules" name="modlogo" />
				<jdoc:include type="modules" name="sitelanguage" />
				<jdoc:include type="modules" name="searchload" />
				<div id="main-menu">
					<jdoc:include type="modules" name="top" />
					<div class="clr"></div>
				</div>
			</div>
		</div>
		<div id="container">
			<?php
			if ($is_show_content_comps) {
			?>
			<div class="banner banner-960">
				<jdoc:include type="modules" name="bannersload" />
			</div>
			<?php
			}
			?>
			<div class="<?php echo $is_show_content_comps ? 'frame960 component-container' : 'no-bg no-padding' ?>">
				<?php
				if ($is_show_content_comps) {
				?>
				<jdoc:include type="modules" name="breadcrumbsload" />
				<div id="left-menu">
					<jdoc:include type="modules" name="leftmenu" />
					<jdoc:include type="modules" name="leftcontact" />
					<jdoc:include type="modules" name="leftonline" />
					<jdoc:include type="modules" name="leftad-1" />
					<jdoc:include type="modules" name="leftad-2" />
				</div>
				<?php
				}
				?>
				<div id="<?php echo $is_show_content_comps ? 'component-content' : 'component-home' ?>">
					<jdoc:include type="modules" name="switcher-home" />
					<jdoc:include type="modules" name="switcher" />
<?php } ?>
					<jdoc:include type="component" />
<?php if (!$is_no_head_footer) { ?>
					<jdoc:include type="modules" name="about-contact" />
					<jdoc:include type="modules" name="inquirybox" />
					<jdoc:include type="modules" name="searchall" />
					<div id="single-site-map"><jdoc:include type="modules" name="single-sitemap" /></div>
				</div>
				<div class="clr"></div>
				<?php
				if ($is_show_content_comps) {
				?>
				<jdoc:include type="modules" name="returnto" />
				<?php
				}
				?>
			</div>
		</div>
		<div id="site-map"><jdoc:include type="modules" name="sitemapload"/></div>
		<div id="footer-copy">
			<div class="footer-copy-text"><jdoc:include type="modules" name="footerload"/></div>
			<a class="footer-in-link" href="sitemap.html">&gt&nbsp;网站地图</a>
		</div>
		<div id="footer">
			<jdoc:include type="modules" name="weblinksload"/>
			<?php
			/**
			 <div class="footer-copy">
				<jdoc:include type="modules" name="footerload"/>
			</div>
			 */
			?>
			<jdoc:include type="modules" name="music-player"/>
			<jdoc:include type="modules" name="footer-companymess"/>
		</div>
		<script type="text/javascript">
			var buffer = new StringBuffer();
			buffer.append("text");
		</script>
		<div class="displaynone">
			<jdoc:include type="message" />
			<jdoc:include type="modules" name="statistics"/>
		</div>
		<jdoc:include type="modules" name="debug" />
		<input type="hidden" value="<?php echo JRequest::getVar('Itemid') ?>" id="curitemid"/>
<?php } ?>
	</body>
</html>
