# phabricator-oauth-adaptor
php oauth login adaptor for any php applications

![image](https://user-images.githubusercontent.com/4509346/143194950-d1355d76-91c8-433a-8a99-ba3b1fb93416.png)
![image](https://user-images.githubusercontent.com/4509346/143195005-db8a899e-d551-46a8-a5e8-823715350828.png)


"Client PHID" is the `$clientId`

By clicking on "Show application secret", `$clientSecret` can be copied


### test.php
```php
include_once("authenticate.php");

$redirectUrl = getenv('baseUrl').'/test.php';
$clientId = getenv('clientId');
$clientSecret = getenv('clientSecret');
$isLogout = !empty($_GET['logout']);
$user = authenticate($clientId, $clientSecret, $redirectUrl, $isLogout);

if (!$user) {
    //  authentication configuration error
} else if ($user && isset($user->login_url)) {
    // show login page with link to $user->login_url
} else {
    // login success with below data
    $user->full_name;
    $user->avatar_url;
}
```
