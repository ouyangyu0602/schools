var imgurl;
CKEDITOR.dialog.add("uploadimg", function (a) {
//    a = a.lang.uploadimg;
    return { title: '上传图片', minWidth: 390, minHeight: 150, contents: [
        { id: "tab1", label: "", title: "", expand: !0, padding: 0, elements: [
            {   type: "html",
                html: "<form action='index.php?app=excelUp&mod=Uploadimg&act=upload' method='post' enctype='multipart/form-data' target='myiframe' ><input name='uploadimg'  style='' id='uploadimg'  type='file' /><br><input name='submit' onclick='return imgpost();'  id='sumit' value='上传' type='submit'style='' /></form><iframe name='myiframe' id='myiframe' width='150' height='150'frameborder='0' scrolling='yes' marginheight='0' marginwidth='0' ></iframe>"}
        ]}
    ],
        onOk: function() {
            //点击确定按钮后的操作
//            a.insertHtml(window.imgurl);

            var eqn = a.document.createElement( 'img' );
            eqn.setAttribute( 'src', window.imgurl);
            a.insertElement(eqn);
        }

    }

});
//target 属性规定在何处打开链接文档。
function imgpost(){
    var req = new XMLHttpRequest();
    if (req != null) {
        req.onreadystatechange = function() {
            //// Checks the asyn request completed or not.
            if (req.readyState == 4) {
                if ((req.status >= 200 && req.status < 300) || req.status == 304) {
                    //// Do something.
//                    document.getElementById("img_tmp").innerHTML= "<img src = "+ req.responseText +">";
                    window.imgurl = req.responseText;
                    return false;
//                    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
                }
                else {
                    alert("Request was unsuccessful: " + req.status);
                }
            }
        };
        req.open("POST", "index.php?app=excelUp&mod=Uploadimg&act=upload", true);
        req.send(null);
    }
}
