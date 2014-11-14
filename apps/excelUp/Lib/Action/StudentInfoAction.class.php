<?php
/**
 *        成绩发送首页
 *
 *
 */
class StudentInfoAction extends Action {


    public function index() {

        $schoolId = $GLOBALS['ts']['user']['school_id'];
        $uTeachBasic = M('UTeachBasic');
        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');
        //获取当前登陆者的学校
        $schoolInfo = $uTeachBasic->getSchool($schoolId);
        //{$变量|default=”默认值”}
        $classTree = $uTeachBasic->getGradeClassTree($schoolId);

        $studentArray = $studentInfo->getStudentBySchool($schoolId);
        $result = $utilModel->studentSort($studentArray);
        $this->assign('result',$result);
        $this->assign('studentCount',count($studentArray));
        $this->assign('userInfo',$GLOBALS['ts']['user']);
        $this->assign('schoolInfo',$schoolInfo);
        $this->assign('classTree',$classTree);
        $this->display();
    }
    public function student() {
        $this->display();
    }

    /**
     *
     * array(12) {
    ["studentLogin"] => string(6) "040104"
    ["studentName"] => string(10) "郭汉三 "
    ["sex"] => string(1) "1"
    ["profile_no"] => string(5) "20006"
    ["studentParent"] => string(11) "13854694801"
    ["parentName"] => string(7) "邓煜 "
    ["class_id"] => string(13) "0101001004001"
    ["class_name"] => string(15) "四年级一班"
    ["grade_id"] => string(10) "0101001004"
    ["gradeName"] => string(9) "四年级"
    ["school_id"] => string(7) "0101001"
    ["schoolName"] => string(15) "北京市一中"
    }
     *
     */
    public function ajaxStudentInfo() {

        /*if (!$this->isAjax() || empty($_REQUEST['class_id']) || empty($_REQUEST['student_id']))
            return;*/

        $studentInfo = M('StudentInfo');
        $class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : $GLOBALS['ts']['teacher_subject_classes']['0']['class_id'];
        $student_id = $_REQUEST['student_id'];
        $studentArray = $studentInfo->getStudentInfo($student_id, $class_id);
        //dump($student_id);
        $html = W('PersonalCenter', array('studentArray' => $studentArray));
        exit($html);
    }

    public function ajaxGradeStudent()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');

        $grade_id = isset($_REQUEST['grade_id']) ? $_REQUEST['grade_id'] : $GLOBALS['ts']['teacher_subject_classes']['0']['grade_id'];
        $studentArray = $studentInfo->getStudentByGrade($grade_id);

        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        $html = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));

        /*$studentInfo = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        $studentList = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));
        */
        //exit(json_encode($data));
        /*$html = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit($html);*/
        exit($html);


    }
    public function ajaxGradeStudentList()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');

        $grade_id = isset($_REQUEST['grade_id']) ? $_REQUEST['grade_id'] : $GLOBALS['ts']['teacher_subject_classes']['0']['grade_id'];
        $studentArray = $studentInfo->getStudentByGrade($grade_id);

        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        $html = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit($html);


    }

    public function ajaxClassStudent()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');

        $class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : $GLOBALS['ts']['teacher_subject_classes']['0']['class_id'];
        $studentArray = $studentInfo->getStudentByClass($class_id);

        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        $html = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit($html);

    }
    public function ajaxClassStudentList()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');

        $class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : $GLOBALS['ts']['teacher_subject_classes']['0']['class_id'];
        $studentArray = $studentInfo->getStudentByClass($class_id);

        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        $html = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));

        exit($html);

    }

    public function ajaxSchoolStudent()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');
        $school_id = isset($_REQUEST['school_id']) ? $_REQUEST['school_id'] : $GLOBALS['ts']['user']['school_id'];
        $studentArray = $studentInfo->getStudentBySchool($school_id);
        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        /*$data['studentInfo'] = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        $data['studentList'] = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit(json_encode($data));*/
        $data = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit($data);
    }
    public function ajaxSchoolStudentList()
    {
        if (!$this->isAjax())
            return;

        $studentInfo = M('StudentInfo');
        $utilModel = M('Util');
        $school_id = isset($_REQUEST['school_id']) ? $_REQUEST['school_id'] : $GLOBALS['ts']['user']['school_id'];
        $studentArray = $studentInfo->getStudentBySchool($school_id);
        $result = $utilModel->studentSort($studentArray);
        //dump($studentArray);
        /*$data['studentInfo'] = W('StudentInfo', array('studentArray' => $result,'studentCount' => count($studentArray)));
        $data['studentList'] = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit(json_encode($data));*/
        $html = W('StudentList', array('studentArray' => $result,'studentCount' => count($studentArray)));
        exit($html);
    }


}