<?php
/**
 * 系统接口API使用的模型
 **/
class SystemApi extends Api {
	
	//全站统一收藏接口
	public function favorite(){
		if(empty($_POST['source_id']) || empty($_POST['source_table_name'])){
			$return['data'] = L('PUBLIC_RESOURCE_ERROR');
			return 0;exit();
		}
		$uid = intval($this->user_id);
		$source_table_name = $this->data['source_table_name'];
		$source_id = $this->data['source_id'];
		$source_app = $this->data['source_app'];
		$map['source_id'] = $uid;
		$map['source_app'] = $source_id;
		$map['source_table_name'] = $source_table_name;
		$map['source_id']	= $source_id;
		if( model('Collection')->addCollection($data)){
			return 1;
		}else{
			return 0;
		}
	}

	//系统统一评论接口
	public function comment(){
		$data = $this->data;
		//$data = $_POST;
    	$data['app'] 	= $data['app_name'];
    	$data['table']	= $data['table_name'];
    	
    	if($data['comment_id'] = model('Comment')->addComment($data)){
    		$return['status'] = 1 ;
    		$return['data']	= $this->parseComment($data);
 
            $oldInfo = model('Source')->getSourceInfo($data['table'], !empty($data['app_row_id']) ? $data['app_row_id'] : $data['row_id'],false,$data['app']);
    		//转发到我的微博

    		if($_POST['ifShareFeed'] == 1){
                $commentInfo  = model('Source')->getSourceInfo($data['table'],$data['row_id'],false,$data['app']);
                $oldInfo      = isset($commentInfo['sourceInfo']) ? $commentInfo['sourceInfo'] : $commentInfo; 
    			//根据评论的对象获取原来的内容
    			$s['sid'] 		= $oldInfo['source_id'];
    			$s['app_name']	= $oldInfo['app'];
    			$s['body']		= $data['content'];
    			$s['type']		= $oldInfo['source_table'];
    			$s['comment']   = $data['comment_old'];
    			model('Share')->shareFeed($s,'comment');
    		}else{//是否评论给原来作者
                if($data['comment_old'] != 0 ){

                    $commentInfo  = model('Source')->getSourceInfo($data['table'],$data['row_id'],false,$data['app']);
                    $oldInfo      = isset($commentInfo['sourceInfo']) ? $commentInfo['sourceInfo'] : $commentInfo;

                    //发表评论
                    $c['app']     = $data['app'];
                    $c['table']   = $oldInfo['source_table'];
                    $c['app_uid'] = $oldInfo['uid'];
                    $c['content'] = $data['content'];
                    $c['row_id']  = !empty($oldInfo['sourceInfo']) ? $oldInfo['sourceInfo']['source_id'] : $oldInfo['source_id'];
                    $c['client_type'] = getVisitorClient();
                    model('Comment')->addComment($c,false,false);
                }
            }
    	}else{
    		return 0;
    	}
		
		return 1;
	}


	//获取指定资源的评论列表
	function get_comments(){
		$where = "row_id ='{$this->id}' AND `table`='{$this->data['table_name']}'";
		return model('Comment')->getCommentListForApi($where,$this->since_id , $this->max_id , $this->count , $this->page);
	}

		//获取当前用户收到的评论
	function comments_to_me() {
		$where = " ( app_uid = '{$this->mid}' or to_uid = '{$this->mid}' )";
		return model('Comment')->getCommentListForApi($where,$this->since_id , $this->max_id , $this->count , $this->page,true);
	}

	//获取当前用户发出的评论
	function comments_by_me() {
		$where = " uid = '{$this->mid}' ";
		return model('Comment')->getCommentListForApi($where,$this->since_id , $this->max_id , $this->count , $this->page,true);
	}

    public function parseComment($data){
    	$data['userInfo'] = model('User')->getUserInfo($GLOBALS['ts']['uid']);
    	$data['content'] = preg_html($data['content']);
    	$data['content'] = parse_html($data['content']);
 	   //return $this->renderFile(dirname(__FILE__)."/_parseComment.html",$data);
	}
	
