<?php
/**
 * 上传控制器
 * @欧阳宇
 * 2014/09/17
 * 版本号：V1.0
 * Step1、选择学校信息直接上传
 * Step2、选择年级信息直接上传,包含年级信息直接上传
 * Step3、选择教师信息直接上传,包含教师任职情况上传,即所教科目
 * Step4、选择学生信息直接上传,包含对应的家长信息，包含对应的班级信息
 * Step5、确保每一步都完成，如果没有完成自动刚刚导入的数据
 *
 *
 */
class SchoolInfoAction extends Action {




    /**
     * 1、显示直接上传第几部上传信息
     * 2、选择文件上传，成功后跳转到第二步
     */
    public function index() {

        //处理提示信息
        //定义提示信息数组
        $alertInfoArray = [
            1 => "请选择您想要上传的学校信息文件，文件以‘ts_schools-学校信息.xlsx’命名",
            2 => "请选择您刚刚上传过的学校班级信息文件，包含年级信息，文件以‘ts_school_classes-学校年级班级信息.xlsx’命名",
            3 => "请选择刚上传学校的教师信息文件，包含对应科目信息，文件以‘ts_teacher_subject_classes-教师班级科目信息.xlsx’命名",
            4 => "请选择学校所有学生信息文件，包含学生所对应的家长信息，文件以‘ts_school_class_students-班级学生家长信息.xlsx’命名"

        ];
        $step = t($_GET['step']);
        if(empty($step) || $step < 1 || $step > count($alertInfoArray)) {
            $step = 1;
        }
        $this->assign('alertInfo', $alertInfoArray[$step]);
        $this->assign('step',$step);
        $this->display();
	}


	// 文件上传
	/*
	 * 得到的info信息
	 * array(1) {
	 *  [0] => array(8) {
	 * 		["name"] => string(22) "schools_学校表.xlsx"
	 * 		["type"] => string(65) "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
	 * 		["size"] => int(9117)
	 * 		["key"] => string(6) "image1"
	 * 		["extension"] => string(4) "xlsx"
	 * 		["savepath"] => string(55) "F:\work_space\PHP\think/apps/excelUp/_static/excelFile/"
	 * 		["savename"] => string(18) "5271f19c40909.xlsx"
	 * 		["hash"] => string(32) "bed4113c84fec8373f52468d4cda52e1"
	 *     }
	 *  }
	 *
	 *
	 */
	public function upload() {
        $step = t($_GET['step']);
        if(!in_array($step,[1,2,3,4])){
            $this->uploadError('非法请求！！！');exit();
        }
		import('ORG.Net.UploadFile');

		//四中表才能上传
		$zhijieIn = array('ts_schools','ts_school_classes','ts_teacher_subject_classes','ts_school_class_students');

		// 实例化上传类
		$upload = new UploadFile();

		// 设置附件上传大小
		//$upload->maxSize  = 3145728 ;
		// 设置附件上传类型
		$upload->allowExts  = array('xls','xlsx');
		// 设置附件上传目录
		$upload->savePath =  APP_PATH.'/_static/excelFile/';

		if(!$upload->upload()) {// 上传错误提示错误信息
            $this->uploadError($upload->getErrorMsg(),$step);
			exit();
		}else{// 上传成功
			//上传成功 获取上传文件

			//文件上传信息
			$info = $upload->getUploadFileInfo();
			//上传文件数量
			$fileCount = count($info);
			for ($i = 0; $i < $fileCount; $i++) {
				$savename=$info[$i]['savename'];
				$file = APP_PATH.'/_static/excelFile/'.$savename;
				$filename = $info[$i]['name'];
				$tableName = explode('-', $filename);
				if($tableName[0] == $zhijieIn[$step-1]) {
					$this->stepControl($file, $tableName[0],$step);
				}else{

                    if(!in_array($tableName[0],$zhijieIn)) {
                        $message = "请使用规定的文件名！！！!";
                    }else {
                        $message = "您上传的不是当前需要上传文件，请核对后再上传！！！";
                    }
                    $this->uploadError($message,$step);
                    exit();
				}

			}

		}

	}

