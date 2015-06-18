<?php
namespace pavlm\modelSelector;

use Yii;
use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    /**
     * @var boolean|string - if 'true' then uses current language 
     */
    public $locale = true;
    
    public $sourcePath = '@vendor/bower/select2';
    
    public $js = [
        'select2.js',
    ];
    
    public $css = [
        'select2.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $publishOptions = [];

    public function init()
    {
        parent::init();
        if ($this->locale !== false) {
            $lang = $this->locale === true ? strtolower(substr(Yii::$app->language, 0, 2)) : $this->locale;
            $this->js[] = 'select2_locale_' . $lang . '.js';
        }
    }
}
