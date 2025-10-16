<?php
// To unset the default router sat at the bottom layers
\_::$Aseq->On()->Reset();

/**
 * Use your routers by below formats
 * \_::$Aseq->On("A Part Of Path?")->Default("Route Name");
 */
\_::$Aseq->On("cart")->Default("cart");
\_::$Aseq->On("(item|merchandise)s")->Default("merchandises");
\_::$Aseq->On("item|merchandise")->Default("merchandise");

// To route other requests to the DefaultRouteName
\_::$Aseq->On()->Default(\_::$Config->DefaultRouteName);
?>