    public function stepControl($file,$table,$step) {
        $result = null;
        switch($step)
        {
            case 1:
                $result = $this->uploadSchool($file,$table);
                break;
            case 2:
                $result = $this->uploadClasses($file,$table);
                break;
            case 3:
                $result = $this->uploadTeacher($file,$table);
                break;
            case 4:
                $result = $this->uploadStudent($file,$table);
                break;
        }
        if(!is_null($result)) {

            if(is_null($result['error'])) {
                if($result['status']){
                    $fileCount = $result['fileCount'];
                    $this->successInfo($fileCount,$result['message'],$step+1);exit;
                }else{
                    $this->uploadError("有重复项或数据格式不正确！！！！",$step);exit;
                }
            } else {
                $this->uploadError($result['error'],$step);exit;
            }


        }else {
            $this->uploadError("非法操作！！！！",$step);exit;
        }
    }

    public function uploadStudent($filetmpname,$table) {
        $tableClassStudent = $table;
        $tableUser = "ts_user";
        $tableStaff = "ts_students_join_users";

        $excelField = [
            'school_id',
            'grade_id',
            'class_id',
            'student_profile_no',
            'student_uname',
            'student_sex',
            'student_password',
            'student_email',
            'parent_uname',
            'parent_login',
            'parent_email',
            'parent_password',
            'parent_sex',
            'location',
        ];





        import('excelUp.PHPExcel.IOFactory');
        $objPHPExcel = PHPExcel_IOFactory::load($filetmpname);

        /**读取excel文件中的第一个工作表*/
        $currentSheet = $objPHPExcel->getActiveSheet();
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        //循环读取每个单元格的内容。注意行从1开始，列从A开始,基于从第三行开始
        //field字段是指第二行的，2.$rowIndex;
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
            for($colIndex='A',$i=0;$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                //$fieldAddr = $colIndex.'2';
                $cell = $currentSheet->getCell($addr)->getValue();
                //$field = $currentSheet->getCell($fieldAddr)->getValue();
                $field = $excelField[$i++];
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();
                else {
                    $cell = strval($cell);
                }

                $data[$rowIndex-2][$field] = $cell;
            }
        }

        $flag = true;
        $staffModelArray = $classStudentModelArray = $studentModelArray = $parentModelArray = array();

        if(is_array($data)) {
            $error = null;
            foreach($data as $dataList){
                //抽取家长和学生对于关系
                $staffModel['student_id'] = $dataList['student_profile_no'];
                $staffModel['staff_id'] = $dataList['parent_login'];

                //抽取班级学生信息
                $classStudentModel['school_id'] = $dataList['school_id'];
                $classStudentModel['grade_id'] = $dataList['grade_id'];
                $classStudentModel['class_id'] = $dataList['class_id'];
                $classStudentModel['login'] = $dataList['student_profile_no'];
                $classStudentModel['school_period_id'] = '01';


                //抽取家长user字段
                $parentModel['uname'] = $dataList['parent_uname'];
                $parentModel['login'] = $dataList['parent_login'];
                $login_salt = rand(11111, 99999);
                $parentModel['login_salt'] = $login_salt;
                $parentModel['password'] = md5(md5($dataList['parent_password']).$login_salt);
                $parentModel['ctime'] = time();
                $parentModel['email'] = $dataList['parent_email'];
                $parentModel['profile_no'] = $dataList['student_profile_no'];
                $parentModel['sex'] = $dataList['parent_sex'];
                $parentModel['location'] = $dataList['location'];
                $parentModel['identity'] = $parentModel['is_audit'] = $parentModel['is_active'] = $parentModel['is_init'] = '1';
                $parentModel['school_id'] = $dataList['school_id'];
                $parentModel['profile_id'] = '2';
                //抽取学生user字段
                $userModel['uname'] = $dataList['student_uname'];
                $userModel['login'] = $dataList['student_profile_no'];
                $login_salt = rand(11111, 99999);
                $userModel['login_salt'] = $login_salt;
                $userModel['password'] = md5(md5($dataList['student_password']).$login_salt);
                $userModel['ctime'] = time();
                $userModel['email'] = $dataList['student_email'];
                $userModel['profile_no'] = $dataList['student_profile_no'];
                $userModel['sex'] = $dataList['student_sex'];
                $userModel['location'] = $dataList['location'];
                $userModel['identity'] = $userModel['is_audit'] = $userModel['is_active'] = $userModel['is_init'] = '1';
                $userModel['school_id'] = $dataList['school_id'];
                $userModel['profile_id'] = '1';

                $upModel = M()->table($tableUser);
                if(!($upModel->add($parentModel))) {
                    $flag = false;break;
                }
                $usModel = M()->table($tableUser);
                if(!($usModel->add($userModel))) {
                    $flag = false;break;
                }

                $csModel = M()->table($tableClassStudent);
                if(!($csModel->add($classStudentModel))) {
                    $flag = false;break;
                }
                $sModel = M()->table($tableStaff);
                if(!($sModel->add($staffModel))) {
                    $flag = false;break;
                }



                $staffModelArray[] = $staffModel;
                $classStudentModelArray[] = $classStudentModel;
                $studentModelArray[] = $userModel;
                $parentModelArray[] = $parentModel;
            }
        } else {
            $flag = false;
            $error = "您上传的是空文件！！！";
        }


