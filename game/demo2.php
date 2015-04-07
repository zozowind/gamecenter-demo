<?php 
	session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0" />
    <title>需要登录的HTML5游戏</title>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <style type="text/css">
    	body {
    		background-color: #1472E9;
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
		#login{
			margin-top: 30px;
			text-align: center;
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
    </style>
</head>
<body>
	<div class="container-fluid page-container">
	    <div class="row">
	    	<?php if(isset($_SESSION['username'])){ ?>
		    	<div id="play-page" class="col-xs-12 main">
		            <div id="title">欢迎你，<?php echo substr($_SESSION['username'],0,7);?></div>
					<div id="description">点击购买金币，金币价格为10个/0.01RMB</div>
					<div id="info">
						<div>我的金币数: <span id="gold"><?php echo $_SESSION['gold'];?></span></div>
					</div>
					<div class="controls">
						<button class="buyBtn" itemId="item001">买10个金币</button>
                        <button class="buyBtn" itemId="item002">买20个金币</button>
                        <button id="refresh">刷新金币记录</button>
					</div>
		        </div>
	    	<?php } else { ?>
		    	<div id="login-page" class="col-xs-12 main">
		    		<form action="server.php?action=login" method="post">
		    			<div id="title">需要登录游戏Demo</div>
		    			<div id="login">
		    				<div>用户名: <input type="text" name="username" placeholder="输入用户名" /></div>
		    				<div><button type="submit">登录</button></div>
		    			</div>
		    		</form>
		    	</div>
	    	<?php } ?>
	    </div>
	</div>
    <script type="text/javascript" src="js/hgame-sdk.js"></script>
	<script type="text/javascript">
        var hGame = new hGame({
            "game_key": 'demo-game-2'
        });
		$(function(){
			var buyBtn = $('.buyBtn');
			var logoutBtn = $('#logout');
            var refreshBtn = $('#refresh');

            //获取购买商品金币的签名
			buyBtn.on('touchend', function(){
                var itemId = $(this).attr('itemId');
				$.ajax({
	                type: "POST",
	                url: "server.php?action=buyGold",
                    data: {"itemId": itemId},
	                dataType: "json",
	                success: function(data){
	                    if(data.code == 0){
                            hGame.pay(data.data.payInfo, data.data.payType);
	                    }else{
	                        alert(data.message);
	                    }
	                },
	                error: function(data){
	                    alert("获取金币失败");
	                }
            	});
			});

            refreshBtn.on('touchend', function(){
                $.ajax({
                    type: "POST",
                    url: "server.php?action=refresh",
                    dataType: "json",
                    success: function(data){
                        if(data.code == 0){
                            $('#gold').text(data.data.gold);
                        }else{
                            alert(data.message);
                        }
                    },
                    error: function(data){
                        alert("获取金币失败");
                    }
                });
            });
		});
	</script>
</body>
</html>