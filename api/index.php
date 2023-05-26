<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Collection\Manager;
use Phalcon\Security\JWT\Token\Parser;

session_start();

define("BASE_PATH", (__DIR__));
require_once(BASE_PATH . '/vendor/autoload.php');
require_once BASE_PATH . '/acl.php';

// Use Loader() to autoload our model
$container = new FactoryDefault();
$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://root:VajsFVXK36vxh4M6@cluster0.nwpyx9q.mongodb.net/?retryWrites=true&w=majority'
        );
        return $mongo->api_store;
    },
    true
);

$container->set(
    'collectionManager',
    function () {
        return new Manager();
    }
);

$app = new Micro($container);

// Retrieves all products
$app->get(
    '/products/get',
    function () {
        $limit = 10;
        $page = 0;
        if (isset($_GET['per_page']) && $_GET['per_page'] > 0) {
            $limit = $_GET['per_page'];
        }
        if (isset($_GET['page']) && $_GET['page'] > 0) {
            $page = $_GET['page'];
        }
        $collection = $this->mongo->products;
        $productList = $collection->find([], ['limit' => (int) $limit, 'skip' => (int) $page * $limit]);
        $data = [];
        foreach ($productList as $product) {
            $data[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price']
            ];
        }
        echo json_encode($data);
    }
);

// Searches for products with $name in their name
$app->get(
    '/products/search/{name}',
    function ($name) {
        $collection = $this->mongo->products;
        $nameList = explode('%20', $name);
        foreach ($nameList as $name) {
            $productList[] = $collection->find(array('name' => array('$regex' => $name)));
        }
        $data = [];
        foreach ($productList as $productL) {
            foreach ($productL as $product) {
                $data[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price']
                ];
            }
        }
        echo json_encode($data);
    }
);

// Retrieves products based on key
$app->get(
    '/products/{id:[0-9]+}',
    function ($id) {
        $collection = $this->mongo->products;
        $product = $collection->findOne(["id" => (int)$id]);
        echo json_encode($product);
    }
);

// update the product
$app->put(
    '/products/{id:[0-9]+}',
    function ($id) use ($app) {
        $product = $app->request->getJsonRawBody();
        $result =  $this->mongo->products->updateOne(
            ['id' => (int) $id],
            ['$set' => ['name' => $product->name, 'price' => $product->price]]
        );
        return $result->getModifiedCount();
    }
);

// Deletes product based on primary key
$app->delete(
    '/products/{id:[0-9]+}',
    function ($id) {
        return $this->mongo->products->deleteOne(["id" => (int) $id]);
    }
);

// Adds a new product
$app->post(
    '/products',
    function () use ($app) {
        $product = $app->request->getJsonRawBody();
        $collection = $this->mongo->products;
        $arr = [
            "id" => $product->id,
            "name" => $product->name,
            "price" => $product->price,
        ];
        return $collection->insertOne($arr);
    }
);

$app->post(
    '/register',
    function () {
        $collection = $this->mongo->users;
        $data = $_POST;
        return $collection->insertOne($data);
    }
);

$app->post(
    '/login',
    function () {
        $collection = $this->mongo->users;
        $credentials = $_POST;
        $admin = $credentials['admin'];
        $user = $collection->findOne(['mail' => $_POST['mail']], ['password' => $_POST['pass']]);
        $token = $user->token;
        $parser = new Parser();
        $tokenObject = $parser->parse($token);
        $role = $tokenObject->getClaims()->getPayload()['sub'];
        if ($admin) {
            if ($role == 'admin') {
                $_SESSION['role'] = 'admin';
                return $_SESSION['role'];
            }
        } else {
            if ($user->mail == $_POST['mail'] && $user->mail != '') {
                $_SESSION['role'] = 'user';
                return $_SESSION['role'];
            }
        }
        return "false";
    }
);

$app->post(
    '/order/create',
    function () {
        $payload = [
            "name" => $_POST['name'],
            "address" => $_POST['address'],
            "product_id" => $_POST['product'],
            "quantity" => $_POST['quantity'],
            "status" => "placed",
            "order_id" => uniqid()
        ];
        $collection = $this->mongo->orders;
        $status = $collection->insertOne($payload);
        var_dump($status);
    }
);

$app->put(
    '/order/update',
    function () use ($app) {
        $order = $app->request->getJsonRawBody();
        $id = $order->id;
        $status = $order->status;
        $response = $this->mongo->orders->updateOne(["order_id" => (string) $id], ['$set' => ['status' => $status]]);
        var_dump($response);
    }
);

// Retrieves all orders
$app->get(
    '/orders/get',
    function () {
        $collection = $this->mongo->orders;
        $orderList = $collection->find();
        $data = [];
        foreach ($orderList as $order) {
            $data[] = [
                'name' => $order['name'],
                'address' => $order['address'],
                'quantity' => $order['quantity'],
                'status' => $order['status'],
                'order_id' => $order['order_id'],
                'product_id' => $order['product_id']
            ];
        }
        echo json_encode($data);
    }
);


// checking for access
$app->before(
    function () {
        $role = $_GET['bearer'];
        $arr = explode('/', $_GET['_url']);
        $access = App\Acl\checkAccess($role, $arr[1], $arr[2]);
        if (!$access) {
            echo "<h1>Not allowed !</h1>";
            die;
        }
    }
);

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo '<h1>This is crazy, but this page was not found!</h1>';
});

$app->handle($_SERVER['REQUEST_URI']);
