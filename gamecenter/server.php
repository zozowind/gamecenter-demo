<?php
    //连接Redis
    $redis = new Redis();
    $redis->connect('127.0.0.1',6379);
    $key = 'center_user||';
    $app_center = 'app_center||';
    $center_app = 'center_app||';
    switch ($_GET['action']) {
        case 'ajaxLogin':
            //检查是否游客登录
            if($_POST['app_user']==''){
                if($_POST['center_user']==''){
                    //创建临时账号
                    $info = array();
                    $center_user = md5(uniqid(mt_rand(), true));
                    $redis->set($key.$center_user, json_encode($info));
                }else{
                    $center_user = $_POST['center_user'];
                    $info = json_decode($redis->get($key.$center_user),true);
                }
            }else{
                $center_user = $redis->get($app_center.$_POST['app'].'||'.$_POST['app_user']);
                if($center_user === false){
                    //检查cookie是否绑定app_user账号
                    $check = $redis->get($center_app.$_POST['app'].'||'.$_POST['center_user']);
                    //客户端没有进入过游戏中心或该cookie已被绑定到其他的账号
                    if($_POST['center_user']=='' || ($check!==false&&$check!=$_POST['app_user'])){
                        //创建账号
                        $info = array();
                        $center_user = md5(uniqid(mt_rand(), true));
                        $redis->set($app_center.$_POST['app'].'||'.$_POST['app_user'], $center_user);
                        $redis->set($center_app.$_POST['app'].'||'.$center_user, $_POST['app_user']);
                        $redis->set($key.$center_user,json_encode($info));
                    }else{
                        $center_user = $_POST['center_user'];
                        //该账号没有绑定, cookie也没有绑定
                        if($check === false){
                            //绑定
                            $redis->set($app_center.$_POST['app'].'||'.$_POST['app_user'], $center_user);
                            $redis->set($center_app.$_POST['app'].'||'.$center_user, $_POST['app_user']);
                        }
                        $info = json_decode($redis->get($key.$center_user),true);
                    }
                }else{
                    $info = json_decode($redis->get($key.$center_user),true);
                }
            }
            $data = array(
                'center_user'=> $center_user,
                'info' => $info
            );
            echo json_encode($data);
            exit;
            break;
        default:
            # code...
            break;
    }
    exit;