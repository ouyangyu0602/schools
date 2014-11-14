<?php
/**
 * 赞Widget
 */
class DiggWidget extends Widget {

	/**
	 * 渲染赞页面
	 * @return string 赞HTML相关信息
	 */
	public function render ($data) {
		$var['tpl'] = 'digg';
		$var['feed_id'] = intval($data['feed_id']);
		$var['digg_count'] = intval($data['digg_count']);
		$var['diggArr'] = $data['diggArr'];
		$var['diggId'] = empty($data['diggId']) ? 'digg' : t($data['diggId']);
		// 获取而微博信息
		$feedInfo = model('Feed')->getFeedInfo($var['feed_id']);
		$var['self_feed'] = ($GLOBALS['ts']['mid'] == $feedInfo['uid']) ? true : false;

		$content = $this->renderFile(dirname(__FILE__).'/'.$var['tpl'].'.html', $var);

		return $content;
	}
}