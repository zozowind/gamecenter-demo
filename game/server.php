<?php  
session_start();
function randString($n){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for($i=0;$i<$n;$i++){
        $str.=$strPol[rand(0,$max)];
    }
    return $str;
}

function signTheData($data){
    ksort($data);
    foreach($data as $k=>$v){
        $tmp[] = $k.'='.$v;
    }
    $str = implode('&',$tmp);
    unset($data['game_secret']);
    $data['signature'] = sha1($str);

    return $data;
}
$mysqli = new mysqli('222.73.184.169', 'chenzhijie', 'chenzhijie', 'demo', '63306');
if(isset($_GET['ticket'])){
    //@ticket 登录后需要验证ticket
    $rs = $mysqli->query("SELECT * FROM game_user WHERE center_user = '".$_GET['ticket']."'");
    if($rs->num_rows > 0){
        $user = $rs->fetch_assoc();
        $gold = $user['gold'];
        $game_user = $user['username'];
    }else{
        //创建账号
        $gold = 0;
        $game_user = 'g_'.md5(uniqid(mt_rand(), true));
        $mysqli->query("INSERT INTO game_user (username, gold, center_user) VALUES ('".$game_user."','".$gold."','".$_GET['ticket']."')");
    }
    $_SESSION['username'] = $game_user;
    $_SESSION['gold'] = $gold;
    $_SESSION['message'] = '登录成功';
}
if(isset($_GET['action'])){
    switch ($_GET['action']) {
        case 'buyGold':
            //game_key可以储存在服务端或由客户端传送
            //open_id由当前用户查询数据库获得
            //game_pay_fee和subject由itemId获得
            $item = array(
                'item001' => array('fee'=>0.01, 'subject'=>'10个金币', 'body'=>'10个游戏金币'),
                'item002' => array('fee'=>0.02, 'subject'=>'20个金币', 'body'=>'20个游戏金币'),
            );
            if(!isset($item[$_POST['itemId']])){
                echo json_encode(array('code'=>'0','message'=>'商品'.$_POST['itemId'].'不存在'));
                exit;
            }
            $rs = $mysqli->query("SELECT * FROM game_user WHERE username = '".$_SESSION['username']."'");
            if($rs->num_rows > 0){
                $user = $rs->fetch_assoc();
            }else{
                echo json_encode(array('code'=>'0','message'=>'用户不存在'));
                $mysqli->close();
                exit;
            }
            $data = array(
                "game_key"  =>'demo-game-2',
                "open_id"   =>$user['center_user'],
                "game_pay_fee" => $item[$_POST['itemId']]['fee'],
                "game_pay_order" => 'order_'.time(),
                "game_pay_subject"   => $item[$_POST['itemId']]['subject'],
                "game_pay_body"      => $item[$_POST['itemId']]['body'],
                "notify_url"=> ''
            );
            //添加时间戳
            $data['timestamp'] = time();
            //生成随机数
            $data['nonce'] = randString(10);
            $data['game_secret'] = 'demo-game-2-secret';
            $data = signTheData($data);
            //优先支付方式可以由客户端选择也可以由服务端指定
            echo json_encode(array('code'=>'0','message'=>'success','data'=>array('payInfo'=>$data,'payType'=>'alipay_wap')));
            $mysqli->close();
            exit;
            break;
        default:
            break;
    }
}
$mysqli->close();
header('Location:demo2.php');
exit;
