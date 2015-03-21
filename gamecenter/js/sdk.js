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
$(function(){
    start();
});

var start = function(){
    $.ajax({
        type : "post",
        url : "http://demo-app/server.php?action=getUser",
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

var jsonpCallback = function(data){
    if(data && data.app_user!= undefined){
        //app账号
        login(data.app_user, getCookie('center_user'));
    }else{
        //游客
        login('',getCookie('center_user'));
    }
}

var getCookie = function (c_name){
    if (document.cookie.length>0){
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1){
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return "";
}

var login = function(app_user, center_user){
    $.ajax({
        type : "post",
        url : "server.php?action=ajaxLogin",
        data: {"app":app_id, "app_user": app_user, "center_user": center_user},
        dataType : "json",
        success : function(data){
            console.log(data);
            if(app_user == ''){
                document.cookie = 'center_user='+data.center_user;
            }else{
                document.cookie = 'app_user='+app_user+'&center_user='+data.center_user;
            }
            showCenter(data);
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



