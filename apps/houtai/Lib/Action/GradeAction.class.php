<?php
class GradeAction extends Action {
	Public function grade() {
		//$this->assign ( "schoolInfo", $this->getSchoolInfo () );

        //dump($_REQUEST);die();
		/*
		 *
		 * if(!empty($_REQUEST['gradeInfo']))
		{
			$id=$_REQUEST['gradeInfo'];
			$this->delGradeInfo($id);
		}
		if(!empty($_REQUEST['ids']))
		{
			//dump($_REQUEST['ids']);
			$this->mulDelete($_REQUEST['ids']);
		}
		 */

        if(!empty($_REQUEST['step']) && $_REQUEST['step'] != 1) {
            $schoolID = $_REQUEST['step'];
            $school = $this->getSchoolInfo($schoolID);
            $schoolGradeList = $this->getSchoolGradeList($schoolID);
        } else {
            $schoolList = $this->getSchoolInfo();
            if(!empty($schoolList)){
                $schoolGradeList = $this->getSchoolGradeList($schoolList[0]['schoolId']);

            }else{
                $this->messageInfo("请先添加学校！",1,'houtai/Index/index');exit();
            }
        }

        $this->assign('requestSchool',$school);
        $this->assign("schoolGradeList",$schoolGradeList);
        $this->assign("schoolMsg",$this->getSchoolInfo());
		$this->display ();
	}


    public function ajaxGradeListView(){
        if (!$this->isAjax())
            return;
        $schoolID = $_REQUEST['school_id'];
        $schoolGradeList = $this->getSchoolGradeList($schoolID);
        $html = W('GradeList', array('schoolGradeList' => $schoolGradeList));
        exit($html);
    }
	
	// 获取学校信息ts_schools数据表
	private function getSchoolInfo($school_id = null) {
        if(is_null($school_id)) {
            $map = null;
        } else{
            $map['school_id'] = $school_id;
        }
		$schoolInfo = M ()->table ( 'ts_schools' )->where($map)->field ( "distinct(school_id) schoolId,title" )->select ();
		return $schoolInfo;
	}
	// 获取年级列表信息--
	private function getSchoolGradeList($schoolID) {
        $model = model("GradeInfo");
        $schoolGradeList = $model -> selectGradeInfo($schoolID);

        return $schoolGradeList;
	}
	//删除单条班级信息--
	public function delGradeInfo(){
        $model = model("GradeInfo");
        if(empty($_REQUEST['grade_id'])) {
            $this->messageInfo('错误！',1,'houtai/Grade/grade');
        }
        $gradeID = $_GET['grade_id'];
        $result = $model -> delGradeInfo($gradeID);
        $schoolID = $_REQUEST['school_id'];
		 if($result)
		{
            $this->messageInfo("删除成功！",$schoolID,'houtai/Grade/grade');
		}
		else
		{
            $this->messageInfo('删除失败！',1,'houtai/Grade/grade');
		}
	}
	//查询要修改的年级信息
	public function modifyGrade()
	{
		$gradeID = $_REQUEST['grade_id'];
        $schoolID = $_REQUEST['school_id'];

		if(!empty($gradeID) && !empty($schoolID))
		{
            $model = model("GradeInfo");
            $schoolGradeInfo = $model -> getSchoolGradeInfo($schoolID,$gradeID);


			$html = W('Modify', array('gradeMsg' => $schoolGradeInfo));
			exit($html);
		} else {
            $this->messageInfo('错误！',1,'houtai/Grade/grade');
        }
	}
 	public function afterModifyGrade(){
 		$data["school_id"]=$_REQUEST['schoolid'];
 		$data["short_name"]=$_REQUEST['shortname'];
 		$data["title"]=$_REQUEST['title'];
 		$data["next_grade_id"]=$_REQUEST['nextgrade'];
 		$data["sort_order"]=$_REQUEST['sortorder'];
		$modifyGrade=M()
			->table("ts_school_gradelevels")
			->where("grade_id='".$_REQUEST['gradeid']."'")
			->save($data); 		
		$this->redirect("houtai/grade/grade");
	}
	//多个删除
	private function mulDelete($ids)
	{
		
		if(!isAjax())return;
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
				->table("ts_school_gradelevels")
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

	//添加
	public function add()
	{

        if(empty($_REQUEST['title']) || empty($_REQUEST['short_name'])) {
            $this->ajaxReturn('请填写完整年级信息！！！');
        }


        //如果这个学校已经有这个排名了
        $data["school_id"]=$_REQUEST['school_id'];
        $data["short_name"]=$_REQUEST['short_name'];
        $data["title"]=$_REQUEST['title'];
        $data["sort_order"]=$_REQUEST['sort_order'];
        $model = model("GradeInfo");
        $sameSchool = $model->getGradeBySortAndSchool($data["school_id"],$data["sort_order"]);
        if(!empty($sameSchool)) {

            $this->ajaxReturn('已经存在此年级排序！！！');
        } else {
            $data["grade_id"]= $data["school_id"].$data["sort_order"].rand(100,999);
            $beforeSchool = $model->getGradeBySortAndSchool($data["school_id"],$data["sort_order"]-1);
            if(empty($beforeSchool)) {
                $data['next_grade_id'] = '0';
            } else {
                $data['next_grade_id'] = $sameSchool['grade_id'];
            }

        }

 		//dump($data);
		$resStr=M()
			->table('ts_school_gradelevels')
			->add($data);

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
?>