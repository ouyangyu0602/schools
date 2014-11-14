<?php
include SITE_PATH.'/apps/gift/Common/common.php';
//礼物接口
class GiftApi extends Api{
	
	//查看礼物
	public function showAll(){
        $result = M('Gift')->where(array('status'=>'1'))->findAll();
        foreach($result as $k=>$v){
        	unset($result[$k]['categoryId']);
        }
		return $result;
	}
	//查看礼物
	public function show(){
		if(!intval($this->data['id'])){
			return 0;
		}
        $result = M('Gift')->where(array('status'=>'1','id'=>intval($this->data['id'])))->find();
        unset($result['categoryId']);
		return $result;
	}
	//赠送礼物
	public function sendGift(){
        $toUserId = trim(t($this->data['uids']),',');
        //获取附加信息
		$sendInfo['sendInfo'] = t($this->data['sendInfo']);
		//获取发送方式
		$sendInfo['sendWay']  = ($this->data['sendWay'])?intval($this->data['sendWay']):1;
        $giftId  =  intval($this->data['giftId']);
        $giftInfo = M('Gift')->where('id='.$giftId)->find();
		if(empty($toUserId) || empty($giftId) || empty($this->mid) ){
			return 0;
		}else{
			$usergift = D('UserGift','gift');
			$usergift->setGift(D('Gift','gift'));
			$result = $usergift->sendGift($toUserId,$this->mid,$sendInfo,$giftInfo);
			return 1;
		}
	}
}