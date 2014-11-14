<?php
/**
 * Created by PhpStorm.
 * User: ouyangyu
 * Date: 14-10-13
 * Time: 下午12:54
 */


/*
 * 主要注意的有以下几点：
 *      1、命名，首先必须的驼峰标识。命名不能与上一层逻辑相关联
 *      2、方法名称，必须用英文，而且必须能通过字面意思得出作用
 *      3、方法作用的描述
 *      4、方法请求参数，以及参数默认值和常规的空值判断等必须有
 *      5、返回数据格式，如数组等，必须写明
 *      6、写明作者
 *
 */

class UTeachBasicModel extends Model {


    /**
     *
     *
     */

    public function getSchool($school_id) {

        $result =  M()
            -> table('ts_schools tss')
            -> field('tss.title,tss.school_id')
            -> where("tss.school_id = '$school_id'")
            -> limit(1)
            -> select();
        return $result[0];
    }

    /*
     *
     *
     * ["short_name"] => string(2) "Y1"
    ["title"] => string(9) "一年级"
    ["next_grade_id"] => string(9) "101001002"
    ["sort_order"] => string(1) "1"
    ["grade_id"] => string(10) "0101001001"
     */
    public function getGradeBySchool($school_id) {
        $result =  M()
            -> table('ts_school_gradelevels tsg')
            -> field('tsg.title,tsg.grade_id')
            -> where("tsg.school_id = '$school_id'")
            -> order('sort_order')
            -> select();
        return $result;
    }

    public function getGradeClassTree($schoolId) {
        $gradeList = $this->getGradeBySchool($schoolId);
        //dump(M()->getLastsql());die();
        $classTree = array();
        foreach($gradeList as $grade) {
            $classArray =  $this->getClassByGrade($grade['grade_id']);
            $tree = $grade;
            $tree['class'] = $classArray;

            $classTree[] = $tree;
        }
        return $classTree;
    }
    /**
     * 通过班级获取所有学生
     * @param $classid 查询条件 班级id
     * @return array 班级学生信息数组
     * @author luo
     * array（4）{
     * ["login"] => string(5) "登录名"
    ["uname"] => string(10) "姓名 "
    ["profile_no"] => string(5) "学号"
    ["class_id"] => string(13) "班级id"
     * }
     *
     */
    public function getStudentByClass($classid){

        $result =  M()
            -> table('ts_school_class_students a,ts_user b')
            -> field('b.login,b.uname,b.profile_no,a.class_id')             //学生id,学生姓名,学号，班级
            -> where("a.class_id = '$classid'  AND a.login = b.login")
            -> group('login')
            -> select();
        return $result;
    }


    /**
     * 通过年级获取所有班级
     * @param $grade 年级 id
     * @author luo
     * @return array
     * array(n) {
    [0] => array(6) {
    ["id"] => string(2) "id"
    ["class_id"] => string(13) "班级id"
    ["class_name"] => string(15) "班级名称"
    ["school_id"] => string(7) "学校id"
    ["grade_id"] => string(10) "年级id"
    ["school_period_id"] => string(2) "学期id"
    }
     * }
     */
    public function getClassByGrade($grade){
        $result = M('school_classes')
            -> where("grade_id = '$grade'")
            -> field('class_id,class_name')
            -> order()
            -> select();
        return $result;
    }


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
    public function selectParentByStu($studentId){
        $result = M()
            -> table('ts_students_join_users a,ts_user b,ts_user c')
            -> field('b.login login_parent,b.uname uname_parent,c.profile_no,c.uname,c.uid')
            -> where("a.student_id = '$studentId'  AND a.staff_id = b.login AND c.login = '$studentId'")
            -> find();
        return $result;
    }

    /**
     * 根据学生获取对应信息
     * @param $studentid 查询条件 id
     * @return array(学生姓名，学校名称，班级名称，入学时间，性别，学号，邮箱，联系方式)  联系方式未掉出没找到
     *array(8) {
    ["uname"] => string(10) "学生姓名 "
    ["title"] => string(15) "学校名称"
    ["school_period_name"] => string(13) "入学时间"
    ["class_name"] => string(15) "班级名称"
    ["ctime"] => string(10) "联系方式"
    ["sex"] => string(1) "性别"
    ["profile_no"] => string(5) "学号"
    ["email"] => 邮件
    }
     */
    public function getStudentInfo($studentId){
        $result = M()
            -> table('ts_user a,ts_school_period b,ts_school_class_students c,ts_school_classes d,ts_schools e')
            -> field('a.uname,e.title,b.school_period_name,d.class_name,a.ctime,a.sex,a.profile_no,a.email')
            -> where("a.login = '$studentId'  AND a.school_id = b.school_id AND a.login = c.login AND c.class_id = d.class_id AND e.school_id = b.school_id")
            -> find();
        return $result;
    }

    /**
     * 统计班级学生数量
     * @return string 学生数量
     * string(1) "数量"
     * */
    public function getClassStudentCount($classID){
        $m= M()
            -> table($this->tablePrefix.'school_class_students');
        $where  = "class_id='".$classID."'";
        $result = $m
            -> where($where)
            -> count();
        return  $result;
    }

}