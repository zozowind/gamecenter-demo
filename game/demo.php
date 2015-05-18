<?php
    session_start();
    //配置由平台提供的game_key,game_secret,check
    const HG_CONFIG_GAME_KEY = 'demo-game-1';
    const HG_CONFIG_GAME_SECRET = 'demo-game-1-secret';
    const HG_CONFIG_CHECK_TICKET_URL = 'http://gc.czj.u1.hgame.com/user/getticketuserinfo';
    //配置游戏接口测试DEMO域名
    const GAME_DOMAIN = 'http://game.czj.u1.hgame.com/';

    //配置游戏数据库
    const GAME_DATABASE_HOST = '222.73.184.169';
    const GAME_DATABASE_PORT = '63306';
    const GAME_DATABASE_NAME = 'demo';
    const GAME_DATABASE_USERNAME = 'chenzhijie';
    const GAME_DATABASE_PASSWORD = 'chenzhijie';

    /**
     * Class hgUtility 工具类，用于签名，生成随机数等
     */
    class hgUtility
    {
        /**
         * 生成随机数方法
         * @param $n int 随机数位置
         * @return null|string 随机字符串
         */

        static function randString($n)
        {
            $str = null;
            $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
            $max = strlen($strPol) - 1;

            for ($i = 0; $i < $n; $i++) {
                $str .= $strPol[rand(0, $max)];
            }
            return $str;
        }

        /**
         * 签名方法
         * @param $data array 需要签名的数据
         * @param $secret string 游戏中心分配的秘钥
         * @return mixed 签名完成后的数据
         */
        static function signTheData($data, $secret)
        {
            ksort($data);
            foreach ($data as $k => $v) {
                $tmp[] = $k . '=' . $v;
            }
            $str = implode('&', $tmp) . $secret;
            $data['signature'] = sha1($str);
            return $data;
        }

        /**
         * 校验签名方法
         * @param $data array 签名的数据
         * @param $secret string 游戏中心分配的秘钥
         * @return bool
         */
        static function checkSign($data, $secret)
        {
            $signature = $data['signature'];
            unset($data['signature']);
            ksort($data);
            foreach ($data as $k => $v) {
                $tmp[] = $k . '=' . $v;
            }
            $str = implode('&', $tmp) . $secret;
            return sha1($str) == $signature;
        }

        /**
         * post获取接口
         * @param $url string ticket接口地址
         * @param $data array 传输数据
         * @return mixed
         */
        function httpRequestJson($url, $data){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    }

    /**
     * Class gameServer 服务器需要实现功能
     */
    class gameServer
    {
        protected $mysql;

        protected $itemList = array(
            'item01'=>array(
                'fee'=>0.01, 'subject'=>'1个游戏币', 'body'=>'测试商品：1个游戏币价值1分钱',
            ),
            'item02'=>array(
                'fee'=>0.10, 'subject'=>'10个游戏币', 'body'=>'测试商品：10个游戏币价值1角钱',
            ),
            'item03'=>array(
                'fee'=>10.00, 'subject'=>'1000个游戏币', 'body'=>'测试商品：1000个游戏币价值10元钱',
            ),
        );

        function getItemList(){
            return $this->itemList;
        }

        /**
         * @param $db array 数据库参数
         */
        function __construct($db){
            $this->mysqli = new mysqli($db['host'],$db['username'], $db['password'], $db['name'], $db['port']);
        }

        /**
         * 生成支付数据
         * @param $itemId string 物品ID
         * @param $username string 购买用户用户名
         * @return array
         */
        function generatePayData($itemId,$username)
        {
            if(!isset($this->itemList[$itemId])){
                return array(
                    'code'=> -2,
                    'message'=>'商品'.$itemId.'不存在',
                    'showMessage' => '亲，您购买的物品已断货了',
                    'data' => array()
                );
            }

            if($this->mysqli){
                $rs = $this->mysqli->query("SELECT * FROM game_user WHERE username = '".$username."'");
                if($rs->num_rows > 0){
                    $user = $rs->fetch_assoc();
                }else{
                    $this->mysqli->close();
                    return array(
                        'code'=> -3,
                        'message'=>'用户不存在',
                        'showMessage' => '亲，您的账号不在系统内哦',
                        'data' => array()
                    );
                }

                $data = array(
                    "game_key"      => HG_CONFIG_GAME_KEY,
                    "open_id"       => $user['center_user'],
                    "total_fee"     => $this->itemList[$itemId]['fee'],
                    "game_orderno"  => 'order_'.time(),
                    "subject"       => $this->itemList[$itemId]['subject'],
                    "description"   => $this->itemList[$itemId]['body'],
                    "notify_url"    => GAME_DOMAIN.'demo.php?action=confirm'
                );
                //添加时间戳
                $data['timestamp'] = time();
                //生成随机数
                $data['nonce'] = hgUtility::randString(10);
                $data = hgUtility::signTheData($data, HG_CONFIG_GAME_SECRET);
                $this->mysqli->query("INSERT INTO game_order (game_user_id, game_order, game_item, game_fee, status)
                        VALUES (".$user['id'].",'".$data['game_orderno']."','".$itemId."',".$this->itemList[$itemId]['fee'].",0)");
                //优先支付方式可以由客户端选择
                $this->mysqli->close();
                return array(
                    'code'=>'0',
                    'message'=>'success',
                    'showMessage' => '成功',
                    'data'=>array(
                        'payInfo'=>$data,'payType'=>'')
                );
            }else{
                return array(
                    'code'=> -4,
                    'message'=>'数据库连接出错',
                    'showMessage' => '亲，服务器出问题了，请联系客服',
                    'data' => array()
                );
            }

        }

        /**
         * 确认购买成功
         * @param $data array 支付数据
         * @return array
         */
        function confirmPay($data){
            //检查签名
            if(checkSign($data,HG_CONFIG_GAME_SECRET)){
                $orderRs = $this->mysqli->query("SELECT * FROM game_order WHERE game_order = '".$data['game_orderno']."'");
                if($orderRs->num_rows > 0){
                    $order = $orderRs->fetch_assoc();
                    if($order['status']==0){
                        $this->mysqli->query("UPDATE game_order SET status = 1 WHERE game_order = '".$data['game_orderno']."'");
                        $gold =  $_POST['total_fee']*100;
                        $this->mysqli->query("UPDATE game_user SET gold = gold + ".$gold." WHERE id = ".$order['game_user_id']);
                        return array(
                            'code'=> 0,
                            'message'=>'success',
                            'showMessage'=>'订单确认成功',
                            'data'=>$data
                        );
                    }else{
                        return array(
                            'code'=> -5,
                            'message'=>'订单重复',
                            'showMessage'=>'订单出错，请联系客服',
                            'data'=>$data
                        );
                    }
                }else{
                    return (array(
                        'code'=> -6,
                        'message'=>'游戏订单不存在',
                        'showMessage'=>'订单出错，请联系客服',
                        'data'=>$data
                    ));
                }
            }else{
                return array(
                    'code'=> -9011,
                    'message'=>'签名验证错误',
                    'showMessage'=>'订单出错，请联系客服',
                    'data'=>$data
                );
            }
        }


        function login(){
            //@ticket 登录后需要验证ticket
            $keys = array('game_key','timestamp','nonce','login_type','ticket','game_url','signature');
            foreach($keys as $key){
                $getData[$key] = $_GET[$key];
            }
            if(!hgUtility::checkSign($getData,HG_CONFIG_GAME_SECRET)){
                return array(
                    'code'=> -9011,
                    'message'=>'签名验证错误',
                    'showMessage'=>'登录出错，请联系客服',
                    'data'=>$getData
                );
            }

            $requestData = array(
                "login_ticket"    => $getData['ticket'],
                "game_key"  => HG_CONFIG_GAME_KEY,
                "timestamp"   => time(),
                "nonce" => hgUtility::randString(8),
                "login_type" => '1',
            );

            $result = json_decode(hgUtility::httpRequestJson(HG_CONFIG_CHECK_TICKET_URL, hgUtility::signTheData($requestData,HG_CONFIG_GAME_SECRET)),true);
            if(!$result){
                return array(
                    'code'=> -7,
                    'message'=>'请求认证服务器出错',
                    'showMessage'=>'无法连接认证服务器',
                    'data'=>$requestData
                );
            }
            if($result && $result['code']!=0){
                return $result;
            }
            $userInfo = $result['data'];
            $rs = $this->mysqli->query("SELECT * FROM game_user WHERE center_user = '".$userInfo['open_id']."'");
            if($rs->num_rows > 0){
                $user = $rs->fetch_assoc();
                $gold = $user['gold'];
                $game_user = $user['username'];
            }else{
                //创建账号
                $gold = 0;
                $game_user = 'g_'.md5(uniqid(mt_rand(), true));
                $this->mysqli->query("INSERT INTO game_user (username, gold, center_user) VALUES ('".$game_user."','".$gold."','".$userInfo['open_id']."')");
            }
            $_SESSION['username'] = $game_user;
            $_SESSION['gold'] = $gold;
            $_SESSION['message'] = '登录成功';

            return true;
        }

    }

    //这里设置成可用于多个服务器的,由GET参数提供
    $checkTicketUrl = isset($_GET['checkUrl'])?$_GET['checkUrl']:HG_CONFIG_CHECK_TICKET_URL;

    $dbConfig = array(
        'host' => GAME_DATABASE_HOST,
        'port' => GAME_DATABASE_PORT,
        'name' => GAME_DATABASE_NAME,
        'username' => GAME_DATABASE_USERNAME,
        'password' => GAME_DATABASE_PASSWORD,
    );
    $server = new gameServer($dbConfig);
    if(isset($_GET['action'])){
        switch ($_GET['action']) {
            case 'buyGold':
                $result = $server->generatePayData($_POST['itemId'], $_SESSION['username']);
                break;
            case 'confirm':
                $payData = array(
                    'game_key' => $_POST['game_key'],
                    'game_orderno' => $_POST['game_orderno'],
                    'orderno' => $_POST['orderno'],
                    'subject' => $_POST['subject'],
                    'description' => $_POST['description'],
                    'total_fee' => $_POST['total_fee'],
                    'signature' => $_POST['signature']
                );
                $result = $server->confirmPay($payData);
                break;
            case 'refresh':
                break;
            default:
                $result = array(
                    'code' => -10,
                    'message' => 'action参数不存在',
                    'showMessage' => '亲，您访问的网站不存在',
                    'data' => array()
                );
                break;
        }
        echo json_encode($result);
        exit;
    }

    if(isset($_GET['ticket'])){
        $result = $server->login();
        if($result!== true){
            echo json_encode($result);
            exit;
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>hGame游戏平台JS-SDK Demo </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <link rel="stylesheet" href="css/style.css?v=1.0">
</head>
<body ontouchstart="">
<div class="wxapi_container">
    <div class="wxapi_index_container">
        <ul class="label_box lbox_close wxapi_index_list">
            <li class="label_item wxapi_index_item"><a class="label_inner" href="#menuPay">支付接口</a></li>
            <li class="label_item wxapi_index_item"><a class="label_inner" href="#menuShare">分享接口</a></li>
            <li class="label_item wxapi_index_item"><a class="label_inner" href="#menuReport">上报接口</a></li>
        </ul>
    </div>
    <div class="lbox_close wxapi_form">
        <h3 id="menuCurrent">当前用户: <span id="currentUser"><?php echo substr($_SESSION['username'],0, 7);?></span></h3>
        <div id="menuPay" class="inter-block">
            <h3 id="menuPay">支付接口 当前的金币：<span id="currentGold"><?php echo $_SESSION['gold'];?></span></h3>
            <span class="desc">小额支付，1分钱购买1个金币</span>
            <button class="btn btn_primary payBtn" id="item01">hGame.pay (1个金币)</button>
            <span class="desc">中额支付，1角钱购买10个金币</span>
            <button class="btn btn_primary payBtn" id="item02">hGame.pay (10个金币)</button>
            <span class="desc">大额支付，10元钱购买1000个金币</span>
            <button class="btn btn_primary payBtn" id="item03">hGame.pay (1000个金币)</button>
        </div>
        <div id="menuShare" class="inter-block" style="display: none">
            <h3>分享接口</h3>
            <button class="btn btn_primary shareBtn" id="startRecord">hGame.share (分享)</button>
        </div>
        <div id="menuReport" class="inter-block" style="display: none">
            <h3>上报接口</h3>
            <span class="desc">创建角色时上报</span>
            <button class="btn btn_primary reportBtn" action="createRole">hGame.gameReport (创建角色)</button>
            <span class="desc">登录进入游戏时上报</span>
            <button class="btn btn_primary reportBtn" action="enterGame">hGame.gameReport (登录)</button>
            <span class="desc">用户角色等级变化时上报</span>
            <button class="btn btn_primary reportBtn" action="levelUpgrade">hGame.gameReport (等级变化)</button>
            <span class="desc">用户角色游戏进度变化时上报</span>
            <button class="btn btn_primary reportBtn" action="processReport">hGame.gameReport (进度变化)</button>
            <span class="desc">用户角色游戏成绩（分数）上报，可用于排行榜</span>
            <button class="btn btn_primary reportBtn" action="scoreReport">hGame.gameReport (成绩变化)</button>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/hgame-sdk.js"></script>
<script type="text/javascript" src="js/demo.js"></script>
<script type="text/javascript">
    var baseData = {
        "game_key":  '<?php echo HG_CONFIG_GAME_KEY; ?>',       //游戏平台提供的game_key
        "timestamp":  <?php echo time(); ?>,                    //时间戳
        "role": '<?php echo $_SESSION['username'];?>',          //游戏角色的唯一ID
        "area": '1区',                                          //游戏区标志
        "group": '1服'                                          //游戏服务器标志
    };
    var hGame = new hGame({
        "game_key": 'demo-game-1'
    });

</script>
</body>
</html>
