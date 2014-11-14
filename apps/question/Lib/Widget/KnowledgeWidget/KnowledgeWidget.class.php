<?php
class KnowledgeWidget extends Widget {


	public function render($data)
	{
        $knowledgeListArray = $data['knowledgeListArray'];


        //dump($studentArray);
        $var['knowledgeListArray'] = $knowledgeListArray;
        $var['sort_order'] = $data['sort_order'];
        $var['gradeList'] = $data['gradeList'];
        $var['badge'] = $data['badge'];
        // 渲染模版
        $content = $this->renderFile(dirname(__FILE__) . "/knowledge.html", $var);
        // 输出数据
        return $content;
		// 渲染模版

	}
	



}

