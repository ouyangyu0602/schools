<?php
class OauthApi extends Api{
	//获取RequestKey
	public function request_key(){
		
		//dump(md5('13451672388'));
		//dump(md5('123456789'));
		return array($this->getRequestKey());
	}

	
	//获取8位RequestKey
	private function getRequestKey(){
		
		return "THINKSNS";	//不要修改
	}

	//认证方法
	public function authorize(){
		
		
		
		if(!function_exists('mcrypt_module_open')){
			$message['message'] = '服务器错误:缺少加密扩展mcrypt';
			$message['code']    = '00000';
			exit( json_encode( $message ) );
		}

		$_REQUEST = array_merge($_GET,$_POST);
		if(!empty($_REQUEST['uid']) && !empty($_REQUEST['passwd'])){
			$message['requid'] = $_REQUEST['uid'];
			$message['reqpd'] = $_REQUEST['passwd'];
			
			//帐号、密码通过加密
			$username = desdecrypt(t($_REQUEST['uid']), $this->getRequestKey());	
			$password = desdecrypt(t($_REQUEST['passwd']), $this->getRequestKey());
			$message['login'] = $username;
			$message['password']    = $password;
			//exit(json_encode( $message ));
			
			//判断帐号类型
	    	if($this->isValidEmail($username)){
	    		$map['email'] = $username;
	    		
	    	}else{
	    		$map['login'] = $username;
	    		
	    	}
	    	//$map['profile_id'] = '2';
	    	//根据帐号获取用户信息
	    	$user = model('User')->where($map)->field('uid,email,password,login_salt,is_audit,is_active')->find();
	    	
			
			//dump($user);
			//判断密码是否正确
			if($user && (md5($password.$user['login_salt']) == $user['password'])){
//				$message['code']    = '10000';
//				exit( json_encode( $message ) );
				//如果未激活提示未激活
				if($user['is_audit']!=1 || $user['is_active']!=1){
					$message['message'] = '您的帐号尚未激活或未通过审核';
	        		$message['code']    = '00002';
	        		exit( json_encode( $message ) );
				}
				//记录token
				if( $login = D('')->table(C('DB_PREFIX').'login')->where("uid=".$user['uid']." AND type='location'")->find() ){
					$data['oauth_token']         = $login['oauth_token'];
					$data['oauth_token_secret']  = $login['oauth_token_secret'];
					$data['uid']                 = $user['uid'];
				}else{
					$data['oauth_token']         = getOAuthToken($user['uid']);
					$data['oauth_token_secret']  = getOAuthTokenSecret();
					$data['uid']                 = $user['uid'];
					$savedata['type']            = 'location';
					$savedata = array_merge($savedata,$data);
					D('')->table(C('DB_PREFIX').'login')->add($savedata);
				}
				return $data;
			}else{
				$this->verifyError();
			}
    	}else{
    		$this->verifyError();
    	}
	}

	//注销帐号，刷新token
	public function logout(){
		$_REQUEST = array_merge($_GET,$_POST);
		if(!empty($_REQUEST['uid'])){
			//帐号、密码通过加密
			$username = desdecrypt(t($_REQUEST['uid']), $this->getRequestKey());	
		}
		//判断帐号类型
    	if($this->isValidEmail($username)){
    		$map['email'] = $username;
    	}else{
    		$map['login'] = $username;
    	}
		//判断密码是否正确
		$user = model('User')->where($map)->field('uid')->find();
		if($user){
			$data['oauth_token']         = getOAuthToken($user['uid']);
			$data['oauth_token_secret']  = getOAuthTokenSecret();
			$data['uid']                 = $user['uid'];
			D('')->table(C('DB_PREFIX').'login')->where("uid=".$user['uid']." AND type='location'")->save($data);
			return 1;
		}else{
			return 0;
		}
	}

	//验证字符串是否是email
	public function isValidEmail($email) {
		return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
	}

