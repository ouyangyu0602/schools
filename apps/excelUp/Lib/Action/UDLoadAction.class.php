<?php
/**
 * Created by PhpStorm.
 * User: ouyangyu
 * Date: 14-10-13
 * Time: 下午1:06
 */
class UDLoadAction extends Action {


    public function addGradeText() {

        if(!isset($_REQUEST['gradeTitle'])) {
            $this->addError('非法请求！');exit();
            //20141015152650604
        }
        if(empty($_REQUEST['gradeTitle']) || empty($_REQUEST['gradeShortTitle']) || empty($_REQUEST['gradeSort'])){
              $this->addError("请填写完整！！！");exit();
        }

        if($_REQUEST('gradeSchool') === "isNull") {
            $school_id = 'uTeach'.sha1(uniqid(mt_rand(),1));
        } else{
            $school_id = $_REQUEST('gradeSchool');
        }

        $grade['title'] = t($_REQUEST['gradeTitle']);
        $grade['short_name'] = t($_REQUEST['gradeShortTitle']);
        $grade['sort_order'] = t($_REQUEST['gradeSort']);
        $grade['next_grade_id'] = t($_REQUEST['gradeSort']-1);
        $grade['school_id'] = $school_id;
        $grade['grade_id'] = $school_id;






    }

    private function addError($msg,$step = 1,$url = 'excelUp/StudentInfo/index') {
        $this->assign('jumpUrl',U($url,array('step'=>$step)));
        $this->assign('msg',$msg);
        $this->assign('waitSecond',"10");
        $this->display('addError');
    }


    public function downLoadStudentInfo() {
        $downLoadType = $_REQUEST['downLoadType'];
        $downLoadTypeId = $_REQUEST['downLoadTypeId'];


    }

    public function downLoad(){


        $paper=$_REQUEST['paperID'];
        //$paper='10000000007';
        $videoList=D('Synchronism')->getVideoInfo($paper);
        /* if(empty($videoList) && !is_array($videoList)){

         }*/
        $num=count($videoList);
        //include_once(SITE_PATH.'/addons/library/PHPExcel/PHPExcel.php');
        include_once('../PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);;
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '视频序号')
            ->setCellValue('B1', '试卷编号')
            ->setCellValue('C1', '视频ID')
            ->setCellValue('D1', '视频名称')
            ->setCellValue('E1', '视频文件路径')
            ->setCellValue('F1', '作者编号')
            ->setCellValue('G1', '作者介绍')
            ->setCellValue('H1', '视频介绍')
            ->setCellValue('I1', '视频类型')
            ->setCellValue('J1', '视频价格');

        for ($i=0; $i < $num; $i++) {
            $n=$i+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$n, $videoList[$i]['id'])
                ->setCellValue('B'.$n, $videoList[$i]['paperid'])
                ->setCellValue('C'.$n, $videoList[$i]['videoid'])
                ->setCellValue('D'.$n, '')
                ->setCellValue('E'.$n, '')
                ->setCellValue('F'.$n, '')
                ->setCellValue('G'.$n, '')
                ->setCellValue('H'.$n, '')
                ->setCellValue('I'.$n, '')
                ->setCellValue('J'.$n, '');

        }
        $countNun=$num+3;
        $miaoshu='模板文档说明：模板中已有数据请不要修改。视频路径为*.mp4(规则 20141008-1.mp4 年月日-题号.mp4),视频类型只能填入0/1（0为免费，1为付费），视频价格默认格式为0.00（单位：元）。';
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$countNun:J$countNun")
            ->setCellValue("A$countNun",$miaoshu);
        $objPHPExcel->getActiveSheet()->setTitle('title');
        $objPHPExcel->setActiveSheetIndex(0);
        spl_autoload_register(array('Think','autoload'));
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="文件名.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}