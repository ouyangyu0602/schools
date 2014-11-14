<?php
/**
 * Created by PhpStorm.
 * User: Qiao
 * Date: 13-11-2
 * Time: 下午3:06
 */

class StudentInfoWidget extends Widget
{
    /**
     * 渲染输出 render方法是Widget唯一的接口
     * 使用字符串返回 不能有任何输出
     * @access public
     * @param mixed $data 要渲染的数据
     * @return string
     */
    public function render($data)
    {
        $studentArray = $data['studentArray'];
        $studentCount = $data['studentCount'];

        //dump($studentArray);
        $var['studentList'] = $studentArray;
        $var['studentCount'] = $studentCount;
        // 渲染模版
        $content = $this->renderFile(dirname(__FILE__) . "/pool.html", $var);
        // 输出数据
        return $content;
    }


}

