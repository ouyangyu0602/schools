
CKEDITOR.plugins.add('hello', {
    requires: ['dialog'],
    init: function(a){
        a.addCommand('hello', new CKEDITOR.dialogCommand('hello'));
        a.ui.addButton('hello', {
            label: "hello",
            command: 'hello',
            icon: this.path + 'images/code.png'
/*            label:当鼠标移动到按钮上面是提示此文本信息。
                className:样式名，默认是'cke_button_' + command
                click：按钮的单击事件出发的方法。如果没有实现单击事件，则执行指定key的命令。
                command：按钮单击默认执行的命令。*/
    });
        CKEDITOR.dialog.add('hello', this.path + 'dialogs/hello.js');
    }
});
