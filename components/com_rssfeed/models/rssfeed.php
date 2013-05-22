<?php
/**
 * @version		$Id: rssfeed.php 21481 2011-06-08 00:38:29Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Rssfeed Component Model for a rssfeed record
 *
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @since		1.5
 */
class RssfeedModelRssfeed extends JModel
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_rssfeed.rssfeed';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		// Load the object state.
		$id	= JRequest::getInt('id');
		if (!$id) {
			$id = $this->getFirstItem()->sid;
		}
		
		$this->setState('rssfeed.id', $id);
		// Load the parameters.
		$this->setState('params', $params);
	}
	
	public function getFeed() {
		jimport('mulan.mldb');
		jimport('mulan.mlhtml');
		date_default_timezone_set("Asia/Shanghai");
//		$get_date = date(DATE_RFC2822);
//		$itemLink = 'index.php?option=com_announcement&view=announcement&Itemid=478&id=';
		$base = 'http://www.d-niu.com';
		
		$feeds = MulanDBUtil::getObjectlistBySql('select * from #__announcement where published=1 order by id desc limit 10');
		$last_feed = MulanDBUtil::getObjectBySql('select * from #__announcement where published=1 order by addtime desc limit 1');
		$xml = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>Woondroo-RSS订阅</title>
		<description>只为做更好的网站！-Woondroo</description>
		<link>'.$base.'/rss.html</link>
		<lastBuildDate>'.date(DATE_RFC2822,strtotime($last_feed->addtime)).'</lastBuildDate>
		<generator>Woondroo</generator>
		<language>zh-cn</language>';
		if (count($feeds)) {
			foreach ($feeds as $feed) {
				$xml .= '
		<item>
			<title>'.$feed->title.'</title>
			<link>'.$base.MulanHTMLUtil::getUrlByAlias('news','id='.$feed->id).'</link>
			<guid>'.$base.MulanHTMLUtil::getUrlByAlias('news','id='.$feed->id).'</guid>
			<description><![CDATA['.$feed->description.']]></description>
			<author>Woondroo</author>
			<category>公告专题</category>
			<pubDate>'.date(DATE_RFC2822,strtotime($feed->addtime)).'</pubDate>
		</item>';
			}
		}
		$xml .= '
	</channel>
</rss>';
		return $xml;
	}
}