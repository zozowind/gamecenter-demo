<?php
    session_start();
    //连接Redis
    $redis = new Redis();
    $redis->connect('127.0.0.1',6379);
    $key = 'app_user|';

    switch ($_GET['action']) {
        //注册新账户并登录
        case 'register':
            //检查是否登录
            if(isset($_SESSION['username'])){
                $_SESSION['error'] = '用户: '.$_SESSION['username'].'已经登录';
            }else{
                if(trim($_POST['username'])==''){
                    $_SESSION['error'] = '用户名为空';
                }else{
                    //检查用户名是否被注册
                    if($redis->get($key.$_POST['username'])===false){
                        $info = array();
                        $redis->set($key.$_POST['username'],json_encode($info));
                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['info'] = $info;
                        $_SESSION['message'] = '登录成功';
                    }else{
                        $_SESSION['error'] = '用户: '.$_POST['username'].'已经存在';
                    }
                }
            }
            break;

        case 'login':
            //检查是否登录
            if(!isset($_SESSION['username'])){
                $info = $redis->get($key.$_POST['username']);
                if($info === false){
                    $_SESSION['error'] = '用户:'.$_POST['username'].'不存在';
                }else{
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['info'] = json_decode($info,true);
                    $_SESSION['message'] = '登录成功';
                }
            }
            break;

        case 'logout':
            unset($_SESSION['userid']);
            unset($_SESSION['username']);
            $_SESSION['message'] = '退出成功';
            break;

        case 'getUser':
            //登录情况
            if(isset($_SESSION['username'])){
                $data = array(
                    'app_user' => $_SESSION['username']
                );
            //不登录情况
            }else{
                $data = array();
            }
            $callback = $_GET['callback'];
            echo $callback.'('.json_encode($data).')';
            exit;
            break;

        default:
            # code...
            break;
    }
    header('Location:index.php');
    exit;