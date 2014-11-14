<?php 
/*ParentConnect IndexAction
 * @author
 * by terry fu  */

class IndexAction extends Action {
	/* function Index  
	 * 
	 * 入口*/
	function Index() {
 		//$currentUser=$GLOBALS['ts']['user'];//获取当前用户的信息
// 		dump($currentUser['login']);
		$classList=	$this->getCurrentClassList();//调用获取班级列表
		$this->assign('classlist',$classList);
		//班级
		$evaluationList=$this->getAllEvaluation();//获得该班级所有的测评
		$this->assign("evaluationlist",$evaluationList);
		//获得该教师收到的家长留言
		$messageParentToTech=$this->getMessageFromParent();
		$this->assign("messageparenttotech",$messageParentToTech);//留言模板赋值
		/*获取一次测评，所有学生的成绩情况  */
		
		/*给学生家长发送消息  */
     	$this->display('index');	
	}
	
	/*获得班级列表  */
	public function getCurrentClassList()
	{
/*  		$currentUser=$GLOBALS['ts']['user'];//获取当前用户的信息		
		$count=M()->table('ts_school_classes sc')
		->join('ts_teacher_subject_classes tsc on tsc.class_id=sc.class_id')
  		->where("tsc.login='".$currentUser['login']."'")
// 		->where("tsc.login='13511111111'")
		->field('sc.class_id,sc.class_name')->count();//获取当前教师所教授班级的数量
 		if($count>0)//如果当前用户拥有班级
 		{
 			$currentClassList=M()->table('ts_school_classes sc')
 			->join('ts_teacher_subject_classes tsc on tsc.class_id=sc.class_id')
 			->where("tsc.login='".$currentUser['login']."'")
 			// 		->where("tsc.login='13511111111'")
 			->field('sc.class_id,sc.class_name')->select();//获取当前教师教授的班级列表
//  			dump("有班级");
// 		dump($currentClassList);//打印当前班级列表
  		$this->assign('cuttentclasslist',$currentClassList);//给模板赋值	
 		}
 		else//如果当前用户没有班级
 		{
//  			dump("没有班级。");
 		} */
 		
 		$currentUserId=$GLOBALS['ts']['user']['login'];//获取当前用户信息
 		$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
 		$classList=array();
 		$teac_class=  $GLOBALS['ts']['teacher_subject_classes'];
 		for($i=0;$i<count($teac_class);$i++){
 			if($teac_class[$i]['subject_type_desc']!=="班主任")
 			{
 				$classList[$i]['classname']=$teac_class[$i]['class_name'];
 				$classList[$i]['classid']=$teac_class[$i]['class_id'];
 			}
 		}
 		//dump($classList);
 		return $classList;
	}
	
