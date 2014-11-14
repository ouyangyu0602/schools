<?php 
	/*
	 * @author by terry  
	 * 
	 * testquestion控制器
	 * */
	class TestQuestionsAction extends Action {
		
		//入口
		public function testquestions(){
		/* 	if(!empty($_REQUEST['sectionbookid']))
			{
				$this->getUnit($_REQUEST['sectionbookid']);
			} */
			$this->assign("sectionbook",$this->getSectionBooks());		
			//$this->assign("coursehour",$this->getCourseHour());
			//$this->assign("questions",$this->getQuestions());
			//$this->getQuestions();
			$this->display();
		} 
		
		/*获取教材
		 * 参数：无  */
		private function getSectionBooks()
		{
			$sectionBook=M()
			->table("ts_evaluation_section_book esb")
			->field("section_book_id sbi,section_book_title sbt")
			->select();
			//dump($sectionBook);
			//dump(M()->getLastSql());			
			return $sectionBook;
		}
		
		/*获取教材单元及课程名称
		 * 参数：教材id  */
		public function getUnit()
		{
			//$sectionid=1;//教材id
			$sectionid=$_REQUEST['sectionbookid'];
			$bookUnit=M()
			->table("ts_evaluation_section es")
			->where("es.section_book_id='".$sectionid."' and es.parent_section_id=''")
			->field("es.section_title ust,es.section_id usid")
			->select();
			for($i=0;$i<count($bookUnit);$i++)
			{
				$bookUnit[$i]['coursename']=$this->getCourseTitle($sectionid,$bookUnit[$i]['usid']);
			}
			//$this->getCourseTitle();
			//dump("=====================单元名称========================");
			//dump($bookUnit);
			//return $bookUnit;
			//echo"34543";
			$this->assign("unitandcourse",$bookUnit);
			$this->ajaxReturn($bookUnit,'',1);
			//$this->display();
		}
		
		/*获取课程名称
		 * 参数：教材id
		 * 参数：单元id  */
		private function getCourseTitle($sbid,$usid)
		{
			$coursetitle=M()
			->table("ts_evaluation_section es1")
			->where("es1.section_book_id='".$sbid."' and es1.parent_section_id='".$usid."'")
			->field("es1.section_title cst,es1.section_id csid")
			->select();
			//dump("====================课程名称========================");
			//dump($coursetitle);
			//dump(M()->getLastSql());
			return $coursetitle;
		}
				
		/*获取课程课时 
		 * 参数：教材id
		 * 参数：目录id */
		public function getCourseHour()
		{
			$sectionBookId=$_REQUEST['sectionbookid'];
			$sectionId=$_REQUEST['sectionid'];
			$coursehour=M()
			->table("ts_evaluation_section es2")
			->where("es2.section_book_id='".$sectionBookId."' and es2.parent_section_id='".$sectionId."'")
			->field("es2.section_title hst,es2.section_id hsid")
			->select();
			//dump($coursehour);
			//dump(M()->getLastSql());
			//return $coursehour;
			$this->ajaxReturn($coursehour,'',1);
		}
		
		/* 获取课时中的试题
		 *参数：教材id
		 *参数：课时id */
		
		public function getQuestions()
		{
			$sectionbookid=$_REQUEST['sectionbookid'];
			$sectionid=$_REQUEST['sectionid'];
			//$sectionid='01-05-02';
			//$sectionbookid='2';
			$questions=M()
			->table("ts_questionpool tq")
			->where("tq.section_book_id='".$sectionbookid."' and tq.section_id='".$sectionid."'")
			->field("tq.question_content_text qc,tq.question_index qi")			
			->select();
			/*
			for ($i=0;$i<count($questions);$i++)
			{
				if(false!==strpos($questions[$i]['qc'],'[img]'))
				{
					//dump('存在【img】'.($i+1));
					//$questions[$i]['qc']=$this->handleImg($questions[$i]['qc']);
				}
			}
			*/
			$this->ajaxReturn($questions,'',1);
			//dump($questions);
			//$this->assign('questionsTest',$questions);
		}
		
		/*处理试题中图片展示问题  */
		private function handleImg($str)
		{
			/*
			 * /\[img\]/  */
			$str=preg_replace("/\[img\]{1,}/",'<img src="/', $str);
			//dump($str);
			$str=preg_replace("/\[\\\img\]{1,}/",'" alt=" "/>' , $str);
			//$str=preg_replace("[\[img\]]",$str);
			//$str=$str.str_replace("/{[img]}+/", "/");
			//$str=$str.str_replace("/[\\img]/", " ");
			dump($str);
			return $str;
		}
	}

?>