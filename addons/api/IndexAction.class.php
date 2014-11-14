<?php
/**
 * 成绩分析控制器
 * @author 林佳宝
 * @version TS3.0
 * @date  2013-10-30
 */
class IndexAction extends Action {
	private $flag="0";
	
	/**
	 * 全班正确率统计（饼图）
	 * @return void
	 */
	public function index() {
		
		
		//$this->left();
		$this->gradeAnalysis();
 		$this->knowledge();
 		$this->learningTrend();
 		$this->historyTest();
 		
 		
 		
 		
		$this->display();
	}
	/**
	 * 所有测评中正确率最低的三次测评统计
	 * Enter description here ...
	 */
	public function gradeAnalysis() {
		
		$this->flag="1";
		
		//查询数据库，得到每次考试的平均成绩
		$avgScore = M()->table('ts_evaluation_paper_result')
						->field('avg(user_total_score) ascore')
						
						->group('paper_id')
						->order('ascore')
						->select();
		//调试
		//dump(M()->getLastSql());
		//dump($avgScore);
		//转换为字符串
		$ydata=array();
		for($i=0;$i<count($avgScore);$i++){
			 $ydata[$i] = implode('"',$avgScore[$i]);
		}
		$ydatas=join($ydata, "|");
		
		//dump($ydatas);
		//查询出考试平均分最低的三次考试才信息
		$resultList = M()->table('ts_evaluation_paper_result r ')
							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
							->join('ts_evaluation_paper_classes cs on p.paper_id=cs.paper_id')
							->join('ts_school_classes sc on sc.class_id=cs.class_id')
							->where('cs.class_id=1')
							->group('r.paper_id')
							->limit(3)
							->field('r.user_id,count(r.user_id),avg(r.user_total_score) avg,p.paper_title')
							->select();
		
		$count = M()->table('ts_evaluation_paper_classes')->where('class_id=1')->field('count(paper_id) count')->select();
		//dump($count['count']);
		//$count_total = "$count";					
		$this->assign('flag',$this->flag);
		$this->assign('ydatas',$ydatas);
		$this->assign('resultList',$resultList);
		$this->assign('count',$count);
		$this->display('top3');
	}
	/**
	 * 根据知识点进行统计
	 * Enter description here ...
	 */
 	public function knowledge(){
 		
// 		$data = "1,2,3,4,";
// 		$str = explode(",", $data);
// 		dump($str);
 		
 		
 		$this->flag="2";
 		//获取各章节的title作为统计图的Y轴
 		$datax = M()->table('ts_evaluation_section s ')
 						->join('ts_evaluation_paper p on s.section_id=p.section_id')
 						->join('ts_evaluation_paper_result r on p.paper_id=r.paper_id')
 						->where(' p.class_id=1')
 						->group('p.section_id')
 						->field('s.section_title')->select();
 		
 		$xdata=array();
		for($i=0;$i<count($datax);$i++){
			 $xdata[$i] = implode('"',$datax[$i]);
		}
		$xdatas=join($xdata, "|");	
		//dump($xdatas);	
 		//将datax传到页面中
 		$this->assign('xdatas',$xdatas);				
 		//dump(M()->getLastSql());
 		//dump($ydata);
 		//取出该班级每次考试的平均成绩
 		$ydata = M()->table('ts_evaluation_section s ')
 						->join('ts_evaluation_paper p on s.section_id=p.section_id')
 						->join('ts_evaluation_paper_result r on p.paper_id=r.paper_id')
 						->where(' p.class_id=1')
 						->group('p.section_id')
 						->field('avg(r.user_total_score)')->select();
// 		$ydata = M()->table('ts_evaluation_paper_result r')
// 						->join('ts_evaluation_paper_classes c on r.paper_id=c.paper_id')
// 						->where('c.class_id=1')
// 						->group('c.paper_id')
// 						->field('avg(r.user_total_score)')
// 						->select();
 		
 		$ydatas=array();
		for($i=0;$i<count($ydata);$i++){
			 $ydatas[$i] = implode('"',$ydata[$i]);
		}
		$ydatass=join($ydatas, "|");
 		//dump($ydatass);
 		$this->assign('ydatass',$ydatass);
 		//$data="4|3|5|2|1";
 		//$this->assign('data',$data);
 		$this->assign('flag',$this->flag);
 		//$this->display('knowledge');
 	}
 	/**
 	 * 学习趋势统计
 	 * Enter description here ...
 	 */
 	public function  learningTrend() {
 		
 		$ydata=M()->table('t_evaluation_section')->field('section_title')->select();
 		$this->assign('ydata',$ydata);
 		
 		
		$this->display('trend');
		
 	}
 	/**
 	 * 历史测评
 	 * Enter description here ...
 	 */
 	public function historyTest(){
 		
 		
 		
 		$this->display('history');
 	}
 	
 	
 	public function name(){
 		
 		$this->name_newest();
 		$this->name_test();
 		//$this->name_trend();
 		$this->name_knowledge();
 		$this->display('name');
 	}
 	/**
 	 * 查看最新测评概况的分析
 	 * Enter description here ...
 	 */
 	public function name_newest(){
 		
 		//查出该学生最近一次考试信息
 		$newest_list = M()->table('ts_evaluation_paper_result r ')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where('r.user_id=4 and p.is_examed=1')
 							->order('p.examdate desc')
 							->limit(1)
 							->field('substring(p.examdate,1,10) examdate,s.section_title,r.user_total_score ')
 							->select();
 		//dump($newest_list);
 		$this->assign('newest_list',$newest_list);
 		//查询出最近一次考试的平均分
 		$avg_score = M()->table('ts_evaluation_paper_result r ')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
 							->where('p.is_examed=1')->group('p.paper_id')
 							->order("p.examdate desc")->limit(1)
 							->field("avg(user_total_score) avgscore")
 							->select();
 		$this->assign('avg_score',$avg_score);
 		//查询出试卷的paper_id
 		$paperId=M()->table('ts_evaluation_paper')->where('is_examed=1 ')->order("examdate desc")->limit(1)
 					->field("paper_id")->select();
 		//dump($paperId);
 		//查询出所选学生的最近一次考试的成绩
 		$stuScore=M()->table(" ts_evaluation_paper_result r ")
 						->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 						->where("p.is_examed=1 and r.user_id=4")
 						->order("examdate desc")->limit(1)
 						->field("r.user_total_score")->select();
 		//dump($stuScore);
 		//查询出比所选学生成绩高的学生的成绩
 		$higerScore=M()->table("ts_evaluation_paper_result r ")
 							->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 							->where("p.is_examed=1 and r.user_total_score >=".$stuScore[0]['user_total_score']." and p.paper_id='".$paperId[0]['paper_id']."'")
 							->field('count(*) count1')
 							->select();
 		//dump(M()->getLastSql());
 		//dump($higerScore);
 		//计算百分位
 		$percent=M()->table("ts_evaluation_paper_result r ")
 						->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 						->where("p.is_examed=1 and p.paper_id='".$paperId[0]['paper_id']."'")
 						->field("round('".$higerScore[0]['count1']."'/count(*)*100,0) percent")->select();
 		//dump(M()->getLastSql());
 		//dump($percent);
 		$this->assign('percent',$percent);
 		//$this->display('name_newest');
 	}
 	
