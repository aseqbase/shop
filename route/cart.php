<?php
use MiMFa\Library\Router;
(new Router())
->Route("cart/payment(?!/)")
    ->Put(fn() => \Res::Set(compute("request/update", \Req::Receive())))
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, ["Name" => "cart/payment"])
    )
->Route("cart/options(?!/)")
    ->Put(function() {
        if(!auth(\_::$Config->UserAccess)) return \Res::Error("You don't have enough access!");
        $received = \Req::ReceivePut();
        $address = [];
        if($v = get($received, "Email"))
            \_::$Back->User->SetMetaValue("Email", $v);
            if($v = get($received, "Contact"))
            \_::$Back->User->SetMetaValue("Contact", $v);

        if($v = get($received, "Address")) {
            \_::$Back->User->SetMetaValue("Address", $v);
            $address[] = $v;
        }
        if($v = get($received, "PostalCode"))  {
            \_::$Back->User->SetMetaValue("PostalCode", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "City"))  {
            \_::$Back->User->SetMetaValue("City", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "Province")) {
            \_::$Back->User->SetMetaValue("Province", $v);
            $address[] = PHP_EOL.$v;
        }
        if($v = get($received, "Country")) {
            \_::$Back->User->SetMetaValue("Country", $v);
            $address[] = PHP_EOL.$v;
        }
        if($address) $address = join(", ", $address);
        else $address = null;
        $uaddress = \_::$Back->User->GetValue("Address");
        if($address && !$uaddress) \_::$Back->User->Set(["Address"=>get($received, "Address")]);
        $r = compute("request/update-physicals", ["Address"=>$address, "Contact"=> get($received, "Contact")])===null?null:true;
        $r = compute("request/update-digitals", ["Address"=> get($received, "Email"), "Contact"=> get($received, "Contact")])===null?$r:true;
        if($r) return \Res::Success("Your requests data updated successfully!");
        else return \Res::Error("Could not update your requests data!");
    })
    ->Get(
    fn () =>
        view(\_::$Config->DefaultViewName, ["Name" => "cart/options"])
    )
->Route("cart/all(?!/)")
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/all"])
    )
->Route("cart/wish(?!/)")
    ->Put(fn() => \Res::Set(compute("request/add-wish", \Req::Receive())))
    ->Delete(fn() => \Res::Set(compute("request/remove-wish", \Req::Receive())))
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/wish"])
    )
->Route("(cart|(cart/current))(?!/)")
    ->Put(fn() => \Res::Set(compute("request/add", \Req::Receive())))
    ->Patch(fn() => \Res::Set(compute("request/update", \Req::Receive())))
    ->Delete(fn() => \Res::Set(compute("request/remove", \Req::Receive())))
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => "cart/current"])
    )
->Route("cart/.*")
    ->Get(
        fn () =>
            view(\_::$Config->DefaultViewName, ["Name" => \Req::$Direction,])
    )->Handle();
?>