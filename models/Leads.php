<?php

namespace Pixelion\AmoCrm\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;


/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 */
class Leads extends ActiveRecord
{

    public $action;
    public $tineline_date;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_leads}}";
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['id'])) {
            $values['lead_id'] = $values['id'];
            if (is_array($values['custom_fields'])) {
                $reCF = [];
                foreach ($values['custom_fields'] as $item) {
                    $reCF[$item['id']] = $item;
                }
                $values['custom_fields'] = json_encode($reCF, JSON_UNESCAPED_UNICODE);
            }
            if (is_array($values['tags'])) {
                $reTags = [];
                foreach ($values['tags'] as $tag) {
                    $reTags[$tag['id']] = $tag['name'];
                }
                // $values['tags'] = json_encode($reTags, JSON_UNESCAPED_UNICODE);
                $values['tags'] = json_encode($values['tags'], JSON_UNESCAPED_UNICODE);
            }
            unset($values['id']);

        }

        parent::setAttributes($values, $safeOnly);

    }

    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {

        if ($insert) {

        }

        $params = [];
        $model = new Timeline();
        $model->element_id = $this->lead_id;
        $model->account_id = $this->account_id;
        $model->responsible_user_id = $this->responsible_user_id;
        $model->created_user_id = $this->created_user_id;
        $model->pipeline_id = $this->pipeline_id;
        $model->created_at = $this->created_at;
        $model->updated_at = $this->updated_at;
        $model->action = $this->action;


        Yii::info($this->checkChanged($changedAttributes), 'diffing');


        if ($this->checkChanged($changedAttributes)) {


            if ($this->action == 'lead_add') {
                $user = AccountUser::findOne(['user_id' => $this->created_user_id]);
                $pipeline = Pipeline::findOne(['pipeline_id' => $this->pipeline_id]);
                $status = PipelineStatues::findOne(['status_id' => $this->status_id, 'pipeline_id' => $this->pipeline_id]);
                $statusResult = '';
                if ($status) {
                    $statusResult = $status->name;
                }

                $pipelineResult = '';
                if ($pipeline) {
                    $pipelineResult = $pipeline->name;
                }

                if ($user) {
                    $model->owner_name = $user->name;
                    $model->owner_photo = $user->photo_url;
                    $model->result = $user->name . ': ' . $pipelineResult . ' &raquo; ' . $statusResult . ' Создал сделку ' . $this->lead_id . ' Бюджен ' . $this->price;
                } else {
                    $model->result = $this->created_user_id . ': Создал сделку ' . $this->lead_id . ' Бюджен ' . $this->price;
                }

            } elseif ($this->action == 'lead_update') {
                $model->modified_user_id = $this->modified_user_id;
                $user = AccountUser::findOne(['user_id' => $this->modified_user_id]);

                //changed after/before
                //if (isset($changedAttributes['price'])) {
                //    $before_values['price'] = $changedAttributes['price'];
                //    $after_values['price'] = $this->oldAttributes['price'];
                //}
                if (isset($changedAttributes['custom_fields'])) {
                    // $tagsDiff2 = $this->diffAssocRecursive(Json::decode($this->oldAttributes['custom_fields']), Json::decode($changedAttributes['custom_fields']));
                    $tagsDiff = $this->diffAssocRecursive(Json::decode($changedAttributes['custom_fields']), Json::decode($this->oldAttributes['custom_fields']));
                    // $tagsDiff = array_intersect(Json::decode($changedAttributes['custom_fields']),Json::decode($this->oldAttributes['custom_fields']));
                    //Yii::info($tagsDiff);
                    //Yii::info($tagsDiff2);
                    //Yii::info(Json::decode($changedAttributes['custom_fields']));
                    // Yii::info(Json::decode($this->oldAttributes['custom_fields']));

                    if ($tagsDiff) {
                        $params['changes']['after_custom_fields'] = Json::decode($this->oldAttributes['custom_fields']);
                        $params['changes']['before_custom_fields'] = $tagsDiff;
                    }
                }
                if ($before_values)
                    $model->before_values = Json::encode($before_values);
                if ($after_values)
                    $model->after_values = Json::encode($after_values);

                if ($user) {
                    $params['owner']['name'] = $user->name;
                    $params['owner']['id'] = $user->user_id;
                }
                if ($changedAttributes['price'] <> $this->price) {
                    $params['changes']['price'] = [
                        'after' => $this->price,
                        'before' => $changedAttributes['price']
                    ];
                }

                if ($before_values && $after_values) {
                    //$params['price']='sd';


                    //  $params['after_values']=$after_values;
                    //  $params['before_values']=$before_values;

                    $model->action = 'lead_update_diff';
                }


                Yii::info($changedAttributes, 'test');


                if ($user) {
                    $model->owner_name = $user->name;
                    $model->owner_photo = $user->photo_url;

                    $tagsResult = '';
                    // $currentTags = $this->tags;
                    if (isset($changedAttributes['tags'])) {

                        // $tags = Json::decode($this->tags);
                        foreach (Json::decode($this->tags) as $ot) {
                            $tags[$ot['id']] = $ot['name'];
                        }
                        // $oldtags = Json::decode($changedAttributes['tags']);
                        foreach (Json::decode($changedAttributes['tags']) as $o) {
                            $oldtags[$o['id']] = $o['name'];
                        }

                        // $test = array_walk_recursive(Json::decode($changedAttributes['tags']), function($item, $key) {
                        //     return [$item['id']=>$item['name']];
                        // });


                        if (count($tags) > count($oldtags)) { //new tags
                            $tagsDiff = array_diff($tags, $oldtags);

                            // $tagsResultItems = [];
                            // foreach ($tagsDiff as $item) {
                            //     $tagsResultItems[] = '<span class="tag">' . $item . '</span>';
                            //  }
                            $model->action = 'lead_tags_add';
                            $params['tags'] = $tagsDiff;
                            //  $tagsResult = '<br/>Таг добавлен ' . implode(' ', $tagsResultItems);

                        } elseif (count($oldtags) > count($tags)) { // remove tags
                            $tagsDiff = $this->diffAssocRecursive($oldtags, $tags);
                            // $tagsResultItems = [];
                            // foreach ($tagsDiff as $item) {
                            //     $tagsResultItems[] = '<span class="tag">' . $item . '</span>';
                            // }
                            Yii::info($tagsDiff);
                            $params['tags'] = $tagsDiff;
                            $model->action = 'lead_tags_deleted';
                            // $tagsResult = '<br/>Таг удален ' . implode(' ', $tagsResultItems);

                        } else {
                            Yii::info('no diff', '$tagsDiff2');
                        }
                        //  $model->action = 'lead_tags';

                    }

                    $model->result = '<strong>' . $user->name . '</strong>: Изменил сделку  <a href="https://pixelion.amocrm.ru/leads/detail/' . $this->lead_id . '">' . $this->lead_id . '</a> Бюджет ' . $this->price . '  ' . $tagsResult . '';
                } else {
                    //$model->result = $this->modified_user_id . ': Изменил сделку ' . $this->lead_id . ' Бюджен ' . $this->price;
                }
            }
            $model->params = Json::encode($params);
            $model->save(false);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    private function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }
        return $aReturn;
    }


    private function array_diff_recursive($arr1, $arr2)
    {
        $outputDiff = [];

        foreach ($arr1 as $key => $value) {
            //if the key exists in the second array, recursively call this function
            //if it is an array, otherwise check if the value is in arr2
            if (array_key_exists($key, $arr2)) {
                if (is_array($value)) {
                    $recursiveDiff[$key] = $this->array_diff_recursive($value, $arr2[$key]);

                    if (count($recursiveDiff[$key])) {
                        $outputDiff[$key] = $recursiveDiff[$key];
                    }
                } else if (!in_array($value, $arr2)) {
                    $outputDiff[$key] = $value;
                }
            }
            //if the key is not in the second array, check if the value is in
            //the second array (this is a quirk of how array_diff works)
            else if (!in_array($value, $arr2)) {
                $outputDiff[$key] = $value;
            }
        }

        return $outputDiff;
    }


    private function diff_recursive($array1, $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value) && isset($array2[$key])) { // it's an array and both have the key
                $new_diff = $this->diff_recursive($value, $array2[$key]);
                if (!empty($new_diff))
                    $difference[$key] = $new_diff;
            } else if (is_string($value) && !in_array($value, $array2)) { // the value is a string and it's not in array B
                $difference[$key] = $value . " is missing from the second array";
            } else if (!is_numeric($key) && !array_key_exists($key, $array2)) { // the key is not numberic and is missing from array B
                $difference[$key] = "Missing from the second array";
            }
        }
        return $difference;
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

    public function getAttributesDiff()
    {
        $res = [];


        $tags = Json::decode($this->tags);
        foreach ($tags as $tag) {
            $res['tags'][] = $tag;
        }


        $custom_fields = Json::decode($this->custom_fields);
        foreach ($custom_fields as $custom_field) {
            // $res['custom_fields'][] = $custom_field;
        }


        $res['id'] = (string)$this->lead_id;
        $res['name'] = $this->name;
        $res['status_id'] = (string)$this->status_id;
        $res['price'] = (string)$this->price;
        $res['responsible_user_id'] = (string)$this->responsible_user_id;
        $res['modified_user_id'] = (string)$this->modified_user_id;
        $res['created_user_id'] = (string)$this->created_user_id;
        $res['pipeline_id'] = (string)$this->pipeline_id;
        $res['account_id'] = (string)$this->account_id;


        return $res;
    }


    public function checkChanged($attributes)
    {
        unset($attributes['created_at'], $attributes['updated_at'], $attributes['last_modified'], $attributes['date_create']);


        $result = [];
        $result['new'] = [];
        $result['old'] = [];

        $result['old']['id'] = (string)$this->lead_id;
        if($this->name)
            $result['old']['name'] = $this->name;
        $result['old']['status_id'] = (string)$this->status_id;
        $result['old']['price'] = (string)$this->price;
        $result['old']['responsible_user_id'] = (string)$this->responsible_user_id;
        $result['old']['modified_user_id'] = (string)$this->modified_user_id;
        $result['old']['created_user_id'] = (string)$this->created_user_id;
        $result['old']['pipeline_id'] = (string)$this->pipeline_id;
        $result['old']['account_id'] = (string)$this->account_id;



        $result['new']['id'] = (string)$attributes['lead_id'];
        if($attributes['name'])
            $result['new']['name'] = $attributes['name'];
        $result['new']['status_id'] = (string)$attributes['status_id'];
        $result['new']['price'] = (string)$attributes['price'];
        $result['new']['responsible_user_id'] = (string)$attributes['responsible_user_id'];
        $result['new']['modified_user_id'] = (string)$attributes['modified_user_id'];
        $result['new']['created_user_id'] = (string)$attributes['created_user_id'];
        $result['new']['pipeline_id'] = (string)$attributes['pipeline_id'];
        $result['new']['account_id'] = (string)$attributes['account_id'];


        $custom_fields = [];
        if (isset($attributes['custom_fields'])) {
           // if (is_array($attributes['custom_fields'])) {
               // $attributes['custom_fields'] = Json::decode($attributes['custom_fields']);
                foreach (Json::decode($attributes['custom_fields']) as $cf) {
                    $result['new']['custom_fields'][$cf['id']] = $cf;
                }
             //   Yii::info(Json::decode($attributes['custom_fields']),'custom_fields');
                $result['old']['custom_fields'] = Json::decode($this->custom_fields);
          //  }
        }
        $tags = [];
        if (isset($attributes['tags'])) {
            foreach ($attributes['tags'] as $tag) {
                $result['new']['tags'][$tag['id']] = $tag;
            }
            foreach (Json::decode($this->tags) as $tag_old) {
                $result['old']['tags'][$tag_old['id']] = $tag_old;
            }
        }


     //   Yii::info($result['old']['custom_fields'],'old');
        return $this->diffAssocRecursive($result['new'], $result['old']);


    }
}
