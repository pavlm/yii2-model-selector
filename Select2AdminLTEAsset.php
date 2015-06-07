<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class Select2AdminLTEAsset extends AssetBundle
{
    public $css = [
        'select2-adminlte.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
    
}
