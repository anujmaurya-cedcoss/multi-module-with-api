<?php
echo $this->tag->linkTo(['/orders/create', 'Place Order', 'class' => 'btn btn-primary ml-5 m-3']);
echo $this->tag->linkTo(['/orders/display', 'Show All Orders', 'class' => 'btn btn-primary m-3']);
echo $this->tag->linkTo(['/products', 'Show All Products', 'class' => 'btn btn-primary m-3']);
echo $this->tag->linkTo(['/products/addNew', 'Add New Product', 'class' => 'btn btn-primary m-3']);
echo $this->tag->linkTo(['/index/logout', 'LogOut', 'class' => 'd-flex float-right btn btn-primary m-3']);
