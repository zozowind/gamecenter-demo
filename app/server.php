<?php
    session_start();
    //定义从游戏管理中心获取的APPKEY_ID和SECRET_KEY
    define('APPKEY_ID', 'demo_appkey_id');
    define('SECRET_KEY', 'demo_secret_key');
    switch ($_GET['action']) {      //模拟路由
        case 'login':               //用户登录
            login();
            break;

        case 'logout':              //用户登出
            logout();
            break;

        case 'getUser':             //获取当前用户
            getUser();
            exit;
            break;

        case 'getTicket':           //获取当前用户的Ticket
            echo getTicket();
            exit;
            break;

        case 'checkTicket':         //校验用户的Ticket
            $data = json_decode($_POST['ticketPackage']);
            if($data){
                echo checkTicket($data);
            }else{
                echo json_encode($result = array(
                    'code' => '-3',
                    'message' => 'Ticket包格式不正确'
                ));
            }
            exit;
            break;

        default:
            break;
    }
    header('Location:index.php');
    exit;

    /**
     * 用户登录模拟
     *
     * 使用Session记录用户登录状态，并用此来判断用户是否登录
     */
    function login()
    {
        //检查是否登录
        if(!isset($_SESSION['username'])){
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['info'] = array();
            $_SESSION['message'] = '登录成功';
        }
    }

    /**
     * 用户登出模拟
     *
     * 登出时清除用户Session
     */
    function logout()
    {
        unset($_SESSION['userid']);
        unset($_SESSION['username']);
        $_SESSION['message'] = '退出成功';
    }

    /**
     * 获取当前用户的ticket
     *
     * 生成随机字符ticket
     */
    function getTicket(){
        //生成ticket
        $ticket = md5(uniqid(mt_rand(), true));
        $data = array(
            'appkey_id' => APPKEY_ID,
            'timestamp' => time(),
            'ticket' => $ticket
        );
        if(isset($_SESSION['username'])){
            //保存ticket
            $mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
            $mysqli->query("Update app_user SET ticket = '".$ticket."' WHERE username = '".$_SESSION['username']."'");
            $mysqli->close();
            $data['userType'] = 'real';
        }else{
            $data['userType'] = 'temp';
        }

        ksort($data);
        $signature = sha1(json_encode($data).SECRET_KEY);
        $data['signature'] = $signature;

        $callback = $_GET['callback'];
        echo $callback.'('.json_encode(array(
                'code' => 0,
                'message' => '成功',
                'data' => $data
            )).')';
    }

    /**
     * 校验ticket并返回用户信息
     *
     * @param $data
     * @return string
     */
    function checkTicket($data){
        $signature = $data['signature'];
        unset($data['signature']);
        ksort($data);
        if($signature == sha1(json_encode($data).SECRET_KEY)){
            $user = getUserFromTicket($data['ticket']);
            if($user === false){
                $result = array(
                    'code' => '-1',
                    'message' => '用户不存在'
                );
            }else{
                $result = array(
                    'code' => '0',
                    'message' => '成功',
                    'app_user_id' => md5($user['username']),
                );
            }

        }else{
            $result = array(
                'code' => '-2',
                'message' => '签名校验失败'
            );
        }

        return json_encode($result);
    }

    /**
     * 根据ticket返回用户信息
     *
     * @param $ticket
     * @return string
     */
    function getUserFromTicket($ticket){
        //这里根据实际情况实现通过$ticket获取用户ID的代码
        //最后输出可以是userId，username, 和user 具有一一对应关系的字符串
        $mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
        $rs = $mysqli->query("SELECT *  FROM app_user WHERE username = '".$_SESSION['username']."'");
        if($rs->num_rows > 0){
            $result = $rs->fetch_assoc();
        }else{
            $result = false;
        }
        $mysqli->close();
        return $result;
    }