	/*获得该班级所有的测评
	 * 
	 * $selectedClassId :选取的班级id */
  public function getAllEvaluation()
	{	
		import('ORG,Util.Page');//分页
		
		$selectedClassId=isset($_REQUEST['selectedClassId'])?$_REQUEST['selectedClassId']:"";
		$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
		$count=M()->table('ts_evaluation_paper_classes epc')
						->join('ts_evaluation_paper ep on ep.paper_id=epc.paper_id')
						->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."'")
						->field('id')->count();
// 		dump($count);	
		if(!empty($selectedClassId))
		{
			$evaluationList=M()
			->table('ts_evaluation_paper_classes epc')
			->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
			->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2'")
			->field('ep.paper_id,createdate,paper_title')->select();//获取测评列表
			return $evaluationList;
			//$this->assign("evaluationlist",$evaluationList);	//模板赋值
		}
		else
		{
			$classid=$this->getCurrentClassList();//获得班级列表
			foreach ($classid as $k=>$v)
			{
				$classIdList="'".$v['classid']."',";
			}
			$afterHandleClassIdList=substr($classIdList,0,-1);
				
			$evaluationList=M()
			->table('ts_evaluation_paper_classes epc')
			->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
			->where("epc.class_id in (".$afterHandleClassIdList.") and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2'")
			->field('ep.paper_id,createdate,paper_title')->select();//获取测评列表
			
			
			return $evaluationList;
			//$this->assign("evaluationlist",$evaluationList);	//模板赋值
		}
/* 		if($count>0)
		{
			$evaluationList=M()			
						->table('ts_evaluation_paper_classes epc')
						->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
						->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."'")
						->field('createdate,paper_title')->select();//获取测评列表
			$this->assign("evaluationlist",$evaluationList);	//模板赋值
		} */

	}
	
	/*获得测评中每个学生的成绩  
	 * 
	 *$evaluationId:测评id */
	function getEvaluationGrade($evaluationId)
	{	
		//$$evaluationId=isset()
		if(!empty($evaluationId)&&!empty($_POST['classId']))
		{
			$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
			//$currentPeriod=$GLOBALS['schoolPeriod'];//获取当前学期
			$classId=$_POST['classId'];//选中班级id
			/*学生姓名，学生学号，考试时间，考试成绩，班级平均分，  */
			$evaluationGrade=M()->table('ts_evaluation_paper ep')
				->join("ts_user u on u.uid=ep.user_id")
				->join("ts_evaluation_paper_result epr on epr.paper_id=ep.parper_id and epr.class_id=ep.class_id")
				->where("ep.class_id='".$_POST['classId']."'"."and"."ep.paper_id='".$evaluationId."' and ep.school_period_id='".$currentSchoolPeriod."'")
				->field('u.uname,u.profile_no,examdate,ep.user_total_score,avg(ep.user_total_score) avgScore');
			$this->assign("evaluationgrade",$evaluationGrade);
			
			/*错题率
			 * 用户的答案与正确答案不符，即为错题 
			 * strcmp(trim('eprd.user_answer'),trim('teq.answer'))=0判断字符串相等 */	
			//查出试卷中的试题数量
			$questionNum=M()->table('ts_evaluation_paper ep')
				->join("ts_evaluation_paper_questions epq on epq.paper_id=ep.paper_id")
				->where("ep.class_id='".$classId."'"."and ep.paper_id='".$evaluationId."' and ep.school_period_id='".$currentSchoolPeriod."'")
				->field('question_id')->count();
			//保存此次测评的学生id
			$userId=array();//保存学生id的数组			
			$userId=M()
				->table("ts_evaluation_paper_result epr")
				/* ->where("paper_id='".$evaluationId."'and epr.class_id='".$classId."' and epr.school_period_id='".$currentPeriod."'") */
				->where("paper_id='".$evaluationId."' and school_period_id='".$currentSchoolPeriod."'")
				->field('user_id')->select();
			//计算错题率					
			$errorPercent=array();//声明存储错误率的数组
			for ($i=0; $i<count($userId);$i++)//循环测评本次学生，给该学生赋错误率
			{
				$errorPercent[$i]=M()
				->table('ts_evaluation_paper_result_detail eprd')
				->join("ts_evaluation_question teq on teq.question_id=eprd.question_id")
				->where("eprd.user_id='".$userId[$i]."'and"."paper_id='".$evaluationId."'"."and"."strcmp(trim('eprd.user_answer'),trim('teq.answer'))=0")
				->field("count(eprd.id)/".$questionNum." err")->select();
			}
			$this->assign("errorpercent",$errorPercent);//模板赋值
			
			/*百分位 
			 * 取出排序后的成绩到数组中。获取索引在经过处理可得百分位 */	
			$originalUId=M()->table('ts_evaluation_paper_result epr')
					->where("epr.class_id='".$classId."'and epr.paper_id='".$evaluationId."' and school_period_id='".$currentSchoolPeriod."'")
					->field('user_id')
					->select();
			//取出排序,仅取出userid
			$gradeRank=M()->table('ts_evaluation_paper_result epr')
					/* ->where("epr.class_id='".$classId."'and epr.paper_id='".$evaluationId."'") */
					->where(" epr.paper_id='".$evaluationId."' and school_period_id='".$currentSchoolPeriod."'")
					->order("user_total_score")
					->field('user_id')
					->select();
			$percentLocation=array();
			for ($k=0;$k<count($originalUId);$k++)//循环遍历原始学生uid
			{				
				for($n=0;$n<count($gradeRank);$n++)//循环遍历按成绩排序后的uid
				{
					if($originalUId[$k]==$gradeRank[$n])//两个数组中的值相等
					$percentLocation[$k]=$n+"%";//百分位
				}
			}
			$this->assign("percentlocation",$percentLocation);//模板赋值		
		}
		else
		{
			$this->error('未获得测评！');
		}		
	}
	
	/*给特定学生的家长发送消息
	 * 
	 * $evaluationId:测评id*/
	function sendMessage($evaluationId)
	{
		$currentUser=$GLOBALS['ts']['user'];//获取当前用户的信息
		$classId=$_POST['classId'];//选定的班级id
		$stuId=$_POST['stuId'];//选定学生的id
		$currentPeriod=$GLOBALS['schoolPeriod'];//获取当前学期
		if(!empty($evaluationId)&&!empty($classId)&&empty($stuId))//测评id,班级id，学生id
		{
			$messageDetail=$_POST['message'];//向特定学生家长发送的消息
			//获得参加这次测评的学生家长的id students_join_users
// 			$stuParentId=array();//保存学生家长id的数组
			$stuParentId=M()//获得发送学生家长id
			->table("ts_evaluation_paper_result epr")
			->join("students_join_users ju on ju.student_id=epr.user_id ")
			->where("paper_id='".$evaluationId."' and epr.user_id='".$stuId."' and epr.school_period_id='".$currentPeriod."'")
			->field('ju.staff_id')->select();
			//执行插入操作
			$data['paper_id']=$evaluationId;//测评id
			$data['school_perid_id']=$currentPeriod;//当前日期
			$data['sendmessage_date']=date('y-m-d H:i:s',time());//当前时间
			$data['staff_id']=$currentUser['login'];//教师id
			$data['message_count']=$messageDetail;//留言内容
			$data['message_receiver']=$stuParentId;//当前学生家长的id
			$recNumber=M()->table('ts_evaluation_paper_result_messages eprm')
			->field('count(id)')->select();//获得当前表中的数据量
			if($recNumber!=0)
			{
				$data['message_id']=100002+$recNumber;
			}
			else
			{
				$data['message_id']=100001;
			}
			$tseprm=M('ts_evaluation_paper_result_messages');//获得试卷留言表对象
			/* $i=0;
			for ($i;$i<count($stuParentId);$i++)
			{
			$data['message_receiver']=$stuParentId[$i];//当前学生家长的id
			$tseprm->add($data);//执行加载
			} */		
			if($tseprm->add($data))
			{
				$this->success('消息发送成功！');
			}
			else
			{
				$this->error('消息发送失败！');
			}
		}
		else
		{
			$this->error('未获得班级编号评测编号！');
		}
	}
	
	/*某次测评后，向所有学生家长发送消息  
	 * 
	 * $evaluationId:测评id*/
	function sendMessageAll($evaluationId) 
	{
		$currentUser=$GLOBALS['ts']['user'];//获取当前用户的信息
		$classId=$_POST['classId'];//选定的班级id
		$currentPeriod=$GLOBALS['schoolPeriod'];//获取当前学期
		if(!empty($evaluationId)&&!empty($classId))//测评id
		{		
			$messageDetail=$_POST['messageToAll'];//向所有学生家长发送的消息
			//获得参加这次测评的学生家长的id students_join_users
			$stuParentId=array();//保存学生家长id的数组
			$stuParentId=M()
			->table("ts_evaluation_paper_result epr")
			->join("students_join_users ju on ju.student_id=epr.user_id ")
/* 			->where("paper_id='".$evaluationId."'and ep.class_id='".$classId."'") */
			->where("paper_id='".$evaluationId."'and epr.school_period_id='".$currentPeriod."'")
			->field('ju.staff_id')->select();
			//执行插入操作
			$data['school_perid_id']=$currentPeriod;//当前学期id
			$data['paper_id']=$evaluationId;//测评id
			$data['staff_id']=$currentUser['login'];//教师id
			$data['message_count']=$messageDetail;//留言内容			
			$recNumber=M()->table('ts_evaluation_paper_result_messages eprm')
			->field('count(id)')->select();//获得当前表中的数据量
			if($recNumber!=0)
			{
				$data['message_id']=100002+$recNumber;
			}
			else
			{
				$data['message_id']=100001;
			}
			$data['sendmessage_date']=date('y-m-d H:i:s',time());//发送时间			
			$tseprm=M('ts_evaluation_paper_result_messages');//获得试卷留言表对象
			$i=0;
			for ($i;$i<count($stuParentId);$i++)
			{
				$data['message_receiver']=$stuParentId[$i];//当前学生家长的id
				$tseprm->add($data);//执行加载								
			}	
			if($i==count($stuParentId))
			{
				$this->success('消息发送成功！');
			}					
			else
			{
				$this->error('消息发送失败！');
			}
		}
		else
		{
			$this->error('班级或评测没有获得！');
		}
	}
		
	/*展示该教师某一个学期内家长发送来收到的消息*/
 private function getMessageFromParent() {
			//$currentUser=;//获取当前用户的信息
			$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
			$currentStaff_Id=$GLOBALS['ts']['user']['login'];//当前教师的id，staff_id
		/* 头一条是教师发送给家长的，lastmessage_id为空 	先找到lastmessage_id不为空的，因为回复的头一条是家长发送给老师的。
		 * 根据家长id进行分组。并且comeform=1，证明是家长发送给老师的。
			 */
			$messageParentToTech=M()->table('ts_evaluation_paper_result_messages eprm')
			->where(" comefrom='1' and school_period_id='".$currentSchoolPeriod."' and eprm.message_receiver='".$currentStaff_Id."'")
			->field('eprm.message_id,eprm.message_content,eprm.sendmessage_date,eprm.staff_id')
			->order("eprm.sendmessage_date asc")
			//->group('message_receiver')
			->select();
			echo "========================家长回复教师信息====================";
			//dump($messageParentToTech);
			dump($messageParentToTech);
			dump(M()->getLastSql());
		 /* 	for($i=0;$i<count($messageParentToTech);$i++)
				{ 
					
					$this->displayMessageFromTeacher($messageParentToTech[$i]['staff_id']);
				}  */
				
				//$getReplyMessaages=$this->getMessageFromParent();//获得教师发给家长的消息
					
				//$combinearray=array_merge($messageParentToTech,$getReplyMessaages);//合并两个结果集
					
				//$afterSortBydateMessageArray=usort($combinearray,cmp);
					
				//dump($combinearray);
				
				
			return $messageParentToTech;
			//$this->assign("messageparenttotech",$messageParentToTech);//给模板赋值									
	}
	
	/*展示教师回复家长的消息
	 *reply-id=message 不为空，并且等于当前家长的回复老师的message_id,  */
	private function displayMessageFromTeacher($currentParentId) {
		//$currentUser=$GLOBALS['ts']['user'];//获取当前用户的信息
		$currentStaff_Id=$GLOBALS['ts']['user']['login'];//登陆教师的id
		$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
		//$currentParentId=$_POST['parentid'];//当前加载的家长id
		//$currentMessageId=$_POST['repmessageid'];//家长回复教师的messageid
		//$currentSchoolPeriod=$GLOBALS['schoolperiod'];//当前学期id
		//if(!empty($currentParentId))

		/* 	$getReplyMessaages=M()->table("ts_message_reply mr")
				->join("ts_user u on u.uid=mr.reply_sender")
				->where("reply_id='".$currentMessageId."' and school_period_id='".$currentSchoolPeriod."' and mr.reply_sender='".$currentStaff_Id."'")
				->field('reply_date,reply_content,u.uname')
				->select(); */
			$getReplyMessaages=M()->table("ts_evaluation_paper_result_messages eprm")
				
				->where("eprm.staff_id='".$currentStaff_Id."' and eprm.comefrom='0'and eprm.lastmessage_id<>'' and eprm.message_receiver='".$currentParentId."'")
				->field("eprm.staff_id,eprm.message_content,eprm.sendmessage_date")
				->order("eprm.sendmessage_date asc")
				->select();
			echo"=====================教师回复家长信息========================";
			dump($getReplyMessaages);
			dump(M()->getLastSql());
			
			$this->assign("getreplymessages",$getReplyMessaages);
	}	
	
	/*回复消息  */
	function replyMessage() {
		$currentPeriod=$GLOBALS['school_perid_id'];//当前学期id
		$messageId=$_POST['messageid'];//获得要回复的信息id
		$replySender=$_POST['teacherid'];//教师id
		$replyReceiver=$_POST['parentid'];//家长id
		$messageContent=$_POST['messagecontent'];//回复内容
		/*教师回复消息，将消息放到replay表中message_id与当前家长发给老师的id一致。*/
		$messageReply=M('ts_message_reply');//获取messagereply的表
		$data['school_period_id']=$currentPeriod;//获得当前日期
		//查询该表中的记录数量，把数量+初始化的值，赋值给新插入的记录
		$recNumber=$messageReply->field('count(id)')->select();//查询当前记录数量		
		if($recNumber!=0)
		{
			$data['message_id']=10001+$recNumber;
		}
		else 
		{
			$data['message_id']=10000;
		}
		$data['reply_id']=$messageId;//要回复的id，留言表中的message_id		
		$data['reply_sender']=$replySender;
		$data['reply_receiver']=$replyReceiver;
		$data['reply_date']=date('y-m-d H:i:s',time());//回复时间
		$data['reply_content']=$messageContent;//回复内容
// 		$data['last_reply_id'];//
		$result=$messageReply->add($data);
		if($result)
		{
			$this->success('消息回复成功！');
		}
		else 
		{
			$this->error('消息回复成功！');
		}
		
	}
}