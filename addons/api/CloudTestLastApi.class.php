<?php
/**
 *
 * @author  欧阳宇
 * @name 云端测评手机端API借口
 * @abstract 继承Action.class.php
 * @version1.0
 *
 *错误代码：
 *
 *		1、错误,比如系统，网络中断,服务器问题等
 *			{
 * 				"message":"错误！",
 * 				"code":"00000"
 * 			}
 *
 *
 *		2、传过来的值为空
 *			{
 * 				"message":"传送数据为空！",
 * 				"code":"00001"
 * 			}
 *
 *		3、最新留言消息为空
 *
 * 			{
 * 				"message":"没有新的留言消息！",
 * 				"code":"00002"
 * 			}
 *
 * 		4、最新测评为空
 *
 * 			{
 * 				"message":"没有新的测评消息！",
 * 				"code":"00003"
 * 			}
 * 		5、没有历史留言消息了
 *
 * 			{
 * 				"message":"没有历史留言消息了！",
 * 				"code":"00004"
 * 			}
 * 		6、没有更多历史测评分数了
 *
 * 			{
 * 				"message":"没有更多历史测评分数了！",
 * 				"code":"00005"
 * 			}
 *		7、回复留言失败
 *			{
 * 				"message":"回复失败，请重试！",
 * 				"code":"00006"
 * 			}
 * 		8、此时间段没有考试
 *
 * 			{
 * 				"message":"此时间段没有考试！",
 * 				"code":"00007"
 * 			}
 *
 *
 */

class CloudTestLastApi extends Api{

	/*
	 *
	 * 获取当前家长的ID
	 *
	 */
	private function getParent_id() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		$login = $user['login'];
		//$login = '13451672388';


