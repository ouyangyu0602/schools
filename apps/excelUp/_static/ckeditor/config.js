/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.extraPlugins = 'eqneditor,hello,uploadimg';
    //注册插件,extraPlugins只允许出现一次，你如果之前有新增别的插件，那么用逗号分隔
	config.language = 'zh-cn';
	//config.uiColor = '#AADC6E';
};
