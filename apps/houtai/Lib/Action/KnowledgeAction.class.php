<?php
class KnowledgeAction extends Action{




    public function index() {
        $gradeList = D("Knowledge")->getGradeList(null,'02');
        //年级学年的id
        $grade_list_id = '1307S1702';//七年级上学期数学
        //$grade_list_id = '1407X2702';//七年级下学期数学
        //得到七年级上学期数学知识点的顶层目录
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

        //dump($knowledgeListArray);

        $this->assign('knowledgeListArray', $knowledgeListArray);
        $this->assign('gradeList',$gradeList);
        $this->assign('grade_list_id',$grade_list_id);
        $subjects = M("Knowledge")->getSubjectAll();
        $grade_master = M("Knowledge")->getGradeMasterAll();
        $this->assign('gradeMast',$grade_master);
        $this->assign('subjectList',$subjects);

        $this->display();
    }


    public function ajaxAddKnowledge() {
        if (!$this->isAjax())
            return;
        $data["area"] = $_REQUEST['area'];
        $data["subject_id"] = $_REQUEST['subject_id'];
        $gradeExplode = explode('-',$_REQUEST['grade_type']);
        $data["grade_type"] = $gradeExplode[0];
        $data["knowledge_name"] = $_REQUEST['knowledgeName'];
        $data['order_value'] = $_REQUEST['order_value'];
        $gradeList = M("Knowledge")->getGradeList($gradeExplode[1],$data["subject_id"],$data["grade_type"]);

        $data['grade_list_id'] = $gradeList[0]['grade_list_id'];
        $data['grade'] = $gradeExplode[1];
        $data['descreption'] = $_REQUEST['descreption'];

        $data['knowledge_id'] = $_REQUEST['area'].rand(11111,99999).$_REQUEST['order_value'];

        $addKnowledgeReturn = M('uteach_knowledge')->add($data);

        if($addKnowledgeReturn) {
            exit("添加成功！");
        } else{
            exit("添加失败！");
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
            exit("添加成功！");
        } else{
            exit("添加失败！");
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
        $data["area"] = '110104';
        $data["subject_id"] = $gradeList['subject_id'];

        //获取父为0的条数
        $getLastKnowledge = M('Knowledge')->getOrderCount($data['grade_list_id'], null,$data['parent_id']);
        $data['order_value'] = $getLastKnowledge + 1;
        $data['knowledge_id'] = $data['parent_id'].$data['level'].rand(111,999).$data['order_value'];

        $addKnowledgeReturn = M('uteach_knowledge')->add($data);
        if($addKnowledgeReturn) {
            exit("添加成功！");
        } else{
            exit("添加失败！");
        }
    }



    public function test(){
        $subjects = M('subject_master')
            ->select();
        $grade_master = M('grade_master')
            ->select();

        for($i = 0;$i<count($grade_master);$i++){

            for($j = 0;$j<count($subjects);$j++){

                $data['grade_list_id'] = $grade_master[$i]["id"].$grade_master[$i]["grade_type"].$grade_master[$i]["syear_id"].$grade_master[$i]["grade_order"].$subjects[$j]['subject_type'];
                $data['grade_list_name'] = $grade_master[$i]["grade_type_desc"].'('.$subjects[$j]['subject_type_desc'].')';
                $data['grade_type'] = $grade_master[$i]["grade_type"];
                $data['syear_id'] =$grade_master[$i]["syear_id"];
                $data['grade_type_desc'] = $grade_master[$i]["grade_type_desc"];
                $data['grade_order'] = $grade_master[$i]["grade_order"];
                $data['subject_id'] = $subjects[$j]['subject_type'];
                $data['subject_name'] = $subjects[$j]['subject_type_desc'];
                $data['sort_order'] = $i+1;

                die();
                dump(M('uteach_grade_list')->add($data));
                dump(M()->getLastsql());
                unset($data);

            }

        }

        $this->display();
    }
}
?>