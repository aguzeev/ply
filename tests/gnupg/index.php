<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
	
/*echo "just a test";
$res = gnupg_init();
gnupg_addencryptkey($res,"8660281B6051D071D94B5B230549F9DC851566DC");
$enc = gnupg_encrypt($res, "just a test");
echo $enc;*/


$gpg = new Crypt_GPG();
$gpg->addSignKey('test@example.com', 'test');
$signature = $gpg->signFile($filename, Crypt_GPG::SIGN_MODE_DETACHED);

echo "Package signature is: ", $signature, "\n";
?>