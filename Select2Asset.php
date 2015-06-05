<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/select2';
    
    public $js = [
        'select2.js',
    ];
    
    public $css = [
        'select2.css',
        //'select2-bootstrap.css',
        'select2.png',
        'select2-spinner.gif',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $publishOptions = [];
    
}
