<?php

declare(strict_types=1);

namespace Tests\Unit;

use MyApp\Controller\IndexController;

class IndexControllerTest extends AbstractUnitTest
{
    public function testdoLoginAction()
    {
        $arr = [
            "mail" => "anuj@mail.com",
            "pass" => "pass",
            "admin" => true
        ];
        $user = new IndexController();
        $result = $user->doLoginAction($arr);
        $this->assertEquals($result, 'admin');

        $arr = [
            "mail" => "ayush@mail.com",
            "pass" => "pass",
            "admin" => true
        ];
        $user = new IndexController();
        $result = $user->doLoginAction($arr);
        $this->assertEquals($result, 'admin');

        $arr = [
            "mail" => "satyam@mail.com",
            "pass" => "pass",
            "admin" => true
        ];
        $user = new IndexController();
        $result = $user->doLoginAction($arr);
        $this->assertEquals($result, 'admin');

        $arr = [
            "mail" => "user@mail.com",
            "pass" => "pass",
            "admin" => false
        ];
        $user = new IndexController();
        $result = $user->doLoginAction($arr);
        $this->assertEquals($result, 'user');
    }
}
