<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> | word图片自动上传 | Applet插件 - 蝙蝠软件</title>
        <script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
        <script type="text/javascript" language="javascript" src="./rere_wimg_up/js/jquery-1.8.3.min.js"></script> 
        <script type="text/javascript" language="javascript" src="./wordimage_uploader.js"></script>
    </head>
    <body>

        <textarea id="editor1"></textarea>
        <script type="text/javascript" >
            //ckEditor加载到editor1
            var editor = CKEDITOR.replace('editor1');
            //构建图片上传地址
            var sUrl ='http://localhost/questionImport/edit/php/upload_json.php';
            //构建应用名称，如本系统名称为‘wordimg’，如果为根应用，请写为空字符串''        
            var appName = '/questionImport';
            //创建WordImageUploader对象
            var uploader = new WordImageUploader(sUrl, appName);
            //当ckeditor内容改变时，自动检测并上传内容中的本地图片
           CKEDITOR.instances["editor1"].on("change", function() {
               uploader.uploadWordImagesFromCKEditor(CKEDITOR.instances["editor1"], '');
            });
        </script>

    </body>
</html>