	//系统统一dig
	public function tips(){
		$sid = intval($this->data['sid']);
		$stable = t($this->data['stable']);
		$uid = intval($this->data['uid']);
		$type = intval($this->data['type']);

		$res = model('Tips')->doSourceExec($sid, $stable, $uid, $type);
		//$res 0 为失败   1为成功  2为已经顶过
		return $res;
	
	}
	
	//系统统一分享接口
	public function share(){
		
//			$data['sid'] = 1;
//			$data['app_name'] = 'support';
//			$data['type'] = 'support';
//			$data['comment'] = 1;
//			$data['forApi'] = 1;
//			$data['body'] = '123321111233211';
//		
		$data = $this->data;
		if(empty($data['sid'])){
			return 0;
		}
		$type = t($data['type']);	//其实这个type就是资源所在的表名
		$app  = isset($data['app_name']) ? $data['app_name']:APP_NAME;	//当前微博产生所属的应用
		$forApi = $data['forApi'] ? true : false;
		
		if(!$oldInfo = model('Source')->getSourceInfo($type,$data['sid'],$forApi,$data['app_name'])){
			$return['data'] = L('PUBLIC_INFO_SHARE_FORBIDDEN');
			return 0;
		}


		$d['content'] = isset($data['content']) ? str_replace(SITE_URL,'[SITE_URL]',$data['content']) : '';
		$d['body'] 	  = str_replace(SITE_URL,'[SITE_URL]', $data['body']);
		
		$feedType = 'repost'; //默认为普通的转发格式
		if(!empty($oldInfo['feedtype']) && !in_array($oldInfo['feedtype'],array('post','postimage','postfile'))){
			$feedType = $oldInfo['feedtype'];
		}

		$d['sourceInfo'] = !empty($oldInfo['sourceInfo']) ? $oldInfo['sourceInfo'] : $oldInfo;
		
		//TODO 判断是否发@给资源作者
		$extUid = ($from =='share' && $d['sourceInfo']['uid']!=$GLOBALS['ts']['mid']) ? $d['sourceInfo']['uid'] : null;
		
		if($res = model('Feed')->put($GLOBALS['ts']['mid'],$app,$feedType,$d,$data['sid'],$oldInfo['source_table'],$extUid)){
				
			if($data['comment'] != 0 ){
				//发表评论
    			$c['app'] 	  = $app;
    			$c['table']	  = $oldInfo['source_table'];
    			$c['app_uid'] = $oldInfo['uid'];
    			$c['content'] = !empty($d['body']) ? $d['body'] : $d['content'];
    			$c['row_id']  = !empty($oldInfo['sourceInfo']) ? $oldInfo['sourceInfo']['source_id'] : $oldInfo['source_id'];
    			$c['client_type'] = getVisitorClient();
    			$notCount = $from == "share" ? true : false;
    			model('Comment')->addComment($c,false,$notCount);
			}
			//渲染数据
			$rdata 				= $res;//渲染完后的结果
			$rdata['feed_id'] 	= $res['feed_id'];
			$rdata['app_row_id']= $data['sid'];
			$rdata['app_row_table'] = $data['type'];
			$rdata['app']		= $app;
			$return['data'] 	= $rdata;
			$return['status']	= 1;
			//被分享内容“评论统计”数+1，同时可检测出app,table,row_id 的有效性
    		D($data['type'],$data['app_name'])->setInc('repost_count', "`{$data['type']}_id`={$data['sid']}", 1);
    		if($data['curid'] != $data['sid'] && !empty($data['curid'])){
    			D($data['curtable'])->setInc('repost_count', "`{$data['curtable']}_id`={$data['curid']}", 1);
    			D($data['curtable'])->cleanCache($data['curid']);
    		}
    		
    		D($data['type'],$data['app_name'])->cleanCache($data['sid']);
			
		}else{
			return 0;
		}
		return 1;
	}
}