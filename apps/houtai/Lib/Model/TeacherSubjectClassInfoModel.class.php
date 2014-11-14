<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-9
 * Time: 下午3:11
 */
class TeacherSubjectClassInfoModel extends Model{
    //教师列表信息
    public function selectTeacherInfo($first,$end){
        $m = M("teacher_subject_classes") -> limit($first,$end) -> select();
        return $m;
    }
    //统计教师信息
    public function sumTeacherInfo(){
        $m = M("teacher_subject_classes") -> count();
        return $m;
    }
    //教师信息添加
    public function addTeacherInfo($data){
        $m = M("teacher_subject_classes") -> add($data);
        return $m;
    }
    //修改教师信息列表
    public function selectSaveTeacherInfo($id){
        $m = M("teacher_subject_classes") -> where("id=$id") -> find();
        return $m;
    }
    //修改教师信息
    public function saveTeacherInfo($id,$data){
        $m = M("teacher_subject_classes") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除教师信息
    public function delTeacherInfo($id){
        $m=M("teacher_subject_classes") -> where("id='".$id."'") -> delete();
        return $m;
    }
    /**
     * 由任课老师查出学科和所教班级
     * @param $condi 任课老师 id
     * @return array 所教班级数组
     * @author   Change By Liujian
     */
    public function selectClassByTeacher($condi){
        $result =  M()->table('ts_teacher_subject_classes a,ts_user b,ts_subject_master c,ts_school_classes d')
            ->field('d.class_name,d.class_id,d.school_id,c.subject_type_desc,c.subject_type')
            ->where("a.login = $condi  AND a.login = b.login AND c.subject_type = a.subject_type AND d.class_id = a.class_id AND a.subject_type != '00'")
            ->select();
        return $result;
    }
}