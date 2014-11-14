<?php
/**
 * 频道应用API接口
 * @author zivss guolee226@gmail.com
 * @version  TS3.0
 */
class ChannelApi extends Api
{
	/**
	 * 获取所有频道分类
	 * @return json 所有频道分类
	 */
	public function get_all_channel(){
		$data = D('ChannelApi', 'channel')->getAllChannel();
		return $data;
	}

	/**
	 * 获取指定分类下的微博
	 * @return json 指定分类下的微博
	 */
	public function get_channel_feed(){
		$cid = intval($this->data['category_id']);
		if(is_null($cid)) {
			return array();
		}
		$data = D('ChannelApi', 'channel')->getChannelFeed($cid, $this->since_id, $this->max_id, $this->count, $this->page);
		return $data;
	}
}