<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-3
 * Time: 下午2:17
 */

class GradeInfoModel extends Model {
    //获取班级信息
    public function selectGradeInfo($schoolID = null){
        /*$m = M("school_gradelevels")
          -> field("school_id,short_name,title,next_grade_id,sort_order,grade_id")
          -> where($map)
          -> limit($first,$end) -> select();*/
        if(is_null($schoolID)) {
            return null;
        }
        $grade = M()
            -> table('ts_school_gradelevels tsg,ts_schools ts')
            -> field("ts.school_id,ts.title schoolName,tsg.title,tsg.grade_id,tsg.short_name,tsg.sort_order")
            -> where("tsg.school_id = ts.school_id AND ts.school_id = $schoolID ")
            -> select();
        return $grade;
    }
    //获取班级信息
    public function getSchoolGradeInfo($schoolID = null,$gradeID = null){

        if(is_null($schoolID) || is_null($gradeID)) {
            return null;
        }
        $grade = M()
            -> table('ts_school_gradelevels tsg,ts_schools ts')
            -> field("ts.school_id,ts.title schoolName,tsg.title,tsg.grade_id,tsg.short_name,tsg.sort_order")
            -> where("tsg.school_id = ts.school_id AND ts.school_id = $schoolID AND tsg.grade_id = $gradeID")
            -> find();
        return $grade;
    }



    public function getGradeBySortAndSchool($schoolID = null,$sort_order = null){
        /*$m = M("school_gradelevels")
          -> field("school_id,short_name,title,next_grade_id,sort_order,grade_id")
          -> where($map)
          -> limit($first,$end) -> select();*/
        if(is_null($schoolID) || is_null($sort_order)) {
            return null;
        }
        $grade = M()
            -> table('ts_school_gradelevels')
            -> field("title,grade_id")
            -> where("sort_order = $sort_order AND school_id = $schoolID ")
            -> select();
        return $grade;
    }
    //统计班级信息
    public function sumGradeInfo($schoolID = null){
        if(is_null($schoolID)) {
            return null;
        }
        $m = M("school_gradelevels") ->where("school_id = $schoolID")-> count();
        return $m;
    }
    //班级信息添加
    public function addGradeInfo($data){
        $m = M("school_gradelevels") -> add($data);
        return $m;
    }
    //修改班级信息
    public function saveGradeInfo($id,$data){
        $m = M("school_gradelevels") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除班级信息
    public function delGradeInfo($gradeID){
        $m=M("school_gradelevels") -> where("grade_id='".$gradeID."'") -> delete();
        return $m;
    }
} 