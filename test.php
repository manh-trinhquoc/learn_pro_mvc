<?php
$mem_var = new Memcache();
$mem_var->addServer("127.0.0.1", 11211);
$response = $mem_var->get("Bilbo");
if ($response) {
    echo $response;
} else {
    echo "Adding Keys (K) for Values (V), You can then grab Value (V) for your Key (K) \m/ (-_-) \m/ ";
    $mem_var->set("Bilbo", "Here s Your (Ring) Master stored in MemCached (^_^)") or die(" Keys Couldn't be Created : Bilbo Not Found :'( ");
}