
CKEDITOR.plugins.add('uploadimg', {
    requires: ['dialog'],
    init: function(a){
        a.addCommand('uploadimg', new CKEDITOR.dialogCommand('uploadimg'));
        a.ui.addButton('uploadimg', {
            label: "uploadimg",
            command: 'uploadimg',
            icon: this.path + 'images/uploadimg.png'
/*            label:当鼠标移动到按钮上面是提示此文本信息。
                className:样式名，默认是'cke_button_' + command
                click：按钮的单击事件出发的方法。如果没有实现单击事件，则执行指定key的命令。
                command：按钮单击默认执行的命令。*/
    });
        CKEDITOR.dialog.add('uploadimg', this.path + 'dialogs/uploadimg.js');
    }
});
