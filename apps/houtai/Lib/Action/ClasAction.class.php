<?php
class ClasAction extends Action {
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
	
	// 获取学校信息ts_schools数据表
	private function getSchoolInfo() {
		$schoolInfo = M ()->table ( 'ts_schools' )->field ( "distinct(school_id) schoolid,title" )->select ();
		 //dump($schoolInfo);
		 //dump(M()->getLastSql());
		return $schoolInfo;
	}
	// 获取列表信息。
	private function getSchoolInfoList() {
		//添加分页
		$num=M()
			->table("ts_school_classes")
			->field("count(id) listNum")
			->select();
		//dump($num[0]['listNum']);
		//dump(M()->getLastSql());
		import('ORG,Util.Page');//分页
		$Page = new Page ( $num[0]['listNum'], 5);//实例化分页类
		$schoolInfoList=M()
			->table("ts_school_classes")
//			->field("school_id,short_name,title,next_grade_id,sort_order,grade_id")
			->limit( $Page->firstRow.','.$Page->listRows)
			->select();
		//dump($schoolInfoList);
		//dump(M()->getLastSql());
		$show=$Page->show();// 分页显示输出\
		$this->assign("pager",$show);
		$this->assign("schoolInfoList",$schoolInfoList);
		//return $schoolInfoList;
	}
	//删除单个
	private function delGradeInfo($gradeId){
		if(!isAjax())return;
		$result=M()
			->table("ts_school_classes")
			->where("grade_id='".$gradeId."'")
			->delete();
		 if($result)
		{
			$this->ajaxReturn($result,'删除成功！',1);		
		}
		else
		{
			$this->ajaxReturn(0,'删除失败！',0);
		} 
	}
	//查询要修改的年级信息
	public function modifyGrade()
	{
		$mGradeId=$_REQUEST['id'];
		if(!empty($mGradeId))
		{
			$modifyGrade=M()
			->table("ts_school_classes")
			->where("grade_id='".$mGradeId."'")
//			->field("school_id,short_name,title,next_grade_id,sort_order,grade_id")
			->select();
			//dump($modifyGrade);
			$html = W('Modify', array('gradeMsg' => $modifyGrade));
			exit($html);
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