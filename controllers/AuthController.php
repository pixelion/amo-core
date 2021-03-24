<?php

namespace Pixelion\AmoCrm\controllers;

use AmoCRM\Exception;
use AmoCRM\Models\Lead;
use Pixelion\AmoCrm\components\AmoCore;
use Pixelion\AmoCrm\models\AccountUser;
use Pixelion\AmoCrm\models\Leads;
use Pixelion\AmoCrm\models\Note;
use Pixelion\AmoCrm\models\Pipeline;
use Pixelion\AmoCrm\models\PipelineStatues;
use Pixelion\AmoCrm\models\Timeline;
use Pixelion\AmoCrm\models\Token;
use Yii;
use Pixelion\AmoCrm\models\Task;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;

define('TOKEN_FILE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');

class AuthController extends \yii\rest\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    public function actionAuth2()
    {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://pixelion.amocrm.ru/oauth2/access_token')
            ->setData([
                'redirect_uri'=>'https://amocrm.pixelion.com.ua/web/token',
                'grant_type'=>'authorization_code',
                'client_id' => '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee',
                'client_secret' => '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr',
                'code'=>'def502001e1ad6cb72b02123dcfd5d375d5945efc9f46ed073177dab8a7062561a108cb1a5ccf75c99c715ccd20b2d654c05c55ab5e83ae212ed1c1265655df94354915bbf5ebb6a476ae96c31296e386605fa3cd24f6d75c492fcfc091bef481641e914d070fa0d905fccd9be887f6edd8722d2c68e6e8c80ba7b6efe720fa342c614c23eb66d683a4dff23912640fc8d54f9637e199157a8b1c134d68c936c32c4c5d3a728364308a445529d207d0d9c3c21ea2f91de7928663c769673000277b3438a85291c35f89272c7d1b74cc3b79386eb80e39790e6ed527ebc956cb8df312e30d85092a0237b12345a32f0a31babd4195e07007f38e122154088976c683e20e88c405484977a61107484463d2b86d5dd82cd0084ecd1929813267a2f3fd6a4d68540f6f0d7dc00249cdf9fe7b0a0ae31718d4663c1d7692467c8cbf72dbe7d0be1d123293af0328e777491cb893635bfe563b8375f7c348d7aaf96407d4765f12b17be3740090e2d64af04d81bf4d7c8097a65e04ee4ba5834df7da0544ea56305fa513f6b51e3255d23e74a3e85550d494d6e3ac250b1c120f5b729d083d002513bd104c521c2ea1f9f2a651ba64033b3451381a8b8a605f625b7e7923978f2f342976d20b223df9380d11ea35d15c4'
            ])
            ->send();
        if ($response->isOk) {
            print_r($response->data);
            $newUserId = $response->data;
        }else{
            echo 'ERROR';
            print_r($response->data);
        }


        die;
    }

    public function beforeAction($action)
    {

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionToken()
    {
        $clientId = '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee';
        $clientSecret = '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr';
        $redirectUri = 'https://amocrm.pixelion.com.ua/web/token';

        $apiClient = new \AmoCRM\Client\AmoCRMApiClient(
            $clientId,
            $clientSecret,
            $redirectUri);


        if (isset($_GET['referer'])) {
            $apiClient->setAccountBaseDomain($_GET['referer']);
        }


        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
            if (isset($_GET['button'])) {

                return $apiClient->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
                // die;
            } else {

                $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            }
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

             unset($_SESSION['oauth2state']);

            exit('Invalid state');
        }

        /**
         * Ловим обратный код
         */
        try {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            die((string)$e);
        }

        $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

        printf('Hello, %s!', $ownerDetails->getName());
        die;
    }


    public function actionAuth()
    {
        $clientId = '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee';
        $clientSecret = '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr';
        $redirectUri = 'https://amocrm.pixelion.com.ua/web/token';

        $apiClient = new \AmoCRM\Client\AmoCRMApiClient(
            $clientId,
            $clientSecret,
            $redirectUri);


        $accessToken = $this->getToken();

        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );
        print_r($apiClient);
        die;
    }


    private function saveToken($accessToken)
    {

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents(TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    /**
     * @return AccessToken
     */
    private function getToken()
    {

        if (!file_exists(TOKEN_FILE)) {
            exit('Access token file not found');
        }

        $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
}