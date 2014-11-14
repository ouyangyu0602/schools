<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-23
 * Time: 上午9:19
 */

class BookGradeAction extends Action{
    public function BookGrade() {
        $model = model("BookGradeInfo");
//        $classModel = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumBookGradeInfo(), 5);//实例化分页类
        $BookGradeInfoList = $model -> selectBookGradeInfo($Page->firstRow.','.$Page->listRows);
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("bookGrade",$BookGradeInfoList);

//        $this->assign("schoolMsg",$classModel->getSchoolInfo());
        $this->display();
    }
    //删除单个- -
    public function delBookGradeInfo(){
//		if(!isAjax())return;
        $model = model("BookGradeInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delBookGradeInfo($Id);
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
    public function selectSaveBookGradeInfo(){
        $id = $_REQUEST['id'];
        $model = model("BookGradeInfo");
        $result = $model -> selectSaveBookGradeInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveBookGradeInfo(){
        $data["section_book_id"]=$_REQUEST['section_book_id'];
        $data["section_book_title"]=$_REQUEST['section_book_title'];
        $data["contextbook_type"]=$_REQUEST['contextbook_type'];
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["gradebook_type"]=$_REQUEST['gradebook_type'];
        $model = model("BookGradeInfo");
        $saveInfo = $model -> saveBookGradeInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--
    public function mulDeleteBookGradeInfo(){
        $model = model("BookGradeInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delBookGradeInfo($id[$i]);
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
    public function addBookGradeInfo(){
        $model = model("BookGradeInfo");
        $data["gradebook_type"]=$_REQUEST['gradebook_type'];
        $data["gradebook_type_desc"]=$_REQUEST['gradebook_type_desc'];
//        $result = $model -> addBookGradeInfo($data);
//        $sql = M()->getlastsql();
//        if($result)
//        {
            $this->ajaxReturn($data["gradebook_type_desc"]);
//        }
//        else
//        {
//            $this->ajaxReturn($sql);
//        }
    }
} 