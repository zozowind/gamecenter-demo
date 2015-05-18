$(function(){
    //支付接口实现
    var payBtn = $('.payBtn');

    payBtn.on('touchend', function(){
        var itemId = $(this).attr('id');
        $.ajax({
            type: "POST",
            url: "demo.php?action=buyGold",
            data: {"itemId": itemId},
            dataType: "json",
            success: function(data){
                if(data.code == 0){
                    hGame.pay(data.data.payInfo, data.data.payType, function(result){
                        alert(JSON.stringify(result));
                    });
                }else{
                    alert(data.message);
                }
            },
            error: function(data){
                alert("获取金币失败");
            }
        });
    });

    //分享接口实现
    var shareBtn = $('.shareBtn');
    shareBtn.on('touchend', function(){
        var title = '这是一个测试用的Demo';
        hGame.share(title,function(result){
            alert(JSON.stringify(result));
        });
    });

    //游戏上报接口实现
    var reportBtn = $('.reportBtn');
    reportBtn.on('touchend',function(){
        var action = $(this).attr('action');

        switch(action){
            case 'createRole':
            case 'enterGame':
                var extendData = {};
                break;
            case 'levelUpgrade':
                var extendData = {
                    "level": 12
                };
                break;
            case 'processReport':
                var extendData = {
                    "process":  'D区第一关'
                };
                break;
            case 'scoreReport':
                var extendData = {
                    "score":  '123456'
                };
                break;
            default:
                console.log('错误的action:'+action);
                break;
        }
        hGame.gameReport(action, baseData,extendData,function(result){
            alert(JSON.stringify(result));
        });
    });

    //菜单标签栏切换
    $('.label_inner').on('click', function(){
        displayBlock($(this).attr('href'));
    });

    //显示对应菜单block
    function displayBlock(id){
        $('.inter-block').hide();
        $(id).show();
    }
});
