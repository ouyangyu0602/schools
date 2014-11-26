
function checkJRE() {
    try {
        if (document.getElementById('wordImageApplet1').isActive()) {
            return 1;
        }
    } catch (error) {
//        if (confirm('您的系统未安装图片自动上传控件所需的JAVA运行环境,是否要下载安装JDK？')) {
//            window.location.href = 'http://www.oracle.com/technetwork/java/javase/downloads/index.html';
//        }
        return 0;
    }
}

function checkOS(){
    return windows = -1 != navigator.userAgent.indexOf("Windows", 0) ? 1 : 0 ,
        windows ? 1 : 0
}

function WordImageUploader(s_url, app_name,image_URL)
{
    var _this = this; //把this保存下来，以后用_this代替this，这样就不会被this弄晕了
    var sUrl = s_url;
    var appName = app_name;
    var imageURL = image_URL;

    var init = function()
    {

        sUrl = s_url;
        appName = app_name;
        if (appName == '/') {
            appName = '';
        }

    };

    var yy1 = '<div id=\"wordImageAppletWrapper\" class="col-sm-10 col-sm-offset-1" ></div>';

    var printRequiredHtml11 = function() {
        var xx = '<div id=\"word_image_container_temp\" style=\"display:none;\"></div>';

/*
        var yy1 = '<div id=\"wordImageAppletWrapper\" class="col-sm-10 col-sm-offset-1" ><applet style="display: none" id=\"wordImageApplet\" name=\"wordImageApplet\" code=\"com.reremouse.applet.FileAdapter\" codebase=\"' + appName + 'questionEdit/rere_wimg_up/applet\"  archive=\"examapplet.jar,apache-mime4j-0.6.jar,commons-logging-1.1.3.jar,httpclient-4.0.3.jar,httpcore-4.0.1.jar,httpmime-4.0.3.jar\" width=\"100%\" height=\"22\"></applet></div>';
*/

        var yy2 = '<applet id=\"wordImageApplet1\" name=\"wordImageApplet1\" code=\"com.reremouse.applet.FileAdapter\" codebase=\"' + appName + 'questionEdit/rere_wimg_up/applet\"  archive=\"examapplet.jar,apache-mime4j-0.6.jar,commons-logging-1.1.3.jar,httpclient-4.0.3.jar,httpcore-4.0.1.jar,httpmime-4.0.3.jar\" width=\"0\" height=\"0\"></applet>';
        //var yy3 = '</div>';

        //alert(yy1)
        document.write(xx);

        jQuery("#JDKWordImage").html(yy1);
         //document.write(yy1);
         document.write(yy2);
         //document.write(yy3);
    }
    init();
    printRequiredHtml11();
    var yy4 = '<p>安装快速传题插件，可直接将文档(Word或Wps)中带有图片的题目内容复制粘贴到下面的输入框中</p><a href="http://java.com/zh_CN/" class="btn btn-lg btn-warning">下载快速传题插件</a><p>提示：安装完成后请重新打开浏览器进入本页，并在随后弹出的对话中选择“允许”，若提示应用程序已被安全设置阻止，请至控制面板-&gt;程序-&gt;java面板中将安全级别设置为“中”,并编辑站点列表，将本网站地址添加为新任！</p>';

    //var yy4 = '<span style="color:blue;font-size:12px;">&nbsp;&nbsp;未安装JAVA环境或JAVA运行不正常，“图片自动上传插件”不能运行，<a href="https://www.java.com/en/download/" target="_blank">点此下载JDK</a>。</span>';
    var cjj = checkJRE();
    if (cjj != 1) {
        jQuery('#wordImageAppletWrapper').html('');
        jQuery('#wordImageAppletWrapper').html(yy4);
        jQuery("#JDKWordImage").show();
    } else{
        jQuery('#wordImageAppletWrapper').html('');

        jQuery("#JDKWordImage").hide();
    }

    _this.uploadWordImagesFromCKEditor = function(editorInstance, pre_id) {
        var cj = checkJRE();
        if (cj != 1) {
            return 0;
        }
        var ed = editorInstance;
        //alert(ed);
        var txt = ed.getData();
        alert(txt);
        var txt0 = txt;
txt = txt.replace(new RegExp('width=\"1\"',"gm"), '');
txt = txt.replace(new RegExp('width=\"\"',"gm"), '');
        jQuery('#word_image_container_temp').html(txt);
        //alert(jQuery('#word_image_container_temp').html());
        var i = 0;




        jQuery('#word_image_container_temp img').each(function() {

            var src = $(this).attr('src');

            if (src.indexOf("file://") != -1) {
                if(!checkOS()) {
                    var srct = src.replace('file://localhost', '');
                } else {
                    var srct = src.replace('file:///', '');
                }



                var serverPath = _this.uploadLocalFile(srct, pre_id + '_' + newGuid121());

                if (serverPath != 'error') {
                    txt = txt.replace(src, imageURL+"questionImages/"+serverPath);

                }
            }
        });
        //jQuery('#container_temp').html(txt);
        if (txt0 != txt) {
            ed.setData(txt);
        }
        //alert(ed.getData());
    }

    _this.uploadLocalFile = function(localUrl, name) {
        var appletObj = document.getElementById("wordImageApplet1");
        //alert(sUrl);
        //alert(localUrl);
        var result = appletObj.jsSendLocalFile(sUrl, localUrl, name);
        //alert(result);
        return result;
    }

}

function newGuid121()
{
    var guid = "";
    for (var i = 1; i <= 32; i++) {
        var n = Math.floor(Math.random() * 16.0).toString(16);
        guid += n;
        if ((i == 8) || (i == 12) || (i == 16) || (i == 20))
            guid += "-";
    }
    return guid;
}