		return $login;
	}


	/*
	 *
	 * 获取当前家长的孩子的ID
	 *
	 */

	private function getStudent_id() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		$login = $user['login'];
		//$login = '13451672388';

		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];

		return $student_id;
	}

	private function getStudentClass_id() {
		$indexSubject = $this->getSubject();
		return $indexSubject[0]['class_id'];
	}

	/*
	 * 此接口用作登录后界面科目列表的初始化界面
	 * 参数：无
	 * 请求方式：GET
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"subject_type":"科目id",
	 * 					"subject_type_desc":"科目名称",
	 * 					"class_id":"班级id",
	 * 					"grade_id":"年级id",
	 * 					"school_id":"学校id",
	 * 					"uid":"此门课程老师的用户id",
	 * 					"login":"此门课程老师的ID",
	 * 					"uname":"教此门课程的老师名字"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2、失败：
	 * 			{
	 * 				"message":"错误！",
	 * 				"code":"00000"
	 * 			}
	 *
	 *
	 */

	public function getIndex() {

		//调用私有方法，获取！
		$indexSubject = $this->getSubject();

		//test
		//dump($indexSubject);

		exit(json_encode($indexSubject));

	}


	/*
	 *
	 * 此方法为内部方法
	 * 用于获取家长登录后自己孩子的信息
	 * 所在班级、年级、学校、所学的科目、科目所对应的老师
	 *
	 */

	private function getSubject() {

		$student_id = $this->getStudent_id();

		//根据班级得到此班级相关的老师以及科目
		$teach_su_cllist = M()->table("ts_teacher_subject_classes tsc")
		->join("ts_school_class_students scs on scs.class_id=tsc.class_id")
		->join("ts_user u on u.login=tsc.login")
		->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
		->where("scs.login='".$student_id."' and sm.subject_type != '00'")
		->field('sm.subject_type,sm.subject_type_desc,scs.class_id,scs.grade_id,scs.school_id,u.uid,u.login,u.uname')
		->select();


		if ($teach_su_cllist) {
			//找到所有的科目以及对应老师信息，以json格式返回给客户端
			//exit(json_encode($indexSubject));
			return $teach_su_cllist;

		}else {		//如果返回来数据为空！

			$message['message'] = "the subject is empty！";
			$message['code']    = "00000";
			//exit( json_encode( $message ) );

			return $message;
		}


	}

	/*
	 * 此接口用作登录后主页面、留言主页面的初始化
	 * 每门科目的最新留言的内容、数目、时间
	 * 每门科目的最新测评的条数
	 *
	 * 参数：无
	 *
	 * 请求方式：GET
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"subject_type":"科目id",
	 * 					"subject_type_desc":"科目名称",
	 * 					"class_id":"班级id",
	 * 					"grade_id":"年级id",
	 * 					"school_id":"学校id",
	 * 					"uid":"此门课程老师的用户id",
	 * 					"login":"此门课程老师的ID",
	 * 					"uname":"教此门课程的老师名字",
	 * 					"messagecount":"此门课程最新留言数目",
	 * 					"messagetime":"留言时间",
	 * 					"testcount":"此门课程最新测评数目"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2、失败：
	 * 			{
	 * 				"message":"错误！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 */


	public function getinitIndex() {
		//得到了主页面科目
		$indexSubject = $this->getSubject();

		for ($i = 0; $i < count($indexSubject); $i++) {

			//将每门科目的最新消息写入到indexSubject数组中,type:false,只用于统计
			$subjectMessages = $this->getOneSubjectMessages($indexSubject[$i]['subject_type'],$indexSubject[$i]['login'],false);

			//test
			//dump($subjectMessages);

			$indexSubject[$i]['newMessagesLastTime'] = $subjectMessages[0]['sendmessage_date'];
			$indexSubject[$i]['newMessagesCount'] = count($subjectMessages);


			//将每门科目的最新消息写入到indexSbject数组中
			$indexSubject[$i]['testcount'] = count($this->getOneSubjectTest($indexSubject[$i]['subject_type']));

		}

		//test
		//dump($indexSubject);

		exit(json_encode($indexSubject));


	}



	/*
	 * 私有方法
	 * 参数：
	 * 		$subject_type:课程的ID
	 * 		$message_receiver：此门课程对应的老师
	 * 		$type:true,用于统计;false,用于查看新消息
	 *
	 * 说明：
	 * 		在ts_evaluation_paper_result_messages表中根据老师的id，家长id找到消息记录数据
	 * 再根据paper，判断是否属于某一科目类型的，如果属于，就添加进去
	 */
	private function getOneSubjectMessages($subject_type,$message_receiver,$type) {
		//获取父母id
		$login = $this->getParent_id();



		//查出某一个老师发给家长的未读的消息
		//老师给家长发的留言，未读的，而且是老师给家长发送的
		$newMessages = M()->table("ts_evaluation_paper_result_messages eprm")
		->where("eprm.message_receiver='".$login."' AND eprm.isread = '0' AND comefrom='0' AND staff_id='".$message_receiver."'")
		->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
		->order('eprm.sendmessage_date desc')
		->select();
		$messageCount = count($newMessages);
		for($i=0; $i<$messageCount; $i++) {
			if($this->isSubjectPaper($newMessages[$i]['paper_id'], $subject_type)) {
				$newMessageList[] = $newMessages[$i];

				if ($type) {

					//如果是查看新消息，那么修改字段为已读eprm.isread=1
					//每次查完后，需要修改字段->field('message_id,staff_id,message_content,sendmessage_date,lastmessage_id')
					M()->table("ts_evaluation_paper_result_messages eprm")
					->where("eprm.message_id='".$newMessages[$i]['message_id']."'")
					->setField('isread','1');
				}

			}
		}

		return $newMessageList;
	}

	/*
	 *
	 * 作用：
	 * 		判断是否属于某一科目
	 *
	 */

	private  function isSubjectPaper($paper_id,$subject_type) {

		$subjectPaper = M()->table("ts_evaluation_paper ep")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section_book esb on es.section_book_id=esb.section_book_id")
		->where("ep.paper_id='".$paper_id."' AND esb.subject_type='".$subject_type."'")
		->select();

		//test
		//dump(M()->getLastsql());
		if($subjectPaper) {
			return true;
		}else {
			return false;
		}
	}

