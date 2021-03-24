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

class DefaultController extends \yii\rest\Controller
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

    public function actionTest()
    {
        $client = new Client();
        /*$response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://pixelion.amocrm.ru/api/v4/events')
            ->setData(['limit' => 100, 'page' => 1])
            ->send();
        if ($response->isOk) {
            print_r($response->data);
            die;
            $newUserId = $response->data;
        } else {
            print_r($response->data);
            die;
        }*/

        /*        $clientId = '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee';
                $clientSecret = '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr';
                $redirectUri = 'https://amocrm.pixelion.com.ua/web/token';

        $tok = Token::findOne(1);


                $apiClient = new \AmoCRM\Client\AmoCRMApiClient(
                    $clientId,
                    $clientSecret,
                    $redirectUri);

                $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode('def50200eabdc63f6fba4e2b3d6f7fd2431619eb8c8a7452804647318e0f2ce5966e1ebc6d65f79a571bde56a502ef8f42a5dd59499552c4ed377b58478746ffbdb6c75021e01d2fc73a7f7e1f245039123a00ca52c359e91306c001fcc089c9ba3bffb5687babbd926195743745f71294b43c70bbb25444f0e5e273f7754bb16521a665880f0a54491b8dc9ac5153df298a72d8b35e6e20e3d871280c3e4d18e048d563182bea9ae65301441040a6ab35f0da40ec6c2932128d1a7f9d9f0b670d4dcb6b2a62bf8037ff6421d406faa3d6014a27155550273fea9a5793d46a9eee287b47c5abdd55e02c68870f0ad98e44eb703088c594efedb910be25cfc44fbe4242fb3a2fd3a5a98f622c45c279f30b910e789a49854ddeaa21903d62651154f13a12bb37a93cb3ea4690e81a9ab3599e663262eacefdbad640d37af58429f4770c9c9e1fc982c8482caa29a9a949e26af31766e496a3ac4836e411f17fcd00d50772f938997eb22a621e2f3971fb2185aedd26d77b01e77f8aa5c3ca19d1803d1d9b5d1ef707b250c7854aa7accb6609bfb6139d70061b91f2aad7baf571377175e523010beba03cd392b7c251a08544508195e8fff9a850ed54fec28befc66c0d03965dc49397268b9f278b7b1d05098ec3');


                $baseDomain = 'pixelion';
                $apiClient->setAccessToken($accessToken)
                    ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
                    ->onAccessTokenRefresh(
                        function (\League\OAuth2\Client\Token\AccessTokenInterface $accessToken, string $baseDomain) {
                            saveToken(
                                [
                                    'accessToken' => $accessToken->getToken(),
                                    'refreshToken' => $accessToken->getRefreshToken(),
                                    'expires' => $accessToken->getExpires(),
                                    'baseDomain' => $baseDomain,
                                ]
                            );
                        });*/


        /*    $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://pixelion.amocrm.ru/oauth2/access_token')
                ->setData([
                    'redirect_uri'=>'https://amocrm.pixelion.com.ua/web/token',
                    'grant_type'=>'refresh_token',
          //  'dd'=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJkN2Q3YmFmNTg1MTVkODkxZDQ4YTUyYWM3NjZlMTE5MTc4ZTVjMzAzZDNmOTgxMjE4YzJhN2RlYmM3NWY3ZTdmN2Y5ODAyNGJiNzc0MDZhIn0.eyJhdWQiOiIxZDBmYjRhOS0xYmMwLTRjN2ItODk3ZS0yNDc2NjkwYjY3ZWUiLCJqdGkiOiIyZDdkN2JhZjU4NTE1ZDg5MWQ0OGE1MmFjNzY2ZTExOTE3OGU1YzMwM2QzZjk4MTIxOGMyYTdkZWJjNzVmN2U3ZjdmOTgwMjRiYjc3NDA2YSIsImlhdCI6MTYxNjE3MjI0NywibmJmIjoxNjE2MTcyMjQ3LCJleHAiOjE2MTYyNTg2NDcsInN1YiI6IjYxNTYyNTAiLCJhY2NvdW50X2lkIjoyODkxNDEzOSwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.DgPnDBqCwB3S1O1LZ62gXBqak08uKpFn09lp-17n2kUGWeLp-ndJgMa3QYLYqoUePJKRM6PtabQbi2TqN0J1pTp01JVNTNId9snFDciYEc2Q5H6PqqfSxuwzrj7jZk-CTHoi2WQrcGByxiAQy6_yj87fudaXI-C-4Bfn5kDZdANmhTPiIDsCSj1Bpsj4pHfgBFspqAppCWx5dJYRx7wY-4HTNLy4XTkEf4_sARPKnTaiNkxafiom0jYoE7cth65wgSmO5btAySQ0j5ukFtvc4kz1OWKE5lJrxo68fk6O63MYGiMKMQqjLTYlVzCPvxZj1Z_4rwXtCKZEtKnW0PoKHg [refresh_token] => def502006f28ec89b11b34cfd48690d76097f9f14a85c60f9878c45b8a48a1508ba58bb94b1fbc4e33b9cddf4af9a8d556077bb5a18405d20649e2bb87cbbfb04b11bafd1bee64d3205eee82a80ffef080d54b598e852c07da2301717c53a40fb7a587bd8df69f9cf115176f161dc4a6d703fb63043efd1d2401800c323caec1b32e6b1dab1ef28b8f33469d49753409a635d1c814ddc887ba20f3565244d9547e87b9a1f8ba0cecb8b668474fe8182eafc39b75a06db024bfe0c32bfad5953b27381f285cd23b7e8e88b182d41c089b8341c144b0b8f3266a928dd2124ffcf0a69b2f283629048b48293dc5e4d9adcb425783ffabd336a006ff25db74e6e2f6978505490d2aa2fe83a4fc78e543bad2b580f298ed002c8645474c8ca23ece36cd57fbb69fcb21241079882af943ce00ef389f96d2338a58e5ae99354be06411a18394db878bc1a9d78cb25d8ca08e29fce86dcacfff80ea76ab11beb52e2155a401a1a2ab928d8e71f5906a59027e5c55ddbee231e13d76bbfc3a6900a4312f202053214484b6227cb9393525fba2b7029a3335d7f50794b19419b7ade38d55339de3906b815d00aeaf87805acb208cc761bac02fa8a055f96748bd933eab5b24e3032a2b5ec47e8727'
                    'client_id' => '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee',
                    'client_secret' => '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr',
                   // 'code'=>'def50200f5bf006de696fc3a8cbe1d657b762700e137cf078252dbf5b0242cbe3d533fe091352040350b2b846be7a52a802f03214012e97b1b706c77d7e77116dda4a954b973b83291114bf2d4fac36edf04f631c49d2cadfb43500e273e6ebde60abb5c66087241629918eefe0a1db86fd39defaa109b9beb73bd487b4f6730b898e40e4b63961faa6cfcdfcd665843b68ccae2cf347a45b77fcab15676195b1884420826d8ffb8ec9acd2389dc934c29e3e10c0c9f8995b7d9f223cf87560d9aa385c2ca6810f61eae95461f375385cfbf0170d06325ca0ef4c9743ad4ad530f7e25503f7971d7d571c27e4987981132c77c8696b9cd55d860ec2e29a65e95fafde3009f4145b298674506d60e68547de2c892f32e1e20c730965bc05b953903b719fa2df9edda411c52fcc036ee1548857505826891f8af2c64c30f5430697a6ef5501115e9466e79f551050ba86f4171f910263856f9ba9f7ef5f9d6175b0ea47e485da73d1f1ef215abd57e7b7a1583945a33f3cc98b018fa2362fa89d45b7ae449ef0b8f51ddf533878b290d034b36e9cd6f027f6297214903e44a0e542aca30c17caeced0e641003f8fa9320ab1750dae9997cfe60d4ebe8de7af1d7b9f212989e5db1f3a0eb2c532e45ca37f42779614'
                ])
                ->send();
            if ($response->isOk) {
                print_r($response->data);die;
                $newUserId = $response->data;
            }else{
                echo 'ERROR2';
                print_r($response->data);die;
            }
            die;*/

//
//Array ( [token_type] => Bearer [expires_in] => 86400 [access_token] => eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJkN2Q3YmFmNTg1MTVkODkxZDQ4YTUyYWM3NjZlMTE5MTc4ZTVjMzAzZDNmOTgxMjE4YzJhN2RlYmM3NWY3ZTdmN2Y5ODAyNGJiNzc0MDZhIn0.eyJhdWQiOiIxZDBmYjRhOS0xYmMwLTRjN2ItODk3ZS0yNDc2NjkwYjY3ZWUiLCJqdGkiOiIyZDdkN2JhZjU4NTE1ZDg5MWQ0OGE1MmFjNzY2ZTExOTE3OGU1YzMwM2QzZjk4MTIxOGMyYTdkZWJjNzVmN2U3ZjdmOTgwMjRiYjc3NDA2YSIsImlhdCI6MTYxNjE3MjI0NywibmJmIjoxNjE2MTcyMjQ3LCJleHAiOjE2MTYyNTg2NDcsInN1YiI6IjYxNTYyNTAiLCJhY2NvdW50X2lkIjoyODkxNDEzOSwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.DgPnDBqCwB3S1O1LZ62gXBqak08uKpFn09lp-17n2kUGWeLp-ndJgMa3QYLYqoUePJKRM6PtabQbi2TqN0J1pTp01JVNTNId9snFDciYEc2Q5H6PqqfSxuwzrj7jZk-CTHoi2WQrcGByxiAQy6_yj87fudaXI-C-4Bfn5kDZdANmhTPiIDsCSj1Bpsj4pHfgBFspqAppCWx5dJYRx7wY-4HTNLy4XTkEf4_sARPKnTaiNkxafiom0jYoE7cth65wgSmO5btAySQ0j5ukFtvc4kz1OWKE5lJrxo68fk6O63MYGiMKMQqjLTYlVzCPvxZj1Z_4rwXtCKZEtKnW0PoKHg [refresh_token] => def502006f28ec89b11b34cfd48690d76097f9f14a85c60f9878c45b8a48a1508ba58bb94b1fbc4e33b9cddf4af9a8d556077bb5a18405d20649e2bb87cbbfb04b11bafd1bee64d3205eee82a80ffef080d54b598e852c07da2301717c53a40fb7a587bd8df69f9cf115176f161dc4a6d703fb63043efd1d2401800c323caec1b32e6b1dab1ef28b8f33469d49753409a635d1c814ddc887ba20f3565244d9547e87b9a1f8ba0cecb8b668474fe8182eafc39b75a06db024bfe0c32bfad5953b27381f285cd23b7e8e88b182d41c089b8341c144b0b8f3266a928dd2124ffcf0a69b2f283629048b48293dc5e4d9adcb425783ffabd336a006ff25db74e6e2f6978505490d2aa2fe83a4fc78e543bad2b580f298ed002c8645474c8ca23ece36cd57fbb69fcb21241079882af943ce00ef389f96d2338a58e5ae99354be06411a18394db878bc1a9d78cb25d8ca08e29fce86dcacfff80ea76ab11beb52e2155a401a1a2ab928d8e71f5906a59027e5c55ddbee231e13d76bbfc3a6900a4312f202053214484b6227cb9393525fba2b7029a3335d7f50794b19419b7ade38d55339de3906b815d00aeaf87805acb208cc761bac02fa8a055f96748bd933eab5b24e3032a2b5ec47e8727 )

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://pixelion.amocrm.ru/oauth2/access_token')
            ->setData([
                'redirect_uri' => 'https://amocrm.pixelion.com.ua/web/token',
                'grant_type' => 'authorization_code',
                'client_id' => '1d0fb4a9-1bc0-4c7b-897e-2476690b67ee',
                'client_secret' => '8nqhEekkyoXUtEDmABUqYxUAT5iOAYjxpfzhQzOmSRvcBJSpFWK9PjDd0fjj9vXr',
                'code' => 'def502001e1ad6cb72b02123dcfd5d375d5945efc9f46ed073177dab8a7062561a108cb1a5ccf75c99c715ccd20b2d654c05c55ab5e83ae212ed1c1265655df94354915bbf5ebb6a476ae96c31296e386605fa3cd24f6d75c492fcfc091bef481641e914d070fa0d905fccd9be887f6edd8722d2c68e6e8c80ba7b6efe720fa342c614c23eb66d683a4dff23912640fc8d54f9637e199157a8b1c134d68c936c32c4c5d3a728364308a445529d207d0d9c3c21ea2f91de7928663c769673000277b3438a85291c35f89272c7d1b74cc3b79386eb80e39790e6ed527ebc956cb8df312e30d85092a0237b12345a32f0a31babd4195e07007f38e122154088976c683e20e88c405484977a61107484463d2b86d5dd82cd0084ecd1929813267a2f3fd6a4d68540f6f0d7dc00249cdf9fe7b0a0ae31718d4663c1d7692467c8cbf72dbe7d0be1d123293af0328e777491cb893635bfe563b8375f7c348d7aaf96407d4765f12b17be3740090e2d64af04d81bf4d7c8097a65e04ee4ba5834df7da0544ea56305fa513f6b51e3255d23e74a3e85550d494d6e3ac250b1c120f5b729d083d002513bd104c521c2ea1f9f2a651ba64033b3451381a8b8a605f625b7e7923978f2f342976d20b223df9380d11ea35d15c4'
            ])
            ->send();
        if ($response->isOk) {
            print_r($response->data);
            $newUserId = $response->data;
        } else {
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


    public function actionDiff()
    {
        $array = Json::decode(file_get_contents(Yii::getAlias('@runtime/crm/') . 'lead_17626631_1616487371.json'));
        unset($array['created_at'], $array['updated_at'], $array['last_modified'], $array['date_create']);
        $model = Leads::findOne(['lead_id' => $array['id']]);
        // VarDumper::dump($model->getAttributesDiff(), 10, true);
        //VarDumper::dump($array, 10, true);


        $result = [];

        $custom_fields = [];
        foreach ($array['custom_fields'] as $cf) {
            $result['new']['custom_fields'][$cf['id']] = $cf;
        }
        $result['old']['custom_fields'] = Json::decode($model->custom_fields);

        $tags = [];
        foreach ($array['tags'] as $tag) {
            $result['new']['tags'][$tag['id']] = $tag;
        }

        foreach (Json::decode($model->tags) as $tag_old) {
            $result['old']['tags'][$tag_old['id']] = $tag_old;
        }
        // $result['old']['tags']=Json::decode($model->tags);


        $tagsDiff = $this->diffAssocRecursive($result['new'], $result['old']);


        VarDumper::dump($tagsDiff, 10, true);


        //VarDumper::dump($this->diffAssocRecursive($array, $model->getAttributesDiff()), 10, true);
        die;
    }


    public function diffAssocRecursive(array $array1, array $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!array_key_exists($key, $array2) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->diffAssocRecursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;


        try {
            $post = Yii::$app->request->post();


            if (empty($post)) {
                throw new Exception('Post is empty!');
            }
            file_put_contents(Yii::getAlias('@runtime/crm/') . 'gggg' . time() . '.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $firstKey = array_keys($post);
            if (isset($post['leads']['note']) || isset($post['contacts']['note'])) {
                $key = array_keys($post[$firstKey[0]]);
                $objects = $post[$firstKey[0]][$key[0]];
                // file_put_contents(Yii::getAlias('@runtime/crm/') . 'note'.time().'.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                foreach ($objects as $object) {
                    // if ($object['note']['created_by'] != 0) { //Igonre amobot
                    file_put_contents(Yii::getAlias('@runtime/crm/') . 'note' . time() . '.json', json_encode($object['note'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $note = Note::findOne(['note_id' => $object['note']['id']]);
                    if (!$note) {
                        $note = new Note;
                    }
                    $note->action = $firstKey[0] . '_' . $key[0];
                    $note->setAttributes($object['note'], false);
                    $note->save(false);
                    // }
                }
            }


            if (isset($post['task'])) {

                if (isset($post['task']['update']) || isset($post['task']['add']) || isset($post['task']['delete'])) {
                    $key = array_keys($post['task']);

                    $objects = $post['task'][$key[0]];
                    file_put_contents(Yii::getAlias('@runtime/crm/') . 'task.json', json_encode($objects));
                    foreach ($objects as $object) {
                        $model = Task::findOne(['task_id' => $object['id']]);
                        if (!$model) {
                            $model = new Task;
                        }
                        $model->action = 'task_' . $key[0];
                        //$model->tineline_date = $object;
                        $model->setAttributes($object, false);
                        $model->save(false);
                    }
                }


                // $response = new \Pixelion\AmoCrm\components\Task($post['task']);
            } elseif (isset($post['leads'])) {
                // $response = new \Pixelion\AmoCrm\components\Leads($post['leads']);


                if (isset($post['leads']['update']) || isset($post['leads']['add'])) {
                    $key = array_keys($post['leads']);

                    $objects = $post['leads'][$key[0]];
                    //  file_put_contents(Yii::getAlias('@runtime/crm/') . 'test2.json', json_encode($object));
                    foreach ($objects as $object) {
                        $model = Leads::findOne(['lead_id' => $object['id']]);
                        if (!$model) {
                            $model = new Leads;
                        }
                        if ($model)
                            file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_' . $object['id'] . '_' . time() . '.json', Json::encode($object, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $model->action = 'lead_' . $key[0];
                        //$model->tineline_date = $object;
                        $model->setAttributes($object, false);
                        $model->save(false);
                    }
                } elseif (isset($post['leads']['delete'])) {
                    $key = array_keys($post['leads']);
                    $objects = $post['leads'][$key[0]];
                    foreach ($objects as $object) {
                        $model2 = Leads::findOne(['lead_id' => $object['id']]);


                        $pipeline = Pipeline::findOne(['pipeline_id' => $object['pipeline_id']]);
                        $status = PipelineStatues::findOne(['status_id' => $object['status_id'], 'pipeline_id' => $object['pipeline_id']]);


                        $statusResult = '';
                        if ($status) {
                            $statusResult = $status->name;
                        }

                        $pipelineResult = '';
                        if ($pipeline) {
                            $pipelineResult = $pipeline->name;
                        }


                        if ($model2) {
                            $history = clone $model2;
                            $model2->delete();
                        }


                        $model = new Timeline();
                        $model->element_id = $object['id'];
                        $model->action = 'lead_deleted';
                        $model->pipeline_id = $object['pipeline_id'];

                        $responsible = AccountUser::findOne(['user_id' => $history->responsible_user_id]);

                        $model->result = 'Сделка удалена: #' . $object['id'] . ' ' . $pipelineResult . ' > ' . $statusResult . ' Ответственный: ' . $responsible->name . ' Бюджет: ' . $history->price;
                        $model->created_at = time();
                        $model->save(false);
                    }
                } elseif (isset($post['leads']['note'])) {

                    $key = array_keys($post['leads']);
                    $objects = $post['leads'][$key[0]];

                    foreach ($objects as $object) {
                        // file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_note'.time().'.json', json_encode($object['note'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $note = Note::findOne(['note_id' => $object['note']['id']]);
                        if (!$note) {
                            $note = new Note;
                        }
                        $note->action = 'lead_' . $key[0];
                        $note->setAttributes($object['note'], false);
                        //  $note->save(false);


                        /*$user = AccountUser::findOne(['user_id'=>$object['note']['modified_by']]);
                        if($user){
                            $result=$user->name.': ';
                        }

                        $model = new Timeline();
                        if (!in_array($object['note']['note_type'],[AmoCore::NOTE_DEAL_CREATED])) {
                            $model->element_id = $object['note']['element_id'];
                            $model->account_id = $object['note']['account_id'];
                            $model->responsible_user_id = $object['note']['main_user_id'];
                            $model->created_user_id = $object['note']['created_by'];
                            $model->modified_user_id = $object['note']['modified_by'];

                            if($object['note']['element_type'] == AmoCore::TYPE_LEAD){
                                $result.='Сделка'.$object['note']['element_id'];
                            }
                            $result.=$object['note']['text'];
                            $model->result = $result;
                            $model->created_at = $object['note']['created_at'];
                            $model->updated_at = $object['note']['updated_at'];
                            $model->action = 'lead_note';
                            $model->save(false);
                        }*/
                    }
                }


            } elseif (isset($post['contacts'])) {
                // $response = new \Pixelion\AmoCrm\components\Contacts($post['contacts']);
            } elseif (isset($post['message'])) {
                $response = new \Pixelion\AmoCrm\components\Message($post['message']);
            } elseif (isset($post['catalogs'])) {
                $response = new \Pixelion\AmoCrm\components\Catalogs($post['catalogs']);
            } else {

                $data = json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $model = new Timeline();
                $model->result = '!!!!!!!!Это дейстиве еще в разработке!!!!!!!!!';
                $model->action = 'ACTION_UNKNOWN';
                $model->params = json_encode($post, JSON_UNESCAPED_UNICODE);
                $model->save(false);


                file_put_contents(Yii::getAlias('@runtime/crm/') . 'unknown_resp.json', $data);
            }

            //
            $data = json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents(Yii::getAlias('@runtime/crm/') . 'resposonse2.json', $data);

            return [];
        } catch (Exception $e) {
            print_r($e);
        }


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


    public function actionTimeline()
    {
        $query = \Pixelion\AmoCrm\models\Timeline::find()->orderBy(['id' => SORT_DESC])->where(['is_show' => 0]);
        $q = clone $query;
        //$filterModel = new FilterModel;
        $post = Yii::$app->request->post('FilterModel');
        if (isset($post['pipeline_id']) && !empty($post['pipeline_id'])) {
            $query->andWhere(['pipeline_id' => $post['pipeline_id']]);
        }
        if (isset($post['owner_id']) && !empty($post['owner_id'])) {
            $query->andWhere(['modified_user_id' => $post['owner_id']]);
        }
        $result = [];
        $result['success'] = true;
        foreach ($q->all() as $item) {
            $item->is_show = 1;
            $item->save(false);
            $result['items'][] = [
                'html' => $this->renderAjax('@app/views/site/' . $item->getRowStyle(), ['item' => $item, 'isEven' => false])
            ];
        }
        return $this->asJson($result);
        return $this->renderAjax('timeline', ['items' => $query->all()]);
    }

}