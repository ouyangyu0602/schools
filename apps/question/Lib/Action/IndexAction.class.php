<?php
/**
 * 课表控制器
 * @author
 *
 */
class IndexAction extends Action {

	private  $flag = "0";


	/**
	 *
	 * @return void
	 */
	public function index() {
		

		$this->display();
	}
	/*获得学校id，及title  */
	public  function questionImport()
	{

        //die();

        $this->display();
	}


    public function test() {
        $this->display();
    }
	
}