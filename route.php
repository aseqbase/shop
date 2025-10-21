<?php
// To unset the default router sat at the bottom layers
\_::$Router->On()->Reset();

/**
 * Use your routers by below formats
 * \_::$Router->On("A Part Of Path?")->Default("Route Name");
 */
\_::$Router->On("cart")->Default("cart");
\_::$Router->On("(item|merchandise)s")->Default("merchandises");
\_::$Router->On("item|merchandise")->Default("merchandise");

// To route other requests to the DefaultRouteName
\_::$Router->On()->Default(\_::$Router->DefaultRouteName);
?>