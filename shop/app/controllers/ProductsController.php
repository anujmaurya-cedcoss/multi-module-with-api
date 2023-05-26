<?php
namespace MyApp\Controller;

use Phalcon\Mvc\Controller;

session_start();
class ProductsController extends Controller
{
    public function indexAction()
    {
        // redirected to view
        $this->response->redirect('/products/display');
    }

    public function displayAction()
    {
        // fetch all the data from products/get
        $ch = curl_init();
        $url = "http://192.168.80.4/products/get?bearer=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
        if (gettype($output) === 'string') {
            echo $output;
            die;
        }
        $this->view->data = $output;
    }

    public function editAction()
    {
        $id = $_GET['id'];
        // fetch all the data from products/get
        $ch = curl_init();
        $url = "http://192.168.80.4/products/$id?bearer=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
        if (gettype($output) === 'string') {
            echo $output;
            die;
        }
        $this->view->data = $output;
    }

    public function updateAction()
    {
        $id = $_GET['id'];
        $_POST['price'] = (int)$_POST['price'];
        $ch = curl_init();
        $url = "http://192.168.80.4/products/$id?bearer=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'put');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_POST));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
        if (gettype($output) === 'string') {
            echo $output;
            die;
        }
        $this->response->redirect('/products/display');
    }

    public function deleteAction()
    {
        $id = $_GET['id'];
        $ch = curl_init();
        $url = "http://192.168.80.4/products/$id?bearer=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'delete');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
        if (gettype($output) === 'string') {
            echo $output;
            die;
        }
        $this->response->redirect('/products/display');
    }

    public function addNewAction()
    {
        // redirected to view
    }
    public function addAction()
    {
        $_POST['id'] = (int)$_POST['id'];
        $_POST['price'] = (int)$_POST['price'];
        $ch = curl_init();
        $url = "http://192.168.80.4/products?bearer=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_POST));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        $this->response->redirect('/products/display');
    }
}