	public function register(){
		$return = array();
		$regmodel = model('Register');
		$regdata = model('Xdata')->get('admin_Config:register');
		
		//昵称、密码、邮箱、性别，如果不正确返回错误信息
		$uname = t( $this->data['uname'] );
		$sex = intval( $this->data['sex'] );
		$password = $this->data['password'];
		$email = t( $this->data['email'] );
		
		//邮箱密码验证
		if(!$regmodel->isValidEmail($email)) {
			$msg = $regmodel->getLastError();
			$return = array('status'=>0, 'msg'=>$msg);
			return $return;
		}
		if(!$regmodel->isValidName($uname)) {
			$msg = $regmodel->getLastError();
			$return = array('status'=>0, 'msg'=>$msg);
			return $return;
		}
		if (!$regmodel->isValidPassword($password, $password)) {
			$msg = $regmodel->getLastError();
			$return = array('status'=>0, 'msg'=>$msg);
			return $return;
		}
		
		$login_salt = rand(11111, 99999);
		
		//如果需要激活，提示激活后才能使用
		//如果需要审核，给用户提示审核后才能登录
		
		$map['uname'] = $uname;
		$map['sex'] = $sex;
		$map['login_salt'] = $login_salt;
		$map['password'] = md5(md5($password).$login_salt);
		$map['login'] = $map['email'] = $email;
		$map['ctime'] = time();
		// 审核状态： 0-需要审核；1-通过审核
		$map['is_audit'] = $regdata['register_audit'] ? 0 : 1;
		$map['is_active'] = $regdata['need_active'] ? 0 : 1;
		$map['first_letter'] = getFirstLetter($uname);
		//如果包含中文将中文翻译成拼音
		if ( preg_match('/[\x7f-\xff]+/', $map['uname'] ) ){
			//昵称和呢称拼音保存到搜索字段
			$map['search_key'] = $map['uname'].' '.model('PinYin')->Pinyin( $map['uname'] );
		} else {
			$map['search_key'] = $map['uname'];
		}
		$uid = model('User')->add($map);
		if ( $uid ){
			//第三方登录数据写入
			if(isset($this->data['type'])){
				$other['oauth_token']         = addslashes($this->data['access_token']);
				$other['oauth_token_secret']  = addslashes($this->data['refresh_token']);
				$other['type']                = addslashes($this->data['type']);
				$other['type_uid']            = addslashes($this->data['type_uid']);
				$other['uid']                 = $uid;
				M('login')->add($other);
			}
			$return = array('status'=>1, 'msg'=>'注册成功');
			return $return;
		} else {
			$return = array('status'=>0, 'msg'=>'注册失败');
			return $return;
		}
	}

	public function setDeviceToken(){
		$token = t ( $this->data['token'] );
		
		$uid = D('mobile_token')->where('uid='.intval($_REQUEST['uid'])." and token='".$token."'")->getField('uid');
		$data['mtime'] = time();
		$data['token'] = $token;
		$data['device_type'] = t ( $this->data['device_type'] );
		if ( $uid ){
			$data['uid'] = $uid;
			$res = D('mobile_token')->add($data);
		} else {
			$res = D('mobile_token')->where('uid='.$uid)->save($data);
		}
		return $res ? 1 : 0;
	}

	public function getOtherLoginInfo(){
		$type = addslashes($this->data['type']);
		$type_uid = addslashes($this->data['type_uid']);
		$access_token = addslashes($this->data['access_token']);
		$refresh_token = addslashes($this->data['refresh_token']);
		$expire = intval($this->data['expire_in']);
		if(!empty($type) && !empty($type_uid)){
			$user = M('login')->where("type_uid='{$type_uid}' AND type='{$type}'")->find();
			if($user && $user['uid']>0){
				if( $login = M('login')->where("uid=".$user['uid']." AND type='location'")->find() ){
					$data['oauth_token']         = $login['oauth_token'];
					$data['oauth_token_secret']  = $login['oauth_token_secret'];
					$data['uid']                 = $login['uid'];
				}else{
					$data['oauth_token']         = getOAuthToken($user['uid']);
					$data['oauth_token_secret']  = getOAuthTokenSecret();
					$data['uid']                 = $user['uid'];
					$savedata['type']            = 'location';
					$savedata = array_merge($savedata,$data);
					$result = M('login')->add($savedata);
					if(!$result)
						return -3;
				}
				return $data; 
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
}