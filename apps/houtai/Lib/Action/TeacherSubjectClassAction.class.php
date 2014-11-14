<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-9
 * Time: 下午3:10
 */

class TeacherSubjectClassAction extends Action{
    public function getTeacherInfoList() {
        $model = model("TeacherSubjectClassInfo");
        $classModel = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumTeacherInfo(), 5);//实例化分页类
        $teacherInfoList = $model -> selectTeacherInfo($Page->firstRow.','.$Page->listRows);
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("teacher",$teacherInfoList);
        $this->assign("schoolMsg",$classModel->getSchoolInfo());
        $this->display("TeacherSubjectClass");
    }
    //删除单个- -
    public function delTeacherInfo(){
//		if(!isAjax())return;
        $model = model("TeacherSubjectClassInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delTeacherInfo($Id);
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
    public function selectSaveTeacherInfo(){
        $id = $_REQUEST['id'];
        $model = model("TeacherSubjectClassInfo");
        $result = $model -> selectSaveTeacherInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveTeacherInfo(){
        $data["login"]=$_REQUEST['login'];
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["class_id"]=$_REQUEST['class_id'];
        $data["school_id"]=$_REQUEST['school_id'];
        $data["school_period_id"]=$_REQUEST['period_id'];
        $model = model("TeacherSubjectClassInfo");
        $saveInfo = $model -> saveTeacherInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--
    public function mulDeleteTeacherSubjectClassInfo(){
        $model = model("Teacher");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delTeacherInfo($id[$i]);
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
    //添加班级信息--
    public function addTeacherInfo(){
        $model = model("TeacherSubjectClassInfo");
        $data["login"]=$_REQUEST['teacher_id'];
        $data["subject_type"]=$_REQUEST['subject_type'];
        $data["class_id"]=$_REQUEST['class_id'];
        $data["school_id"]=$_REQUEST['school_id'];
        $data["school_period_id"]=$_REQUEST['school_period_id'];
        $result = $model -> addTeacherInfo($data);
        if($result)
        {
            $this->ajaxReturn("添加成功！");
        }
        else
        {
            $this->ajaxReturn('添加失败！');
        }
    }
    //教师班级信息
    public function selectClassByTeacher(){
        $model = model("TeacherSubjectClassInfo");
        $teacherid = $_REQUEST['teacherid'];
        $class = $model -> selectClassByTeacher($teacherid);
        if($class){
            $this -> ajaxReturn($class);
        }
    }
} 