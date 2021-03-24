<?php
namespace Pixelion\AmoCrm;
class FilterModel extends \yii\base\Model
{
    public $owner_id;
    public $pipeline_id;

    public function rules()
    {
        return [
            // тут определяются правила валидации
        ];
    }
}