
function checkJRE() {
    try {
        if (document.getElementById('wordImageApplet').isActive()) {
            return 1;
        }
    } catch (error) {
//        if (confirm('您的系统未安装图片自动上传控件所需的JAVA运行环境,是否要下载安装JDK？')) {
//            window.location.href = 'http://www.oracle.com/technetwork/java/javase/downloads/index.html';
//        }
        return 0;
    }
}


function WordImageUploader(s_url, app_name)
{
    var _this = this; //把this保存下来，以后用_this代替this，这样就不会被this弄晕了
    var sUrl = 'http://localhost:8084/editor/servlet/WordImageUploader';
    var appName = "/editor1";

    var init = function()
    {
// 构造函数代码 
        sUrl = s_url;
        appName = app_name;
        if (appName == '/') {
            appName = '';
        }

    };

    var printRequiredHtml11 = function() {
        var xx = '<div id=\"word_image_container_temp\" style=\"display:none;\"></div>';

        var yy1 = '<div id=\"wordImageAppletWrapper\" style=\"height: 22px;background-color: #f2f1f1;border-top: 1px solid gray;position:fixed; bottom:0;left:0; width:100%; overflow: hidden;z-index:1000;\" > ';
        var yy2 = '<applet id=\"wordImageApplet\" name=\"wordImageApplet\" code=\"com.reremouse.applet.FileAdapter\" codebase=\"' + appName + '/rere_wimg_up/applet\" archive=\"examapplet.jar,apache-mime4j-0.6.jar,commons-logging-1.1.3.jar,httpclient-4.0.3.jar,httpcore-4.0.1.jar,httpmime-4.0.3.jar\" width=\"100%\" height=\"22\"></applet>';
        var yy3 = '</div>';

        //alert(yy2);
        document.write(xx);
        document.write(yy1);
        document.write(yy2);
        document.write(yy3);
    }
    init();
    printRequiredHtml11();

    var yy4 = '<span style="color:blue;font-size:12px;">&nbsp;&nbsp;未安装JAVA环境或JAVA运行不正常，“图片自动上传插件”不能运行，<a href="http://www.oracle.com/technetwork/java/javase/downloads/index.html" target="_blank">点此下载JDK</a>。</span>';
    var cjj = checkJRE();
    if (cjj != 1) {
        jQuery('#wordImageAppletWrapper').html('');
        jQuery('#wordImageAppletWrapper').html(yy4);
    }

    _this.uploadWordImagesFromCKEditor = function(editorInstance, pre_id) {
        //alert('');
        var cj = checkJRE();
        if (cj != 1) {
            return 0;
        }
        var ed = editorInstance;
        var txt = ed.getData();
        var txt0 = txt;
        jQuery('#word_image_container_temp').html(txt);
        //alert(jQuery('#container_temp').html());
        var i = 0;
        jQuery('#word_image_container_temp img').each(function() {
            var src = $(this).attr('src');
            if (src.indexOf("file:///") != -1) {
                var srct = src.replace('file:///', '');
                //alert(srct);
                var serverPath = _this.uploadLocalFile(srct, pre_id + '_' + newGuid121());
                if (serverPath != 'error') {
                    //alert(serverPath);
                    txt = txt.replace(src, serverPath);
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
        var appletObj = document.getElementById("wordImageApplet");
        var result = appletObj.jsSendLocalFile(sUrl, localUrl, name);
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

