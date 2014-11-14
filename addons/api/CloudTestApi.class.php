<?php
/**
 *
 * @author jason
 *
 */

class CloudTestApi {

	/*
	 * 传入老师的id，根据老师id和家长id定位这个老师给他发的
	 */
private function getSomeNewMessages($message_receiver) {
		
		
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		
		//查出某一个老师发给家长的未读的消息，并要区分是老师给家长发的留言，数据库表中加字段，不仅要是未读的，而且是老师给家长发送的。
		$teacher_idarr = M()->table("ts_evaluation_paper_result_messages eprm")
		->where("eprm.message_receiver='".$message_receiver."' AND eprm.isread = '0' AND comefrom='0' AND staff_id='".$login."'")->select();
		
		//每次查完后，需要修改字段
		M()->table("ts_evaluation_paper_result_messages")->where("eprm.message_receiver='".$message_receiver."' AND eprm.isread = '0' AND comefrom='0' AND staff_id='".$login."'")->setField('isread','1');
		
		return $teacher_idarr;
	}
	
	/*
	 * 返回此学生科目、老师、消息数量，也就是留言页面
	 * 初始化
	 */
	public function getinitIndex() {
		//得到了主页面科目
		$indexSubject = $this->getSubject();
	
	//根据科目
	
		
	
			for ($i = 0; $i < count($indexSubject); $i++) {
				$subject = $this->getSomeNewMessages($indexSubject[$i]['login']);
				$indexSubject[$i]['newMessages'] = $subject;
				$indexSubject[$i]['newMessagesCount'] = count($subject);
				$indexSubject[$i]['test'] = $this->getsomeNewSubjectTest($indexSubject[$i]['subject_type']);
				$indexSubject[$i]['testcount'] = count($this->getsomeNewSubjectTest($indexSubject[$i]['subject_type']));
			}
			
			
			exit(json_encode($indexSubject));
	
	}
	
	/*
	 * 第一步
	 * 登录后，首先看可用否
	 */
	public function getIndex() {
		$indexSubject = $this->getSubject();
	
		exit(json_encode($indexSubject));

	}
	
	
	
