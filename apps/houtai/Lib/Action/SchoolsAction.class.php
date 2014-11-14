<?php
header('Content-Type:text/html;charset=utf-8');
class SchoolsAction extends Action{
    public function getSchoolInfoList() {
        $model = model("SchoolInfo");
		import('ORG,Util.Page');//分页
		$Page = new Page ($model->sumSchoolInfo(), 10);//实例化分页类
        $schoolInfoList = $model -> selectSchoolInfo($Page->firstRow.','.$Page->listRows);
		$show=$Page->show();// 分页显示输出\
		$this->assign("page",$show);

		$this->assign("school",$schoolInfoList);
        $this->display();
	}
    //添加信息页面
    public function addInfo(){

        $this -> display();
    }
//    /**
//     *         私有函数，供getTreeKnow函数调用
//     *       把扁平数组按照层级树形势遍历并输出树
//     */
    private function genTree($items)
    {
        $tree = array(); //格式化好的树
        $tmpMap = array(); //临时扁平数据
        foreach ($items as $item) {
            $tmpMap[$item['area_id']] = $item;
        }
        foreach ($items as $item) {
            if (isset($tmpMap[$item['pid']]) != 0) {
                //加入儿子节点统计字段
                //$tmpMap[$item['parent_id']]['son_id'] = $tmpMap[$item['parent_id']]['son_id'] . '/' . $item['id'];
                $tmpMap[$item['pid']]['son'][] = & $tmpMap[$item['area_id']];
            } else {
                $tree[] = & $tmpMap[$item['area_id']];
            }
        }
        unset($tmpMap);
        return $tree;
    }


    /**
     *
     * ["id"] => string(6) "101001"
    ["syear"] => string(4) "2012"
    ["title"] => string(15) "北京市一中"
    ["address"] => string(18) "北京市朝阳区"
    ["state"] => NULL
    ["zipcode"] => NULL
    ["area_code"] => NULL
    ["phone"] => string(11) "13811111111"
    ["principal"] => NULL
    ["www_address"] => string(11) "www.abc.com"
    ["e_mail"] => NULL
    ["ceeb"] => NULL
    ["reporting_gp_scale"] => NULL
    ["province"] => string(1) "0"
    ["city"] => string(1) "0"
    ["area"] => string(1) "0"
    ["postcode"] => NULL
    ["school_id"] => string(7) "0101001"
     *
     *
     *
     *
     * ["app"] => string(6) "houtai"
    ["mod"] => string(7) "Schools"
    ["act"] => string(13) "addSchoolInfo"
    ["syear"] => string(4) "2014"
    ["title"] => string(1) "f"
    ["www_address"] => string(1) "f"
    ["zipcode"] => string(1) "f"
    ["e_mail"] => string(1) "f"
    ["principal"] => string(1) "f"
    ["phone"] => string(1) "f"
    ["postcode"] => string(2) "fd"
    ["province"] => string(6) "120000"
    ["city"] => string(6) "120200"
    ["area"] => string(6) "120223"
    ["city_ids"] => string(20) "120000,120200,120223"
    ["city_names"] => string(23) "天津市 县 静海县"
    }
     */
    //添加信息表单 插入数据库
    public function addSchoolInfo(){
        //在添加学校的时候添加学校管理员
 		$schoolData["syear"] = $_REQUEST['syear'];
        $schoolData["syear_id"] = $_REQUEST['syear_id'];
        $schoolData["title"] = $_REQUEST['title'];
        $schoolData['address'] = $_REQUEST['city_names'];
        $schoolData["state"] = 1;
        $schoolData["zipcode"] = $_REQUEST['zipcode'];
        $schoolData["area_code"] = $_REQUEST['city_ids'];
        $schoolData["phone"] = $_REQUEST['phone'];
        $schoolData["principal"] = $_REQUEST['principal'];
        $schoolData["www_address"] = $_REQUEST['www_address'];
        $schoolData["e_mail"] = $_REQUEST['e_mail'];
        $schoolData["ceeb"] = "";
        $schoolData["reporting_gp_scale"]= "";
        $schoolData["province"]=$_REQUEST['province'];
        $schoolData["city"]=$_REQUEST['city'];
        $schoolData["area"]=$_REQUEST['area'];
        $schoolData["postcode"]=$_REQUEST['zipcode'];
        //学校id的构成
        $model = model("SchoolInfo");
        $return = $model ->selectSchoolLastInfo();
        $schoolID = $schoolData["area"].$schoolData["phone"].$return['id'];
        $schoolData['school_id'] = $schoolID;

        //添加学校管理员
        $addUser['login'] = $_REQUEST['phone'];
        $addUser['password'] = "uteach123";
        $addUser['uname'] = $_REQUEST['principal'];
        $addUser['email'] = $_REQUEST['e_mail'];
        $addUser['sex'] = 1;
        $addUser['location'] = $_REQUEST['city_names'];
        $addUser["province"]=$_REQUEST['province'];
        $addUser["city"]=$_REQUEST['city'];
        $addUser["area"]=$_REQUEST['area'];
        $addUser['intro'] = "校长、学校管理员";
        $addUser['school_id'] = $schoolID;
        $addUser['profile_id'] = "4";
        $addUser['profile_no'] = $_REQUEST['area'];


        //准备添加学校

        if(empty($return) || !$return) {
            $return['id'] = 100;

        }else {
            $return['id'] += $return['id'];
        }


        $sameSchool["title"] = $schoolData["title"];
        $sameSchool["phone"] = $schoolData["phone"];

        $sameSchool = $model ->selectSchoolInfo("","",$sameSchool);

        if(empty($schoolData['area'])){
            $this->messageInfo("请选择地区信息！！！",1,'houtai/Schools/addInfo');exit();
        }
        if(empty($sameSchool)){
            $schoolAdd = $model->addSchoolInfo($schoolData);
        } else {
            $this->messageInfo("该学校已经添加！",1,'houtai/Schools/addInfo');exit();
        }
        $addUserReturn = D('User')->addUser($addUser);
        if(!empty($schoolAdd) && $addUserReturn) {
            $this->messageInfo("学校添加成功！",1,'houtai/Schools/getSchoolInfoList');exit();
        } else {
            $this->messageInfo("添加失败，未知错误！！",1,'houtai/Schools/addInfo');exit();

        }
   }