/*
	 * 私有方法
	 * 找到某一门课程当前学生最新测试试卷
	 * 这里找到的只是paper条数，不包含试卷的题目
	 * 参数：
	 * 		$subject_type:课程的ID
	 *
	 * 说明：
	 * 		在ts_evaluation_paper_result_messages表中根据老师的id，家长id找到消息记录数据
	 * 再根据paper，判断是否属于某一科目类型的，如果属于，就添加进去
	 */
	
	private function getOneSubjectTest($subject_type) {

		$student_id = $this->getStudent_id();

		//把是否需要发送给家长的测试找出来,并且是家长未读的
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='0'")
		->field("epr.paper_id,epr.user_total_score,es.section_title,esparent.section_title parent_section_title,ep.examdate")
		->order('ep.examdate')
		->select();
		//dump(M()->getLastSql());
		//dump($paperSublist);
		return $subjectPaper;

	}


	/*
	 *
	 * 此接口用于当点击某一门课程的时候查出此课程的老师给家长的最新留言
	 *
	 * 参数：
	 * 		subject_type ： 点击的课程
	 * 		login ：点击的课程的老师
	 *
	 * 请求方式：POST
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"message_id":"留言ID",
	 * 					"paper_id":"试卷ID",
	 * 					"staff_id":"老师ID",
	 * 					"message_content":"留言内容",
	 * 					"message_receiver":"家长的ID",
	 * 					"sendmessage_date":"留言日期1899-12-30 01:00:00",
	 * 					"school_period_id":"学期ID,以后可能用到",
	 * 					"isread":"是否已读，0代表没有读，1代表已读",
	 * 					"comefrom":"1代表家长发给老师，0代表老师发给家长",
	 * 					"lastmessage_id":"上一条message_id"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2、失败：
	 *		1、传过来的值为空
	 *			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2、最新留言消息为空
	 *
	 * 			{
	 * 				"message":"没有新的留言消息！",
	 * 				"code":"00002"
	 * 			}
	 *
	 *
	 */
	public function getNewMessages() {
		$_REQUEST = array_merge($_GET,$_POST);

		if (!empty($_REQUEST['subject_type']) && !empty($_REQUEST['login'])) {

			//获取老师的ID
			$subject_type = $_REQUEST['subject_type'];

			//获取老师教的学科ID
			$login = $_REQUEST['login'];

			//得到此门科目的最新消息,type:true,获取所有最新消息内容,这是为了更新数据库表字段
			$subjectMessages = $this->getOneSubjectMessages($subject_type,$login,true);
			if($subjectMessages) {
				exit( json_encode( $subjectMessages ) );
			}else {
				$message['message'] = '没有新的留言消息！';
				$message['code']    = '00002';
				$message['$subject_type'] = $subject_type;
				$message['$login'] = $login;
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '传过来的数据为空！';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

	}



	/*
	 *
	 * 此接口用于当家长下拉获取历史留言时请求
	 *
	 * 参数：
	 * 		lastmessage_id ： 当前留言的上一条留言message_id
	 *
	 * 请求方式：POST
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"message_id":"留言ID",
	 * 					"paper_id":"试卷ID",
	 * 					"staff_id":"老师ID",
	 * 					"message_content":"留言内容",
	 * 					"message_receiver":"家长的ID",
	 * 					"sendmessage_date":"留言日期1899-12-30 01:00:00",
	 * 					"school_period_id":"学期ID,以后可能用到",
	 * 					"isread":"是否已读，0代表没有读，1代表已读",
	 * 					"comefrom":"1代表家长发给老师，0代表老师发给家长",
	 * 					"lastmessage_id":"上一条message_id"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2、失败
	 *		1.lastmessage_id取到空了
	 *			{
	 * 				"message":"没有历史留言消息了！",
	 * 				"code":"00004"
	 * 			}
	 *
	 * 		2.传过来的数据为空
	 *			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 *
	 */

	public function getHistoryMessages() {

		//		//test
		//		$lastmessage_id = "1";
		//			for ($i=0;$i<10;$i++) {
		//			if(empty($lastmessage_id)){//如果这条留言的没有下条留言
		//					break;
		//				}
		//			$messages = M()->table("ts_evaluation_paper_result_messages eprm")
		//			->where("eprm.message_id='".$lastmessage_id."'")
		//			->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
		//			->select();
		//			$lastmessage_id = $messages[0]['lastmessage_id'];
		//			$messagesList[] = $messages[0];
		//
		//			}
		//
		//			dump($messagesList);
		//			dump(json_encode($messagesList));
		//			die("");


		$_REQUEST = array_merge($_GET,$_POST);
		if (!empty($_REQUEST['lastmessage_id'])) {
			$lastmessage_id = $_REQUEST['lastmessage_id'];

			//找出以这条为起点的十条
			for ($i=0;$i<10;$i++) {

				if(empty($lastmessage_id)){//如果这条留言的没有下条留言
					break;
				}

				$messages = M()->table("ts_evaluation_paper_result_messages eprm")
				->where("eprm.message_id='".$lastmessage_id."'")
				->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
				->select();
				//寻找这条留言的下一条留言
				$lastmessage_id = $messages[0]['lastmessage_id'];
				$messagesList[] = $messages[0];
			}

			if ($messagesList) {

				exit(json_encode($messagesList));
			}else {
				$message['message'] = '没有历史留言消息了！';
				$message['code']    = '00004';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '传送过来的数据为空！';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

			
	}




	/*
	 *
	 * 此接口是用来回复老师的留言的
	 * 参数：messageJSON
	 * 		直接将被回复的留言转换成json
	 * 		[
	 * 			{
	 * 				"message_id":"空着",
	 * 				"paper_id":"试卷ID，保持原来的不变",
	 * 				"staff_id":"家长的ID，保持原来的不变",
	 * 				"message_content":"填入回复的内容",
	 * 				"message_receiver":"家长ID，保持原来的不变",
	 * 				"sendmessage_date":"空着",
	 * 				"school_period_id":"学期ID，保持原来的不变",
	 * 				"isread":"填0，是否已读，0代表没有读，1代表已读",
	 * 				"comefrom":"填1，1代表家长发给老师，0代表老师发给家长",
	 * 				"lastmessage_id":"填入被回复的留言的message_id"
	 * 			}
	 * 		]
	 *
	 * 请求方式：
	 *		POST
	 *
	 *返回：
	 *		1、成功
	 *			{
	 *				"message_id":"2841247443524，回复的留言的ID"
	 *				"sendmessage_date":"2013-11-14 20:15:33,刚回复留言的",
	 *				"code":"11111"
	 *			}
	 *
	 *		2、失败
	 *
	 * 			{
	 * 				"message":"回复失败，请重试！",
	 * 				"code":"00006"
	 * 			}
	 * 		3、
	 * 			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 *
	 *
	 */

	public function setMessages() {

		$_REQUEST = array_merge($_GET,$_POST);
		$messageJSO = $_REQUEST['messageJSON'];
		
		$messageJSON  = stripslashes($messageJSO);
		
		if (!empty($_REQUEST['messageJSON'])) {
			$messagearr = json_decode($messageJSON);
			$messagearr = $this->object_array($messagearr);
			
			$messageModel =  M('Evaluation_paper_result_messages');

			//$messageModel->message_id = $messagearr[0]['message_id'];//留言ID，唯一设置
			$messageModel->message_id = $this->getRandOnlyId();
			//保持不变的
			$messageModel->paper_id = $messagearr[0]['paper_id'];
			$messageModel->staff_id = $messagearr[0]['staff_id'];
			$messageModel->school_period_id = $messagearr[0]['school_period_id'];
			$messageModel->message_content = $messagearr[0]['message_content'];

			//$messageModel->sendmessage_date = $messagearr[0]['sendmessage_date'];//留言时间，获取当前时间
			$messageModel->sendmessage_date =  date('Y-m-d H:i:s');

			//$messageModel->message_receiver = $messagearr[0]['message_receiver'];//写成login
			$messageModel->message_receiver = $this->getParent_id();

			$messageModel->isread = '0';
			$messageModel->comefrom = '1';
			$messageModel->lastmessage_id = $messagearr[0]['lastmessage_id'];


			if($messageModel->add()){
				$message['message_id'] = $messageModel->message_id;
				$message['sendmessage_date'] = $messageModel->sendmessage_date;
				
				$message['code']    = '11111';
				exit( json_encode( $message ) );
			}else{
				$message['message'] = '插入失败：数据为空！';
				$message['code']    = '00006';
				$message['messageJSON']    = $messageJSON; 
				exit( json_encode( $message ) );
			}

		}else {
			$message['message'] = '传送过来的数据为空！';
			$message['code']    = '00001';
			$message['messageJSON']    = $messageJSON;
			exit( json_encode( $message ) );
		}



		//test

		//		$jsonStr = '{"message_id":null,"paper_id":"11","staff_id":"1","message_content":"我爱你","message_receiver":"1","sendmessage_date":"1899-12-30 01:00:00","school_period_id":"1","isread":"1","comefrom":"1","lastmessage_id":null}';
		//
		//		$messagearr = json_decode($jsonStr);
		//		$messagearr = $this->object_array($messagearr);
		//		$messageModel =  M('Evaluation_paper_result_messages');
		//
		//
		//
		//		//$messageModel->message_id = $messagearr['message_id'];//留言ID，唯一设置
		//		$messageModel->message_id = $this->getRandOnlyId();
		//		//保持不变的
		//		$messageModel->paper_id = $messagearr['paper_id'];
		//		$messageModel->staff_id = $messagearr['staff_id'];
		//		$messageModel->school_period_id = $messagearr['school_period_id'];
		//		$messageModel->message_content = $messagearr['message_content'];
		//
		//		//$messageModel->sendmessage_date = $messagearr['sendmessage_date'];//留言时间，获取当前时间
		//		$messageModel->sendmessage_date =  date('Y-m-d H:i:s');
		//
		//		$messageModel->message_receiver = $messagearr['message_receiver'];//写成login
		//		//$messageModel->message_receiver = $login;
		//
		//		$messageModel->isread = '0';
		//		$messageModel->comefrom = '1';
		//		$messageModel->lastmessage_id = $messagearr['lastmessage_id'];
		//
		//
		//		if($messageModel->add()){
		//			$message['message_id'] = $messageModel->sendmessage_date;
		//			$message['sendmessage_date'] = $messageModel->message_id;
		//
		//			$message['code']    = '11111';
		//			exit( json_encode( $message ) );
		//		}else{
		//			$message['message'] = '插入失败：数据为空！';
		//			$message['code']    = '00001';
		//			exit( json_encode( $message ) );
		//		}


			
	}

	/*
	 * 获取唯一的随机数
	 */
	private function getRandOnlyId() {
		//新时间截定义,基于世界未日2012-12-21的时间戳。
		$endtime=1356019200;
		//2012-12-21时间戳
		$curtime=time();
		//当前时间戳
		$newtime=$curtime-$endtime;
		//新时间戳
		$rand=rand(0,99999);
		//获取五位随机
		$all=$newtime.$rand;

		//$onlyid=base_convert($all,10,36);
		//把10进制转为36进制的唯一ID
		return $all;
	}

	/*
	 * 把json转换后的数组转换为
	 * 键值对的数组
	 */
	private function object_array($array){
		if(is_object($array)){
			$array = (array)$array;
		}
		if(is_array($array)){
			foreach($array as $key=>$value){
				$array[$key] = $this->object_array($value);
			}
		}
		return $array;
	}




	/*
	 *
	 * 此接口用于当点击某一门课程的时候查出此课程的最新测试
	 *
	 * 参数：
	 * 		subject_type ： 点击的课程
	 *
	 * 请求方式：POST
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"paper_id":"试卷ID",
	 * 					"user_total_score":"学生此试卷的得分",
	 * 					"section_title":"知识点子标题",
	 * 					"parent_section_title":"知识点父标题",
	 * 					"examdate":"考试时间",
	 * 					"percent":"百分位",
	 * 					"avg":"此考试班级的平均成绩",
	 * 					"question":[
	 * 									{
	 * 										"question_id":"此试卷题目id",
	 * 										"question_index":"题目号",
	 * 										"question_content":"题目内容",
	 * 										"question_content_text":"题目内容",
	 * 										"question_type":"题目类型",
	 * 										"answer":"题目正确答案",
	 * 										"difficulties":"题目难度",
	 * 										"user_answer":"题目错误答案"
	 * 									},
	 * 									{},
	 * 									...
	 * 									{}
	 * 								]
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2、失败：
	 *		1、传过来的值为空
	 *			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2、最新测评为空
	 *
	 * 			{
	 * 				"message":"最新测评为空！",
	 * 				"code":"00003"
	 * 			}
	 *
	 *
	 */
	public function getNewTest() {
		//得到一个关于某一门课程试卷id的数组，最新的就发已经发送过去了的
		$_REQUEST = array_merge($_GET,$_POST);
		if(!empty($_REQUEST['subject_type'])) {
			$subject_type = $_REQUEST['subject_type'];
			//拿到某一课程的所有最新试卷ID，并更新数据库
			$subjectPaper = $this->getOneSubjectTest($subject_type);
			$class_id = $this->getStudentClass_id();
			//得到这个班级的所有的人
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					$kaoshiCount = 0;
					//循环这个班级的人，得出每个人的$paper_id所对应的分数
					for ($k=0;$k<$classStudentCount;$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$k]['login']);
						if($score < $thisScore) {
							$percentCount--;
						}
						

						if(!empty($score) && $score !=0) {
							$kaoshiCount++;
						}
						
						$sumScore = $sumScore + $score;
						$score = 0;
					}
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$kaoshiCount);
					//$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					
					//拿到某一课程的所有最新试卷ID，并更新数据库
					$this->setTestToHis($subjectPaper[$i]['paper_id']);
					
				}
				//dump($subjectPaper);
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '最新测评为空！';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '传送过来的数据为空！';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

	}

	
	
	/*
	 * 
	 * 如果已经查出了最新测试试卷详细，那么更新数据库设置为已读
	 * 
	 */
	
private function setTestToHis($paper_id) {
		
		$student_id = $this->getStudent_id();
		
		M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."' AND epr.send_status = '1' AND epr.is_read='0'")
		->setField('is_read','1');
	}
	
	private function getStudentClass($class_id) {
		$class_student_cllist = M()->table("ts_school_class_students scs")
		->where("scs.class_id='".$class_id."'")
		->field('scs.class_id,scs.login')
		->select();
		
		return $class_student_cllist;
	}
	
	private function getStudentScore($paper_id,$student_id) {
		$studentScore = M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."'")
		->field("epr.user_total_score")
		->select();
		if($studentScore) {
			$score = floatval($studentScore[0]['user_total_score']);
			return $score;
		}else {
			return "0";
		}
	}
	
	/*
	 * 用作测试用！
	 */

	
	/*
	public function getNewTestTest() {
//		$j = "20";
//		$k = "100";
//		$p = floatval($j)/floatval($k)*100;
//		dump($p);
//		dump(strval($p));
		
		
		//得到一个关于某一门课程试卷id的数组，最新的就发已经发送过去了的
			$subject_type = "01";
			//拿到某一课程的所有最新试卷ID，并更新数据库
			$subjectPaper = $this->getOneSubjectTest($subject_type);
			$class_id = $this->getStudentClass_id();
			//得到这个班级的所有的人
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					//循环这个班级的人，得出每个人的$paper_id所对应的分数
					for ($k=0;$k<count($classStudentCount);$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$i]['login']);
						
						if($score < $thisScore) {
							$percentCount--;
						}
						$sumScore = $sumScore + $score;
						
					}
					echo "============================";
					
					dump($sumScore);
					dump($thisScore);
					dump($percentCount);
					dump($classStudentCount);
					dump($percentCount/$classStudentCount);
					dump($sumScore/$classStudentCount);
					
					echo "********************************";
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					//$this->setTestToHis($subjectPaper[$i]['paper_id']);
					
				}
				dump($subjectPaper);
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '最新测评为空！';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}

	}

	*/
	
	
	

		/*
	 *
	 * 此接口用于当点击某一门课程的时候查出此课程的最新测试
	 *
	 * 参数：
	 * 		subject_type ： 点击的课程
	 * 		daybegin: 这个时间之前的测试  2013-10-10
	 *
	 * 请求方式：POST
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"paper_id":"试卷ID",
	 * 					"user_total_score":"学生此试卷的得分",
	 * 					"section_title":"知识点子标题",
	 * 					"parent_section_title":"知识点父标题",
	 * 					"examdate":"考试时间",
	 * 					"percent":"百分位",
	 * 					"avg":"此考试班级的平均成绩",
	 * 					"question":[
	 * 									{
	 * 										"question_id":"此试卷题目id",
	 * 										"question_index":"题目号",
	 * 										"question_content":"题目内容",
	 * 										"question_content_text":"题目内容",
	 * 										"question_type":"题目类型",
	 * 										"answer":"题目正确答案",
	 * 										"difficulties":"题目难度",
	 * 										"user_answer":"题目错误答案"
	 * 									},
	 * 									{},
	 * 									...
	 * 									{}
	 * 								]
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2、失败：
	 *		1、传过来的值为空
	 *			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2、没有更多历史测评分数了！
	 *
	 * 			{
	 * 				"message":"没有更多历史测评分数了！！",
	 * 				"code":"00004"
	 * 			}
	 *
	 *
	 */
	public function getHistoryTest() {
		$_REQUEST = array_merge($_GET,$_POST);

		
//		$subjectPaper = $this->getTimeBeforTest("01","2013-12-30");
//		dump($subjectPaper);
//		dump(json_encode($subjectPaper));
		
		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['subject_type'])) {
				$daybegin = $_REQUEST['daybegin'];
				$subject_type = $_REQUEST['subject_type'];
			
				//拿到某一课程的历史试卷ID
				$subjectPaper = $this->getTimeBeforTest($subject_type,$daybegin);
			//得到了一串paperid
		
			
			$class_id = $this->getStudentClass_id();
			//得到这个班级的所有的人
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					$kaoshiCount = 0;
					//循环这个班级的人，得出每个人的$paper_id所对应的分数
					for ($k=0;$k<$classStudentCount;$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$k]['login']);
						if($score < $thisScore) {
							$percentCount--;
						}
						if(!empty($score) && $score !=0) {
							$kaoshiCount++;
						}
						$sumScore = $sumScore + $score;
						$score = 0;
					}
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$kaoshiCount);
				//	$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					
				}
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '历史测评为空！';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '传送过来的数据为空！';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}
	}

	