 	/**
 	 * 个人测评概况分析统计
 	 * Enter description here ...
 	 */
 	public function name_test(){
 		//查询知识点
 		$section_list = M()->table('ts_evaluation_paper p ')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where('p.is_examed=1')->field('s.section_title')->select();
 		$section=array();
		for($i=0;$i<count($section_list);$i++){
			 $section[$i] = implode('"',$section_list[$i]);
		}
		$sections=join($section, "|");
		//dump($sections);
 		//查询出该生的每次测评的成绩
 		$score_list = M()->table('ts_evaluation_paper p')
 							->join('ts_evaluation_paper_result r on p.paper_id=r.paper_id ')
 							->where('is_examed=1 and r.user_id=4')
 							->field('r.user_total_score')
 							->select();
 		$score=array();
		for($i=0;$i<count($score_list);$i++){
			 $score[$i] = implode('"',$score_list[$i]);
		}
		$scores=join($score, "|");
		//dump($scores);
 		//查询出出每次测评的全班的平均分
 		$avg_list = M()->table('ts_evaluation_paper p ')
 							->join('ts_evaluation_paper_result r on p.paper_id=r.paper_id')
 							->where('p.is_examed=1')
 							->group('p.paper_id')
 							->field('avg(r.user_total_score)')
 							->select();
// 		dump(M()->getLastSql());
// 		dump($avg_list);
 		$avg=array();
		for($i=0;$i<count($avg_list);$i++){
			 $avg[$i] = implode('"',$avg_list[$i]);
		}
		$avgs=join($avg, "|");
		//dump($avgs);
 		
		
		//将所需参数传到页面中
		$this->assign('sections',$sections);
		$this->assign('scores',$scores);
		$this->assign('avgs',$avgs);
		
		//$this->display('name');
		
 	}
 	
// 	public function name_trend(){
// 		
// 		
// 		
// 		$this->display('name');
// 	}
 	/**
 	 * 知识点测评错误分析
 	 * Enter description here ...
 	 */
 	public function name_knowledge(){       
 		//查询出该生的所有考试信息
 		$exam_list = M()->table('ts_evaluation_paper_result r')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id ')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where('p.is_examed=1 and r.user_id=4')
 							->field('p.paper_id,substring(p.examdate,1,10) examdate,s.section_title,p.paper_type')
 							->select();
 		
 		//dump(M()->getLastSql());
 		//dump($exam_list);
 		$this->assign('exam_list',$exam_list);
 		//查询出每次考试的信息
 		$detail_list = M()->table('ts_evaluation_question q ')
 							->join('ts_evaluation_paper_result_detail d on q.question_id=d.question_id')
 							->join('ts_evaluation_paper p on d.paper_id=p.paper_id')
 							->where('d.user_id=4 and p.is_examed=1 and p.paper_id=1')
 							->order('q.id')
 							->select();
 		$this->assign('detail_list',$detail_list);
 		//$this->display('name_knowledge');
 		
 	}
 	
