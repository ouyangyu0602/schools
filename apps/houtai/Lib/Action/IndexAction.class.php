<?php
/**
 * 课表控制器
 * @author
 *
 */
class IndexAction extends Action {

	private  $flag = "0";


	/**
	 * 后台首页
	 * @return void
	 */
	public function index() {
		
        $schoolId = $GLOBALS['ts']['user']['school_id'];

        //如果是管理员，那么显示所有的学校
        if($schoolId === "uTeach") {
            $schoolId = "allSchools";
        }
        //获取当前登陆者的学校
        $this->assign('schoolID',$schoolId);
		$this->display();
	}
	/*获得学校id，及title  */
	private function getSchoolInfo()
	{
		$schoolInfo=M()
				->table('ts_schools ts')
				->field("distinct(school_id),title")
				->select();
//		dump($schoolInfo);
//		dump(M()->getLastSql());
	}
	
}