        $result = [
            'status' => $flag,
            'message' => [
                [
                    'table' => $tableUser,
                    'count' =>  count($studentModelArray)
                ],
                [
                    'table' => $tableUser,
                    'count' =>  count($parentModelArray)
                ],
                [
                    'table' => $tableStaff,
                    'count' => count($staffModelArray)
                ],
                [
                    'table' => $tableClassStudent,
                    'count' => count($classStudentModelArray)
                ]

            ],
            'fileCount' => count($data),
            'error' => $error
        ];
        return $result;
    }

    public function uploadTeacher($filetmpname,$table) {
        $tableSubject = $table;
        $tableUser = "ts_user";

        $excelField = [
            'uname',
            'login',
            'email',
            'password',
            'profile_no',
            'sex',
            'location',
            'school_id',
            'class_id',
            'subject_type'
        ];



        import('excelUp.PHPExcel.IOFactory');
        $objPHPExcel = PHPExcel_IOFactory::load($filetmpname);

        /**读取excel文件中的第一个工作表*/
        $currentSheet = $objPHPExcel->getActiveSheet();
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        //循环读取每个单元格的内容。注意行从1开始，列从A开始,基于从第三行开始
        //field字段是指第二行的，2.$rowIndex;
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
            for($colIndex='A',$i=0;$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                //$fieldAddr = $colIndex.'2';
                $cell = $currentSheet->getCell($addr)->getValue();
                //$field = $currentSheet->getCell($fieldAddr)->getValue();
                $field = $excelField[$i++];
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();
                else {
                    $cell = strval($cell);
                }

                $data[$rowIndex-2][$field] = $cell;
            }
        }

        $flag = true;
        $subjectModelArray= array();
        $userModelArray = array();
        if(is_array($data)) {
            $error = null;
            foreach($data as $dataList){
                //抽取教师user字段
                $userModel['login'] = $dataList['login'];
                $login_salt = rand(11111, 99999);
                $userModel['login_salt'] = $login_salt;
                $userModel['password'] = md5(md5($dataList['password']).$login_salt);
                $userModel['ctime'] = time();
                $userModel['uname'] = $dataList['uname'];
                $userModel['email'] = $dataList['email'];
                $userModel['profile_no'] = $dataList['profile_no'];
                $userModel['sex'] = $dataList['sex'];
                $userModel['location'] = $dataList['location'];
                $userModel['school_id'] = $dataList['school_id'];
                $userModel['profile_id'] = '3';
                $userModel['identity'] = $userModel['is_audit'] = $userModel['is_active'] = $userModel['is_init'] = '1';

                $subjectModel['login'] = $dataList['login'];
                $subjectModel['class_id'] = $dataList['class_id'];
                $subjectModel['subject_type'] = $dataList['subject_type'];
                $subjectModel['school_id'] = $dataList['school_id'];
                $subjectModel['school_period_id'] = '01';
                $subjectModelArray[] = $subjectModel;
                $userModelArray[] = $userModel;
            }

            $userModelArray = $this->deleteSameKey("login",$userModelArray);

            //插入到class表中
            foreach($subjectModelArray as $model){

                $cModel = M()->table($tableSubject);
                if(!($cModel->add($model))) {
                    $flag = false;break;
                }
            }

            //插入到grade中去
            foreach($userModelArray as $model) {
                $gModel = M()->table($tableUser);
                if(!($gModel->add($model))) {
                    $flag = false;break;
                }
            }

        } else {
            $flag = false;
            $error = "您上传的是空文件！！！";
        }


        $result = [
            'status' => $flag,
            'message' => [
                [
                    'table' => $tableUser,
                    'count' =>  count($userModelArray)
                ],
                [
                    'table' => $tableSubject,
                    'count' => count($subjectModelArray)
                ]

            ],
            'fileCount' => count($data),
            'error' => $error
        ];
        return $result;
    }
    public function uploadClasses($filetmpname,$table){

        $tableClass = $table;
        $tableGrade = "ts_school_gradelevels";

        $classExcelField = [
            'school_id',
            'grade_id',
            'short_name',
            'title',
            'next_grade_id',
            'sort_order',
            'class_id',
            'class_name',
            'school_period_id'
        ];


        import('excelUp.PHPExcel.IOFactory');
        $objPHPExcel = PHPExcel_IOFactory::load($filetmpname);

        /**读取excel文件中的第一个工作表*/
        $currentSheet = $objPHPExcel->getActiveSheet();
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        //循环读取每个单元格的内容。注意行从1开始，列从A开始,基于从第三行开始
        //field字段是指第二行的，2.$rowIndex;
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
            for($colIndex='A',$i=0;$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                //$fieldAddr = $colIndex.'2';
                $cell = $currentSheet->getCell($addr)->getValue();
                //$field = $currentSheet->getCell($fieldAddr)->getValue();
                $field = $classExcelField[$i++];
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();
                else {
                    $cell = strval($cell);
                }

                $data[$rowIndex-2][$field] = $cell;
            }
        }

        $flag = true;
        if(is_array($data)) {
            $error = null;
            foreach($data as $dataList){
                $gradeModel['school_id'] = $classModel['school_id'] = $dataList['school_id'];
                $gradeModel['grade_id'] = $classModel['grade_id'] = $dataList['grade_id'];
                $gradeModel['short_name'] = $dataList['short_name'];
                $gradeModel['title'] = $dataList['title'];
                $gradeModel['next_grade_id'] = $dataList['next_grade_id'];
                $gradeModel['sort_order'] = $dataList['sort_order'];
                $classModel['class_id'] = $dataList['class_id'];
                $classModel['class_name'] = $dataList['class_name'];
                $classModel['school_period_id'] = $dataList['school_period_id'];
                $gradeModelArray[] = $gradeModel;
                $classModelArray[] = $classModel;
            }

            $gradeModelArray = $this->deleteSameKey("grade_id",$gradeModelArray);

            //插入到class表中
            foreach($classModelArray as $model){

                $cModel = M()->table($tableClass);
                if(!($cModel->add($model))) {
                    $flag = false;break;
                }
            }

            //插入到grade中去
            foreach($gradeModelArray as $model) {
                $gModel = M()->table($tableGrade);
                if(!($gModel->add($model))) {
                    $flag = false;break;
                }
            }


        } else {
            $flag = false;
            $error = "您上传的是空文件！！！";
        }


        $result = [
            'status' => $flag,
            'message' => [
                [
                    'table' => $tableClass,
                    'count' =>  count($classModelArray)
                ],
                [
                    'table' => $tableGrade,
                    'count' => count($gradeModelArray)
                ]

            ],
            'fileCount' => count($data),
            'error' => $error
        ];
        return $result;

    }

    /*
     * 此方法合并$key不同的数组，将$field相同的数组删除！
     */
    private function deleteSameKey($field,$dataArray) {
        $arrayCount = count($dataArray);
        for($i= $arrayCount -1;$i > 0 ;$i--) {
            if($dataArray[$i][$field] == $dataArray[$i-1][$field]) {
                unset($dataArray[$i]);
            }
        }
        return $dataArray;

    }

    public function uploadSchool($filetmpname,$table){

        $schoolField = [
            'school_id',
            'syear',
            'title',
            'address',
            'province',
            'city',
            'area',
            'www_address',
            'phone',
            'postcode',
            'e_mail'
        ];

        import('excelUp.PHPExcel.IOFactory');
        $objPHPExcel = PHPExcel_IOFactory::load($filetmpname);

        /**读取excel文件中的第一个工作表*/
        $currentSheet = $objPHPExcel->getActiveSheet();
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        $flag = true;
        //循环读取每个单元格的内容。注意行从1开始，列从A开始,基于从第三行开始
        //field字段是指第二行的，2.$rowIndex;
        for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
            for($colIndex='A',$i=0;$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                //$fieldAddr = $colIndex.'2';
                $cell = $currentSheet->getCell($addr)->getValue();
                //$field = $currentSheet->getCell($fieldAddr)->getValue();
                $field = $schoolField[$i++];
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();
                else {
                    $cell = strval($cell);
                }

                $data[$field] = $cell;
            }

            $m = M()->table($table);
            if($m->add($data)) {
            }else {
                $flag = false;
                break;
            }

        }
        $rowIndex--;
        if(is_array($data)) {
            $error = null;
        } else {
            $error = "您上传的是空文件！！！";
        }
        $result = [
            'status' => $flag,
            'message' => [
                [
                    'table' => $table,
                    'count' =>$rowIndex-1
                ],

            ],
            'fileCount' => $allRow -1,
            'error' => $error
        ];
        return $result;

    }


    private function uploadError($msg,$step = 1,$url = 'excelUp/SchoolInfo/index') {
        $this->assign('jumpUrl',U($url,array('step'=>$step)));
        $this->assign('msg',$msg);
        $this->assign('waitSecond',"2");
        $this->display('uploadError');
    }

    private function successInfo($fileCount, $msg,$step = 1,$url = 'excelUp/SchoolInfo/index') {
        $this->assign('jumpUrl',U($url,array('step'=>$step)));
        $this->assign('msg',$msg);
        $this->assign('fileCount',$fileCount);
        $this->assign('waitSecond',"10");
        $this->display('successInfo');
    }



    public function uploaduser($filetmpname,$table){

		import('excelUp.PHPExcel.IOFactory');
		$objPHPExcel = PHPExcel_IOFactory::load($filetmpname);

		/**读取excel文件中的第一个工作表*/
		$currentSheet = $objPHPExcel->getActiveSheet();
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();

		echo '============================================';
		dump($allColumn);
		dump($allRow);
		
		
		//循环读取每个单元格的内容。注意行从1开始，列从A开始,基于从第三行开始
		//field字段是指第二行的，2.$rowIndex;
		for($rowIndex=3;$rowIndex<=$allRow;$rowIndex++){
			for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
				$addr = $colIndex.$rowIndex;
				$fieldAddr = $colIndex.'2';
				$cell = $currentSheet->getCell($addr)->getValue();
				$field = $currentSheet->getCell($fieldAddr)->getValue();
				if($cell instanceof PHPExcel_RichText)  { //富文本转换字符串
					$cell = $cell->__toString();
				}  else {
					$cell = strval($cell);
				} 
					
					
				
				if($field == 'password'){
					$login_salt = rand(11111, 99999);
					$data['login_salt'] = $login_salt;
					$data['password'] = md5(md5($cell).$login_salt);
					$data['ctime'] = time();
				}else {
					
					$data[$field] = $cell;
					//echo '-----------'.$field.'----'.$cell.'-------------------';

				}
			}
			//dump($data);
				$m = M()->table($table);
				if($m->add($data)) {
					dump(M()->getLastSql());
					//die("添加成功");
					//$this->success('添加成功','__URL__/index');
					echo '+++++++++++++++++++++++++++++++++'.($rowIndex-3).'+++++++++++++++++++++++++++++++++++++';

				}else {
					dump(M()->getLastSql());
					//die("没有添加数据,有重复项或数据格式不正确");
					//$this->error("没有添加数据,有重复项或数据格式不正确");
					echo "-----------没有添加数据,有重复项或数据格式不正确--------------------------";
				}

		}



		}






}