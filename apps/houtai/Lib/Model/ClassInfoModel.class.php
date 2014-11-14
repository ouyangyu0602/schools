<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-4
 * Time: 上午9:42
 */

class ClassInfoModel extends Model{
    //班级列表信息
    public function selectClassInfo($first,$end){
        $m = M("school_classes") -> limit($first,$end) -> select();
        return $m;
    }
    //统计班级信息
    public function sumClassInfo(){
        $m = M("school_classes") -> count();
        return $m;
    }
    //班级信息添加
    public function addClassInfo($data){
        $m = M("school_classes") -> add($data);
        return $m;
    }
    //修改班级信息列表
    public function selectSaveClassInfo($id){
        $m = M("school_classes") -> where("id=$id") -> find();
        return $m;
    }
    //修改班级信息
    public function saveClassInfo($id,$data){
        $m = M("school_classes") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除班级信息
    public function delClassInfo($id){
        $m=M("school_classes") -> where("id='".$id."'") -> delete();
        return $m;
    }
    // 获取学校信息ts_schools数据表
    public function getSchoolInfo() {
        $schoolInfo = M ()->table ( 'ts_schools' )->field ( "distinct(school_id) schoolid,title" )->select ();
        return $schoolInfo;
    }
} 