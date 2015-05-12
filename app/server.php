<?php
    session_start();
    //定义从游戏管理中心获取的APPKEY_ID和SECRET_KEY
    define('APPKEY_ID', 'sdk_test_key');
    define('SECRET_KEY', 'sdk_test_secret');

    $payConfirmUrl = 'https://pay.dev.gc.hgame.com/pay/apple/notify';

    /**
     * 生成随机数方法
     * @param $n int 随机数位置
     * @return null|string 随机字符串
     */
    function randString($n){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$n;$i++){
            $str.=$strPol[rand(0,$max)];
        }
        return $str;
    }

    /**
     * 签名方法
     * @param $data array 需要签名的数据
     * @param $secret string 游戏中心分配的秘钥
     * @return mixed 签名完成后的数据
     */
    function signTheData($data, $secret){
        ksort($data);
        foreach($data as $k=>$v){
            $tmp[] = $k.'='.$v;
        }
        $str = implode('&',$tmp).$secret;
        $data['signature'] = sha1($str);
        return $data;
    }


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
            echo checkTicket($_GET['ticket']);
            /*
            $data = json_decode($_POST['ticketPackage']);
            if($data){
                echo checkTicket($data);
            }else{
                echo json_encode($result = array(
                    'code' => '-3',
                    'message' => 'Ticket包格式不正确'
                ));
            }
            */
            exit;
            break;

        case 'applePayConfirm':
            $data = array(
                "receipt_data" => $_POST['receipt_data'],
                "open_id" => $_POST['open_id'],
                "orderno" => $_POST['orderno'],
            );

            $result = httpRequestJson($payConfirmUrl, http_build_query(signTheData($data,SECRET_KEY)));
            echo $result;
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
    function getTicket()
    {
        //生成ticket
        $ticket = md5(uniqid(mt_rand(), true));
        $data = array(
            'app_key' => APPKEY_ID,
            'timestamp' => time(),
            'ticket' => $ticket
        );
        $demoUser = 'demo_user';
        if (isset($demoUser)) {
            //保存ticket
            $mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
            $mysqli->query("Update app_user SET ticket = '" . $ticket . "' WHERE username = '" . $demoUser . "'");
            $mysqli->close();
            $data['user_type'] = 'real';
        } else {
            $data['user_type'] = 'temp';
        }
        $data = signTheData($data, SECRET_KEY);

        return json_encode(array(
                'code' => 0,
                'message' => '成功',
                'data' => $data
            ));
    }

    /**
     * 校验ticket并返回用户信息
     *
     * @param $data
     * @return string
     */
    function checkTicket($data)
    {
        $user = getUserFromTicket($data);
        if ($user === false) {
            $result = array(
                'code' => '-1',
                'message' => '用户不存在',
                'data' => array()
            );
        } else {
            $result = array(
                'code' => '0',
                'message' => '成功',
                'data' => array(
                    'app_user_id' => $user['username'],
                )
            );
        }
        /*
        $signature = $data['signature'];
        unset($data['signature']);
        ksort($data);
        if ($signature == sha1(json_encode($data) . SECRET_KEY)) {
            $user = getUserFromTicket($data['ticket']);
            if ($user === false) {
                $result = array(
                    'code' => '-1',
                    'message' => '用户不存在'
                );
            } else {
                $result = array(
                    'code' => '0',
                    'message' => '成功',
                    'app_user_id' => md5($user['username']),
                );
            }

        } else {
            $result = array(
                'code' => '-2',
                'message' => '签名校验失败'
            );
        }
        */
        return json_encode($result);
    }

    /**
     * 根据ticket返回用户信息
     *
     * @param $ticket
     * @return string
     */
    function getUserFromTicket($ticket)
    {
        //这里根据实际情况实现通过$ticket获取用户ID的代码
        //最后输出可以是userId，username, 和user 具有一一对应关系的字符串
        $mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
        $rs = $mysqli->query("SELECT *  FROM app_user WHERE ticket = '" . $ticket . "'");
        if ($rs->num_rows > 0) {
            $result = $rs->fetch_assoc();
        } else {
            $result = false;
        }
        $mysqli->close();
        return $result;
    }

    /**
    * post获取接口
    * @param $url string ticket接口地址
    * @return mixed
    */
    function httpRequestJson($url, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }