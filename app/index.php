<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0" />
    <title>APP Demo页面</title>
    <script src="http://libs.baidu.com/jquery/2.0.3/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
    	body {
    		background-color: #861AE9;
    		color:#ffffff;
            font-size: 1.8rem;
            text-align: center;
		}
		#title{
			margin-top: 40px;
		}
        #error{
             margin-top: 10px;
        }
        select{
            margin-bottom: 30px;
        }
		.block{
			margin-top: 30px;
		}
        .block a{
            color: #ffffff;
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
                        <button type="submit" class="btn btn-lg btn-success">退出登录</button>
                    </div>
                </form>
            <?php } else { ?>
                <div id="title">APP Demo</div>
                <div class="block">
                    <form action="server.php?action=login" method="post">
                        <select name="username">
                            <option value="User A">User A</option>
                            <option value="User B">User B</option>
                        </select>
                        <div><button type="submit" class="btn btn-lg btn-success">登录</button></div>
                    </form>
                </div>
            <?php } ?>
            <div class="block">
                <a href="http://gamecenter/home/index">进入游戏中心</a>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['error']);?>
</div>
</body>
</html>