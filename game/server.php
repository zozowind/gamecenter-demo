<?php  
session_start();
//连接Redis
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$gold_key = 'game_user||';
$center_game = 'center_game||';
switch ($_GET['action']) {
    case 'centerLogin':
        $game_user = $redis->get($center_game.$_GET['center_user']);
        if( $game_user === false){
            //创建账号
            $gold = 0;
            $game_user = 'g_'.md5(uniqid(mt_rand(), true));
            $redis->set($center_game.$_GET['center_user'], $game_user);
            $redis->set($gold_key.$game_user,$gold);
        }
        $_SESSION['username'] = $game_user;
        $_SESSION['gold'] = $redis->get($gold_key.$game_user);
        $_SESSION['message'] = '登录成功';
        $_SESSION['center'] = $_SERVER['HTTP_REFERER'];
        break;

    case 'login':
        $game_user = $redis->get($center_user.$_GET['center_user']);
        if($game_user===false){
            //登录失败
            $_SESSION['message'] = '该用户不存在';
        } else {
            $_SESSION['username'] = $game_user;
            $_SESSION['gold'] = $redis->get($gold_key.$game_user);
            $_SESSION['message'] = '登录成功';
        }
        break;

    case 'logout':
        unset($_SESSION['username']);
        unset($_SESSION['gold']);
        $center_url = $_SESSION['center'];
        unset($_SESSION['center']);
        $_SESSION['message'] = '退出成功';
        header('Location: '.$center_url);
        exit;
        break;

    case 'getGold':

        $gold = $redis->get($gold_key.$_SESSION['username']);

        if($gold===false){
            echo json_encode(array('message'=>'用户不存在！'));
        }else{
            $gold += rand(0,10);
            $redis->set($gold_key.$_SESSION['username'],$gold);
            echo json_encode(array('message'=>'success','gold'=>$gold));
        }
        exit;
        break;
    
    default:
        # code...
        break;
}
header('Location:demo2.php');
exit;