	/*
	 * 获取当前家长的id
	 * 
	 */
	private function getStaff() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		return $user['login'];
	}
	
	
	/*
	 * 根据学生ID得出相对应老师以及科目的所有信息
	 * 
	 * 
	 */
	private function getSubject() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		//根据班级得到此班级相关的老师以及科目
		
		$teach_su_cllist = M()->table("ts_teacher_subject_classes tsc")
        	->join("ts_school_class_students scs on scs.class_id=tsc.class_id")
        	->join("ts_user u on u.login=tsc.login")
        	->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
        	->where("scs.login='".$student_id."' and sm.subject_type_desc<> '班主任'")
        	->field('sm.subject_type,sm.subject_type_desc,scs.class_id,scs.grade_id,scs.school_id,u.uid,u.login,u.uname')
        	->select();

        	return $teach_su_cllist;
	}
	
	
	
	
	
	/*
	 * ts_evaluation_paper
	 * ts_evaluation_paper_result
	 * 
	 * 找到某学生某一门课程最新的测试试卷
	 * 
	 * 传送过去了此门课程最新的所有test，还不是细节
	 * 
	 */
	private function getsomeNewSubjectTest($subject_type) {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		
		//把是否需要发送给家长的测试找出来,并且是家长未读的
		$paperSublist = M()->table("ts_evaluation_paper_result epr")
        	->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
        	->where("ep.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status = '1' AND epr.is_read='0'")
        	->select();
        //dump(M()->getLastSql());
		return $paperSublist;
		
	}
	
	
	/*
	 * 
	 * 写
	 */
	private function getsomeSubjectTest($subject_type,$daybegin,$dayend) {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		
		//把是否需要发送给家长的测试找出来,并且是家长未读的
		$paperSublist = M()->table("ts_evaluation_paper_result epr")
        	->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
        	->where("ep.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status = '1' AND epr.is_read='1' examdate between '".$daybegin."' and '".$dayend."'")
        	->select();
        //dump(M()->getLastSql());
		return $paperSublist;
		
	}
	
	
	/*
	 * 客户端发送科目、试卷id
	 * 
	 * 
	 */
	public function getNewTest() {
		//得到一个关于某一门课程试卷id的数组，最新的就发已经发送过去了的
		$_REQUEST = array_merge($_GET,$_POST);
		$paper_json = $_REQUEST['paper_json'];
		//把传过来的json字符串转换，得到对象数组
			$paperarr = json_decode($paper_json);

			//把对象数组转换为数组键值对形式
			$paperarr = $this->object_array($paperarr);
	for($i=0;$i<count($paperarr);$i++) {
				$paperAll[$i] = $this->getSomeOneTestDeatls($paperarr[$i]['paper_id']);
			}
			
			exit(json_encode($paperAll));
	}
	
	/*
	 * 传开始时间结束时间、学科
	 */
	public function getHistoryTest() {
		$_REQUEST = array_merge($_GET,$_POST);

		$daybegin = $_REQUEST['daybegin'];
		$dayend = $_REQUEST['dayend'];
		$subject_type = $_REQUEST['subject_type'];
		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['dayend'])) {
			$paperSublist = $this->getsomeSubjectTest($subject_type,$daybegin,$dayend);
			//得到了一串paperid
			//循环遍历paperid，得到多个测试
			for($i=0;$i<count($paperSublist);$i++) {
				$paperAll[$i] = $this->getSomeOneTestDeatls($paperSublist[$i]['paper_id']);
			}
			
		}
		
		exit(json_encode($paperAll));
		
	}
	
	/*
	 * 
	 * 得到某一次测评的
	 * 试卷id
	 * 
	 * 测评时间paper表里有
	 * 知识点通过ts_evaluation_section得到标题
	 * 得分：根据当前学生，传送过来的paper得到得分
	 * 平均分，根据paper得到所有的
	 * 百分位
	 */
	private function getSomeOneTestDeatls($paper_id) {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		
		
		
		//查询知识点
 		$section_list = M()->table('ts_evaluation_paper p ')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where("p.paper_id='".$paper_id."'")
 							->field('s.section_title')
 							->select();
 							
 		//查询出学生的某试卷的成绩以及考试时间
 		$stuScore=M()->table(" ts_evaluation_paper_result r ")
 						->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 						->where("p.paper_id='".$paper_id."'and r.user_id='".$student_id."'")
 						->field("r.user_total_score,p.examdate")->select();
 		//dump($stuScore);
 		//查询出比所选学生成绩高的学生的成绩
 		$higerScore=M()->table("ts_evaluation_paper_result r ")
 							->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 							->where("p.is_examed=1 and r.user_total_score >=".$stuScore[0]['user_total_score']." and p.paper_id='".$paper_id."'")
 							->field('count(*) count1')
 							->select();
 		//dump(M()->getLastSql());
 		//dump($higerScore);
 		//计算百分位
 		$percent=M()->table("ts_evaluation_paper_result r ")
 						->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 						->where("p.paper_id='".$paper_id."'")
 						->field("round('".$higerScore[0]['count1']."'/count(*)*100,0) percent")->select();
 		
		//根据paper_id得到此试卷的题目、题干、正确答案等，在paper——restut——detal里面有用户的答案也要得到
		
 		
 		
 						
 						
		
	}
	
	
	/*
	 * 通过当前登录用户，到ts_evaluation_paper_result_messages表中查得未读
	 * 统计条数，传过去
	 * 然后针对老师，在当前用户所在班级中所担任的课程
	 */
	public function getNewMessages() {
		
		
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		dump($student_idarr);
		
		//根据学生得到班级
		$class_idarr = M()->table("ts_school_class_students scs")->where("scs.login='".$student_id."'")->select();
		$class_id = $class_idarr[0]['class_id'];
		
		dump($class_idarr);
		
		$teacher_idarr = M()->table("ts_evaluation_paper_result_messages eprm")
		->where("eprm.message_receiver='".$login."' AND eprm.isread = '0'")->select();
		
		
		
		//查出所有的消息，分别取出
		$teacher_idcount = count($teacher_idarr);
			
			for ($i = 0; $i < $teacher_idcount; $i++) {
				$message[$i] = M()->table("ts_teacher_subject_classes tsc")
        	->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
        	->join("ts_schools s on s.school_id=tsc.school_id")
        	->where("tsc.login='".$teacher_idarr[$i]['staff_id']."' AND tsc.class_id='".$class_id."'")
        	->select();

        	M()->table("ts_evaluation_paper_result_messages")->where("id='".$teacher_idarr[$i]['id']."'")->setField('isread','1');
				

			}
		
			dump($message);
			exit(json_encode($message));
//		M()->table("ts_evaluation_paper_result_messages eprm")
//        	->join("ts_teacher_subject_classes tsc on sc.class_id=tsc.class_id")
//        	->join("ts_school_class_students scs on scs.login='".$student_id."'")
//        	->join("ts_schools s on s.school_id=tsc.school_id")
//        	->where("tsc.login='".$login."'");                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
//		dump();
//		die("////////////////////////////");
//		
//		//通过家长得到学生
//		//通过学生得到班级
//		//通过老师和班级，在老师班级科目表中查出科目
//		$teach_su_cllist = M()->table("ts_teacher_subject_classes tsc")
//        	->join("ts_school_classes sc on sc.class_id=tsc.class_id")
//        	->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
//        	->join("ts_schools s on s.school_id=tsc.school_id")
//        	->where("tsc.login='".$login."'")
//        	->select();
//        												
//        	
//        	
//        	$GLOBALS['ts']['teacher_subject_classes'] = $teach_su_cllist;
		
	}

	/*
	 * 得到历史消息
	 * 得到某一个老师以前的消息
	 * 传过来的有
	 * staff_id 老师ID，login
	 * 开始时间begintime
	 * 结束时间endtime
	 * 
	 * 
	 */
	
	public function getHistoryMessages() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		//$login = $user['login'];
		$login = '13451672388';
		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];
		
		
		//根据学生得到班级
		$class_idarr = M()->table("ts_school_class_students scs")->where("scs.login='".$student_id."'")->select();
		$class_id = $class_idarr[0]['class_id'];
		
		$teacher_idarr = M()->table("ts_evaluation_paper_result_messages eprm")->where("eprm.message_receiver='".$login."' AND eprm.isread = '1'")->select();
		//查出所有的消息，分别取出
		$teacher_idcount = count($teacher_idarr);
			
			for ($i = 0; $i < $teacher_idcount; $i++) {
				$message[$i] = M()->table("ts_teacher_subject_classes tsc")
        	->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
        	->join("ts_schools s on s.school_id=tsc.school_id")
        	->where("tsc.login='".$teacher_idarr[$i]['staff_id']."' AND tsc.tsc.class_id='".$class_id."'")
        	->select();
	
				

			}
			
			exit(json_encode($message));
			
	}
	
	
	/*
	 * 根据传过来的科目ID、用户(科目已知)
	 * 通过ts_evaluation_paper_result试卷成绩表得出试卷ID，跟试卷表ts_evaluation_paper关联，查出某一段时间的测评
	 * 
	 * 
	 */
	
	
	

	
	
	
	/*
	 * 判断传过来的时间
	 * 1、否超过当今时间：页面控制
	 * 2、开始时间大于结束时间：页面控制
	 * 3、如果传过来的数据在数据库中没有找到的时候，提示用户，没有当前
	 * 4、在传时间上，提示用户控制
	 *
	 */
	public function listPaper() {
		$_REQUEST = array_merge($_GET,$_POST);

		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['dayend'])) {
			$daybegin = $_REQUEST['daybegin'];
			$dayend = $_REQUEST['dayend'];
			dump($daybegin);
			dump($dayend);
			$Paper = M('Evaluation_paper');
			$map['examdate'] = array('between',array("'".$daybegin."'","'".$dayend."'"));
			$paperList = $Paper->where($map)->select();
				
			//			$paperList = M()->table('ts_evaluation_paper')
			//						->where('examdate between "'.$daybegin.'" and "'.$dayend.'"')
			//						->select();
			dump(M()->getLastSql());
			dump($paperList);
			exit(json_encode($paperList));
		}else {
			$message['message'] = '查找失败：考试区间时间为空！';
			$message['code']    = '00003';
			exit(json_encode($message));
		}

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

	
	
	
	
}

?>