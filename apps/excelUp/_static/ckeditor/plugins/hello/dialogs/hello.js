/*
hello world - by watersay
 */
    CKEDITOR.dialog.add("hello",
        function(a) {
            return {
                title: "hello",
                minWidth: 500,
                minHeight:500,
                contents: [{
                    id: 'info',
                    label: '名字',
                    title: '名字',
                    elements:
                        [
                            {
                                id: 'text',
                                type: 'text',
                                style: 'width: 50%;',
                                label: '名字',
                                'default': '',
                                required: true,
                                validate: CKEDITOR.dialog.validate.notEmpty('名字不能为空'),
                                commit: function () {
                                    var text ='Hello'+this.getValue();
                                    alert(text);
                                }
                            }
                        ]
//                    elements: [{
//                        type: "html",
//                        style: "width:300px;height:300px",
//                        html: '内容fsdfsdfds测试daskhfjkjdhs f'
//                    }]
                }],
                onOk: function() {
                    //点击确定按钮后的操作
                    a.insertHtml("helloworld");
                },
                onLoad: function () {
                    alert('onLoad');
                },
                onShow: function () {
                    alert('onShow');
                },
                onHide: function () {
                    alert('onHide');
                },
                onCancel: function () {
                    alert('onCancel');
                }
            }
    });