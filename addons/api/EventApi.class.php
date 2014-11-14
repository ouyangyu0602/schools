<?php
include_once SITE_PATH.'/apps/event/Lib/Model/BaseModel.class.php';
//活动接口
class EventApi extends Api{
    
    //查看所有分类
    public function type(){
        return D('EventType','event')->getType();
    }
    //查看所有活动
    public function showAll(){
        return D('Event','event')->findall();
    }
	//发起活动
	public function createEvent(){
        $map['title']      = t($this->data['title']);
        $map['address']    = t($this->data['address']);
        $map['limitCount'] = intval(t( $this->data['limitCount'] ));
        $map['type']       = intval($this->data['type']);
      	$map['explain']    = h($this->data['explain']);
        $map['contact']    = t($this->data['contact']);
        $map['deadline']   = $this->_paramDate($this->data['deadline'] );
        $map['sTime']      = $this->_paramDate($this->data['sTime']);
        $map['eTime']      = $this->_paramDate($this->data['eTime']);
        $map['uid']        = $this->mid;
		if(!$map['title'] || !$map['address'] || !$map['type'] || !$map['deadline'] || !$map['sTime'] || !$map['eTime']){
			return -1;exit;
		}
        //处理省份，市，区
        list( $opts['province'],$opts['city'],$opts['area'] ) = explode(" ",$this->data['city']);
		//得到上传的图片
        $config     =   $this->getConfig();
 		$options['userId']		=	$this->mid;
		$options['max_size']    =   $config['photo_max_size'];
		$options['allow_exts']	=	$config['photo_file_ext'];
        $cover	=	X('Xattach')->upload('event',$options);
        //处理选项
        $opts['cost']        = intval( $this->data['cost'] );
        $opts['costExplain'] = t( $this->data['costExplain'] );
        $friend              = isset( $this->data['friend'] )?1:0;
        $allow               = isset( $this->data['allow'] )?1:0;
        $opts['opts']        = array( 'friend'=>$friend,'allow'=>$allow );
		$result = D('Event','event')->doAddEvent($map,$opts,$cover);
		return (int)$result;
	}
	private function setTitle($input)
    {
    	global $ts;
    	$ts['site']['page_title'] = $input;
	}
	//参与活动
	public function joinEvent(){
        $result['id']   = intval( $this->data['id'] );
        $result['uid']  = $this->mid;
        $allow	= 0; //intval( $this->data['allow'] );
        $result['action'] = 'joinIn'; //t( $this->data['action'] );
        $res = D('Event','event')->doAddUser( $result,$allow );
        if($res == -2){
            return 1;
        }else{
            return 0;
        }
	}
    //查看活动
    public function showEvent() {
        if(empty($this->data['id']) && empty($this->data['uid'])){
            return 0;
        }else{
            $res = D('Event','event')->getEventContent($this->data['id'],$this->data['uid']);
            if($res == false){
                return 0;
            }else{
                return $res;
            }
        }
    }
    private function getConfig(){
    	$config = model('Xdata')->lget('event');
		$config['limitpage']    || $config['limitpage'] =10;
		$config['canCreate']===0 || $config['canCreat']=1;
	    ($config['credit'] > 0   || '0' === $config['credit']) || $config['credit']=100;
	    $config['credit_type']  || $config['credit_type'] ='experience';
		($config['limittime']   || $config['limittime']==='0') || $config['limittime']=10;//换算为秒
		if($key){
			return $config[$key];
		}else{
			return $config;
		}
    }
	private function _paramDate( $date ) {
        //$date_list = implode('-',explode(' ',$date));
		return strtotime($date);
		//list( $year,$month,$day ) = explode( '-',$date_list[0] );
        //list( $hour,$minute,$second ) = explode( ':',$date_list[1] );
        //return mktime( $hour,$minute,$second,$month,$day,$year );
    }
}