<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-2
 * Time: 上午9:01
 */

class KnowledgeModel extends Model {
    public function getGradeList($grade_order = null, $subject_id = null,$grade_type = null) {
        if(!is_null($grade_order)) {
            $map['grade_order'] = $grade_order;
        }
        if(!is_null($grade_type)) {
            $map['grade_type'] = $grade_type;
        }
        if(!is_null($subject_id)) {
            $map['subject_id'] = $subject_id;
        }
        $grade_master = M('uteach_grade_list')
            ->field("grade_list_id,grade_list_name,sort_order")
            ->where($map)
            ->select();
        return $grade_master;

    }

    public function getGradeListByGradeListId($grade_list_id = null) {
        if(!is_null($grade_list_id)) {
            $data['grade_list_id'] = $_REQUEST['grade_list_id'];
            $grade_master = M('uteach_grade_list')
                ->field("grade_order,subject_id")
                ->where($data)
                ->find();
            return $grade_master;
        } else {
            return null;
        }



    }

    public function getPrentLever($grade_list_id = null, $knowledge_id = null) {
        if(!is_null($grade_list_id)) {
            $map['grade_list_id'] = $grade_list_id;
        }

        if(!is_null($knowledge_id)) {
            $map['knowledge_id'] = $knowledge_id;
        }


        $count = M('uteach_knowledge')->where($map)->field('level')->find();
        return $count;
    }

    public function getKnowledgeLever($grade_list_id = null, $level = null, $parent_id = null) {
        if(!is_null($grade_list_id)) {
            $map['grade_list_id'] = $grade_list_id;
        }
        if(!is_null($level)) {
            $map['level'] = $level;
        }
        if(!is_null($parent_id)) {
            $map['parent_id'] = $parent_id;
        }


        $count = M('uteach_knowledge')->where($map)->select();
        return $count;
    }

    public function getOrderCount($grade_list_id = null, $level = null, $parent_id = null){
        if(!is_null($grade_list_id)) {
            $map['grade_list_id'] = $grade_list_id;
        }
        if(!is_null($level)) {
            $map['level'] = $level;
        }
        if(!is_null($parent_id)) {
            $map['parent_id'] = $parent_id;
        }

        $count = M('uteach_knowledge')->where($map)->count();
        return $count;


    }

    public function getGradeMasterAll() {
        $grade_master = M('grade_master')
            ->select();
        return $grade_master;
    }
    public function getSubjectAll() {
        $subjects = M('subject_master')
            ->field('subject_type,subject_type_desc')
            ->select();
        return $subjects;
    }
}