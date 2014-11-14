<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-11
 * Time: 下午4:49
 */

class BookVersionAction extends Action{
    public function getBookVersionInfoList() {
        $model = model("BookVersionInfo");
//        $classModel = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumBookVersionInfo(), 5);//实例化分页类
        $BookVersionInfoList = $model -> selectBookVersionInfo($Page->firstRow.','.$Page->listRows);
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("bookVersion",$BookVersionInfoList);
//        $this->assign("schoolMsg",$classModel->getSchoolInfo());
         $this->display("bookVersion");
    }
    //删除单个- -
    public function delBookVersionInfo(){
//		if(!isAjax())return;
        $model = model("BookVersionInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delBookVersionInfo($Id);
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
    public function selectSaveBookVersionInfo(){
        $id = $_REQUEST['id'];
        $model = model("BookVersionInfo");
        $result = $model -> selectSaveBookVersionInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveBookVersionInfo(){
        $data["section_book_id"]=$_REQUEST['section_book_id'];
        $data["section_book_title"]=$_REQUEST['section_book_title'];
        $data["contextbook_type"]=$_REQUEST['contextbook_type'];
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["gradebook_type"]=$_REQUEST['gradebook_type'];
        $model = model("BookVersionInfo");
        $saveInfo = $model -> saveBookVersionInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--
    public function mulDeleteBookVersionInfo(){
        $model = model("BookVersionInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delBookVersionInfo($id[$i]);
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
    public function addBookVersionInfo(){
        $model = model("BookVersionInfo");
        $data["section_book_id"]=$_REQUEST['section_book_id'];
        $data["section_book_title"]=$_REQUEST['section_book_title'];
        $data["contextbook_type"]=$_REQUEST['contextbook_type'];
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["gradebook_type"]=$_REQUEST['gradebook_type'];
        $result = $model -> addBookVersionInfo($data);
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