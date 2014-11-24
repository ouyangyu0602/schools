<?php
/**
 * 课表控制器
 * @author
 *
 */
class QuestionEditAction extends Action {


    private function publicFuction($grade_list_id){


        //得到七年级上学期数学知识点的顶层目录
        $knowledgeList = M('Knowledge')->getKnowledgeLever($grade_list_id, '1','0');

        $knowledgeListLevel1 = M('Knowledge')->getKnowledgeLever($grade_list_id, '1','0');

        foreach($knowledgeListLevel1 as $knowledge1){
            $knowledgeListLevel2 = M('Knowledge')->getKnowledgeLever($grade_list_id, $knowledge1['level']+1,$knowledge1['knowledge_id']);
            $level1 = $knowledge1;
            foreach($knowledgeListLevel2 as $knowledge2) {
                $level2 = $knowledge2;
                $knowledgeListLevel3 = M('Knowledge')->getKnowledgeLever($grade_list_id, $knowledge2['level']+1,$knowledge2['knowledge_id']);

                $level2['level3'] = $knowledgeListLevel3;
                $level1['level2'][] = $level2;
            }

            $knowledgeListArray['level1'][] =  $level1;

        }
        $this->assign('grade_list_id',$grade_list_id);
        $this->assign('knowledgeListArray', $knowledgeListArray);

        $this->assign('knowledgeList', $knowledgeList);

    }

    private function getUpdateQuestion($updateQuestionID){
        $result = M('uteach_question')->where("id = ".$updateQuestionID)->find();
        return $result;

    }



