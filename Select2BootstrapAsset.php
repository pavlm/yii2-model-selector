<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class Select2BootstrapAsset extends AssetBundle
{
    public $sourcePath = '@vendor/pavlm/yii2-model-selector/assets';
    
    public $css = [
        'select2-bootstrap.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
}
