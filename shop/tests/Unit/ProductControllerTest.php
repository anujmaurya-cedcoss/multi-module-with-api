<?php

declare(strict_types=1);

namespace Tests\Unit;

use MyApp\Controller\ProductsController;

class ProductControllerTest extends AbstractUnitTest
{
    public function testUpdateAction()
    {
        $arr = [
            "id" => 2,
            "name" => "update via test1",
            "price" => 150
        ];
        $product = new ProductsController();
        $result = $product->updateAction($arr);
        $this->assertTrue($result > 0);
    }
}
