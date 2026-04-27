<?php

use MiMFa\Library\Struct;

(new Router())
    ->On(\_::$Joint->Shop->ItemUrlPath)->Default("content", [
            "Compute" => [
                "ComputeName" => "shop/content/merchandise",
                "ContentTable" => table("Content"),
                "MerchandiseTable" => table("Merchandise"),
                "RequestTable" => table("Request"),
            ],
            "View" => [
                "Part" => "shop/content/merchandise",
                "DefaultTitle" => \_::$Joint->Shop->ItemDefaultTitle,
                "DefaultDescription" => \_::$Joint->Shop->ItemDefaultDescription,
                "Root" => \_::$Joint->Shop->ItemRootUrlPath,
                "CollectionRoot" => \_::$Joint->Shop->ItemsRootUrlPath,
                "CheckAccess" => function ($item) {
                    return \_::$User->HasAccess(\_::$User->AdminAccess) || \_::$User->HasAccess(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$User->VisitAccess))));
                }
            ],
            "ErrorHandler" => "Could not find related merchandise"
        ])
    ->On(\_::$Joint->Shop->ItemsUrlPath)->Default("contents", [
            "Compute" => [
                "ComputeName" => "shop/content/merchandises",
                "ContentTable" => $ctable = table("Content"),
                "MerchandiseTable" => table("Merchandise"),
                "RequestTable" => table("Request"),
                "Order" => "$ctable->Name.`UpdateTime` DESC"
            ],
            "View" => [
                "Part" => "shop/content/merchandises",
                "Root" => \_::$Joint->Shop->ItemRootUrlPath,
                "CollectionRoot" => \_::$Joint->Shop->ItemsRootUrlPath,
                "Title" => \_::$Joint->Shop->ItemsTitle,
                "Description" => \_::$Joint->Shop->ItemsDescription,
                "Image" => "box",
                "CheckAccess" => function ($item) {
                    return \_::$User->HasAccess(\_::$User->AdminAccess) || \_::$User->HasAccess(\MiMFa\Library\Convert::ToSequence(\MiMFa\Library\Convert::FromJson(getValid($item, 'Access', \_::$User->VisitAccess))));
                }
            ],
            "ErrorHandler" => "Could not find related merchandise"
        ])
    ->On(\_::$Joint->Shop->PaymentUrlPath . "(?!/)")
        ->Put(fn() => deliver(compute("shop/request/update", receive())))
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => "shop/payment"]))
    ->On(\_::$Joint->Shop->DiscountUrlPath . "(?!/)")
        ->Put(function () {
            $msg = null;
            return \_::$Joint->Shop->SetDiscountCode(receivePut("DiscountCode"), $msg) ? deliverRedirect($msg) : deliver($msg, 400);
        })
        ->Delete(fn() => \_::$Joint->Shop->PopDiscountCode() ? redirect() : null)
    ->On(\_::$Joint->Shop->OptionsUrlPath . "(?!/)")
        ->Post(function () {
            if (!\_::$User->HasAccess(\_::$User->UserAccess))
                return error("You don't have enough access!");
            $received = receivePost();
            $address = [];
            if ($v = get($received, "Email"))
                \_::$User->SetMetaValue("Email", $v);
            if ($v = get($received, "Contact"))
                \_::$User->SetMetaValue("Contact", $v);

            if ($v = get($received, "Address")) {
                \_::$User->SetMetaValue("Address", $v);
                $address[] = $v;
            }
            if ($v = get($received, "PostalCode")) {
                \_::$User->SetMetaValue("PostalCode", $v);
                $address[] = PHP_EOL . $v;
            }
            if ($v = get($received, "City")) {
                \_::$User->SetMetaValue("City", $v);
                $address[] = PHP_EOL . $v;
            }
            if ($v = get($received, "Province")) {
                \_::$User->SetMetaValue("Province", $v);
                $address[] = PHP_EOL . $v;
            }
            if ($v = get($received, "Country")) {
                \_::$User->SetMetaValue("Country", $v);
                $address[] = PHP_EOL . $v;
            }

            \_::$User->SetMetaValue("CartDescription", get($received, "Description"));

            if ($address)
                $address = join(", ", $address);
            else
                $address = null;
            $uaddress = \_::$User->GetValue("Address");
            if ($address && !$uaddress)
                \_::$User->Set(["Address" => get($received, "Address")]);
            $r = compute("shop/request/update-physicals", ["Address" => $address, "Contact" => get($received, "Contact")]) === null ? null : true;
            $r = compute("shop/request/update-digitals", ["Address" => get($received, "Email"), "Contact" => get($received, "Contact")]) === null ? $r : true;
            if ($r)
                return deliverRedirect(Struct::Success("Your requests data updated successfully!"), get($received, "Next"));
            else
                return error("Could not update your requests data!");
        })
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => "shop/options"]))
    ->On(\_::$Joint->Shop->RequestsUrlPath . "(?!/)")
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => "shop/requests"]))
    ->On(\_::$Joint->Shop->ResponsesUrlPath . "(?!/)")
        ->Patch(fn() => compute("shop/response/response"))
    ->On(\_::$Joint->Shop->GroupsUrlPath . "(?!/)")
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => "shop/groups"]))
    ->On(\_::$Joint->Shop->CartUrlPath . "(?!/)")
        ->Put(fn() => deliver(compute("shop/request/add", receive())))
        ->Patch(fn() => deliver(compute("shop/request/update", receive())))
        ->Delete(fn() => deliver(compute("shop/request/remove", receive())))
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => "shop/cart"]))
    ->On(\_::$Joint->Shop->RootUrlPath . ".*")
        ->Get(fn() => view(\_::$Front->DefaultViewName, ["Name" => \_::$Address->UrlRoute,]))
->Handle();