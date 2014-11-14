<?php
// 卡号导入控制器
class KaAction extends Action {
	
	public $module_name = "卡号导入";
	public $obj;
	
	public function _initialize(){
	    
		$this->obj = D("jihuoma");
		$this->assign("module_name",$this->module_name);
	 	
	}
	
    public function index(){
		
		
    }
	
	//格式案例文件下载
	public function xiazai(){
		$file="./Public/txt/xiazai/jihuoma.txt";
		header('Content-Description:File Transfer');
		header('Content-Type:application/octet-stream');
		header('Content-Disposition:attachment; filename="卡号.txt"');//确保浏览器弹出对话框，提示下载还是保存
		header('Content-Transfer-Encoding:binary');
		header('Expires:0');
		header('Cache-Control:must-revalidate');
        header('Pragma:public');
		header('Content-Length: ' . filesize($file));//返回时文件的大小
		ob_clean();//丢弃输出缓冲区中的内容
		flush();
		readfile($file);//
		exit;
				
		
	}
	//展示导入模板
	public function daoka(){
		
		
		$this->display();
		
		
	}
	
	//导入卡号控制器
	public function doka(){//上传文件最大是2M，要想上传更大文件，请修改php配置文件中upload_max_filesize=2m
		
		    
		if($_GET['page']<=1)
		{   
		    
			import("ORG.NET.UploadFile");
			$upload = new UploadFile();//实例化上传类
		    $upload->savePath = './Public/txt/';//设置附件上传目录
		    $upload->saveRule = time();
			if(!$upload->upload())
			{   
				// 上传错误，提示错误信息
				$this->error($upload->getErrorMsg());//上传失败，文件上传大小不符
				
			}
			else
			{  
			    //上传成功 获取上传文件
			      $info = $upload->getUploadFileInfo();
				
				  $_SESSION['upload']=$info;//将上传文件的路径存在session中，确保跳页找到路径
			}
		}
			//读取txt文档
			
			$mulu=APP_PATH.'Public/txt/';
			$file=fopen($mulu.$_SESSION['upload'][0]['savename'],r);//打开txt文件,fopen是不能被输出的
		   //echo $mulu.$_SESSION['upload'][0]['savename'];die; 
            $page    = empty($_GET['page'])? 1 :intval($_GET['page']);
			

			$nowpage = $page-1;
			$meici   = 10000;
			$num     = $meici*$nowpage;
			$size    = 30;
		    $flag    = fseek($file,$size*$num,SEEK_SET);
			
		   $count_hang=$size*$meici;//一共过少行
           $count_zijie = filesize($mulu.$_SESSION['upload'][0]['savename']);//返回文件字节的大小数
			 //$allpage = filesize($mulu.$_SESSION['upload'][0]['savename'])/($size*$meici);//求总页数
           $allpage = $count_zijie/$count_hang;
			  if($flag==0)
			  {
				 $this->obj = D("jihuoma");
				
				// $fread=fread($file,$size*$meici);
				
				 $sqlARR=explode("\n",trim(fread($file,$count_hang)));//trim()去空格；explode()将字符串分割数组；fread()读取文件
				 
				 $sqlARRChild=array();
				   //echo "<pre>";
				 
				     for($i=0;$i<count($sqlARR);$i++)
					 {
						 
						 $sqlARRChild[$i]=explode("\t",$sqlARR[$i]);
				     } 

					 for($i=0;$i<count($sqlARRChild);$i++)
					 {   
					      
					     $map[slma]=$sqlARRChild[$i][0];//输出的全是卡号
						 $map[xzma]=$sqlARRChild[$i][1];//输出的全是密码
						 
						  $ka =$this->obj->add($map);
					 } 

					 if($ka)//判断是否添加成功
					 {  
						 
						if($page<$allpage)
						{    
						    
						    echo $page++;
							fclose($file);
                            
						    echo $this->success("当前已处理".$num."条","?page={$page}");
							
						    //unlink('./Public/txt/'.$_SESSION['upload'][0]['savename']);
						}
						else
						{    
						    
							$this->success("","__URL__/admin.php");
						}
                      //$this->success("","__URL__/admin.php/Ka/daoka");
					 }

			  }
									
			
		
	}



}