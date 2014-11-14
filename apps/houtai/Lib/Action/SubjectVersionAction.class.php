<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-23
 * Time: 下午3:57
 */

class SubjectVersionAction extends Action{
    public function SubjectVersion() {
        $model = model("SubjectVersionInfo");
//        $classModel = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumSubjectVersionInfo(), 5);//实例化分页类
        $SubjectVersionInfoList = $model -> selectSubjectVersionInfo($Page->firstRow.','.$Page->listRows);
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("SubjectVersion",$SubjectVersionInfoList);
//        $this->assign("schoolMsg",$classModel->getSchoolInfo());
        $this->display();
    }
    //删除单个- -
    public function delSubjectVersionInfo(){
//		if(!isAjax())return;
        $model = model("SubjectVersionInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delSubjectVersionInfo($Id);
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
    public function selectSaveSubjectVersionInfo(){
        $id = $_REQUEST['id'];
        $model = model("SubjectVersionInfo");
        $result = $model -> selectSaveSubjectVersionInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveSubjectVersionInfo(){
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["subject_type_desc"]=$_REQUEST['subject_type_desc'];
        $model = model("SubjectVersionInfo");
        $saveInfo = $model -> saveSubjectVersionInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--
    public function mulDeleteSubjectVersionInfo(){
        $model = model("SubjectVersionInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delSubjectVersionInfo($id[$i]);
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
    public function addSubjectVersionInfo(){
        $model = model("SubjectVersionInfo");
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["subject_type_desc"]=$_REQUEST['subject_type_desc'];
        $result = $model -> addSubjectVersionInfo($data);
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