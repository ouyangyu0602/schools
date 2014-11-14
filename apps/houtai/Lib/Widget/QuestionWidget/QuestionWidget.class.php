<?php
class QuestionWidget extends Widget {


	public function render($data)
	{
		$evaluationId=$data['evaluationId'];
		$classId=$data['classId'];
		//dump($evaluationId.$classId);
		// TODO: Implement render() method.
		//$question_ids = array_values($data['question_ids']);
		
		//$timufenye=$data['timu_fenye'];
		//dump($data);
		/*if (count($question_ids) > 0)
		 $condition = $question_ids[0];
		for ($_id = 1; $_id < count($question_ids); $_id++) {
		$condition .= "," . $question_ids[$_id];
		}*/

		// 获取相关数据
		/*$list = M()->table('ts_evaluation_question')
		 ->field("id question_id,question_content_text, answer, keyword, difficulties, IFNULL(use_count, 0) use_count")
		->where('id in (' . $condition . ')')
		->select();

		foreach ($question_ids as $key => $value) {
		foreach ($list as $nkey => $nvalue) {
		if ($nvalue['question_id'] == $value) {
		$var['examlist'][] = $nvalue;
		}
		}
		}*/
		//dump($timufenye);
		
		if(!empty($evaluationId)&&!empty($classId))
		{
		//$url = $_SERVER['QUERY_STRING'];
		//dump($url);
		//import('ORG,Util.nPage');//分页
		//分页总数
		//$timuCount=M()->table('ts_evaluation_question a,ts_evaluation_paper_questions b')
		//->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'".' limit  10' )
		//->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'" )
		//->field('count(a.question_content_text) num')
		//->select();
		//$WtimuPage = new nPage( $timuCount[0]['num'], 1 );//实例化分页类
		//查询班级名称，试卷标题，
			
		//dump($titleClass);
		//dump(M()->getLastSql());
		// $list=M()->table('ts_evaluation_question')->join('ts_evaluation_paper_questions  on  ts_evaluation_paper_questions.question_id = ts_evaluation_question.question_id and'. ' ts_evaluation_paper_questions.paper_id='."'$evaluationId'".' limit  10' )->field('ts_evaluation_question.question_content_text')->select();
		$question_content_list=M()->table('ts_evaluation_question a,ts_evaluation_paper_questions b')
		//->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'".' limit  10' )
		->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'" )
		->field('a.question_content_text,b.question_index')
		//->limit( $WtimuPage->firstRow.','.$WtimuPage->listRows)
		->select();
		//dump(M()->getLastSql());
			
		//dump($question_content_list);
		//$Wtimushow=$WtimuPage->show();// 分页显示输出\
		
		$this->disTitleClass($classId, $evaluationId);
		
		
		
		
		$var['exam_list'] = $question_content_list;
		//dump($var['exam_list']);
		//$var['fenye']=$Wtimushow;
		//$var['titleClass']=$data['titleClass'];
		//dump($var['titleClass']);
		}
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__) . "/question.html", $var);
		// 输出数据
		//dump(dirname(__FILE__));                                                                                                                                                                     
		//dump($content);
		return $content;
	}
	
//展示试卷
	public function getQuestionContentList($evaluationId,$selectClsId){
		
		//$evaluationId=isset($_REQUEST['ids'])?$_REQUEST['ids']:'';
		/* $evaluationId=$_REQUEST['ids']==""?'':$_REQUEST['ids'];
		$selectClsId=$_REQUEST['claId']==""?'':$_REQUEST['claId']; */
		//dump($selectClsId);
		//dump($evaluationId);
		if(!empty($evaluationId)&&!empty($selectClsId))
		{
			
			import('ORG,Util.Page');//分页
			//分页总数
			$timuCount=M()->table('ts_evaluation_question a,ts_evaluation_paper_questions b')
			//->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'".' limit  10' )
			->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'" )
			->field('count(a.question_content_text) num')
			->select();	
			$timuPage = new Page ( $timuCount[0]['num'], 1 );//实例化分页类
			//查询班级名称，试卷标题，
			
			//dump($titleClass);
			//dump(M()->getLastSql());
		// $list=M()->table('ts_evaluation_question')->join('ts_evaluation_paper_questions  on  ts_evaluation_paper_questions.question_id = ts_evaluation_question.question_id and'. ' ts_evaluation_paper_questions.paper_id='."'$evaluationId'".' limit  10' )->field('ts_evaluation_question.question_content_text')->select();
		$question_content_list=M()->table('ts_evaluation_question a,ts_evaluation_paper_questions b')
			//->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'".' limit  10' )
			->where('b.question_id = a.question_id and  b.paper_id='."'$evaluationId'" )
			->field('a.question_content_text')
			->limit( $timuPage->firstRow.','.$timuPage->listRows)
			->select();
		//dump(M()->getLastSql());
		 
		//dump($question_content_list);
		$timushow=$timuPage->show();// 分页显示输出\
		//dump($show);
		//试题名称，班级名称,'classPaperTitle'=>$classTItle
		$classTItle=$this->disPaperTitle($evaluationId, $selectClsId);
		$html = W('Question', array('question_ids' => $question_content_list,'timu_fenye' => $timushow,'classPaperTitle'=>$classTItle));
		//dump($html);
		exit($html);
		}
		//$this->display('index');
	}
	
	
	
	/*  */
	
	
	
	//获取试卷题目，班级
	private function disTitleClass($classid,$paperId)
	{
		$titleClass=M()->table("ts_evaluation_paper ep")
		->join("ts_evaluation_paper_classes epc on epc.paper_id=ep.paper_id")
		->join("ts_school_classes sc on sc.class_id=epc.class_id")
		->join("ts_evaluation_section es on ep.section_id=es.section_id")
		->join("ts_evaluation_section es1 on es1.section_id=es.parent_section_id")
		->join("ts_evaluation_section es2 on es2.section_id=es1.parent_section_id")
		->where("epc.class_id='".$classid."' and ep.paper_id='".$paperId."'")
		->field("ep.paper_id,es2.section_title zhang ,es1.section_title jie ,es.section_title keshi,sc.class_name")
		->select();
		//dump($titleClass);
		//dump(M()->getlastsql());
		session_start();
		if(isset($_SESSION["test"]["titleCls"]))
			//$_SESSION['titleCls']='';//重置session
			$_SESSION["test"]["titleCls"]="";
			//unset($_SESSION["titleCls"]);	
		$_SESSION["test"]["titleCls"]=$titleClass;//给session赋值}
		//dump($_SESSION["test"]["titleCls"]);
		return $titleClass;
	}


}

