(function(window,document,Math){
    //hGame 初始化
    var hGame = function(config){
        this.game_key = config.game_key;
        this.hGameDomain = 'http://gamecenter';
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
            alert('分享回调');
        },
        payCallback: function(){
            alert('支付回调');
        },
        scoreReport: function(score){
            var message = {
                "action": 'scoreReport',
                "data": {
                    "game_key": this.game_key,
                    "score": score
                }
            };
            sendMessage(message, this.hGameDomain);
        }
    };

    var sendMessage = function(message, hGameDomain){
        var iframe = window.parent;
        if(typeof iframe != undefined){
            console.log(message);
            iframe.postMessage(message, hGameDomain);
        }else{
            //报错
            alert('没有找到父窗口');
        }
    };

    var messageHandler = function(messagee){
        alert(message.action);
        return;
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