<?php
/**
 * 
 * @author jason
 *
 */
class AppListApi extends Api
{
	
	private function formatList($list,$usedCheck=false){
		if(!empty($list)){
			$return = $d = array();
			$map['uid'] = $this->mid;
			$map['inweb'] = 0;
			
			$usedHash = model('UserApp')->where($map)->getAsFieldArray('app_id');
			
			foreach($list as $v){
				$d['app_id'] 	= $v['app_id']; 
				$d['uid']	 	= $this->mid;
				$d['app_name']  = $v['app_alias'];  //应用别名
				$d['type']		= $v['app_name'];	//应用名称
				$d['app_icon']  = $v['icon_url'];
				$d['app_large_icon_url'] = $v['large_icon_url'];
				$d['iphone_icon']  = !empty($v['iphone_icon']) ? $v['iphone_icon'] : '';
				$d['android_icon'] = !empty($v['android_icon']) ? $v['android_icon']:'';
				$d['host_type']	= $v['host_type'];
				$d['app_link']  = $v['app_entry'];
				$d['is_used']   = in_array($d['app_id'],$usedHash) ? 1 :0;//用户是否安装
				!empty($this->data['keyword']) && $v['keyword'] = $this->data['keyword'];
				$return[] = $d; 
			}
			$list = $return;
		}
		return $list;
	}
	/**
	 * 返回某个用户的安装的应用
	 *  传入参数 
	 *  mid，当前用户ID
	 *	format 返回格式 默认为json
	 *
	 * @return json
	 * 
	 */
	public function user_app_list(){
		
		$list = model('UserApp')->getUserApp($this->mid,0);
		
		//格式化数据
		return $this->formatList($list);
	}
	
	/**
	 * 返回所有应用列表，列表按已启用未启用列表排序
	 * 传入参数：
	 *  mid: 当前登录用户	//没用
	 *  since_id: 起始应用ID
	 *  max_id: 最大应用ID
	 *  count 分页时，指定每页显示条数
	 *  page 分页时候，指定获取的页码
	 *  format  返回格式
	 */
	
	public function get_app_list(){
		$map = array();
		if(!empty($this->max_id) &&  !empty($this->since_id)){
			$map['_string'] = " app_id between '{$this->since_id}' AND '{$this->max_id}'";
		}else if(!empty($this->max_id)){
			$map['app_id'] = array('lt',$this->max_id);
		}else if(!empty($this->since_id)){
			$map['app_id'] = array('gt',$this->since_id);
		}
		$map['status'] = 1;//可选的
		$map['has_mobile'] = 1;
		!empty($this->data['keyword']) && $map['app_alias'] = array('like','%'.t($this->data['keyword']).'%');
		$start = ($this->page-1)*$this->count;
		$limit = "{$start},{$this->count}"; 
		$list = model('App')->getAppList($map,$limit);
		
		return $this->formatList($list,true);
		
	}
	
	/**
	 * 用户添加莫一个应用
	 * 传入参数：
	 * mid
	 * data['app_id']
	 *  
	 * @return int 0、1 
	 */
	public function create(){
		if(model('UserApp')->install($this->mid,$this->data['app_id'],0)){
			return 1;
		}else{
			return 0;
		}
	}
	
	/**
	 * 用户卸载某个应用
	 * 传入参数：
	 * mid
	 * data['app_id']
	 * @return int 0、1
	 */
	public function destroy(){
		if(model('UserApp')->uninstall($this->mid,$this->data['app_id'],0)){
			return 1;
		}else{
			return 0;
		}
	}
	
	/**
	 * 返回超找的应用列表结果
	 * 
	 * mid，当前登录用户UID
 	 * keyword，关键字
	 * since_id，起始应用ID
	 * max_id，最大应用ID
	 * count，分页显示时，指定每页显示条数（默认20）
	 * page，分页显示时，指定获取的页码（默认取第1页）
     *
	 */
	public function search_app(){
		return $this->get_app_list();
	}
	
}