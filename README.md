# phabricator-oauth-adaptor
php oauth login adaptor for any php applications


// test.php

    include_once("authenticate.php");

    $redirectUrl = getenv('baseUrl').'/test.php';
    $clientId = getenv('clientId');
    $clientSecret = getenv('clientSecret');
    $isLogout = !empty($_GET['logout']);
    $user = authenticate($clientId, $clientSecret, $redirectUrl, $isLogout);



