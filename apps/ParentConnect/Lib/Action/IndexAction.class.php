<?php 
/*ParentConnect IndexAction
 * @author
 * by terry fu  */

class IndexAction extends Action {
	/* function Index  
	 * 
	 * 入口*/
	function Index() {
		//dump("234234234234324234");
 		$currentUser=$GLOBALS['ts']['user']['uname'];//获取当前用户的信息
		$this->assign("uname",$currentUser);
		$classList=	$this->getCurrentClassList();//调用获取班级列表
		$this->assign('classlist',$classList);
		
		if(!empty($_REQUEST['parentId']))//执行留言 
		{
			//dump($_REQUEST['parentId']);
			//$pa = $_REQUEST['parentId'];
			$this->replyMessage($_REQUEST['parentId'],$_REQUEST['content'],$_REQUEST['messageId'],$_REQUEST["paperId"]);
			//$this->ajaxReturn($pa);			
		}		
		//add start baoyt 20131124
		if(!empty($_REQUEST['paper_id'])) {
			$this->updateStatus($_REQUEST['paper_id']);
		}
		//add end baoyt 20131124
		
		$evaluationList=$this->getAllEvaluation();//获得该班级所有的测评
		$this->assign("evaluationlist",$evaluationList);
		
		$messageParentTT=$this->getMessageFromParent();//获得该教师第一次给家长发送的消息
		$this->assign("messageparenttotech",$messageParentTT);//留言模板赋值
		//$messageAllOfTeacher=$this->getMessageFromTeacher();//获得该教师的所有会话
		//$this->assign("messageAllOfTeacher",$messageAllOfTeacher);//模板赋值
		$num=$this->getConversationNum();
			$this->assign("replynum",$num); 
		
		/*给学生家长发送消息  */
     	$this->display('index');	
	}
	
