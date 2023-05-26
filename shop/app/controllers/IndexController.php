<?php

namespace MyApp\Controller;

use Phalcon\Mvc\Controller;

// session_start();
class IndexController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }
    // send $_POST in POSTFields on line 21
    public function doLoginAction($arr)
    {
        $ch = curl_init();
        $url = "http://192.168.80.4/login?bearer=admin";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
        if ($output == 'admin') {
            $_SESSION['role'] = 'admin';
            $this->response->redirect('/orders/display');
        } elseif ($output == 'user') {
            $_SESSION['role'] = 'user';
            $this->response->redirect('/orders/create');
        } else {
            // authentication failed
            $this->response->redirect('/index/');
        }
    }

    public function logoutAction() {
        session_unset();
        session_destroy();
        $this->response->redirect('/index');
    }
}
