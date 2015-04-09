<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0" />
    <title>不需要登录的HTML5游戏</title>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <style type="text/css">
    	body {
    		background-color: #889FE9;
    		background-image:none;
    		background-repeat:repeat;
    		background-position:50% 0;
    		color:#fff;
		}
		#title{
			margin-top: 40px;
			font-size: 1.5rem;
			text-align: center;
		}
		#description{
			margin-top: 30px;
			font-size: 1rem;
		}
		#info{
			margin-top: 30px;
			font-size: 1rem;
		}
		#gold{
			margin-left: 30px;
			color: #E9A52A;
			font-size: 1.5rem;
		}
		.controls{
			margin-top: 30px;
			text-align: center;
		}
        button{
            margin-top: 30px;
            width: 120px;
            height: 40px;

        }
		#get{
			display: none;
		}
		#end{
			display: none;
		}
    </style>
</head>
<body>
<div class="container-fluid page-container">
    <div class="row">
        <div id="play-page" class="col-xs-12 main">
            <div id="title">不需要登录游戏Demo</div>
			<div id="description">点击开始游戏后，点击获取金币，可以随机获取0-10000分数，游戏结束</div>
			<div id="info">
				<div>我的分数: <span id="gold">0</span></div>
			</div>
            <div class="controls">
                <button id="start">开始游戏</button>
                <button id="get">获取分数</button>
                <button id="end">重新开始</button>
            </div>
			<div class="controls">
                <button id="share" style="display: none">分享游戏</button>
                <button id="report" style="display: none">上报成绩</button>
			</div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/hgame-sdk.js"></script>
<script type="text/javascript">
    var hGame = new hGame({
        "game_key": 'demo-game-1'
    });


	$(function(){
		var startBtn = $('#start');
		var getBtn = $('#get');
		var endBtn = $('#end');
        var shareBtn = $('#share');
        var reportBtn = $('#report');
		var gold = parseInt($('#gold').text());

		startBtn.on('touchend', function(){
            $('#gold').text('0');
			startBtn.hide();
			getBtn.show();
			endBtn.show();
            shareBtn.hide();
            reportBtn.hide();
		});

		getBtn.on('touchend', function(){
			gold = Math.round(Math.random()*10000);
            $('#gold').text(gold);
            getBtn.hide();
            endBtn.show();
            shareBtn.show();
            reportBtn.show();
		});

		endBtn.on('touchend', function(){
			myGold = 0;
			$('#gold').text('0');
			startBtn.show();
			getBtn.hide();
			endBtn.hide();
            shareBtn.hide();
            reportBtn.hide();
		});

        shareBtn.on('touchend', function(){
            hGame.share('这里游戏可以自己定义分享的消息', function(result){
                alert(result.code);
            });
        });

        reportBtn.on('touchend', function(){
            hGame.scoreReport(gold);
        });

	});
</script>
</body>
</html>