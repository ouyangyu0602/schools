<?php
class QuestionListViewWidget extends Widget {



    /*
     * array(1) {
    ["questionListArray"] => array(11) {
    ["app"] => string(8) "question"
    ["mod"] => string(12) "QuestionEdit"
    ["act"] => string(16) "ajaxQuestionList"
    ["gradeListIDView"] => string(9) "1307S1702"
    ["knowledgeLevel1ID"] => string(12) "110104960551"
    ["knowledgeLevel2ID"] => string(17) "11010496055124911"
    ["knowledgeLevel3ID"] => string(22) "1101049605512491139393"
    ["questionTypeIDView"] => string(1) "1"
    ["questionDifficultIDView"] => string(1) "2"
    ["questionTeacherLoginView"] => string(11) "13711715571"
    ["whereIDView"] => string(1) "2"
  }
}
     *
     *
     * 根据上面条件判断查询即可
     *
     */
	public function render($data)
	{

        $data = array_filter($data['questionListArray'],function($v) {
            return !empty($v);
        });
        $questionList = $this->getQuestionList($data);
        //dump($questionList);


		$var['arrayListView'] = $questionList;
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__) . "/question.html", $var);

		return $content;
	}


    private function getQuestionList($map=null){
        $result = M('uteach_question')->where($map)->select();
        if($result){
            $uteach_knowledge = M('uteach_knowledge');
            foreach($result as $res) {

                if(!empty($res["knowledge_level1"])) {
                    $level1 = $uteach_knowledge->where("knowledge_id = '".$res["knowledge_level1"]."'")->field("knowledge_name")->find();

                }
                if(!empty($res["knowledge_level2"])) {
                    $level2 = $uteach_knowledge->where("knowledge_id = '".$res["knowledge_level2"]."'")->field("knowledge_name")->find();
                }
                if(!empty($res["knowledge_level3"])) {
                    $level3 = $uteach_knowledge->where("knowledge_id = '".$res["knowledge_level3"]."'")->field("knowledge_name")->find();
                }
                $questionKnowledge = $res;

                $questionKnowledge['levelKnowledgeName'] = $level1["knowledge_name"].'  /  '.$level2["knowledge_name"].'  /  '.$level3["knowledge_name"];
                /*$questionKnowledge['level2KnowledgeName'] = $level2;
                $questionKnowledge['level3KnowledgeName'] = $level3;*/

                $questionKnowledgeArray [] = $questionKnowledge;
            }

            return $questionKnowledgeArray;
        }

    }

}

