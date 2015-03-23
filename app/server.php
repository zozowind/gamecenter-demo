<?php
    session_start();
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
     * 获取当前用户模拟
     *
     * 此功能是WEB网页来模拟原生APP时使用，原生APP中此功能由SDK实现
     * preload.js通过调用获取当前用户接口，使用jsonp来进行调用
     */
    function getUser()
    {
        //获取用户数据
        $data = isset($_SESSION['username'])?array('app_user' => $_SESSION['username']):array();
        $callback = $_GET['callback'];
        echo $callback.'('.json_encode($data).')';
    }
