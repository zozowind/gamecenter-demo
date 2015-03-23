<?php
//连接数据库
$mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
$message = 'success';
switch ($_GET['action']) {
    case 'ajaxLogin':
        //检查是否游客登录
        //这里验证ticketPackage传输的数据是否通过验证
        //@TODO 验证TicketPackage

        if($_POST['ticketPackage']['userType']=='temp'){
            if($_POST['accessToken']==''){
                //创建临时账号
                $info = array();
                $center_user = md5(uniqid(mt_rand(), true));
                $mysqli->query("INSERT INTO center_user (username, info) VALUES ('".$center_user."','".json_encode($info)."')");
            }else{
                $center_user = $_POST['accessToken'];
                $rs = $mysqli->query("SELECT * FROM center_user WHERE username = '".$center_user."'");
                if($rs){
                    if($rs->num_rows > 0){
                        $user = $rs->fetch_assoc();
                        $info = json_decode($user['info'],true);
                    }else{
                        $info = array();
                        $message = '用户不存在';
                    }
                }else{
                    $message = '数据库查询错误';
                }
            }
        }else{
            //这里实现从游戏服务中向APP服务器验证
            //@TODO 验证TicketPackage,获得app_user
            $rs = $mysqli->query("SELECT * FROM app_center WHERE app_user = '".$_POST['app_user']."' AND app_id = '".$_POST['app']."'");
            if($rs->num_rows == 0){
                //检查cookie是否绑定app_user账号
                //$check = $redis->get($center_app.$_POST['app'].'||'.$_POST['center_user']);
                $check = $mysqli->query("SELECT * FROM app_center WHERE center_user = '".$_POST['center_user']."'");
                //客户端没有进入过游戏中心或该cookie已被绑定到其他的账号
                $checkArr =  $check->fetch_assoc();
                if($_POST['center_user']=='' || ($check->num_rows > 0 && $checkArr['app_user']!=$_POST['app_user'])){
                    //创建账号
                    $info = array();
                    $center_user = md5(uniqid(mt_rand(), true));
                    $mysqli->query("INSERT INTO app_center (app_user, center_user, app_id) VALUES ('".$_POST['app_user']."','".$center_user."','".$_POST['app']."')");
                    $mysqli->query("INSERT INTO center_user (username, info) VALUES ('".$center_user."','".json_encode($info)."')");
                }else{
                    $center_user = $_POST['center_user'];
                    //该账号没有绑定, cookie也没有绑定
                    if($check->num_rows == 0){
                        //绑定
                        $mysqli->query("INSERT INTO app_center (app_user, center_user, app_id) VALUES ('".$_POST['app_user']."','".$center_user."','".$_POST['app']."')");
                    }
                    $crs = $mysqli->query("SELECT * FROM center_user WHERE username = '".$center_user."'");
                    $crsArr = $crs->fetch_assoc();
                    $info = json_decode($crsArr['info'],true);
                }
            }else{
                $rsArr = $rs->fetch_assoc();
                $center_user = $rsArr['center_user'];
                $crs = $mysqli->query("SELECT * FROM center_user WHERE username = '".$center_user."'");
                $crsArr = $crs->fetch_assoc();
                $info = json_decode($crsArr['info'],true);
            }
        }
        $data = array(
            'center_user'=> $center_user,
            'info' => $info,
            'message' => $message
        );
        echo json_encode($data);
        exit;
        break;
    default:
        # code...
        break;
}
exit;