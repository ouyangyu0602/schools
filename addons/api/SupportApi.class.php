<?php
/**
 * 帮助中心应用API接口
 * @author zivss
 **/
class SupportApi extends Api {
	
	//获取热门问题
	public function get_hot_question(){
//		$mid = intval($this->mid);
		$data = D('SupportApi', 'support')->getHotQuestion($this->since_id, $this->max_id, $this->count, $this->page,$this->mid);
		empty($data) && $data = array();
		return $data;
	}
	
	//获取分类
	public function get_category(){
		$data = D('SupportApi', 'support')->getCateGory();
		empty($data) && $data = array();
		return $data;
	}
	
	//根据分类获取问题
	public function get_question_by_category(){
		$category_id = intval($this->data['category_id']);
		$data = D('SupportApi', 'support')->getQuestionByCategory($this->since_id, $this->max_id, $this->count, $this->page,$category_id);
		empty($data) && $data = array();
		return $data;
	}
	
	//获取问题详情
	public function show(){
		$mid = intval($this->mid);
		$support_id = intval($this->data['support_id']);
		$data = D('SupportApi', 'support')->getSupportById($support_id);
		empty($data) && $data = array();
		return $data;
	}
	
	//搜索问题
	public function search_question(){
		$mid = intval($this->mid);
		$keyword = t($this->data['keyword']);
		$data = D('SupportApi', 'support')->getSearchQuesiton($this->since_id, $this->max_id, $this->count, $this->page, $keyword);
		empty($data) && $data = array();
		return $data;
	}
	
	//搜索分类
	public function search_category(){
		$keyword = t($this->data['keyword']);
		$data = D('SupportApi','support')->getSearchCategory($keyword);
		empty($data) && $data = array();
		return $data;	
	}
}