	private function getConversationNum()
	{
		$currentUser=$GLOBALS['ts']['user']['login'];//获取当前用户的信息	
		$replyNum=M()->table("ts_evaluation_paper_result_messages eprm")
			->where("eprm.staff_id='".$currentUser."' and eprm.comefrom='1'")
			->field("count(id) num")
			->select();
			return $replyNum[0]['num'];
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
 			if($teac_class[$i]['subject_type']!=="00")
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
	/*	$count=M()->table('ts_evaluation_paper_classes epc')
						->join('ts_evaluation_paper ep on ep.paper_id=epc.paper_id')
						->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
						->field('id')->count();*/
// 		dump($count);
		//dump($count);	
		//dump(M()->getLastSql());						
		if(!empty($selectedClassId))
		{
			$evalustionNum=M()
			->table('ts_evaluation_paper_classes epc')
			->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
			->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
			->field('count(ep.paper_id) num')->select();//获取该班测评列表数量
			//dump($evalustionNum);
			//dump(M()->getLastSql());
			$Page = new Page ( $evalustionNum[0]['num'], 10 );//实例化分页类
			$evaluationList=M()
			->table('ts_evaluation_paper_classes epc')
			->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
			->where("epc.class_id='".$selectedClassId."' and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
			->field('ep.paper_id,createdate,paper_title')
			->limit( $Page->firstRow.','.$Page->listRows)
			->select();//获取测评列表
			
			$show=$Page->show();// 分页显示输出\
			//return $evaluationList;
			//$this->assign("evaluationlist",$evaluationList);	//模板赋值and ep.createdby='".$GLOBALS['ts']['user']['login']."'")			
			$this->assign("pages",$show);
		}
		else
		{
			$classid=$this->getCurrentClassList();//获得班级列表
			//dump($classid[0]['classid'].$classid[0]['classname']);
			$defaultClass=$classid[0]['classid'];//默认选中第一个班级
		/*	foreach ($classid as $k=>$v)
			{
				$classIdList="'".$v['classid']."',";
			}
			$afterHandleClassIdList=substr($classIdList,0,-1);*/
			$evalustionNum1=M()
				->table('ts_evaluation_paper_classes epc')
				->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
				->where("epc.class_id='".$defaultClass."' and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
				->field('count(ep.paper_id) num')->select();//获取该班测评列表数量
			$Page1 = new Page ( $evalustionNum1[0]['num'], 10 );//实例化分页类
			
			
			$evaluationList=M()
			->table('ts_evaluation_paper_classes epc')
			->join('ts_evaluation_paper  ep on ep.paper_id=epc.paper_id')
			//->where("epc.class_id in (".$afterHandleClassIdList.") and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
			->where("epc.class_id in (".$defaultClass.") and epc.school_period_id='".$currentSchoolPeriod."' and paper_exam_status='2' and  ep.createdby='".$GLOBALS['ts']['user']['login']."'")
			->field('ep.paper_id,createdate,paper_title')
			->limit( $Page1->firstRow.','.$Page1->listRows)
			->select();//获取测评列表
			
			$show=$Page1->show();// 分页显示输出\
			//return $evaluationList;
			//$this->assign("evaluationlist",$evaluationList);	//模板赋值
			$this->assign("pages",$show);
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
		
		//dump($defaultClass);
		if($selectedClassId=="")
		$selectedClassId=$defaultClass;
		//add start by baoyt 20131124
		$evaluationListCount = count($evaluationList);
		for ($i=0;$i<count($evaluationList);$i++)
		{
			$examdate = $this->getExamDate($evaluationList[$i]['paper_id'],$selectedClassId );
			$evaluationList[$i]['examdate'] = substr($examdate[0]['examdate'],0,10);
			$evaluationList[$i]['selectedClassId'] = $selectedClassId;
			$evaluationList[$i]['section_title'] = $examdate[0]['section_title'];

			//得到这个班级的所有的人
			$classStudent = $this->getStudentClass($selectedClassId);
			$classStudentCount = count($classStudent);
			
			//$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
			$percentCount = $classStudentCount;
			$kaoshiCount = 0;
			$sumScore = 0;
			$tmp_score = 0;
			$tmp_status = "0";
			//循环这个班级的人，得出每个人的$paper_id所对应的分数
			
			for ($k=0;$k<$classStudentCount;$k++) 
			{
				if (!$this->isStudentInPaperResult($evaluationList[$i]['paper_id'], $classStudent[$k]['login']))					
					continue;

				$scoreList = $this->getStudentScore($evaluationList[$i]['paper_id'], $classStudent[$k]['login']);
				if($scoreList) {
					$score = floatval($scoreList[0]['user_total_score']);
				}else {
					$score =  "0";
				}
				
				$evaluationList[$i]['student'][$k]['login'] = $classStudent[$k]['login'];
				$evaluationList[$i]['student'][$k]['uname'] = $classStudent[$k]['uname'];
				$evaluationList[$i]['student'][$k]['parent_id'] = $classStudent[$k]['parent_id'];
				$evaluationList[$i]['student'][$k]['score'] = $score;
				$evaluationList[$i]['student'][$k]['wrong_rate'] = $this->getWrongRate($score);
				$tmp_status = $evaluationList[$i]['student'][$k]['send_status'] = $scoreList[0]['send_status1'];
				$evaluationList[$i]['student'][$k]['msg_status'] = $this->getResultMsg(
																		$evaluationList[$i]['paper_id']
 																		,$classStudent[$k]['parent_id']);
				$evaluationList[$i]['student'][$k]['percentRate']=$this->getPercentRate($evaluationList[$i]['paper_id'],$classStudent[$k]['login'],$selectedClassId);
				
				//如果$score为空，则考试人数减1
				if(!empty($score) && $score !=0) {
					$kaoshiCount++;
				}
	
				$sumScore = $sumScore + $score;
				$score = 0;
	
			}
			
			$evaluationList[$i]['percent'] = strval(intval($percentCount/$classStudentCount * 100));
			//
			$evaluationList[$i]['send_status'] = $tmp_status;
				
			$evaluationList[$i]['avg'] = strval(intval($sumScore/$kaoshiCount));			
			 
		}
			
		//dump($evaluationList);
		
		return $evaluationList;
		
		
		
	}
	

	
	
//add start by baoyt 20131124
	private function updateStatus($paper_id)
	{
		//$paper_id = $_REQUEST['paper_id'];
		$data['send_status']='1';
			
		$result_001=M()->table('ts_evaluation_paper_result')
					   ->where("paper_id='".$paper_id."'")->save($data);
		
	}
	
	
	
	/*回复消息  */
	function insertMessages() {
		$paperId = $_REQUEST['paper_id'];
		$parentId = $_REQUEST['parent_id'];
		$content = $_REQUEST['content'];
		
		$currentPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
		//$messageId=$_POST['messageid'];//获得要回复的信息id
		$replySender=$GLOBALS['ts']['user']['login'];//登陆教师的id

		//获得message_id
		$messageId=M()->table("ts_evaluation_paper_result_messages eprm")
		->field("count('eprm.id')+1 num")->select();
		$data['school_period_id']=$currentPeriod;//获得当前日期
		$data['message_id']=$messageId[0]['num'];//回复id
		$data['paper_id']=$paperId;
		$data['staff_id']=$replySender;//当前老师
		$data['message_content']=$content;
		$data['sendmessage_date']=date("Y-m-d H:i:s",time());
		$data['message_receiver']=$parentId;
		$data['isread']='0';
		$data['comefrom']='0';
		//$data['lastmessage_id']=$messageId[0]['num']-1;//回复id
	
		$result=M()->table("ts_evaluation_paper_result_messages")->add($data);
		//dump($data);
		//dump(M()->getLastSql());
		if($result)
		{
			$this->ajaxReturn('回复成功！',1,1);	
		}
		else
		{
				
		}
		
	
	
	}
	
	private function getWrongRate($score)
	{
		return (1-$score/100) * 100;
	}
	
	private function getExamDate($paper_id,$class_id)
	{
		$section_book_id = $this->getSectionBookid($class_id);
		$examList = M()->table("ts_evaluation_paper ep")
		->join('ts_evaluation_section se on ep.section_id=se.section_id')
		->where("ep.paper_id='".$paper_id."' and se.section_book_id='".$section_book_id[0]['section_book_id']."'")
		->field('ep.examdate, se.section_title')
		->select();


		return $examList;
	}
	
	private function getSectionBookid($class_id)
	{
		$sectionbookList = M()->table("ts_school_gl_cb_sub_gb gl")
							->join('ts_school_classes cl on gl.grade_id=cl.grade_id')
							->where("cl.class_id='".$class_id."'")
							->select();

		return $sectionbookList;
	}
	
	private function getStudentClass($class_id) 
	{
		$class_student_cllist = M()->table("ts_school_class_students scs, ts_user u, ts_students_join_users tju")
		->where(" u.login=scs.login and tju.student_id=scs.login and scs.class_id='".$class_id."'")
		->field('scs.class_id,scs.login, u.uname, tju.staff_id parent_id')
		->select();

		return $class_student_cllist;
	}
	
	private function isStudentInPaperResult($paper_id,$student_id)
	{
		$studentcount = M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."'")
		->field("count(epr.id) AS count1")
		->select();
		
		return (intval($studentcount[0]['count1']) != 0?true:false);
		
	}
	
	private function getStudentScore($paper_id,$student_id) 
	{
		$studentScore = M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."'")
		->field("epr.user_total_score, ifnull(epr.send_status, '0') send_status1")
		->select();
		
		return $studentScore;

	}
	
	private function getResultMsg($paper_id, $parent_id)
	{
		$messageCount = M()->table("ts_evaluation_paper_result_messages epr")
		->where("epr.paper_id='".$paper_id."' AND epr.staff_id='".$GLOBALS['ts']['user']['login']."' 
				and epr.message_receiver='".$parent_id."' and epr.comefrom='0' and lastmessage_id is null")
		->field("count(id) count1")
		->select();
	
		return $messageCount[0]['count1'];
	
	}
	
	
	//add end by baoyt 20131124

	private function getPercentRate($paper_id,$stuId,$classId)
	{
		//获得该班参加考试学生的成绩排名
		$studentGrade=M()->table("ts_evaluation_paper_result epr")
			->join(" inner join ts_evaluation_paper_classes epc on epr.paper_id=epc.paper_id ")
			->join(" inner join ts_school_class_students scs on scs.login=epr.user_id and epc.class_id=scs.class_id ")
			->where("epc.paper_id='".$paper_id."' and epc.class_id='".$classId."'")
			->field("epr.user_id")
			->order("user_total_score desc")
			->select();
		//dump($studentGrade)	;
		//dump(M()->getLastSql());
		$studentGradeNum=count($studentGrade);
		
		for($i=0;$i<$studentGradeNum; $i++)
		{
			if($studentGrade[$i]['user_id']==$stuId)
			{
				$re=(intval($i/$studentGradeNum*100))==0?1:(intval($i/$studentGradeNum*100));
				$percentRate=$re."%";
				break;
			}
		}
		return $percentRate;
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

	//usort排序
/*	private function my_compare($a, $b) { 
		//if ($a['sendmessage_date'] < $b['sendmessage_date']) 
		//if (strtotime($a[3]) < strtotime($b[3])) 
		if(date("Y-m-d h:i:s",strtotime($a['sendmessage_date']))<date("Y-m-d h:i:s",strtotime($b['sendmessage_date'])))
			return -1; 
		//else if ($a['sendmessage_date'] == $b['sendmessage_date']) 
		//else if (strtotime($a[3]) == strtotime($b[3]))
		else if(date("Y-m-d h:i:s",strtotime($a['sendmessage_date']))==date("Y-m-d h:i:s",strtotime($b['sendmessage_date']))) 
			return 0; 
		else 
			return 1; 
	} */
	
	
	/*展示该教师某一个学期内家长发送来收到的消息*/
 	private function getMessageFromParent() {
			//$currentUser=;//获取当前用户的信息
			$currentSchoolPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
			$currentStaff_Id=$GLOBALS['ts']['user']['login'];//当前教师的id，staff_id
		/* 头一条是教师发送给家长的，lastmessage_id为空 	先找到lastmessage_id不为空的，因为回复的头一条是家长发送给老师的。
		 * 根据家长id进行分组。并且comeform=1，证明是家长发送给老师的。
			 */
			$messageParentToTech=M()->table('ts_evaluation_paper_result_messages eprm')//先查询出来老师第一次给家长发送的消息
			->where(" comefrom='0' and school_period_id='".$currentSchoolPeriod."' and eprm.staff_id='".$currentStaff_Id."' and (eprm.lastmessage_id is null or eprm.lastmessage_id = '') ")
			->field('eprm.message_id,eprm.staff_id,eprm.message_content,eprm.sendmessage_date,eprm.comefrom,eprm.message_receiver,eprm.paper_id')
			//->order("eprm.sendmessage_date asc")
			->group('eprm.message_receiver')
			->select();
			//echo "========================教师首次给家长发送消息====================";
			//dump($messageParentToTech);
			//dump(M()->getLastSql());
			for($i=0;$i<count($messageParentToTech);$i++)
			{
				$receiver=$messageParentToTech[$i]['message_receiver'];//获得家长手机号
				//获得学生id
				$stuId=M()->table("ts_user u")
					->join("left join ts_students_join_users s on s.staff_id=u.login")
					->where("u.login='".$receiver."' and u.profile_id='2'")
					->field("s.student_id ")
					->select();
					//dump($stuId);
					//dump(M()->getLastSql());	
				$stuName=M()->table("ts_user u")
					->where("u.login='".$stuId[0]['student_id']."'")
					->field("u.uname")
					->select();	
					//dump(M()->getLastSql());				
				$messageParentToTech[$i]['conversation']=$this->getMessageFromTeacher($receiver);
				$messageParentToTech[$i]['studentname']=$stuName[0]['uname'];
			}
			//echo"===========================输出的所有的会话根据家长分组===========================";
			//dump($messageParentToTech);
			//dump(M()->getLastSql());
/*	 		$date2=$messageParentToTech[0]['sendmessage_date'];
			$date=$messageParentToTech[1]['sendmessage_date'];
	
			if(date("Y-m-d h:i:s",strtotime($date))>date("Y-m-d h:i:s",strtotime($date2))){
	   		 	echo "Y";
			}
			else{
	    		echo "N";
			}*/

				//$getReplyMe=$this->getMessageFromTeacher();//获得教师发给家长的消息
	
			
			//echo "========================教师回复家长信息====================";
			//dump($getReplyMe);
			//dump(M()->getLastSql());
			//$arrTemp = array_merge($messageParentToTech, $getReplyMessaages);
			//echo "--------------------------------------------";
			//dump($arrTemp);
			
			//$combinearray=array_merge($messageParentToTech,$getReplyMessaages);//合并两个数组结果集,追加
			//$combinearray=array();

			//$ParentToTechNum=count($messageParentToTech);//家长回复数量
			//$ReplyMessaages=count($getReplyMe);//教师回复数量
			//echo"================教师回复数量===============";
			//dump($ReplyMessaages);
			/*for($i=0;$i<($ParentToTechNum+$ReplyMessaages);$i++)	
			{
				if($i<$ParentToTechNum)
				{	
					//echo"===============if==".dump($i)."=============";				
					$combinearray[$i]["message_id"]=$messageParentToTech[$i]["message_id"];
					$combinearray[$i]["staff_id"]=$messageParentToTech[$i]["message_id"];
					$combinearray[$i]["message_content"]=$messageParentToTech[$i]["message_content"];
					$combinearray[$i]["sendmessage_date"]=$messageParentToTech[$i]["sendmessage_date"];
					$combinearray[$i]["comefrom"]=$messageParentToTech[$i]["comefrom"];
					$combinearray[$i]["message_receiver"]=$messageParentToTech[$i]["message_receiver"];					
				}
				else 
				{
					//echo"==============getReplyMe===================";
					//dump($getReplyMe);
					//dump($getReplyMe[$i]["message_id"]."    ".dump($i)."  ".$getReplyMe[$i]["staff_id"]);					
					$combinearray[$i]["message_id"]=$getReplyMe[$i-$ParentToTechNum]["message_id"];
					$combinearray[$i]["staff_id"]=$getReplyMe[$i-$ParentToTechNum]["staff_id"];
					$combinearray[$i]["message_content"]=$getReplyMe[$i-$ParentToTechNum]["message_content"];
					$combinearray[$i]["sendmessage_date"]=$getReplyMe[$i-$ParentToTechNum]["sendmessage_date"];
					$combinearray[$i]["comefrom"]=$getReplyMe[$i-$ParentToTechNum]["comefrom"];
					$combinearray[$i]["message_receiver"]=$getReplyMe[$i-$ParentToTechNum]["message_receiver"];
				}
			}		 						
				//$afterSortBydateMessageArray=usort($combinearray,cmp);
			//echo"=====================合并后的数组====================";	
			//dump($combinearray);
						
			foreach ($combinearray as $key => $value) {
				//echo $key.$value.$value['sendmessage_date']."<br/>";
				$name[$key] = $value['sendmessage_date'];
				//$rating[$key] = $value['rating'];
			}
			array_multisort($name, $combinearray); 
			//echo"=====================排序后的数组====================";
			//dump($combinearray);
			//return $messageParentToTech;
			//$this->assign("messageparenttotech",$messageParentToTech);//给模板赋值	*/
			return 	$messageParentToTech;							
	}
	
	/*展示教师回复家长的消息
	 *reply-id=message 不为空，并且等于当前家长的回复老师的message_id,  */
	private function getMessageFromTeacher($parentId) {
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
		$getReplyMessaages=M()->table("ts_evaluation_paper_result_messages eprm")//再查询所有属于该老师的会话记录				
				->where("eprm.staff_id='".$currentStaff_Id."' and eprm.lastmessage_id <>'' and eprm.message_receiver='".$parentId."'")
				->field("eprm.message_id,eprm.staff_id,eprm.message_content,eprm.sendmessage_date,eprm.comefrom,eprm.message_receiver,eprm.paper_id")
				->order("eprm.sendmessage_date asc")
				->select();
			//echo"=====================教师和家长的会话========================";
			//dump($getReplyMessaages);
			//dump(M()->getLastSql());
		/*$resultArray1=array();
		while ($row = mysql_fetch_array($getReplyMessaages, MYSQL_NUM)) {
				array_push($resultArray1, $row);
				}
		dump($resultArray1);*/
			return $getReplyMessaages;
		//$this->assign("getreplymessages",$getReplyMessaages);
	}	
	
	/*回复消息  */
	private	function replyMessage($parentId,$content,$lastMessageId,$paperIdFromMsg) {
		$currentPeriod=$GLOBALS['ts']['teacher_subject_classes'][0]['school_period_id'];//获取当前学期id
		$messageId=$_POST['messageid'];//获得要回复的信息id
		$replySender=$GLOBALS['ts']['user']['login'];//登陆教师的id
		//获得paper_id
		/*$paperId=M()->table("ts_evaluation_paper_result_messages eprm")
			->where("eprm.staff_id='".$replySender."' and eprm.lastmessage_id=''")
			->field("eprm.paper_id")
			->limit('1')
			->select();*/
		//获得message_id
		$messageId=M()->table("ts_evaluation_paper_result_messages eprm")
			->field("100001+count(id) num")->select();
		$data['school_period_id']=$currentPeriod;//获得当前日期
		$data['message_id']=$messageId[0]['num'];//回复id
		$data['paper_id']=$paperIdFromMsg;
		$data['staff_id']=$replySender;//当前老师
		$data['message_content']=$content;
		$data['sendmessage_date']=date("Y-m-d H:i:s",time());
		$data['message_receiver']=$parentId;
		$data['isread']='0';
		$data['comefrom']='0';
		$data['lastmessage_id']=$lastMessageId;//回复id
		
		$result=M()->table("ts_evaluation_paper_result_messages")->add($data);
		dump($data);
		dump(M()->getLastSql());
		if($result)
		{
			
		}
		else 
		{
			
		}
		
		
	}
}