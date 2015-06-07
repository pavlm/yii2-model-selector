<?php
namespace pavlm\modelSelector;

use yii\web\AssetBundle;

class ModelSelectorAsset extends AssetBundle
{
    public $js = [
        'model-selector.js',
    ];
    
    public $adminLTE = false;
    
    public $depends = [
        'yii\web\JqueryAsset',
        'pavlm\modelSelector\Select2Asset',
    ];
    
    public $publishOptions = [];
    
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->depends[] = !$this->adminLTE ? 'pavlm\modelSelector\Select2BootstrapAsset' : 'pavlm\modelSelector\Select2AdminLTEAsset';
        parent::init();
    }
    
}
