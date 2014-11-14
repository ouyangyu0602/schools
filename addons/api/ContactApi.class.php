<?php
/**
 * 通讯录应用API接口
 * @author zivss
 */
class ContactApi extends Api {

	// 根据部门ID返回用户通讯录列表
	public function get_colleague_by_department() {
		$id = intval($this->id);
		$data = D('ContactApi', 'contact')->getUserInfoWithDepartment($id, $this->since_id, $this->max_id, $this->page, $this->count);
		return $data;
	}

	// 根据指定用户返回用通讯录列表
	public function get_my_contacter() {
		$userId = empty($this->user_id) ? $this->mid : intval($this->user_id);
		$data = D('ContactApi', 'contact')->getMyContacter($userId);
		return $data;
	}

	// 获取所有用户通讯录列表
	public function get_all_colleague() {
		$data = D('ContactApi', 'contact')->getAllContacter($this->since_id, $this->max_id, $this->page, $this->count);
		return $data;
	}

	// 搜索用户
	public function search_colleague() {
		$key = t($this->data['key']);
	//	$key = auto_charset($key, 'GB2312', 'UTF8');		// TODO:是否需要修改，待定
		$data = D('ContactApi', 'contact')->getSearchContacter($key);
		return $data;
	}

	// 增加收藏该用户
	public function contacter_create() {
		$sid = intval($this->user_id);
		$data = D('ContactApi', 'contact')->addMyContacter($this->mid, $sid);
		return $data;
	}

	// 取消收藏该用户
	public function contacter_destroy() {
		$sid = intval($this->user_id);
		$data = D('ContactApi', 'contact')->delMyContactor($this->mid, $sid);
		return $data;
	}

	// 返回部门列表
	public function get_department_list() {
		$id = intval($this->data['deptId']);

		$list = model('Department')->getHashDepartment($id);
		$data = array();
		foreach($list as $key => $val) {
			if($key == 0) {
				continue;
			}
			$a['departId'] = $key;
			$a['departName'] = $val;
			$data[] = $a;
			unset($a);
		}
	//	$data = empty($data) ? 0 : $data;

		return $data;
	}

	// 获取部门与相关用户信息 - TODO：有两次部门数据
	public function get_data_by_department() {
		$id = intval($this->id);
		$isDepart = intval($this->data['isDepart']);
		$data = array();
		// 添加部门数据
		if($isDepart == 1) {
			$departInfo = model('Department')->getHashDepartment($id);
			foreach($departInfo as $key => $val) {
				$a['departId'] = $key;
				$a['departName'] = $val;
				$a['type'] = 'department';
				$data[] = $a;
				unset($a);
			}
		}
		// 添加用户数据
		$userInfo = D('ContactApi', 'contact')->getUserInfoWithDepartment($id, $this->since_id, $this->max_id, $this->page, $this->count);
		foreach($userInfo as &$value) {
			$value['type'] = 'user';
			$data[] = $value;
		}

		return $data;
	}

}