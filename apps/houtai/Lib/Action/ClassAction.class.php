<?php
class ClassAction extends Action {
	Public function clas() {
		if(!empty($_REQUEST['gradeInfo']))
		{
			$id=$_REQUEST['gradeInfo'];
			$this->delGradeInfo($id);
		}
		if(!empty($_REQUEST['ids']))
		{
			$this->mulDelete($_REQUEST['ids']);
		}
		$this->getSchoolInfoList();
		$this->assign("schoolMsg",$this->getSchoolInfo());
		$this->display ();
	}

	// 获取学校信息ts_schools数据表
//	private function getSchoolInfo() {
//		$schoolInfo = M ()->table ( 'ts_schools' )->field ( "distinct(school_id) schoolid,title" )->select ();
//		 //dump($schoolInfo);
//		 //dump(M()->getLastSql());
//		return $schoolInfo;
//	}

//	// 获取学校信息ts_schools数据表
//	private function getSchoolInfo() {
//		$schoolInfo = M ()->table ( 'ts_schools' )->field ( "distinct(school_id) schoolid,title" )->select ();
//		 //dump($schoolInfo);
//		 //dump(M()->getLastSql());
//		return $schoolInfo;
//	}
	// 获取列表信息- -。
	public function getClassInfoList() {
        $model = model("ClassInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumClassInfo(), 5);//实例化分页类
        $classInfoList = $model -> selectClassInfo($Page->firstRow.','.$Page->listRows);
        $show=$Page->show();// 分页显示输出\
        $this->assign("schoolMsg",$model->getSchoolInfo());
        $this->assign("page",$show);
        $this->assign("class",$classInfoList);
        $this->display("class");
	}
	//删除单个- -
	public function delClassInfo(){
//		if(!isAjax())return;
        $model = model("ClassInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delClassInfo($Id);
		 if($result)
		{
			$this->ajaxReturn($result,"删除成功！",1);
		}
		else
		{
			$this->ajaxReturn(0,"删除失败！",0);
		}
	}
//	//查询要修改的年级信息
//	public function modifyGrade()
//	{
//		$mGradeId=$_REQUEST['id'];
//		if(!empty($mGradeId))
//		{
//			$modifyGrade=M()
//			->table("ts_school_classes")
//			->where("grade_id='".$mGradeId."'")
////			->field("school_id,short_name,title,next_grade_id,sort_order,grade_id")
//			->select();
//			//dump($modifyGrade);
//			$html = W('Modify', array('gradeMsg' => $modifyGrade));
//			exit($html);
//		}
//	}
    //修改-列表
    public function selectSaveClassInfo(){
        $id = $_REQUEST['id'];
        $model = model("ClassInfo");
        $result = $model -> selectSaveClassInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
	//修改信息
    public function saveClassInfo(){
        $data["class_id"]=$_REQUEST['class_id'];
        $data["class_name"]=$_REQUEST['class_name'];
        $data["school_id"]=$_REQUEST['school_id'];
        $data["grade_id"]=$_REQUEST['grade_id'];
        $data["school_period_id"]=$_REQUEST['period_id'];
        $model = model("classInfo");
        $saveInfo = $model -> saveClassInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
 	public function afterModifyGrade(){
 		$data["school_id"]=$_REQUEST['schoolid'];
 		$data["short_name"]=$_REQUEST['shortname'];
 		$data["title"]=$_REQUEST['title'];
 		$data["next_grade_id"]=$_REQUEST['nextgrade'];
 		$data["sort_order"]=$_REQUEST['sortorder'];
		$modifyGrade=M()
			->table("ts_school_classes")
			->where("grade_id='".$_REQUEST['gradeid']."'")
			->save($data); 		
		$this->redirect("houtai/grade/grade");
	}
    //复选框多个删除--
    public function mulDeleteClassInfo(){
        $model = model("ClassInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delClassInfo($id[$i]);
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
	//多个删除
	private function mulDelete($ids)
	{

//		if(!isAjax())return;
		//$ids=$_REQUEST[''];
		if(!empty($ids))
		{
			//dump($ids);
			$id=explode(',',$ids);
			//dump($id);
			$count=count($id);
			$realCount=0;
			for($i=0;$i<$count;$i++)
			{
				$resStr=M()
				->table("ts_school_classes")
				//->where("grade_id in ('".$ids."')")
				->where("grade_id ='".$id[$i]."'")
				->delete();
				if($resStr)
				{
					$realCount++;
				}
				//dump($resStr);
				//dump(M()->getlastsql());
			}
			/* $resStr=M()
				->table("ts_school_gradelevels")
				->where("grade_id in ('".$ids."')")
				->delete(); */
			//dump($resStr);
			//dump(M()->getLastSql());
			//dump($realCount."+++++".$count);
			if($count==$realCount)
			{
				$this->ajaxReturn($resStr,'删除成功！',1);
			}
		}
	}
    //添加班级信息--
    public function addClassInfo(){
        $model = model("ClassInfo");
        $data["class_id"]=$_REQUEST['class_id'];
        $data["class_name"]=$_REQUEST['class_name'];
        $data["school_id"]=$_REQUEST['school_id'];
        $data["grade_id"]=$_REQUEST['grade_id'];
        $data["school_period_id"]=$_REQUEST['school_period_id'];
        $result = $model -> addClassInfo($data);
        if($result)
        {
            $this->ajaxReturn('添加成功');
        }
        else
        {
            $this->ajaxReturn('添加失败');
        }
    }
	//添加
	public function add()
	{
		/* alert("学校ID："+$('#schoolid option:selected').val()+"+简称:"+$('#shortname').val()+",+年及名称:"+$('#gradename').val()+",+下一年级:"+
		$('#nextgradeid').val()+",+排序:"+$('#sort option:selected').text()+",+年级ID:"+$('#gradeid').val()); */		
		$data["school_id"]=$_REQUEST['schoolid'];
 		$data["short_name"]=$_REQUEST['shortname'];
 		$data["title"]=$_REQUEST['title'];
 		$data["next_grade_id"]=$_REQUEST['nextgradeid'];
 		$data["sort_order"]=$_REQUEST['sort'];
 		$data["grade_id"]=$_REQUEST['gradeid'];
 		//dump($data);
		$resStr=M()
			->table('ts_school_classes')
			->add($data);
		//dump(M()->getLastSql());
		if($resStr)
		{
			$this->ajaxReturn('添加成功');
		}
		else
		{
			$this->ajaxReturn('添加失败');
		}
	}
}