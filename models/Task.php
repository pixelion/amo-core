<?php

namespace Pixelion\AmoCrm\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 */
class Task extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_task}}";
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        // set initial rules

        $pr = $this->agreement();
        $rules = [];
        if ($pr) {
            $rules[] = ['agreement', 'required', 'requiredValue' => 1, 'message' => self::t('AGREEMENT_MESSAGE'), 'on' => 'register'];
            $rules[] = ['agreement', 'boolean', 'on' => 'register'];
        }
        $rules[] = ['subscribe', 'boolean'];
        // $rules = [
        $rules[] = [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg']];
        // general email and username rules
        $rules[] = [['email', 'username', 'phone', 'first_name', 'last_name', 'middle_name'], 'string', 'max' => 50];
        $rules[] = [['email', 'username'], 'unique'];
        $rules[] = [['email', 'username'], 'filter', 'filter' => 'trim'];
        $rules[] = [['email'], 'email'];
        $rules[] = ['image', 'file'];
        $rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
        $rules[] = ['new_password', 'string', 'min' => 4, 'on' => ['reset', 'change']];
        $rules[] = [['image', 'city', 'instagram_url', 'facebook_url'], 'default'];
        $rules[] = [['instagram_url', 'facebook_url'], 'url'];
        // [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => Yii::t('user/default', '{attribute} can contain only letters, numbers, and "_"')],
        // password rules
        //[['newPassword'], 'string', 'min' => 3],
        //[['newPassword'], 'filter', 'filter' => 'trim'],
        $rules[] = [['new_password'], 'required', 'on' => ['reset', 'change']];
        $rules[] = [['password_confirm'], 'required', 'on' => ['register', 'create_user']];
        $rules[] = [['city'], 'string'];
        $rules[] = [['password_confirm', 'password'], 'string', 'min' => 4];
        $rules[] = [['gender', 'points'], 'integer'];
        $rules[] = [['password'], 'required', 'on' => ['register', 'create_user']];
        $rules[] = ['phone', 'panix\ext\telinput\PhoneInputValidator'];
        //[['password_confirm'], 'compare', 'compareAttribute' => 'new_password', 'message' => Yii::t('user/default', 'Passwords do not match')],
        $rules[] = [['password_confirm'], 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('user/default', 'PASSWORD_NOT_MATCH'), 'on' => 'register'];
        // account page
        $rules[] = [['currentPassword'], 'required', 'on' => ['account']];
        $rules[] = [['currentPassword'], 'validateCurrentPassword', 'on' => ['account']];

        // admin rules
        $rules[] = [['ban_time'], 'date', 'format' => 'php:Y-m-d H:i:s', 'on' => ['admin', 'create_user']];
        $rules[] = [['ban_reason'], 'string', 'max' => 255, 'on' => ['admin', 'create_user']];
        $rules[] = [['role', 'username', 'status'], 'required', 'on' => ['admin', 'create_user']];
        //  ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7)); // "email" or "username"
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }



    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            'register_fast' => ['username', 'email', 'phone', 'points', 'points_expire'],
            'register' => ['username', 'email', 'password', 'password_confirm'],
            'reset' => ['new_password', 'password_confirm'],
            'admin' => ['role', 'username', 'points', 'points_expire'],
        ]);
    }

    /**
     * Validate current password (account page)
     */
    public function validateCurrentPassword()
    {
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
    }


    public function behaviors()
    {
        $a = [];
        $a['uploadFile'] = [
            'class' => '\panix\engine\behaviors\UploadFileBehavior',
            'files' => [
                'image' => '@uploads/user',
            ],
            'options' => [
                'watermark' => false
            ]
        ];
        return ArrayHelper::merge($a, parent::behaviors());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'new_password' => self::t('NEW_PASSWORD'),
            'password_confirm' => self::t('PASSWORD_CONFIRM'),
            'role' => self::t('ROLE'),
        ]);
    }



    public static function find()
    {
        return new TaskQuery(get_called_class());
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {


        // hash new password if set
        if ($this->password && $insert) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        if (in_array($this->scenario, ['reset', 'admin'])) {

            if ($this->new_password)
                $this->password = Yii::$app->security->generatePasswordHash($this->new_password);
        }

        // convert ban_time checkbox to date
        if ($this->ban_time) {
            $this->ban_time = date("Y-m-d H:i:s");
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username", "ban_time", "ban_reason"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->role) {
            Yii::$app->authManager->revokeAll($this->id);
            if (is_array($this->role)) {
                foreach ($this->role as $role) {
                    Yii::$app->authManager->assign(Yii::$app->authManager->getRole($role), $this->id);
                }
            } elseif (is_string($this->role)) {
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole($this->role), $this->id);
            }
        }


        if (Yii::$app->hasModule('mailchimp')) {
            /** @var \DrewM\MailChimp\MailChimp $mailchimp */
            $list = Yii::$app->settings->get('mailchimp', 'list_user');
            if ($list) {
                $mailchimp = Yii::$app->mailchimp->getClient();


                $result = $mailchimp->post('lists/' . $list . '/members', [
                    //'merge_fields' => [
                    //    'FNAME' => $fname,
                    //    'LNAME' => $lname
                    //],
                    'email_address' => $this->email,
                    'status' => 'subscribed',
                ]);

                if ($mailchimp->success()) {
                    // $class   = 'alert-success';
                    // $message = $result['email_address']. ' ' .$result['status'];
                } else {
                    // $class   = 'alert-warning';
                    // $message = $result['title'];
                }
            }


        }

        parent::afterSave($insert, $changedAttributes);
    }

}
