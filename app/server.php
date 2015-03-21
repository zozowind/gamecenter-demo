<?php
    session_start();
    //连接数据库
    $mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
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
                    $rs = $mysqli->query("SELECT * FROM app_user WHERE username = '".$_POST['username']."'");
                    if($rs){
                        if($rs->num_rows > 0){
                            $_SESSION['error'] = '用户: '.$_POST['username'].'已经存在';
                        }else{
                            $info = array();
                            $mysqli->query("INSERT INTO app_user (username, info) VALUES ('".$_POST['username']."','".json_encode($info)."')");
                            $_SESSION['username'] = $_POST['username'];
                            $_SESSION['info'] = $info;
                            $_SESSION['message'] = '登录成功';
                        }

                    }else{
                        $_SESSION['error'] = '数据库连接查询不正确';
                    }
                }
            }
            break;

        case 'login':
            //检查是否登录
            if(!isset($_SESSION['username'])){
                $rs = $mysqli->query("SELECT * FROM app_user WHERE username = '".$_POST['username']."'");
                if($rs){
                    if($rs->num_rows > 0){
                        $user = $rs->fetch_assoc();
                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['info'] = json_decode($user['info'],true);
                        $_SESSION['message'] = '登录成功';
                    }else{
                        $_SESSION['error'] = '用户:'.$_POST['username'].'不存在';
                    }
                }else{
                    $_SESSION['error'] = '数据库连接查询不正确';
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
            $mysqli->close();
            exit;
            break;

        default:
            # code...
            break;
    }
    $mysqli->close();
    header('Location:index.php');
    exit;