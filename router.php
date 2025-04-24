<?php
// To unset the default router sat at the bottom layers
\_::$Back->Router->Route->Reset();

/**
 * Use your routers by below formats
 * \_::$Back->Router->Route("A Part Of Path?")->Default("Route Name");
 */
\_::$Back->Router->Route("cart")->Default("cart");
\_::$Back->Router->Route("(item|merchandise)s")->Default("merchandises");
\_::$Back->Router->Route("item|merchandise")->Default("merchandise");

// To route other requests to the DefaultRouteName
\_::$Back->Router->Route->Default(\_::$Config->DefaultRouteName);
?>