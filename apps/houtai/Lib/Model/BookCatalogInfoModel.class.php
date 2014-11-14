<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-24
 * Time: 上午9:49
 */

class BookCatalogInfoModel extends Model{
    //教材目录列表信息
    public function selectBookCatalogInfo($first,$end){
        $data['section_id'] = array("like","_____");
        $m = M("evaluation_section")->where($data) -> limit($first,$end) -> select();
        return $m;
    }
    //查询课本单元
    public function selectBookUnit($data){
        $da["section_id"] = $data;
        $m = M("evaluation_section") -> where($da) -> select();
        return $m;
    }
    //统计教材目录信息
    public function sumBookCatalogInfo(){
        $data['section_id'] = array("like","_____");
        $m = M("evaluation_section") -> where($data) -> count();
        return $m;
    }
    //教材目录信息添加
    public function addBookCatalogInfo($data){
        $m = M("evaluation_section") -> add($data);
        return $m;
    }
    //修改教材目录信息列表
    public function selectSaveBookCatalogInfo($id){
        $m = M("evaluation_section") -> where("id=$id") -> find();
        return $m;
    }
    //修改教材目录信息
    public function saveBookCatalogInfo($id,$data){
        $m = M("evaluation_section") -> where("id=$id") -> save($data);
        return $m;
    }
    //删除教材目录信息
    public function delBookCatalogInfo($id){
        $m=M("evaluation_section") -> where("id='".$id."'") -> delete();
        return $m;
    }
} 