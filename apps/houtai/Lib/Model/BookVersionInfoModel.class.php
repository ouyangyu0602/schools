<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-11
 * Time: 下午4:50
 */
class BookVersionInfoModel extends Model{
    //教师列表信息
    public function selectBookVersionInfo($first,$end){
        $m = M("evaluation_section_book") -> limit($first,$end) -> select();
        return $m;
    }
    //统计教师信息
    public function sumBookVersionInfo(){
        $m = M("evaluation_section_book") -> count();
        return $m;
    }
    //教师信息添加
    public function addBookVersionInfo($data){
        $m = M("evaluation_section_book") -> add($data);
        return $m;
    }
    //修改教师信息列表
    public function selectSaveBookVersionInfo($id){
        $m = M("evaluation_section_book") -> where("id=$id") -> find();
        return $m;
    }
    //修改教师信息
    public function saveBookVersionInfo($id,$data){
        $m = M("evaluation_section_book") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除教师信息
    public function delBookVersionInfo($id){
        $m=M("evaluation_section_book") -> where("id='".$id."'") -> delete();
        return $m;
    }
}