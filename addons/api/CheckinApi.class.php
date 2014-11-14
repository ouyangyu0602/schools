<?php
/**
 * 频道应用API接口
 * @author zivss guolee226@gmail.com
 * @version  TS3.0
 */
class CheckinApi extends Api
{
	/**
	 * 获取所有频道分类
	 * @return json 所有频道分类
	 */
	public function get_check_info(){

		$uid = $this->mid;

		$data = model( 'Cache' )->get('check_info_'.$uid.'_'.date('Ymd'));
		
		if ( !$data ){
		
			$map['uid'] = $uid;
		
			$map['ctime'] = array ( 'gt' , strtotime( date('Ymd') ) );
		
			$res = D('check_info')->where($map)->find();
		
			//是否签到
			$data['ischeck'] = $res ? true : false;

			$checkinfo = D('check_info')->where('uid='.$uid)->order('ctime desc')->limit(1)->find();
			if ( $checkinfo ){
				if ( $checkinfo['ctime'] > (strtotime( date('Ymd') )-86400 ) ){
					$data['con_num'] = $checkinfo['con_num'];
				} else {
					$data['con_num'] = 0;
				}
				$data['total_num'] = $checkinfo['total_num'];
			} else {
				$data['con_num'] = 0;
				$data['total_num'] = 0;
			}
			$data['day'] = date('m.d');
			model( 'Cache' )->set('check_info_'.$uid.'_'.date('Ymd') , $data );
		}

		return $data;
	}

	/**
	 * 获取指定分类下的微博
	 * @return json 指定分类下的微博
	 */
	public function checkin(){

		$uid = $this->mid;

    $map['ctime'] = array ( 'gt' , strtotime( date('Ymd') ) );
        
    $map['uid'] = $uid;
        
    $ischeck = D('check_info')->where($map)->find();
    //未签到
    if ( !$ischeck ){  	
      //清理缓存
      model( 'Cache' )->set('check_info_'.$uid.'_'.date('Ymd') , null);
      
      $map['ctime'] = array( 'lt' , strtotime( date('Ymd') ) );
      $last = D('check_info')->where($map)->order('ctime desc')->find();
      $data['uid'] = $uid;
      $data['ctime'] = $_SERVER['REQUEST_TIME'];
      //是否有签到记录
      if ( $last ){
         //是否是连续签到
         if ( $last['ctime'] > ( strtotime( date('Ymd') ) - 86400 ) ){
            $data['con_num'] = $last['con_num'] + 1;
         } else {
            $data['con_num'] = 1;
         }
         $data['total_num'] = $last['total_num'] + 1;
      } else {
         $data['con_num'] = 1;
         $data['total_num'] = 1;
      }

      if ( D('check_info')->add($data) ){
         //更新连续签到和累计签到的数据
         $connum = D('user_data')->where('uid='.$uid." and `key`='check_connum'")->find();
         if ( $connum ){
            $connum = D('check_info')->where('uid='.$uid)->getField('max(con_num)');
            D('user_data')->setField('value' , $connum , "`key`='check_connum' and uid=".$uid);
            D('user_data')->setField('value' , $data['total_num'] , "`key`='check_totalnum' and uid=".$uid);
            
         } else {
            $connumdata['uid'] = $uid;
            $connumdata['value'] = $data['con_num'];
            $connumdata['key'] = 'check_connum';
            D('user_data')->add($connumdata);
            
            $totalnumdata['uid'] = $uid;
            $totalnumdata['value'] = $data['total_num'];
            $totalnumdata['key'] = 'check_totalnum';
            D('user_data')->add($totalnumdata);
         }
      }
    }
    return $this->get_check_info();
	}
}