<?php

namespace panix\mod\banner;

use Yii;
use panix\engine\Html;
use panix\engine\WebModule;

class Module extends WebModule
{

    public $icon = 'images';

    public function afterInstall()
    {
        // if (!file_exists(Yii::getPathOfAlias('webroot.uploads.banner'))) {
        //     CFileHelper::createDirectory(Yii::getPathOfAlias('webroot.uploads.banner'), 0777);
        // }
        return parent::afterInstall();
    }

    public function afterUninstall()
    {
        //Удаляем таблицу модуля
        // Yii::app()->db->createCommand()->dropTable(Banner::model()->tableName());
        // Yii::app()->db->createCommand()->dropTable(BannerTranslate::model()->tableName());
        // if (file_exists(Yii::getPathOfAlias('webroot.uploads.banner'))) {
        //      CFileHelper::removeDirectory(Yii::getPathOfAlias('webroot.uploads.banner'), array('traverseSymlinks' => true));
        //  }
        return parent::afterUninstall();
    }

    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => $this->name,
                        'url' => ['/admin/banner'],
                        'icon' => $this->icon,
                    ],
                ],
            ],
        ];
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('banner/default', 'MODULE_NAME'),
            'author' => 'dev@pixelion.com.ua',
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('banner/default', 'MODULE_DESC'),
            'url' => ['/admin/banner'],
        ];
    }
}
