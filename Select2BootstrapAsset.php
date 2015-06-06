<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class Select2BootstrapAsset extends AssetBundle
{
    public $css = [
        'select2-bootstrap.css',
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