/*
	 *
	 * 此方法用于得到某时间以前十条数据的paper
	 */
	private function getTimeBeforTest($subject_type,$daybegin) {
		$student_id = $this->getStudent_id();

		//把是否需要发送给家长的测试找出来,并且是家长已经读的
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='1' AND ep.examdate < '".$daybegin."'")
		->field("epr.paper_id,epr.user_total_score,es.section_title,esparent.section_title parent_section_title,ep.examdate")
		->order('ep.examdate')
		->limit(10)
		->select();
		//dump(M()->getLastSql());
		//dump($paperSublist);
		return $subjectPaper;

		

	}
	
	
	/*
	 * 得到某个人某一张试卷的所有内容
	 * 用户答案
	 * 试卷信息
	 */
	private function getSomePaperDeatls($paper_id) {
		$student_id = $this->getStudent_id();
		
		//根据paper_id得到此试卷的题目、题干、正确答案等，在paper——restut——detal里面有用户的答案也要得到
		$paper_detals=M()->table("ts_evaluation_paper_questions epq ")
		->join("ts_evaluation_question eq on epq.question_id=eq.question_id")
		->join("ts_evaluation_paper_result_detail eprd on eprd.question_id=eq.question_id")
		->where("epq.paper_id='".$paper_id."' AND eprd.user_id='".$student_id."' AND epq.paper_id=eprd.paper_id")
		->field("eq.question_id,epq.question_index,eq.question_content,eq.question_content_text,eq.question_type,eq.answer,eq.difficulties,eprd.user_answer")
		->select();
		return $paper_detals;
		
	}
	
	
		/*
	 *
	 * 此接口用于当点击某一门课程的时候查出此课程的最新测试
	 *
	 * 参数：
	 * 		subject_type ： 点击的课程
	 * 		daybegin: 这个时间之前的测试  2013-10-09
	 * 		dayend: 这个时间之后的测试  2013-10-10
	 *
	 * 请求方式：POST
	 *
	 * 返回：
	 * 1、成功返回数据列表的json格式：
	 * 			[
	 * 				{
	 * 					"score":"当天考试的平均成绩",
	 * 					"exam_date":"2013-11-14 某一天"
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2、失败：
	 *		1、传过来的值为空
	 *			{
	 * 				"message":"传送数据为空！",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2、此时间段没有考试！！
	 *
	 * 			{
	 * 				"message":"没有更多历史测评分数了！！",
	 * 				"code":"00007"
	 * 			}
	 *
	 *
	 */
	public function getTestquXian() {
		$_REQUEST = array_merge($_GET,$_POST);

		$daybegin = $_REQUEST['daybegin'];
		$dayend = $_REQUEST['dayend'];
		$subject_type = $_REQUEST['subject_type'];
		
//		//test
//		
//		$daybegin = "2012-10-09";
//		$dayend = "2014-12-11";
//		$subject_type = "01";
//		$paperSublist = $this->getTimeTest($subject_type, $daybegin, $dayend);
//		dump(M()->getLastSql());
//		dump($paperSublist);
//		exit(json_encode($paperSublist));
		
		
		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['dayend']) && !empty($_REQUEST['subject_type'])) {
			
			
			
			$paperSublist = $this->getTimeTest($subject_type, $daybegin, $dayend);
				
			//得到了一串paperid
			//循环遍历paperid，得到多个测试
			if($paperSublist) {
				exit(json_encode($paperSublist));
					
			}else {
				$message['message'] = '此时间段没有考试！';
				$message['code']    = '00007';
				exit( json_encode( $message ) );
			}

		}else {
			$message['message'] = '传送过来的数据为空！';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}


	}
	
	
	
	/*
	 *
	 * 此方法用于得到某时间段内的试卷
	 */
	private function getTimeTest($subject_type,$daybegin,$dayend) {
		$student_id = $this->getStudent_id();

		
		//把是否需要发送给家长的测试找出来,并且是家长已经读的
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='1' AND examdate between '".$daybegin."' and '".$dayend."'")
		->field("avg(epr.user_total_score) score,convert(examdate,date) exam_date")
		->group('exam_date')
		->order('exam_date desc')
		->select();
		

		return $subjectPaper;

		

	}
	





}

?>
