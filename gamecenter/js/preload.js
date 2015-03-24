/**
 * 1. SDK先访问APP接口获取内容(demo中使用js跨域访问来代替)
 * 2. if成功获取了用户信息:
 * 3.    if cookie存在&&用户名一致
 *          使用cookie信息登录
 *       else
 *          使用用户信息登录
 * 4. else
 *      请求临时账号
 */
var loginUrl = 'server.php?action=ajaxLogin'; //login链接
var storageType = 'localStorage';

$(function(){
    start();
});

//使用jsonp方式模拟通过APP接口获取ticket
var start = function(){
    $.ajax({
        type : "post",
        url : "http://demo-app/server.php?action=getTicket",
        dataType : "jsonp",
        jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(默认为:callback)
        jsonpCallback: "jsonpCallback",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名
        success : function(json){
        },
        error:function(){
            jsonpCallback(false);
        }
    });
};

//获取到服务器生成的Ticket后
var jsonpCallback = function(data){
    if(data){
        login(data.data, getAccessToken(storageType));
    }else{
        //没有服务端的APP按接口约定规则在APP中实现签名
        var data = {};
        login(data, getAccessToken(storageType))
    }
}

//获取当前APP中登录的AccessToken
var getAccessToken = function(type){
    switch (type) {
        case 'localStorage':
            return window.localStorage.getItem("AccessToken");
            break;
        case 'cookie':
            if (document.cookie.length>0){
                var cStart=document.cookie.indexOf("AccessToken=");
                if (cStart!=-1){
                    cStart=cStart + "AccessToken".length+1;
                    var cEnd=document.cookie.indexOf(";",cStart);
                    if (cEnd==-1) cEnd=document.cookie.length;
                    return unescape(document.cookie.substring(ctart,cEnd));
                }
            }
            return "";
            break;
        default :
            return "";
            break;
    }
}

//设置AccessToken
var setAccessToken = function(type, accessToken){
    switch (type) {
        case 'localStorage':
            window.localStorage.setItem("AccessToken", accessToken);
            break;
        case 'cookie':
            document.cookie = 'AccessToken='+accessToken;
            break;
        default :
            break;
    }
}

var login = function(data, accessToken){
    $.ajax({
        type : "post",
        url : loginUrl,
        data: {"ticketPackage": data, "accessToken": accessToken},
        dataType : "json",
        success : function(data){
            if(data.code == 0){
                setAccessToken(data.data.accessToken);
                showCenter(data.data.page);
            }else{
                alert(data.message);
            }
        },
        error:function(){
            alert('认证出现错误');
        }
    });
}

var showCenter = function(data){
    var html = '<div id="title">欢迎你，'+data.center_user.substr(0,5)+'</div><div class="block">'+
        '<button onclick="location.href=\'http://demo-game/demo1.php\'">游戏一，随便玩玩</button>'+
        '<button onclick="location.href=\'http://demo-game/server.php?action=centerLogin&center_user='+
        data.center_user+'\'">游戏二，要登录</button>'+
        '</div>';
    $('#page').append(html);
}



