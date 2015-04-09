(function(window,document,Math){
    /**
     * hGame初始化
     * @param config 初始化参数, 包括game_key
     */
    var hGame = function(config){
        var _self = this;
        this.game_key = config.game_key;
        this.hGameDomain = 'http://dev1.h5.gamexhb.com';  //填写实际的游戏服务器地址
        //添加消息监听
        window.addEventListener('message', function(event){
            _self.messageHandler(event.data);
        }, false)
    };

    /**
     * hGame接口定义
     * @type {{share: Function, pay: Function, shareCallback: Function, payCallback: Function, scoreReport: Function}}
     */
    hGame.prototype = {
        /**
         * 游戏内分享内容接口
         * @param string title 分享的文字
         */
        share: function(title, callback){
            var message = {
                "action": 'share',
                "data": {
                    "title": title
                }
            };
            this.sendMessage(message, this.hGameDomain);
            this.afterShare = callback;
        },

        /**
         * 游戏内购买接口
         * @param payData 支付信息，支付信息字段和签名要求详见文档
         * @param pay_name 优先支付方式，支付方式详细清单见文档
         */
        pay: function(payData, pay_name, callback){
            var message = {
                "action": 'pay',
                "data": {
                    "payData": payData,
                    "pay_name":pay_name
                }
            };
            this.sendMessage(message, this.hGameDomain);
            this.afterPay = callback;
        },

        /**
         * 游戏内上报游戏成绩到排行榜接口
         * @param int score 游戏成绩
         */
        scoreReport: function(score, callback){
            var message = {
                "action": 'scoreReport',
                "data": {
                    "game_key": this.game_key,
                    "score": score
                }
            };
            this.sendMessage(message, this.hGameDomain);
            this.afterScoreReport = callback;
        },

        /**
         * 消息发送方法
         * @param message 消息体
         * @param hGameDomain 发送对象域名
         */
        sendMessage: function(message, hGameDomain){
            var iframe = window.parent;
            if(typeof (iframe != undefined)){
                iframe.postMessage(message, hGameDomain);
            }else{
                //报错
                alert('没有找到父窗口');
            }
        },

        /**
         * 消息接受后执行
         * @param message 消息体
         */
        messageHandler: function(message){
            if(typeof message == 'object'){
                switch(message.action){
                    case 'share':
                        this.shareCallback(message.content);
                        break;
                    case 'pay':
                        this.payCallback(message.content);
                        break;
                    case 'scoreReport':
                        this.scoreReportCallback(message.content);
                        break;
                    default:
                        break;
                }
            }else{
                //报错
                alert('消息体格式不正确');
            }
        },

        /**
         * 游戏内分享完成后的回调接口
         */
        shareCallback: function(data){
            if(this.afterShare!=undefined){
                this.afterShare(data);
            }
        },


        /**
         * 游戏内支付完成后的回调接口
         */
        payCallback: function(data){
            if(this.afterPay!=undefined){
                this.afterPay(data);
            }
        },

        /**
         * 游戏内上报
         * @param data
         */
        scoreReportCallback: function(data){
            if(this.afterScoreReport!=undefined){
                this.afterScoreReport(data);
            }

        }
    };


    if ( typeof module != 'undefined' && module.exports ) {
        module.exports = hGame;
    } else {
        window.hGame = hGame;
    }
})(window,document,Math);