<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0" />
    <title>游戏中心 Demo页面</title>
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        var app_id = '<?php $_GET['app'];?>';
    </script>
    <style type="text/css">
    	body {
    		background-color: #0c9ae9;
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
		.block{
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
        <div id="page" class="col-xs-12 main">

        </div>
    </div>
</div>
<script type="text/javascript" src="js/sdk.js"></script>
</body>
</html>