<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-20
 * Time: 下午4:33
 */

class TeacherInfoModel extends Model {
    //学生列表信息
    public function selectTeacherInfo($first,$end){
        $m = M("user") -> where("profile_id=3")-> limit($first,$end) -> select();
        return $m;
    }
    //统计学生信息
    public function sumTeacherInfo(){
        $m = M("user") ->where("profile_id=3") -> count();
        return $m;
    }
    //查找学生最后一条数据
    public function selectTeacherLastInfo(){
        $m = M("user") -> where("profile_id=3") -> order("uid desc") ->field("profile_no")-> find();
        return $m;
    }
    //查找学校名称
    public function schoolTitle($data){
        $m = M("schools") -> where("school_id=$data") -> field("title") -> find();
        return $m;
    }
    //学生信息添加
    public function addTeacherInfo($data){
        dump($data);
        $m = M("user") -> add($data);
        return $m;
    }
    //修改学生信息列表
    public function selectSaveTeacherInfo($id){
        $m = M("user") -> where("uid=$id") -> find();
        return $m;
    }
    //修改班级信息
    public function saveTeacherInfo($id,$data){
        $m = M("user") -> where("uid=$id") -> save($data);
        return $m;
    }
    //删除学生信息
    public function delTeacherInfo($id){
        $m=M("user") -> where("uid='".$id."'") -> delete();
        return $m;
    }
} 