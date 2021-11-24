<?php
session_start();
use GuzzleHttp\Client as GuzzleClient;
function authenticate($client_id, $client_secret, $redirect_url, $logout) {
    if (!empty($client_id) && !empty($client_secret) && !empty($redirect_url)) {
        $authservice_base_url = 'https://phabricator.localhost.com';
        $auth_redirect_url = "$authservice_base_url/oauthserver/auth?".http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_url,
            'response_type' => 'code',
            'state' => $_SERVER['QUERY_STRING']
        ]);
        if ($logout) {
            session_unset();
            return (object) ['login_url' => $auth_redirect_url];
        }
        $client = new GuzzleClient();
        if (!empty($_GET['code'])) {
            try {
                $response = $client->request('POST', "$authservice_base_url/oauthserver/token/", [
                    'headers' => [ 'content-type' => 'application/json' ],
                    'query' => [
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'code' => $_GET['code'],
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => $redirect_url
                    ],
                    'connect_timeout' => 3600,
                    'timeout' => 3600
                ]);
                $resp = json_decode($response->getBody()->getContents());
                $_SESSION['access_token'] = $resp->access_token;
                if (!empty($_GET['state'])) {
                    header('Location: '.$redirect_url.'?'.$_GET['state']);
                    die('hard');
                }
            } catch (GuzzleHttp\Exception\ClientException $e) {
                session_unset();
                header("Location: $auth_redirect_url");
                die($e->getResponse()->getBody(true));
            }
        }
        $authenticated = false;
        if (!empty($_SESSION['access_token'])) {
            try {
                $response = $client->request('GET', "$authservice_base_url/api/user.whoami?access_token=".$_SESSION['access_token'], [
                    'headers' => [ 'Authorization' => 'token '.$_SESSION['access_token'] ],
                    'connect_timeout' => 3600,
                    'timeout' => 3600
                ]);
                $_SESSION['user'] = $response->getBody()->getContents();
                $authenticated = true;
            } catch (GuzzleHttp\Exception\ClientException $e) {
                session_unset();
                die($_SESSION['access_token']);
            }
        }
        if (!$authenticated) {
            session_unset();
            return (object) ['login_url' => $auth_redirect_url];
        } else {
            $user = json_decode($_SESSION['user']);
            return (object) [
                'full_name' => $user->result->realName,
                'avatar_url' => $user->result->image
            ];
        }
    }
    return null;
}
