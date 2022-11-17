<?php $this->extends('layouts/products'); ?>
<h1>Product</h1>
<p>
    This is the product page for <?php echo $parameters['product']; ?>.
    <?php echo $this->escape($scary); ?>
</p>