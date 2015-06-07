<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class Select2AdminLTEAsset extends AssetBundle
{
    public $sourcePath = '@vendor/pavlm/yii2-model-selector/assets';
    
    public $css = [
        'select2-adminlte.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
}
