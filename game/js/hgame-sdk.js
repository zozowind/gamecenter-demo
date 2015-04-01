(function(window,document,Math){
    //hGame 初始化
    var hGame = function(config){
        this.app_key = config.app_key;
    };
    //定义h5center的接口
    hGame.prototype = {
        //游戏内点击分享按钮
        share: function(options){

        },
        //游戏内点击购买按钮
        pay: function(options){
            this.afterPay = afterPay;
        },
        shareCallback: function(){
            this.afterShare();
        },
        payCallback: function(){
            this.afterPay();
        },
        scoreReport: function(score){
            alert(score);
            alert(this.app_key);
        }
    };

    var sendMessage = function(message){
        var iframe = window.parent;
        if(typeof iframe != undefined){
            iframe.postMessage(message, url);
        }else{
            //报错
            alert('没有找到父窗口');
        }
    };

    var messageHandler = function(messagee){
        var data = JSON.parse(message);
        if(typeof data == 'object'){
            switch(data.action){
                case 'share':
                    hGame.shareCallback();
                    break;
                case 'pay':
                    hGame.payCallback();
                    break;
                default:
                    break;
            }
        }else{
            //报错
            alert('消息体格式不正确');
        }
    };

    window.addEventListener('message', function(event){
        messageHandler(event.data);
    }, false)

    window.hGame = hGame;
})(window,document,Math);