	public function index() {


        //如果设置了questionID，那说明是更新操作
        if(isset($_REQUEST['questionID']) && !empty($_REQUEST['questionID'])) {
            $updateQuestion = $this->getUpdateQuestion($_REQUEST['questionID']);
            if(!empty($updateQuestion)) {
                $this->assign("questionClick",$updateQuestion);

            }
        }

        if(isset($_REQUEST['isSubmitForm']) && !empty($_REQUEST['isSubmitForm'])){ //进行更新操作
             $update= $this->updateQuestion($_REQUEST['isSubmitForm']);
            if(!empty($update)) {
                $this->assign("questionClick",$update);
            }
        } elseif(isset($_REQUEST['isSubmitForm']) && empty($_REQUEST['isSubmitForm'])){ //进行保存操作
            $update = $this->submitImportQuestion();
            if(!empty($update)) {

                $this->assign("questionClick",$update);
            }

        }

        $userInfo['uname'] = $GLOBALS['ts']['user']['uname'];
        $userInfo['login'] = $GLOBALS['ts']['user']['login'];
        $userInfo['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $subjectId = M('teacher_subject_classes')
                        ->field('subject_id')
                        ->where("login = ".$userInfo['login']." AND school_id = ".$userInfo['school_id'])
                        ->find();

        dump(M()->getLastsql());
        dump($subjectId);
        die();
        $gradeList = D("Knowledge")->getGradeList(null,$subjectId['subject_type']);
        //年级学年的id
        if(empty($_REQUEST['sort_order'])) {
            $sort_order = '12';//七年级上学期数学
        } else {
            $sort_order =$_REQUEST['sort_order']-1;
        }


        $grade_list_id = $gradeList[$sort_order]['grade_list_id'];

        $questionType = M('uteach_type')->where("grade_list_id = '$grade_list_id'")->select();
        $questionDifficult = M('uteach_difficult')->where("grade_list_id = '$grade_list_id'")->select();

        $this->publicFuction($grade_list_id);
        $this->assign("badge",'0');
        $this->assign('questionType',$questionType);
        $this->assign('questionDifficult',$questionDifficult);
        $this->assign('sort_order',$sort_order);
        $this->assign('gradeList',$gradeList);
        $this->display();

    }





    public function questionRecord() {

        if(isset($_REQUEST['questionId']) && !empty($_REQUEST['questionId'])) {
            $deleteQuestion = $this->deleteQuestion($_REQUEST['questionId']);
            $this->assign("questionClick",$deleteQuestion);
        }

        $userInfo['uname'] = $GLOBALS['ts']['user']['uname'];
        $userInfo['login'] = $GLOBALS['ts']['user']['login'];
        $userInfo['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $subjectId = M('teacher_subject_classes')
            ->field('subject_id')
            ->where("login = ".$userInfo['login']." AND school_id = ".$userInfo['school_id'])
            ->find();


        $gradeList = D("Knowledge")->getGradeList(null,$subjectId['subject_type']);
        //$gradeList = D("Knowledge")->getGradeList(null,'02');
        //年级学年的id
        if(empty($_REQUEST['sort_order'])) {
            $sort_order = '12';//七年级上学期数学
        } else {
            $sort_order =$_REQUEST['sort_order']-1;
        }
        $grade_list_id = $gradeList[$sort_order]['grade_list_id'];

        $questionType = M('uteach_type')->where("grade_list_id = '$grade_list_id'")->select();
        $questionDifficult = M('uteach_difficult')->where("grade_list_id = '$grade_list_id'")->select();

        $this->publicFuction($grade_list_id);
        $this->assign("badge",'1');
        $this->assign('questionType',$questionType);
        $this->assign('questionDifficult',$questionDifficult);

        //出题人
        $questionTeacher = M('uteach_question_teacher')->where("grade_list_id = '$grade_list_id'")->select();
        $this->assign("questionTeacher",$questionTeacher);
        //来源
        $questionWhere = M('uteach_where')->where("grade_list_id = '$grade_list_id'")->select();
        $this->assign("questionWhere",$questionWhere);

        $this->publicFuction($grade_list_id);

        //默认查看这个年级中所有的题目
        $questionView['grade_list_id'] = $grade_list_id;
        $questionView['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $this->assign('questionListArray',$questionView);

        $this->assign('sort_order',$sort_order);
        $this->assign('gradeList',$gradeList);
        $this->display();
    }



    public function ajaxQuestionList(){
        if (!$this->isAjax())
            exit("错误！");
        $questionParameter = null;

        //对request进行过滤，将空值过滤掉

        $_REQUEST = array_filter($_REQUEST,function($v) {
            return !empty($v);
        });

        if(isset($_REQUEST['gradeListIDView'])) {
            $questionParameter['grade_list_id'] = $_REQUEST['gradeListIDView'];
            $questionParameter['school_id'] = $GLOBALS['ts']['user']['school_id'];
        }else{
            exit("非法请求！");
        }



        if(isset($_REQUEST['knowledgeLevel1ID'])) {
            $questionParameter['knowledge_level1'] = $_REQUEST['knowledgeLevel1ID'];

        }
        if(isset($_REQUEST['knowledgeLevel2ID'])) {
            $questionParameter['knowledge_level2'] = $_REQUEST['knowledgeLevel2ID'];
        }
        if(isset($_REQUEST['knowledgeLevel3ID'])) {
            $questionParameter['knowledge_level3'] = $_REQUEST['knowledgeLevel3ID'];
        }
        if(isset($_REQUEST['questionTypeIDView'])) {
            $questionParameter['question_type'] = $_REQUEST['questionTypeIDView'];
        }
        if(isset($_REQUEST['questionDifficultIDView'])) {
            $questionParameter['difficult_id'] = $_REQUEST['questionDifficultIDView'];
        }
        if(isset($_REQUEST['questionDifficultIDView'])) {
            $questionParameter['difficult_id'] = $_REQUEST['questionDifficultIDView'];
        }
        if(isset($_REQUEST['questionTeacherLoginView'])) {
            $questionParameter['createdby'] = $_REQUEST['questionTeacherLoginView'];
        }
        if(isset($_REQUEST['whereIDView'])) {
            $questionParameter['where_id'] = $_REQUEST['whereIDView'];
        }

        $html = W('QuestionListView', array('questionListArray' => $questionParameter));
        exit($html);
    }

    public function ajaxUpdateKnowledgeName(){
        if (!$this->isAjax())
            return;
        $data['grade_list_id'] = $_REQUEST['grade_list_id'];
        $knowledge_name = $_REQUEST['knowledge_name'];
        $data['knowledge_id'] = $_REQUEST['knowledge_id'];
        $result = M('uteach_knowledge')->where($data)->setField('knowledge_name',$knowledge_name);
        if($result) {
            exit('1');
        } else{
            exit('0');
        }
    }

    public function ajaxAddFirstKnowledge(){
        if (!$this->isAjax())
            return;
        $data['grade_list_id'] = $_REQUEST['grade_list_id'];
        $data["knowledge_name"] = $_REQUEST['knowledge_name'];

        $gradeList = M("Knowledge")->getGradeListByGradeListId($data['grade_list_id']);
        $data['parent_id'] = 0;
        $data['level'] = 1;

        $data['descreption'] = null;
        $data['is_leaf'] = $data['_left'] = $data['_right'] = $data['weight']= 0;
        $data['grade'] = $gradeList['grade_order'];
        $data["area"] =0;
        $data["subject_id"] = $gradeList['subject_id'];

        //获取父为0的条数
        $getLastKnowledge = M('Knowledge')->getOrderCount($data['grade_list_id'], $data['level'],$data['parent_id']);
        $data['order_value'] = $getLastKnowledge + 1;
        $data['knowledge_id'] = $data['level'].$data['grade'].$data["subject_id"].rand(11111,99999).$data['order_value'];

        $addKnowledgeReturn = M('uteach_knowledge')->add($data);
        if($addKnowledgeReturn) {
            exit($data['knowledge_id']);
        } else{
            exit('0');
        }
    }

    public function ajaxAddMoreKnowledge(){
        if (!$this->isAjax())
            return;
        $data['grade_list_id'] = $_REQUEST['grade_list_id'];
        $data["knowledge_name"] = $_REQUEST['knowledge_name'];
        $data['parent_id'] = $_REQUEST['parent_id'];

        $gradeList = M("Knowledge")->getGradeListByGradeListId($data['grade_list_id']);

        //得到父亲的level
        $getParentKnowledgeLevel = M('Knowledge')->getPrentLever($data['grade_list_id'],$data['parent_id']);
        $data['level'] = $getParentKnowledgeLevel['level']+1;

        $data['descreption'] = null;
        $data['is_leaf'] = $data['_left'] = $data['_right'] = $data['weight']= 0;
        $data['grade'] = $gradeList['grade_order'];
        $data["area"] = 0;
        $data["subject_id"] = $gradeList['subject_id'];

        //获取父为0的条数
        $getLastKnowledge = M('Knowledge')->getOrderCount($data['grade_list_id'], null,$data['parent_id']);
        $data['order_value'] = $getLastKnowledge + 1;
        $data['knowledge_id'] = $data['parent_id'].$data['level'].rand(111,999).$data['order_value'];

        $addKnowledgeReturn = M('uteach_knowledge')->add($data);
        if($addKnowledgeReturn) {
            exit($data['knowledge_id']);
        } else{
            exit('0');
        }
    }

    public function deleteQuestion($questionID){

            $result = M('uteach_question')->where("id = ".$questionID)->field("knowledge_level1,knowledge_level2,knowledge_level3")->find();
            if(!is_null($result)) {
                $delete = M('uteach_question')->where("id = ".$questionID)->delete();
                if(!empty($delete)) {
                    $this->reduceKnowledgeCount($result['knowledge_level1']);
                    $this->reduceKnowledgeCount($result['knowledge_level2']);
                    $this->reduceKnowledgeCount($result['knowledge_level3']);

                }

            }
        return $result;

    }

    private function reduceKnowledgeCount($knowledgeId = null) {
        if($knowledgeId != "0") {
            $uteach_knowledge = M('uteach_knowledge');
            $knowledgeCount = $uteach_knowledge->where("knowledge_id = '".$knowledgeId."'")->field("question_count")->find();

            $uteach_knowledge->where("knowledge_id = '".$knowledgeId."'")->setField("question_count",$knowledgeCount['question_count']-1);
            unset($uteach_knowledge);

        }
    }

    private function addKnowledgeCount($knowledgeId = null) {
        if($knowledgeId != "0") {
            $uteach_knowledge = M('uteach_knowledge');
            $knowledgeCount = $uteach_knowledge->where("knowledge_id = '".$knowledgeId."'")->field("question_count")->find();

            $uteach_knowledge->where("knowledge_id = '".$knowledgeId."'")->setField("question_count",$knowledgeCount['question_count']+1);
            unset($uteach_knowledge);

        }
    }

    public function ajaxImportQuestion(){
        if (!$this->isAjax())
            exit("非法请求！");

        $gradeListId = isset($_REQUEST['grade_list_id']) ? $_REQUEST['grade_list_id'] : "0";
        $question_content = empty($_REQUEST['question_content']) ? "0":$_REQUEST['question_content'];
        $answer_text = empty($_REQUEST['answer_text']) ? "0":$_REQUEST['answer_text'];
        //分情况进行写入
        $selectTypeAnswer = isset($_REQUEST['selectTypeAnswer']) ? $_REQUEST['selectTypeAnswer']: "0";
        if(isset($_REQUEST['knowledgeLevel1ID']) || !empty($_REQUEST['knowledgeLevel1ID'])) {
            $level1 = $_REQUEST['knowledgeLevel1ID'];
        }else{
            exit("请选择知识点");
        }

        $level2 = (isset($_REQUEST['knowledgeLevel2ID']) && !empty($_REQUEST['knowledgeLevel2ID']))? $_REQUEST['knowledgeLevel2ID'] : "0";
        $level3 = (isset($_REQUEST['knowledgeLevel3ID']) && !empty($_REQUEST['knowledgeLevel3ID']))? $_REQUEST['knowledgeLevel3ID'] : "0";

        $this->addKnowledgeCount($level1);
        $this->addKnowledgeCount($level2);
        $this->addKnowledgeCount($level3);
        $questionType = isset($_REQUEST['questionType']) ? $_REQUEST['questionType']:0;
        $questionTypeName = isset($_REQUEST['questionTypeName']) ? $_REQUEST['questionTypeName']:"无";

        $questionDifficult = isset($_REQUEST['questionDifficult']) ? $_REQUEST['questionDifficult']:0;

        $questionDifficultName = isset($_REQUEST['questionDifficultName']) ? $_REQUEST['questionDifficultName']:"无";

        $where_name_text = isset($_REQUEST['where_name_text']) ? $_REQUEST['where_name_text']:"无";


        $userInfo['uname'] = $GLOBALS['ts']['user']['uname'];
        $userInfo['login'] = $GLOBALS['ts']['user']['login'];
        $userInfo['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $questionTeacher = $userInfo;
        $questionTeacher['grade_list_id'] = $gradeListId;

        $exits = M('uteach_question_teacher')->where($questionTeacher)->find();
        if(empty($exits)) {
            //插入到此年级的出题人教师
            M('uteach_question_teacher')->add($questionTeacher);
        }
        unset($exits);
        $whereData['where_name'] = $where_name_text;
        $whereData['grade_list_id'] = $gradeListId;
        $whereData['school_id'] = $userInfo['school_id'];

        $exitsWhere = M('uteach_where')->where($whereData)->field("where_id")->find();
        if(empty($exitsWhere)) {
            $where_id = M('uteach_where')->add($whereData);
        } else{
            $where_id = $exitsWhere['where_id'];
        }
        unset($exitsWhere);

        $questionData = $userInfo;


        $questionData['school_id'] = $userInfo['school_id'];
        $questionData['createdby'] = $userInfo['login'];
        $questionData['create_name'] = $userInfo['uname'];
        $questionData['createdate'] = date('y-m-d h:i:s',time());
        unset($userInfo);

        $questionData['grade_list_id'] = $gradeListId;
        $questionData['knowledge_level1'] = $level1;
        $questionData['knowledge_level2'] = $level2;
        $questionData['knowledge_level3'] = $level3;

        $questionData['question_content'] = $question_content;
        $questionData['question_content_text'] = "";
        //分情况插入答案
        $questionData['answer'] = $selectTypeAnswer;
        $questionData['answer_text'] = $answer_text;

        $questionData['question_type'] = $questionType;
        $questionData['type_name'] = $questionTypeName;
        $questionData['difficult_id'] = $questionDifficult;
        $questionData['difficult_name'] = $questionDifficultName;

        $questionData['where_id'] = $where_id;
        $questionData['where_name'] = $where_name_text;

        /*
         * 以下字段使用默认值
         * keyword/questionpool_type/section_book_id/section_id/
         * question_score/modifiedby/modifydate/
         *
         * download_count
         * hit_count
         * use_count
         * question_value
         * question_error_flag
         *
         *
        */
        $result = M('uteach_question')->add($questionData);
        if($result){
            exit("添加成功！");
        }else {
            exit("添加失败！");
        }

    }



    public function submitImportQuestion(){

        $gradeListId = isset($_REQUEST['grade_list_id']) ? $_REQUEST['grade_list_id'] : "0";
        $question_content = empty($_REQUEST['question_content']) ? "0":$_REQUEST['question_content'];
        $answer_text = empty($_REQUEST['answer_text']) ? "0":$_REQUEST['answer_text'];
        //分情况进行写入
        $selectTypeAnswer = isset($_REQUEST['selectTypeAnswer']) ? $_REQUEST['selectTypeAnswer']: "0";
        if(isset($_REQUEST['knowledgeLevel1ID']) || !empty($_REQUEST['knowledgeLevel1ID'])) {
            $level1 = $_REQUEST['knowledgeLevel1ID'];
        }else{
            echo "<script>alert('请选择知识点！')</script>";return null;
        }

        $level2 = (isset($_REQUEST['knowledgeLevel2ID']) && !empty($_REQUEST['knowledgeLevel2ID']))? $_REQUEST['knowledgeLevel2ID'] : "0";
        $level3 = (isset($_REQUEST['knowledgeLevel3ID']) && !empty($_REQUEST['knowledgeLevel3ID']))? $_REQUEST['knowledgeLevel3ID'] : "0";

        $this->addKnowledgeCount($level1);
        $this->addKnowledgeCount($level2);
        $this->addKnowledgeCount($level3);
        $questionType = isset($_REQUEST['questionType']) ? $_REQUEST['questionType']:0;
        $questionTypeName = isset($_REQUEST['questionTypeName']) ? $_REQUEST['questionTypeName']:"无";

        $questionDifficult = isset($_REQUEST['questionDifficult']) ? $_REQUEST['questionDifficult']:0;

        $questionDifficultName = isset($_REQUEST['questionDifficultName']) ? $_REQUEST['questionDifficultName']:"无";

        $where_name_text = isset($_REQUEST['where_name_text']) ? $_REQUEST['where_name_text']:"无";


        $userInfo['uname'] = $GLOBALS['ts']['user']['uname'];
        $userInfo['login'] = $GLOBALS['ts']['user']['login'];
        $userInfo['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $questionTeacher = $userInfo;
        $questionTeacher['grade_list_id'] = $gradeListId;

        $exits = M('uteach_question_teacher')->where($questionTeacher)->find();
        if(empty($exits)) {
            //插入到此年级的出题人教师
            M('uteach_question_teacher')->add($questionTeacher);
        }
        unset($exits);
        $whereData['where_name'] = $where_name_text;
        $whereData['grade_list_id'] = $gradeListId;
        $whereData['school_id'] = $userInfo['school_id'];

        $exitsWhere = M('uteach_where')->where($whereData)->field("where_id")->find();
        if(empty($exitsWhere)) {
            $where_id = M('uteach_where')->add($whereData);
        } else{
            $where_id = $exitsWhere['where_id'];
        }
        unset($exitsWhere);

        $questionData = $userInfo;


        $questionData['school_id'] = $userInfo['school_id'];
        $questionData['createdby'] = $userInfo['login'];
        $questionData['create_name'] = $userInfo['uname'];
        $questionData['createdate'] = date('y-m-d h:i:s',time());
        unset($userInfo);

        $questionData['grade_list_id'] = $gradeListId;
        $questionData['knowledge_level1'] = $level1;
        $questionData['knowledge_level2'] = $level2;
        $questionData['knowledge_level3'] = $level3;

        $questionData['question_content'] = $question_content;
        $questionData['question_content_text'] = "";
        //分情况插入答案
        $questionData['answer'] = $selectTypeAnswer;
        $questionData['answer_text'] = $answer_text;

        $questionData['question_type'] = $questionType;
        $questionData['type_name'] = $questionTypeName;
        $questionData['difficult_id'] = $questionDifficult;
        $questionData['difficult_name'] = $questionDifficultName;

        $questionData['where_id'] = $where_id;
        $questionData['where_name'] = $where_name_text;

        /*
         * 以下字段使用默认值
         * keyword/questionpool_type/section_book_id/section_id/
         * question_score/modifiedby/modifydate/
         *
         * download_count
         * hit_count
         * use_count
         * question_value
         * question_error_flag
         *
         *
        */
        $result = M('uteach_question')->add($questionData);
        if($result){
            echo "<script>alert('添加成功！！')</script>";
            $returnData['knowledge_level1'] = $level1;
            $returnData['knowledge_level2'] = $level2;
            $returnData['knowledge_level3'] = $level3;
            return $returnData;
        }else {
            echo "<script>alert('添加失败！！')</script>";
            return null;
        }
    }


    private function updateQuestion($updateQuestionID){
        $updateQuestion = $this->getUpdateQuestion($updateQuestionID);

        $gradeListId = isset($_REQUEST['grade_list_id']) ? $_REQUEST['grade_list_id'] : "0";
        $question_content = empty($_REQUEST['question_content']) ? "0":$_REQUEST['question_content'];
        $answer_text = empty($_REQUEST['answer_text']) ? "0":$_REQUEST['answer_text'];
        //分情况进行写入
        $selectTypeAnswer = isset($_REQUEST['selectTypeAnswer']) ? $_REQUEST['selectTypeAnswer']: "0";
        if(isset($_REQUEST['knowledgeLevel1ID']) || !empty($_REQUEST['knowledgeLevel1ID'])) {
            $level1 = $_REQUEST['knowledgeLevel1ID'];
        }else{
            echo "<script>alert('请选择知识点！')</script>";return null;
        }

        $level2 = (isset($_REQUEST['knowledgeLevel2ID']) && !empty($_REQUEST['knowledgeLevel2ID']))? $_REQUEST['knowledgeLevel2ID'] : "0";
        $level3 = (isset($_REQUEST['knowledgeLevel3ID']) && !empty($_REQUEST['knowledgeLevel3ID']))? $_REQUEST['knowledgeLevel3ID'] : "0";

        $this->reduceKnowledgeCount($updateQuestion['knowledge_level1']);
        $this->reduceKnowledgeCount($updateQuestion['knowledge_level2']);
        $this->reduceKnowledgeCount($updateQuestion['knowledge_level3']);
        $this->addKnowledgeCount($level1);
        $this->addKnowledgeCount($level2);
        $this->addKnowledgeCount($level3);
        $questionType = isset($_REQUEST['questionType']) ? $_REQUEST['questionType']:0;
        $questionTypeName = isset($_REQUEST['questionTypeName']) ? $_REQUEST['questionTypeName']:"无";

        $questionDifficult = isset($_REQUEST['questionDifficult']) ? $_REQUEST['questionDifficult']:0;

        $questionDifficultName = isset($_REQUEST['questionDifficultName']) ? $_REQUEST['questionDifficultName']:"无";

        $where_name_text = isset($_REQUEST['where_name_text']) ? $_REQUEST['where_name_text']:"无";


        $userInfo['uname'] = $GLOBALS['ts']['user']['uname'];
        $userInfo['login'] = $GLOBALS['ts']['user']['login'];
        $userInfo['school_id'] = $GLOBALS['ts']['user']['school_id'];

        $questionTeacher = $userInfo;
        $questionTeacher['grade_list_id'] = $gradeListId;

        $exits = M('uteach_question_teacher')->where($questionTeacher)->find();
        if(empty($exits)) {
            //插入到此年级的出题人教师
            M('uteach_question_teacher')->add($questionTeacher);
        }
        unset($exits);
        $whereData['where_name'] = $where_name_text;
        $whereData['grade_list_id'] = $gradeListId;
        $whereData['school_id'] = $userInfo['school_id'];

        $exitsWhere = M('uteach_where')->where($whereData)->field("where_id")->find();
        if(empty($exitsWhere)) {
            $where_id = M('uteach_where')->add($whereData);
        } else{
            $where_id = $exitsWhere['where_id'];
        }
        unset($exitsWhere);

        $questionData = $userInfo;


        $questionData['school_id'] = $userInfo['school_id'];
        $questionData['createdby'] = $userInfo['login'];
        $questionData['create_name'] = $userInfo['uname'];
        $questionData['createdate'] = date('y-m-d h:i:s',time());
        unset($userInfo);

        $questionData['grade_list_id'] = $gradeListId;
        $questionData['knowledge_level1'] = $level1;
        $questionData['knowledge_level2'] = $level2;
        $questionData['knowledge_level3'] = $level3;

        $questionData['question_content'] = $question_content;
        $questionData['question_content_text'] = "";
        //分情况插入答案
        $questionData['answer'] = $selectTypeAnswer;
        $questionData['answer_text'] = $answer_text;

        $questionData['question_type'] = $questionType;
        $questionData['type_name'] = $questionTypeName;
        $questionData['difficult_id'] = $questionDifficult;
        $questionData['difficult_name'] = $questionDifficultName;

        $questionData['where_id'] = $where_id;
        $questionData['where_name'] = $where_name_text;

        /*
         * 以下字段使用默认值
         * keyword/questionpool_type/section_book_id/section_id/
         * question_score/modifiedby/modifydate/
         *
         * download_count
         * hit_count
         * use_count
         * question_value
         * question_error_flag
         *
         *
        */
        $questionData['download_count'] = $updateQuestion['download_count'];
        $questionData['hit_count'] = $updateQuestion['hit_count'];

        $questionData['use_count'] = $updateQuestion['use_count'];
        $questionData['question_value'] = $updateQuestion['question_value'];

       // $saveQuestionResult = M('uteach_question')->where(array('id'=>$updateQuestion['id']))->data($questionData)->save();

        $saveQuestionResult = M('uteach_question')->add($questionData);
        if($saveQuestionResult){
            echo "<script>alert('修改成功！！')</script>";
            $returnData['knowledge_level1'] = $level1;
            $returnData['knowledge_level2'] = $level2;
            $returnData['knowledge_level3'] = $level3;
            return $returnData;
        }else {
            echo "<script>alert('修改失败！！')</script>";
            return null;
        }
    }

}