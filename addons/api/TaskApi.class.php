<?php
/**
 * 任务应用API接口
 * @author zivss
 **/
class TaskApi extends Api {

	// 获取任务分类 - 分类数组信息，包括分类ID，分类名称，分类任务数目，是否共享分类
	public function get_task_category() {
		$mid = intval($this->mid);
		$data = D('TaskApi', 'task')->getUserCategroyInfo($mid);
		return $data;
	}

	// 添加任务分类 返回值0表示失败，1表示成功，2表示重复
	public function create_task_category() {
		$mid = intval($this->mid);
		$title = t($this->data['category_name']);
		if(empty($title)) {
			return 0;
		}
		// $title = auto_charset($title, 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$result = D('TaskApi', 'task')->addUserCategory($mid, $title);
		return $result;
	}

	// 删除任务分类 返回值0表示失败，1表示成功
	public function destroy_task_category() {
		$cid = intval($this->data['category_id']);
		if(is_null($cid)) {
			return 0;
		}
		$result = D('TaskApi', 'task')->deleteUserCategory($cid);
		return $result;
	}

	// 编辑任务分类 返回值0表示失败，1表示成功，2表示重复
	public function edit_task_category() {
		$mid = intval($this->mid);
		$title = t($this->data['category_name']);
		// $title = auto_charset($title, 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$cid = intval($this->data['category_id']);
		if(empty($title) || is_null($cid)) {
			return 0;
		}
		$result = D('TaskApi', 'task')->editUserCategory($cid, $title, $mid);
		return $result;
	}

	// 分享任务分类
	public function share_task_category() {
		$mid = intval($this->mid);
		$cid = intval($this->data['category_id']);
		$userEmails = t($this->data['user_emails']);
		$userEmails = explode(',', $userEmails);
		foreach($userEmails as &$value) {
			$value = "'".trim($value)."'";
		}
		$userEmails = array_unique($userEmails);
		$userEmails = array_filter($userEmails);
		$userIds = array();
		if(!empty($userEmails)) {
			$map['email'] = array('IN', $userEmails);
			$userIds = model('User')->where($map)->getAsFieldArray('uid'); 
		}
		if(is_null($cid) || empty($userIds)) {
			return 0;
		}

		$result = D('TaskApi', 'task')->updateShareCategory($userIds, $cid);
		return $result;
	}

	// 取消整个分享任务分类
	public function cancel_share_task_category() {
		$cid = intval($this->data['category_id']);
		if(is_null($cid)) {
			return 0;
		}

		$result = D('TaskApi', 'task')->deleteShareCategory($cid);
		return $result;
	}

	// 根据分类获取任务
	public function get_task_by_category() {
		$mid = intval($this->mid);
		$cid = intval($this->data['category_id']);
		if(is_null($cid)) {
			return array();
		}
		$data = D('TaskApi', 'task')->getList($mid, '', $cid, '', $this->since_id, $this->max_id, $this->page, $this->count);
		return $data;
	}

	// 根据类型获取任务
	public function get_task_by_type() {
		$mid = intval($this->mid);
		$type = t($this->data['type']);
		$valideType = array('today', 'tomorrow', 'someday', 'after', 'allafter', 'tome', 'myassign', 'star', 'overdue', 'over');
		if(!in_array($type, $valideType)) {
			return array();
		}
		$data = D('TaskApi', 'task')->getList($mid, $type, '', '', $this->since_id, $this->max_id, $this->page, $this->count);
		return $data;
	}

	// 获取任务提醒数
	public function get_task_notify() {
		$mid = intval($this->mid);
		$data = D('TaskApi', 'task')->getNotify($mid);
		return $data;
	}

	// 添加任务
	public function create_task() {
		$data['title'] = t($this->data['task']);
		// $data['title'] = auto_charset($data['title'], 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		if(empty($data['title'])) {
			return 0;
		}
		$data['joiner'] = intval($this->user_id);
		$data['deadline'] = t($this->data['date']);
		$data['desc'] = t($this->data['task_detail']);
		// $data['desc'] = auto_charset($data['desc'], 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$data['type'] = t($this->data['type']);
		$data['category_id'] = intval($this->data['category_id']);
		$result = D('TaskApi', 'task')->addTask($data);
		return $result;
	}

	// 编辑任务
	public function edit_task() {
		$sid = intval($this->data['task_id']);
		$data['title'] = t($this->data['task']);
		// $data['title'] = auto_charset($data['title'], 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$data['joiner'] = intval($this->user_id);
		$data['deadline'] = t($this->data['date']);
		$data['desc'] = t($this->data['task_detail']);
		// $data['desc'] = auto_charset($data['desc'], 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$result = D('TaskApi', 'task')->editTask($sid, $data);
		return $result;
	}

	// 删除任务
	public function destroy_task() {
		$sid = intval($this->data['task_id']);
		$cid = intval($this->data['category_id']);
		$result = D('TaskApi', 'task')->deleteTask($sid, $cid);
		return $result;
	}

	// 查看任务详情
	public function show_task() {
		$sid = intval($this->data['task_id']);
		$data = D('TaskApi', 'task')->getDetail($sid);
		return $data;
	}

	// 给任务加星标
	public function starred_task() {
		$sid = intval($this->data['task_id']);
		$result = D('TaskApi', 'task')->setStar($sid, 1);
		return $result;
	}

	// 取消任务星标
	public function cancel_starred_task() {
		$sid = intval($this->data['task_id']);
		$result = D('TaskApi', 'task')->setStar($sid, 0);
		return $result;
	}

	// 标注任务已完成
	public function finished_task() {
		$sid = intval($this->data['task_id']);
		$result = D('TaskApi', 'task')->setOver($sid, 1, $cid);
		return $result;
	}

	// 取消标注任务已完成
	public function cancel_finished_task() {
		$sid = intval($this->data['task_id']);
		$result = D('TaskApi', 'task')->setOver($sid, 0, $cid);
		return $result;
	}

	// 搜索任务
	public function search_task() {
		$mid = intval($this->mid);
		$keyword = t($this->data['keyword']);
		// $keyword = auto_charset($keyword, 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$data = D('TaskApi', 'task')->getList($mid, 'search', '', $keyword, $this->since_id, $this->max_id, $this->page, $this->count);
		return $data;
	}

	// 获取分类分享的人
	public function get_share_users() {
		$cid = intval($this->data['category_id']);
		if(is_null($cid)) {
			return array();
		}
		$data = D('TaskApi', 'task')->getShareUser($cid);
		return $data;
	}
}