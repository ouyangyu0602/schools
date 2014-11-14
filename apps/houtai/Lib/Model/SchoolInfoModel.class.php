<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-2
 * Time: 上午9:01
 */

class SchoolInfoModel extends Model {
    //学校列表信息
    public function selectSchoolInfo($first,$end,$data){
        if($first=="" && $end=="" && $data !=""){
            $m = M("schools") -> where($data) -> select();
        } else {
            $m = M("schools") -> where($data)->limit($first,$end) -> select();
        }
        return $m;
    }
    //获取学校最后一条数据
    public function selectSchoolLastInfo(){
        $m = M("schools") -> order("id desc")->field("id")-> find();
        return $m;
    }
    //获取地址信息
    public function selectAddressInfo($data){
        $m = M("area") -> where($data) -> field("title") -> find();
        return $m;
    }
    //地址
    public function selectAreaInfo($data){
        $m = M("area") -> where($data) -> select();
        return $m;
    }
    //统计学校信息
    public function sumSchoolInfo(){
        $m = M("schools") -> count();
        return $m;
    }
    //学校信息添加
    public function addSchoolInfo($data){
        $m = M("schools") -> add($data);
        return $m;
    }
    //修改学校信息
    public function saveSchoolInfo($map,$data){
        $m = M("schools")->where($map)->data($data)->save();
        return $m;
    }
    //删除学校信息
    public function delSchoolInfo($id){
        $m=M("schools") -> where("id='".$id."'") -> delete();
        return $m;
    }
}