 	public function section(){
 		
 		$this->section_current();
 		$this->section_avg();
 		$this->display("section");
 	}
 	/**
 	 * 查询出所选章的所有考试信息
 	 * Enter description here ...
 	 */
 	public function section_current(){
 		//
 		$section_id=M()->table('ts_evaluation_section s ')->where('s.parent_section_id="01"')
 						->field('s.section_id section_id')->select();
 		for($i = 0 ;$i<count($section_id);$i++) {
 			$sec[$i] = "'".(string)$section_id[$i]['section_id']."'";
 		}
 		//dump($sec);
 		$map["p.section_id"]=array('in',$sec);
 		//查询该章下所有的测评信息
 		$section_list=M()->table('ts_evaluation_paper_result r ')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where($map)
 							->group('s.section_id')
 							->field('s.section_title,substring(p.examdate,1,10) examdate,count(r.user_id) count,round(avg(r.user_total_score),0) countStu ')
 							->select();
 		//dump($section_id);
 		//dump(M()->getLastSql());
 		//dump($section_list);
 		
 		$zong_count=M()->table('ts_evaluation_paper_result r ')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where($map)
 							->group('s.section_id')
 							->field('count(r.user_id) count_zong')
 							->select();
 		//dump($zong_count);	
 		//查询出本章考试中所有测评中及格的人数
 		$map['_string']='r.user_total_score>60';
 		$count=M()->table('ts_evaluation_paper_result r ')
 							->join('ts_evaluation_paper p on r.paper_id=p.paper_id')
 							->join('ts_evaluation_section s on p.section_id=s.section_id')
 							->where($map)
 							->group('s.section_id')
 							->field('count(r.user_id) count')
 							->select();		
 		//dump(M()->getLastSql());
 		//dump($count);
 		//计算出及格率$count_per
 		$count_per[]=array();
 		for($k=0;$k<count($zong_count);$k++){
 			$count_per[$k]=round(($count[$k]['count']/$zong_count[$k]['count_zong'])*100);
 		}
 		//dump($count_per);
 		
 		//查询出每张卷子的题数
 		$count_question=M()->table("ts_evaluation_paper p")
 							->join("ts_evaluation_paper_questions q on p.paper_id=q.paper_id")
 							->where("p.is_examed=1 ")->group("q.paper_id")
 							->field("count(q.question_id) sumquestion")->select();
 		//dump($count_question);
 		
// 		dump($section_list);
// 		dump($count_per);
// 		dump($count_question);
 		
 		for($k=0;$k<count($zong_count);$k++){
 			$section_list[$k]['count_per'] = $count_per[$k];
 			$section_list[$k]['count_question'] = $count_question[$k]['sumquestion'];
 		}
 		
 		//dump($section_list);
 		//die("111111111111111111");
 		$this->assign('section_list',$section_list);	
// 		$this->assign('count_per',$count_per);
// 		$this->assign('count_question',$count_question);
 		
 	}
 	/**
 	 * 查询出所选章的所有测评的平均成绩
 	 * Enter description here ...
 	 */
 	public function section_avg(){
 		//查询所选章下的所有section_id
 		$section_id=M()->table('ts_evaluation_section s ')->where('s.parent_section_id="01"')
 						->field('s.section_id section_id')->select();
 		for($i = 0 ;$i<count($section_id);$i++) {
 			$sec[$i] = "'".(string)$section_id[$i]['section_id']."'";
 		}
 		//dump($sec);
 		$map["p.section_id"]=array('in',$sec);
 		$map["_string"]="p.is_examed=1";
 		//查询出y轴要显示的值
 		$section_avg = M()->table("ts_evaluation_paper_result r ")
 							->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 							->join("ts_evaluation_section s on s.section_id=p.section_id")
 							->where($map)
 							->group("p.paper_id")
 							->field("round(avg(r.user_total_score),0) avg")->select();
 		dump($section_avg);
 		dump(M()->getLastSql());
 		//查询出平均分
 		$section_title = M()->table("ts_evaluation_paper_result r ")
 							->join("ts_evaluation_paper p on r.paper_id=p.paper_id")
 							->join("ts_evaluation_section s on s.section_id=p.section_id")
 							->where($map)
 							->group("p.paper_id")
 							->field("s.section_title title")->select();
 		$avg=array();
		for($i=0;$i<count($section_avg);$i++){
			 $avg[$i] = implode('"',$section_avg[$i]);
			 
		}
		$avgs=join($avg, "|");

		$title=array();
		for($i=0;$i<count($section_title);$i++){
			 $title[$i] = implode('"',$section_title[$i]);
		}
		$titles=join($title, "|");
		dump($avgs);
		dump($titles);
// 		die("******************");	
 		$this->assign('avgs',$avgs);
 		$this->assign('titles',$titles);					
 	}
 	
 	
}
?>





























