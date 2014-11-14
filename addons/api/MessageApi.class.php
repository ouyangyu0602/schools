<?php
/**
 * 
 * @author jason
 *
 */
class MessageApi extends Api{
	private function __formatMessageList($message) {
		
		foreach ($message as $k => $v) {
			$message[$k] = $this->__formatMessageDetail($v);
		}
		return $message;
	}
	private function __formatMessageDetail($message) {
		unset($message['deleted_by']);
		$fromUserInfo = model('User')->getUserInfo($message['from_uid']);
		$message['from_uname']	= $fromUserInfo['uname'];
		$message['from_face']	= $fromUserInfo['avatar_middle'];
		$message['timestmap']	= $message['mtime'];
		$message['ctime']		= date('Y-m-d H:i', $message['mtime']);
		return $message;
	}
	//原来的box函数
	public function box(){
		return $this->get_message_list();
	}
	public function get_message_list(){
		$_REQUEST['p'] = $_REQUEST['page'] = $this->page;
		$this->data['type'] 	= $this->data['type']	? $this->data['type'] : array(1,2);
		$this->data['order']	= $this->data['order'] == 'ASC'	? '`mb`.`list_ctime` ASC' : '`mb`.`list_ctime` DESC';
		$message = model('Message')->getMessageListByUidForAPI($this->mid, $this->data['type'], $this->since_id, $this->max_id, $this->count, $this->page);
		$message = $this->__formatMessageList($message);
		foreach ($message as &$_l) {
            $_l['from_uid'] = $_l['last_message']['from_uid'];
            $_l['content']  = $_l['last_message']['content'];
            $_l['mtime']    = $_l['list_ctime'];
        }
		return $message;
	}
	// 获取当前登陆用户的私信详情
	
	public function show(){
		return $this->get_message_detail();
	}
	public function get_message_detail() {
		//$res = model('Message')->getDetailById($this->mid, $this->data['id'], $show_cascade);
		$res = model('Message')->getMessageByListId($this->data['id'], $this->mid, $this->since_id, $this->max_id, 10);
		// 设置私信为已读
		model('Message')->setMessageIsRead(array($this->data['id']),$this->mid);
		return $this->__formatMessageList($res['data']);
	}
	// 发送私信
	public function create() {
		if ( empty($this->data['to_uid']) || empty($this->data['content']) ) {
			return false;
		}
		$data['to'] 		= $this->data['to_uid'];
		$data['title']		= $this->data['title'];
		$data['content']	= $this->data['content'];
		return (int) model('Message')->postMessage($data, $this->mid);
	}
	// 回复私信
	public function reply() {
		if ( empty($this->data['id']) || empty($this->data['content']) ) {
			return false;
		}
		return (int) model('Message')->replyMessage($this->data['id'], $this->data['content'], $this->mid);
	}
	// 删除私信
	public function destroy() {
		return (int) model('Message')->deleteMessageByListId($this->mid, t($this->data['list_id']));
	}
}
?>