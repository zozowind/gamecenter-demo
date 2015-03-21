<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0" />
    <title>APP Demo页面</title>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <style type="text/css">
    	body {
    		background-color: #861AE9;
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
        #error{
             margin-top: 10px;
             font-size: 1.2rem;
             text-align: center;
        }
		.block{
			margin-top: 30px;
			text-align: center;
		}
        .block a{
            color: #ffffff;
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
        <div id="page" class="col-xs-12 main">
            <div id="error"><?php if(isset($_SESSION['error'])){ echo $_SESSION['error'];}?></div>
            <?php if(isset($_SESSION['username'])){?>
                <div id="title">欢迎你，<?php echo $_SESSION['username'];?></div>
                <form action="server.php?action=logout" method="post">
                    <div class="block">
                        <button type="submit">退出登录</button>
                    </div>
                </form>
                <div class="block">
                    <a href="http://demo-gamecenter/index.php?app=ad1">登录后进入游戏中心</a>
                </div>
            <?php } else { ?>
                <div id="title">APP Demo</div>
                <form action="server.php?action=login" method="post">
                    <div class="block">
                        <div>用户名: <input type="text" name="username" placeholder="输入用户名" /></div>
                        <div><button type="submit">登录</button></div>
                    </div>
                </form>
                <form action="server.php?action=register" method="post">
                    <div class="block">
                        <div>用户名: <input type="text" name="username" placeholder="输入用户名" /></div>
                        <button type="submit">注册新账号并登录</button>
                    </div>
                </form>
                <div class="block">
                    <a href="http://demo-gamecenter/index.php?app=ad1">不登录进入游戏中心</a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php unset($_SESSION['error']);?>
</div>
</body>
</html>