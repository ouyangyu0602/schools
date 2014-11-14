<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-23
 * Time: 下午3:58
 */

class SubjectVersionInfoModel extends Model{
    //科目列表信息
    public function selectSubjectVersionInfo($first,$end){
        $m = M("subject_master") -> limit($first,$end) -> select();
        return $m;
    }
    //统计科目信息
    public function sumSubjectVersionInfo(){
        $m = M("subject_master") -> count();
        return $m;
    }
    //科目信息添加
    public function addSubjectVersionInfo($data){
        $m = M("subject_master") -> add($data);
        return $m;
    }
    //修改科目信息列表
    public function selectSaveSubjectVersionInfo($id){
        $m = M("subject_master") -> where("id=$id") -> find();
        return $m;
    }
    //修改科目信息
    public function saveSubjectVersionInfo($id,$data){
        $m = M("subject_master") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除科目信息
    public function delSubjectVersionInfo($id){
        $m=M("subject_master") -> where("id='".$id."'") -> delete();
        return $m;
    }
} 