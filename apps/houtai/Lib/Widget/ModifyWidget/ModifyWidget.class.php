<?php 
	class ModifyWidget extends Widget {
		public function render($data){
			//接受参数
			$gradeMsg=$data['gradeMsg'];
			//dump($gradeMsg);
			//dump($mGrade[0]['school_id']);
			//$var['gradeMsg']=$this->modifyGrade($gradeid);
			$var['gradeMsgOut']=$gradeMsg;
			//dump($var);
			$content = $this->renderFile(dirname(__FILE__) . "/modify.html", $var);
			return $content;
		}
	}
?>