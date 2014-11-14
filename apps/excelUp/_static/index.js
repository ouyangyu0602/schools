var id = "";
var name = "";

var idPre = "";
var namePre = "";

var isInit = true;


function ClientInit(){
    // 创建信息模拟通讯录
    builePeopleList();
}

function selectPeople(thisObject){
    if (thisObject != null){
        var nameId = "name" +thisObject.id;
        var statusId = "status" +thisObject.id;
        id = thisObject.id;
        name = document.getElementByIdx_x(nameId).innerHTML;
        //alert(" 1 -- id:" + id + ",name:" + name + ",idPre:" + idPre + ",namePre:" + namePre);
        if (idPre != id) {
            if(document.getElementByIdx_x(statusId).className  == "status"){
                document.getElementByIdx_x(statusId).className = "statusOnclick";
            } else {
                document.getElementByIdx_x(statusId).className = "status";
            }
            var statusIdPre = "status" + idPre;
            if ( statusIdPre != "status" ) {
                var preClass = document.getElementByIdx_x(statusIdPre).className;
                document.getElementByIdx_x(statusIdPre).className = "status";
            }
        }
        idPre = id;
        namePre = name;
    }
}

function selectAction() {
    if (name == '' || name == null) {
        alert("请选择人员");
        return;
    }
    alert("选择人员为：" + name + ", ID:" + id);
}

var ChineseArray =  new Array();

function builePeopleList(){

    isInit = true; // 初始化效果

    requestPeopleList(); // 请求人员适时数据
}

var resultData = '';

function buildPeopleSelectListHtml(){
    resultData = '';
    // A - Z
    buildPeoples(PY_Str_1,'A');
    buildPeoples(PY_Str_2,'B');
    buildPeoples(PY_Str_3,'C');
    buildPeoples(PY_Str_4,'D');
    buildPeoples(PY_Str_5,'E');
    buildPeoples(PY_Str_6,'F');

    buildPeoples(PY_Str_7,'G');
    buildPeoples(PY_Str_8,'H');
    buildPeoples(PY_Str_9,'I');
    buildPeoples(PY_Str_10,'J');
    buildPeoples(PY_Str_11,'K');
    buildPeoples(PY_Str_12,'L');

    buildPeoples(PY_Str_13,'M');
    buildPeoples(PY_Str_14,'N');
    buildPeoples(PY_Str_15,'O');
    buildPeoples(PY_Str_16,'P');
    buildPeoples(PY_Str_17,'Q');
    buildPeoples(PY_Str_18,'R');

    buildPeoples(PY_Str_19,'S');
    buildPeoples(PY_Str_20,'T');
    buildPeoples(PY_Str_21,'U');
    buildPeoples(PY_Str_22,'V');
    buildPeoples(PY_Str_23,'W');
    buildPeoples(PY_Str_24,'X');

    buildPeoples(PY_Str_25,'Y');
    buildPeoples(PY_Str_26,'Z');

    //alert("resultData:" + resultData);

    if ( resultData == null || resultData == '')
    {
        $("#peopleList").html('<div class="people"><div></div><div class="name" style="text-align:center; float:none;"> 无人员信息  </div> </div>');
    } else {

        // 人员总数显示
        var peopleSum = '<div class="people"><div></div><div class="name" style="text-align:center; float:none;"> 搜索结果 </div> </div>';

        // 新增100位人员信息按钮
        var buttonGetMore = '';

        // 最终构建人员列表视图
        resultData = peopleSum + resultData + buttonGetMore ;
        $("#peopleList").html(resultData);
    }
}

function buildPeoples(PY_Str,ZM){
    if (PY_Str != null && PY_Str != '' && ZM != null && ZM != ''){
        var pys = PY_Str.split(',');
        if (pys.length >= 2){
            resultData +=
                '<div id="list1" class="barDiv"> '
                    +'<div class="ZM">' + ZM + '</div>'
                    +'</div>';
        }
        for(var i = 0 ; i < pys.length ; i++) {
            if (pys[i] != null && pys[i] != ''){
                var peopleInfo = pys[i].split(':');
                resultData +=
                    '<div class="people" id="'+peopleInfo[0]+'" onclick="selectPeople(this)">'
                        +'<div id="status' + peopleInfo[0] +'" class="status"> '
                        +'</div>'
                        +'<div id="name' + peopleInfo[0] +'" class="name">'
                        + peopleInfo[1]
                        +'</div>'
                        +'</div>';
            }
        }
    }
}

var keyword4Searching = '';
$(function(){

    $("#searchInput").click( function() {
        if( $("#searchInput").val() == '请输入人员名称' ){
            $("#searchInput").val('');
        }
    });


    $("#searchBtn").click( function() {
        idPre = ""; // 清除前个选择人员id
        namePre = ""; // 清除前个选择人员name
        id = ""; // 清除前个选择人员id
        name = ""; // 清除前个选择人员name
        var peopleName = $("#searchInput").val();
        var name_ = Trim(peopleName);
        if( name_ == '请输入人员名称'){
            alert('请输入人员名称');
            return;
        }
        keyword4Searching = name_;

        requestPeopleList(); // 请求人员适时数据
    });

    $("#ok").click( function() {
        selectAction();
    });

});

function requestPeopleList(){

    // 有效的人员信息个数
    var k = 0;

    // 赋值给全局数组
    ChineseArray = [
        {"id":1,"content": "潘深练"},
        {"id":2,"content": "奥巴马"},
        {"id":3,"content": "斯坦森·杰克逊"},
        {"id":4,"content": "James"},
        {"id":5,"content": "波波维奇"},
        {"id":6,"content": "亚当"},
        {"id":7,"content": "贝克汉姆"},
        {"id":8,"content": "Senlypan"},
        {"id":9,"content": "乔治·格林"},
        {"id":10,"content": "马云"},
        {"id":11,"content": "天使"},
        {"id":12,"content": "MM妹子什么的我最爱"},
        {"id":13,"content": "利民考"},
        {"id":14,"content": "普京"},
        {"id":15,"content": "阿汤哥"},
        {"id":16,"content": "C罗"},
        {"id":17,"content": "梅西"},
        {"id":18,"content": "罗志祥"},
        {"id":19,"content": "小花"},
        {"id":20,"content": "吴准收"},
        {"id":21,"content": "准吴收"},
        {"id":22,"content": "收准吴"}
    ];

    if ( isInit ) {
        // 传值
        var ChineseArrayIn =  new Array();
        ChineseArrayIn = ChineseArray;

        // 排序操作
        sortChinese(ChineseArrayIn);

        isInit = false;
    } else {

        // 搜索
        choiceByName(ChineseArray,keyword4Searching);
    }

}