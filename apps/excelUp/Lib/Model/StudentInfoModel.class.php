<?php
/**
 * Created by PhpStorm.
 * User: ouyangyu
 * Date: 14-10-13
 * Time: 下午4:04
 */

class StudentInfoModel extends Model{

    /**
     * 根据学生获取对应家长
     * @param $studentid 查询条件 id
     *  b查询教师的user表 c 查询学生的user表
     * @author luo
     *@return array(家长id，家长姓名，学生学号，学生姓名)
     * array(5) {
    ["login_parent"] => string(11) "家长登录名"
    ["uname_parent"] => string(10) "家长名 "
    ["profile_no"] => string(5) "学号"
    ["uname"] => string(10) "学生名 "
    ["uid"] => string(3) "用户id"
    }
     */
    public function getParentInfo($studentId){
        $result = M()
            -> table('ts_students_join_users a,ts_user b')
            -> field('b.login studentParent,b.uname parentName')
            -> where("a.student_id = '$studentId'  AND a.staff_id = b.login")
            -> find();
        return $result;
    }


    public function getStudentInfo($studentID,$classID) {

        $parent = $this->getParentInfo($studentID);
        //dump($parent);
        $student = M()
            -> table('ts_user b')
            -> field('b.login studentLogin,b.uname studentName, b.sex,b.profile_no')
            -> where("b.login = '$studentID'")
            ->limit(1)
            -> find();

        //dump($student);
        $studentSchoolInfo =  M()
            -> table('ts_school_classes a,ts_school_gradelevels b,ts_schools c')
            -> field('a.class_id,a.class_name,a.grade_id,b.title gradeName,a.school_id,c.title schoolName')             //学生id,学生姓名,学号，班级
            -> where("a.class_id = '$classID'  AND a.grade_id = b.grade_id AND b.school_id = c.school_id")
            -> find();

        $result = array_merge($student,$parent,$studentSchoolInfo);
        return $result;
    }

    public function getGradeNameByGradeID($gradeID){
        $result = M()
            -> table('ts_school_gradelevels')
            -> where("grade_id='$gradeID'")
            -> field('title')
            -> find();
        return $result;
    }

    public function getStudentBySchool($school_id) {
        $result =  M()
            -> table('ts_user b,ts_school_class_students a')
            -> field('b.uid,b.login,b.uname,b.profile_no,a.class_id')
            -> where("b.school_id = '$school_id' AND a.school_id = '$school_id' AND a.login = b.login AND profile_id = '1'")
            -> order('uname')
            -> select();
        return $result;
    }

    public function getStudentByClass($classId){
        $result =  M()
            -> table('ts_school_class_students a,ts_user b')
            -> field('b.login,b.uname,b.profile_no,a.class_id')             //学生id,学生姓名,学号，班级
            -> where("a.class_id = '$classId'  AND a.login = b.login")
            -> group('login')
            -> select();
        return $result;
    }

    public function getStudentByGrade($gradeId){
        $result =  M()
            -> table('ts_school_class_students a,ts_user b')
            -> field('b.login,b.uname,b.profile_no,a.class_id')             //学生id,学生姓名,学号，班级
            -> where("a.grade_id = '$gradeId'  AND a.login = b.login")
            -> group('login')
            -> select();
        return $result;
    }
} 