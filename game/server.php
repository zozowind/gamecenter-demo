<?php  
session_start();
$mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
switch ($_GET['action']) {
    case 'centerLogin':
        $rs = $mysqli->query("SELECT * FROM game_user WHERE center_user = '".$_GET['center_user']."'");
        if($rs->num_rows > 0){
            $user = $rs->fetch_assoc();
            $gold = $user['gold'];
            $game_user = $user['username'];
        }else{
            //创建账号
            $gold = 0;
            $game_user = 'g_'.md5(uniqid(mt_rand(), true));
            $mysqli->query("INSERT INTO game_user (username, gold, center_user) VALUES ('".$game_user."','".$gold."','".$_GET['center_user']."')");
        }
        $_SESSION['username'] = $game_user;
        $_SESSION['gold'] = $gold;
        $_SESSION['message'] = '登录成功';
        $_SESSION['center'] = $_SERVER['HTTP_REFERER'];
        break;

    case 'logout':
        unset($_SESSION['username']);
        unset($_SESSION['gold']);
        $center_url = $_SESSION['center'];
        unset($_SESSION['center']);
        $_SESSION['message'] = '退出成功';
        $mysqli->close();
        header('Location: '.$center_url);
        exit;
        break;

    case 'getGold':
        $rs = $mysqli->query("SELECT * FROM game_user WHERE username = '".$_SESSION['username']."'");
        if($rs->num_rows > 0){
            $gold = $rs->fetch_assoc()['gold'] + rand(0,10);
            $mysqli->query("UPDATE game_user SET gold = ".$gold." WHERE username = '".$_SESSION['username']."'");
            echo json_encode(array('message'=>'success','gold'=>$gold));
        }else{
            echo json_encode(array('message'=>'用户不存在！'));

        }
        $mysqli->close();
        exit;
        break;
    
    default:
        # code...
        break;
}
$mysqli->close();
header('Location:demo2.php');
exit;
