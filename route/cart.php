<?php
use MiMFa\Library\Router;
(new Router())
->On("cart/payment(?!/)")
    ->Put(fn() => response(compute("request/update", receive())))
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, ["Name" => "cart/payment"])
    )
->On("cart/options(?!/)")
    ->Put(function() {
        if(!auth(\_::$Config->UserAccess)) return renderError("You don't have enough access!");
        $received = receivePut();
        $address = [];
        if($v = get($received, "Email"))
            \_::$User->SetMetaValue("Email", $v);
            if($v = get($received, "Contact"))
            \_::$User->SetMetaValue("Contact", $v);

        if($v = get($received, "Address")) {
            \_::$User->SetMetaValue("Address", $v);
            $address[] = $v;
        }
        if($v = get($received, "PostalCode"))  {
            \_::$User->SetMetaValue("PostalCode", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "City"))  {
            \_::$User->SetMetaValue("City", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "Province")) {
            \_::$User->SetMetaValue("Province", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "Country")) {
            \_::$User->SetMetaValue("Country", $v);
            $address[] = PHP_EOL.$v;
        }
        if($address) $address = join(", ", $address);
        else $address = null;
        $uaddress = \_::$User->GetValue("Address");
        if($address && !$uaddress) \_::$User->Set(["Address"=>get($received, "Address")]);
        $r = compute("request/update-physicals", ["Address"=>$address, "Contact"=> get($received, "Contact")])===null?null:true;
        $r = compute("request/update-digitals", ["Address"=> get($received, "Email"), "Contact"=> get($received, "Contact")])===null?$r:true;
        if($r) return renderSuccess("Your requests data updated successfully!");
        else return renderError("Could not update your requests data!");
    })
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, ["Name" => "cart/options"])
    )
->On("cart/all(?!/)")
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/all"])
    )
->On("cart/wish(?!/)")
    ->Put(fn() => response(compute("request/add-wish", receive())))
    ->Delete(fn() => response(compute("request/remove-wish", receive())))
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/wish"])
    )
->On("(cart|(cart/current))(?!/)")
    ->Put(fn() => response(compute("request/add", receive())))
    ->Patch(fn() => response(compute("request/update", receive())))
    ->Delete(fn() => response(compute("request/remove", receive())))
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/current"])
    )
->On("cart/.*")
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => \_::$Direction,])
    )->Handle();
?>