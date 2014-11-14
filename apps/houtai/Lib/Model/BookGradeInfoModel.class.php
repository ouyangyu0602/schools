<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-23
 * Time: 上午9:21
 */

class BookGradeInfoModel extends Model{
    //教材年级列表信息
    public function selectBookGradeInfo($first,$end){
        $m = M("gradebook_master") -> limit($first,$end) -> select();
        return $m;
    }
    //统计教材年级信息
    public function sumBookGradeInfo(){
        $m = M("gradebook_master") -> count();
        return $m;
    }
    //教材年级信息添加
    public function addBookGradeInfo($data){
        $m = M("gradebook_master") -> add($data);
        return $m;
    }
    //修改教材年级信息列表
    public function selectSaveBookGradeInfo($id){
        $m = M("gradebook_master") -> where("id=$id") -> find();
        return $m;
    }
    //修改教材年级信息
    public function saveBookGradeInfo($id,$data){
        $m = M("gradebook_master") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除教材年级信息
    public function delBookGradeInfo($id){
        $m=M("gradebook_master") -> where("id='".$id."'") -> delete();
        return $m;
    }
} 