<?php
// To unset the default router sat at the bottom layers
\_::$Back->Router->On()->Reset();

/**
 * Use your routers by below formats
 * \_::$Back->Router->On("A Part Of Path?")->Default("Route Name");
 */
\_::$Back->Router->On("cart")->Default("cart");
\_::$Back->Router->On("(item|merchandise)s")->Default("merchandises");
\_::$Back->Router->On("item|merchandise")->Default("merchandise");

// To route other requests to the DefaultRouteName
\_::$Back->Router->On()->Default(\_::$Config->DefaultRouteName);
?>