    //修改
   public function updateSchool(){

       if(!isset($_REQUEST['school_id']) || empty($_REQUEST['school_id'])) {
           $this->messageInfo("错误！！！",1,'houtai/Schools/getSchoolInfoList');exit();
       }
       $schoolID = $_REQUEST['school_id'];
       $school = M()->table("ts_schools")->where("school_id=$schoolID")->find();
       if(!empty($school)) {
           $schoolManager = D('User')->getUserInfoByName($school['principal']);
       } else {
           $this->messageInfo("没有此学校！！！",1,'houtai/Schools/getSchoolInfoList');exit();
       }
       $this->assign('sch',$school);
       $this->assign('uid',$schoolManager["uid"]);
       $this->display();
   }



    //修改
   public function saveSchoolInfo(){

       //在添加学校的时候添加学校管理员
       $schoolData["syear"] = $_REQUEST['syear'];
       $schoolData["title"] = $_REQUEST['title'];
       $schoolData["zipcode"] = $_REQUEST['zipcode'];
       $schoolData["phone"] = $_REQUEST['phone'];
       $schoolData["principal"] = $_REQUEST['principal'];
       $schoolData["www_address"] = $_REQUEST['www_address'];
       $schoolData["e_mail"] = $_REQUEST['e_mail'];
       $schoolData["postcode"]=$_REQUEST['zipcode'];
       if(!empty($_REQUEST['city_names']) && !empty($_REQUEST['city_ids'])) {
           $schoolData['address'] = $_REQUEST['city_names'];
           $schoolData["area_code"] = $_REQUEST['city_ids'];
           $schoolData["province"]=$_REQUEST['province'];
           $schoolData["city"]=$_REQUEST['city'];
           $schoolData["area"]=$_REQUEST['area'];

           $addUser['location'] = $_REQUEST['city_names'];
           $addUser["province"]=$_REQUEST['province'];
           $addUser["city"]=$_REQUEST['city'];
           $addUser["area"]=$_REQUEST['area'];
           $addUser['profile_no'] = $_REQUEST['area'];
       }

       //添加学校管理员
       $addUser['login'] = $_REQUEST['phone'];
       $addUser['uname'] = $_REQUEST['principal'];
       $addUser['email'] = $_REQUEST['e_mail'];
       $uid = $_REQUEST['uid'];
       $school_id = $_REQUEST['school_id'];
       $model = model("SchoolInfo");


       $saveUserResult = M('User')->where(array('uid'=>$uid))->data($addUser)->save();
       //修改学校信息
       if($saveUserResult) {
           $saveInfo = $model -> saveSchoolInfo(array('school_id'=> $school_id),$schoolData);
           if($saveInfo) {
               $this->messageInfo("修改成功！！！",1,'houtai/Schools/getSchoolInfoList');exit();
           } else {
               $this->messageInfo("修改失败！",1,'houtai/Schools/getSchoolInfoList');exit();

           }
       } else {
           $this->messageInfo("修改失败，管理员用户修改失败，学校信息没修改！",1,'houtai/Schools/getSchoolInfoList');exit();
       }




       if($saveInfo){
           $this -> success("修改成功");
       } else {
           $this -> error("修改失败");
       }
	}
    //复选框多个删除--
    public function mulDeleteSchoolInfo(){
        $model = model("SchoolInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delSchoolInfo($id[$i]);
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
    //删除
	public function delSchoolInfo(){
		$schoolID = $_REQUEST['school_id'];
        $phone = $_REQUEST['phone'];
        $model = model("SchoolInfo");
        $result = $model -> delSchoolInfo($schoolID,$phone);
		  if($result)
		{
			$this->success('删除成功！');		
		}
		else
		{
			$this->success('删除失败！');
		} 
	}


    public function ajaxStatus() {

        if (!$this->isAjax())
            return;
        $schoolID = $_REQUEST['school_id'];

        $state = $_REQUEST['state'];
        $m = M("schools") -> where("school_id = $schoolID") -> setField('state',$state);;
        if($m) {
            exit("成功！");
        }else{
            exit("失败");
        }
    }
}
