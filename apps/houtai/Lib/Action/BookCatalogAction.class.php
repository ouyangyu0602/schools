<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-24
 * Time: 上午9:46
 */

class BookCatalogAction extends Action {
    public function BookCatalog() {
        $model = model("BookCatalogInfo");
//        $classModel = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumBookCatalogInfo(), 5);//实例化分页类
        $BookCatalogInfoList = $model -> selectBookCatalogInfo($Page->firstRow.','.$Page->listRows);
        foreach($BookCatalogInfoList as $key=>$v){
            $Book = $model -> selectBookUnit($BookCatalogInfoList[$key]["parent_section_id"]);
            $BookCatalogInfoList[$key]['unit_title'] = $Book[0]["section_title"];
        }
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("BookCatalog",$BookCatalogInfoList);
//        dump($BookCatalogInfoList);
//        $this->assign("schoolMsg",$classModel->getSchoolInfo());
        $this->display();
    }
    //删除单个- -
    public function delBookCatalogInfo(){
//		if(!isAjax())return;
        $model = model("BookCatalogInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delBookCatalogInfo($Id);
        if($result)
        {
            $this->ajaxReturn($result,"删除成功！",1);
        }
        else
        {
            $this->ajaxReturn(0,"删除失败！",0);
        }
    }
    //修改-列表
    public function selectSaveBookCatalogInfo(){
        $id = $_REQUEST['id'];
        $model = model("BookCatalogInfo");
        $result = $model -> selectSaveBookCatalogInfo($id);
        $unit = $model -> selectBookUnit($result["parent_section_id"]);
        $result["unit_title"] = $unit[0]["section_title"];
        $result["uid"] = $unit[0]["id"];
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveBookCatalogInfo(){
        $data["section_book_title"]=$_REQUEST['section_book_title'];
        $da["section_title"]=$_REQUEST['unit_title'];
        $data["section_title"]=$_REQUEST['section_title'];
        $model = model("BookCatalogInfo");
        $model -> saveBookCatalogInfo($_GET['id'],$data);
        $save = $model -> saveBookCatalogInfo($_GET['uid'],$da);
        if($save){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--
    public function mulDeleteBookCatalogInfo(){
        $model = model("BookCatalogInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delBookCatalogInfo($id[$i]);
            if($delInfo)
            {
                $realCount++;
            }
        }

        if($count==$realCount)
        {
            $this->ajaxReturn($delInfo,'删除成功！',1);
        }
        else
        {
            $this -> ajaxReturn(0,"删除失败！",0);
        }
    }
    //添加信息--
    public function addBookCatalogInfo(){
        $model = model("BookCatalogInfo");
        $data["section_title"]=$_REQUEST['section_title'];
        $data["section_book_title"]=$_REQUEST['section_book_title'];
        $da["section"]=$_REQUEST['section'];
        $result = $model -> addBookCatalogInfo($data);
        if($result)
        {
            $this->ajaxReturn("添加成功！");
        }
        else
        {
            $this->ajaxReturn("添加失败！");
        }
    }
} 