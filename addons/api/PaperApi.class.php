<?php
/**
 *
 * @author jason
 *
 */

class PaperApi extends Api{

	public function savaPaper()	{

		$_REQUEST = array_merge($_GET,$_POST);

		//判断传过来的参数是否为空
		if(!empty($_REQUEST['data'])){

			//初始化模型
			$Paper = M('Evaluation_paper');

			//把传过来的json字符串转换，得到对象数组
			$dataArray = json_decode($data);

			//把对象数组转换为数组键值对形式
			$dataArray = $this->object_array($dataArray);

			//把数组存入数据库，取第一个数组，返回来的是个二维数组
			if ($Paper->create($dataArray[0])) {
				if (false !== $xuean->add()) {
					$message['message'] = '插入成功！';
					$message['code']    = '00001';
					exit( json_encode( $message ) );
				} else {
					$message['message'] = '插入失败：数据为空！';
					$message['code']    = '00000';
					exit( json_encode( $message ) );
				}
			}else {
				$message['message'] = '创建数据库连接失败！';
				$message['code']    = '00000';
				exit( json_encode( $message ) );
			}

		} else {
			$message['message'] = '插入失败：数据为空！';
			$message['code']    = '00000';
			exit( json_encode( $message ) );
		}


	}

	/*
	 * 判断传过来的时间
	 * 1、否超过当今时间：页面控制
	 * 2、开始时间大于结束时间：页面控制
	 * 3、如果传过来的数据在数据库中没有找到的时候，提示用户，没有当前
	 * 4、在传时间上，提示用户控制
	 *
	 */
	public function listPaper() {
		$_REQUEST = array_merge($_GET,$_POST);

		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['dayend'])) {
			$daybegin = $_REQUEST['daybegin'];
			$dayend = $_REQUEST['dayend'];
			dump($daybegin);
			dump($dayend);
			$Paper = M('Evaluation_paper');
			$map['examdate'] = array('between',array("'".$daybegin."'","'".$dayend."'"));
			$paperList = $Paper->where($map)->select();
				
			//			$paperList = M()->table('ts_evaluation_paper')
			//						->where('examdate between "'.$daybegin.'" and "'.$dayend.'"')
			//						->select();
			dump(M()->getLastSql());
			dump($paperList);
			exit(json_encode($paperList));
		}else {
			$message['message'] = '查找失败：考试区间时间为空！';
			$message['code']    = '00003';
			exit(json_encode($message));
		}

	}

	public function savaPaperResultDetail()	{

		$_REQUEST = array_merge($_GET,$_POST);

		//判断传过来的参数是否为空
		if(!empty($_REQUEST['answer'])){
			$answer = $_REQUEST['answer'];
				
			//把传过来的json字符串转换，得到对象数组
			$answer_obj = json_decode($answer);
			//把对象数组转换为数组键值对形式
			$answer_array = $this->object_array($answer_obj);
			$newAnswer = $answer_array['answer_list'];
				
			$model = new Model();
				
			//开始事务
			$model->startTrans();
			$flag = true ;
				
			//把数组存入数据库，取第一个数组，返回来的是个二维数组
			foreach ($newAnswer as $i => $value) {
				$newAnswer[$i]['paper_id'] = $answer_array['paper_id'];
				$newAnswer[$i]['user_id'] = $answer_array['user_id'];

				$question_id = M()->table("ts_evaluation_paper_questions pq")
				->join("ts_evaluation_question q on pq.question_id = q.question_id")
				->where("pq.paper_id = '".$answer_array['paper_id']."' and q.question_index = '".$newAnswer[$i]['question_index']."'")
				->field("q.question_id")->select();

				$newAnswer[$i]['question_id'] = $question_id[0]['question_id'];


				//初始化模型
				$Paper = M('Evaluation_paper_result_detail');

				if ($Paper->create($newAnswer[$i])) {
					if (false == $Paper->add()) {
						$flag = false;
					}
				}else {
					$flag = false;
				}
					

			}



			if($flag) {
				$model->commit();
				$message['message'] = '上传成功！';
				$message['code']    = '00000';
				exit(json_encode($message));
			} else {
				$model->rollback();
				$message['message'] = '上传成功！';
				$message['code']    = '00001';
				exit(json_encode($message));
			}
				
				


		}else {
			$message['message'] = '上传失败：传过来的数据为空！';
			$message['code']    = '00003';
			exit(json_encode($message));
		}

	}




	/*
	 * 把json转换后的数组转换为
	 * 键值对的数组
	 */
	private function object_array($array){
		if(is_object($array)){
			$array = (array)$array;
		}
		if(is_array($array)){
			foreach($array as $key=>$value){
				$array[$key] = $this->object_array($value);
			}
		}
		return $array;
	}

	public function test_resultPaper() {
		//$_REQUEST = array_merge($_GET,$_POST);
		$answer = '{"paper_id":"0001","user_id":"0010","answer_list":[{"question_index":"1","user_answer":"C"},{"question_index":"2","user_answer":"B"}]}';
		//$answer = $_REQUEST['answer'];
		$answer_obj = json_decode($answer);
		dump($answer_obj);
		//dump($answer_obj->answer_list[0]->index);


		echo "$answer_obj->paper_id";
		$answer_array = $this->object_array($answer_obj);
		dump($answer_array);

		echo "***********************************************************";

		dump($answer_array['paper_id']);
		dump($answer_array['user_id']);

		dump($answer_array['answer_list']);


		echo $answer_array['answer_list'][0]['index'];


		echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";


		$newAnswer = $answer_array['answer_list'];
		//		foreach ($newAnswer as ) {
		//			dump($new);
		//			dump($new['answer']);
		//			echo $new['answer'];
		//			$new['paper_id'] = $answer_array['paper_id'];
		//			$new['user_id'] = $answer_array['user_id'];
		//			dump($new);
		//		}
		//		$newAnswer[0]['paper_id'] = $answer_array['paper_id'];
		//		$newAnswer[0]['user_id'] = $answer_array['user_id'];


		$success = 0;
		$error = 0;
		foreach ($newAnswer as $i => $value) {

			$newAnswer[$i]['paper_id'] = $answer_array['paper_id'];
			$newAnswer[$i]['user_id'] = $answer_array['user_id'];
			$newAnswer[$i]['question_id'] = $i;
			//dump($newAnswer);
			dump($newAnswer[$i]);
			//初始化模型
			$Paper = M('Evaluation_paper_result_detail');
			if ($Paper->create($newAnswer[$i])) {
				if (false !== $Paper->add()) {
					$message['message'] = '33333插入成功！';
					$message['code']    = '00001';
					$message['index'] = $i;
				} else {
					$message['message'] = '22222插入失败：数据为空！';
					$message['code']    = '00000';
					$message['index'] = $i;
				}
			}else {
				$message['message'] = '11111创建数据库连接失败！';
				$message['code']    = '00000';
				$message['index'] = $i;
			}

			$message_list[$i] = $message;
		}


		//dump($message_list);
		//dump($newAnswer);

		exit( json_encode( $message_list) );
		echo "$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$";
	}


	public function test()	{


		//$_REQUEST = array_merge($_GET,$_POST);

		$n = M('Evaluation_xuean');
		$xuean = $n->order('createdate desc')->select();
		dump($xuean);

		$xuean_json = json_encode($xuean);

		echo "JSON字符串！";
		dump($xuean_json);

		echo "取得一串！";

		$str = '{"xuean_id":"2605746849218","section_id":"1\u8bfe\u65f6","contextbook_type":"1\u7c7b\u578b","subject_type":"1\u6570\u5b66","grade_type":"\u4e00\u5e74\u7ea7","xuean_title":"\u4e00\u5e74\u7ea7","xuean_content":"<p>\u4f5c\u4e1a\u3002\u3002\u3002\u3002\u3002<\/p>\r\n","createdby":"lmy@163.com","createdate":"2013-10-18 14:11:08","modifiedby":null,"modifydate":null,"reserve1":null,"reserve2":null,"reserve3":null,"reserve4":null,"reserve5":null}';

		$str_obj = json_decode($str);
		dump($str_obj);
		echo "$str_obj->id";
		$str_array = $this->object_array($str_obj);
		dump($str_array);


		if ($n->create($str_array)) {
			if (false !== $n->add()) {
				$message['message'] = '插入成功！';
				$message['code']    = '00001';
				exit( json_encode( $message ) );
			} else {
				$message['message'] = '插入失败：数据为空！';
				$message['code']    = '00000';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '创建数据库连接失败！';
			$message['code']    = '00000';
			exit( json_encode( $message ) );
		}


		//		dump($n->create($str_obj));
		//		dump($n->create($str_array));
		die("");

	}

}

?>