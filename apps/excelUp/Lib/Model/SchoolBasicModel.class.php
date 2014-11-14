<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-10-13
 * Time: 上午10:38
 */

class SchoolModel extends Model{
    /**
        查询所有学校
     * return array 学校信息
     * array() {
            [0] => array(18) {
            ["id"] => string(1) "1"
            ["syear"] => string(4) "2012"
            ["title"] => string(15) "北京市一中"
            ["address"] => string(18) "北京市朝阳区"
            ["state"] => NULL
            ["zipcode"] => NULL
            ["area_code"] => NULL
            ["phone"] => string(11) "13811111111"
            ["principal"] => NULL
            ["www_address"] => string(12) "www.abc.com "
            ["e_mail"] => string(7) "a@a.com"
            ["ceeb"] => NULL
            ["reporting_gp_scale"] => NULL
            ["province"] => string(1) "0"
            ["city"] => string(1) "0"
            ["area"] => string(1) "0"
            ["postcode"] => string(6) "100001"
            ["school_id"] => string(7) "0101001"
            }
     * }
     */
    public function selectSchool(){
        $m = M('schools') -> select();
        return $m;
    }
    /**
        根据学校得到这个学校所有年级
     * @param $schoolid 学校id
     * @return array 年级信息
     * array(n) {
            [0] => array(7) {
            ["id"] => string(1) "1"
            ["school_id"] => string(7) "0101001"
            ["short_name"] => string(2) "Y1"
            ["title"] => string(9) "一年级"
            ["next_grade_id"] => string(9) "101001002"
            ["sort_order"] => string(1) "1"
            ["grade_id"] => string(10) "0101001001"
            }
     * }
     */
    public function getGradeBySchool($schoolid){
        $m = M('school_gradelevels') -> where("school_id=$schoolid") -> select();
        return $m;
    }



    /**
        根据学校和年级得到这个年级的班级
     * param $array(school_id,grade_id)
     * return array 本年级所班级
     * array(n) {
            [0] => array(6) {
            ["id"] => string(2) "13"
            ["class_id"] => string(13) "0101001005013"
            ["class_name"] => string(15) "五年级一班"
            ["school_id"] => string(7) "0101001"
            ["grade_id"] => string(10) "0101001005"
            ["school_period_id"] => string(2) "01"
            }
     * }
     */
    public function getClassBySchool($where){
        $m = M('school_classes') -> where($where) -> select();
        return $m;
    }
    /**
     * 通过班级获取所有学生
     * @param $classid 查询条件 班级id
     * @return array 班级学生信息数组
     * @author luo
     * array（n）{
             [0] => array(4) {
     *      ["login"] => string(5) "登录名"
            ["uname"] => string(10) "姓名 "
            ["profile_no"] => string(5) "学号"
            ["class_id"] => string(13) "班级id"
     *     }
     * }
     *
     */
    public function selectStudentByClass($classid){
        $result =  M()
            -> table('ts_school_class_students a,ts_user b')
            -> field('b.login,b.uname,b.profile_no,a.class_id')             //学生id,学生姓名,学号，班级
            -> where("a.class_id = '$classid'  AND a.login = b.login")
            -> group('login')
            -> select();
        return $result;
    }
    /**
        根据年级得到这个年级所有的学生
     * @param grade_id 年级id
     * @return array 年级所有学生
     * array（n）{
            [0] => array(4) {
 *              ["login"] => string(5) "登录名"
                ["uname"] => string(10) "姓名 "
                ["profile_no"] => string(5) "学号"
                ["class_id"] => string(13) "班级id"
     *           }
     *     }
     *
     */
    public function getStudentByGrade($gradeid){
        $m = M()
            -> table('ts_school_classes sc,ts_school_class_students scs,ts_user u')
            -> field('u.login,u.uname,u.profile_no,scs.class_id')
            -> where("sc.grade_id = '$gradeid' AND sc.class_id=scs.class_id AND scs.login = u.login")
            -> group('login')
            -> select();
        return $m;
    }
    /**
     * 根据学校得到这个学校所有的学生
     *   * @param school_id 学校id
     * @return array 年级所有学生
     * array（n）{
            [0] => array(4) {
     *      ["login"] => string(5) "登录名"
            ["uname"] => string(10) "姓名 "
            ["profile_no"] => string(5) "学号"
            ["class_id"] => string(13) "班级id"
     *           }
     *     }
     *
    */
    public function getStudentBySchool($schoolid){
        $m = M()
            -> table('ts_schools s,ts_school_classes sc,ts_school_class_students scs,ts_user u')
            -> field('u.login,u.uname,u.profile_no,scs.class_id')
            -> where("sc.school_id=$schoolid AND sc.class_id=scs.class_id AND scs.login = u.login")
            -> group('login')
            -> select();
        return $m;
    }
}