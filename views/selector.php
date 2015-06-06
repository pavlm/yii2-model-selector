<?php
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Json;
use pavlm\modelSelector\ModelSelectorAsset;
use pavlm\modelSelector\Select2Asset;
use pavlm\modelSelector\Select2BootstrapAsset;

/* @var $widget pavlm\modelSelector\ModelSelector */
/* @var $this yii\web\View */
Select2Asset::register($this);
Select2BootstrapAsset::register($this);
ModelSelectorAsset::register($this);

$widget = $this->context;
$opts = $widget->getJSOptions();
$selected = !empty($opts['model']);
$defaultOptions = [
    'class' => 'model-selector input-group ms-input-group',
];
?>

<? 
echo Html::beginTag('div', array_merge(['id' => $widget->id, 'data-options' => ($widget->manualInit ? $opts : null) ], $defaultOptions, $widget->options));
?>
	<? 
	$name = $widget->name ?: Html::getInputName($widget->model, $widget->attribute);
	echo Html::textInput($name, null, array_merge($widget->options, ['id' => $widget->id . '-field', 'class' => 'ms-field form-control'])); 
	?>
	<? if ($widget->itemLink): // !empty($opts['model']['link']) ?>
    	<? echo Html::a('<i class="glyphicon glyphicon-share-alt"></i>', 
    	    $selected ? $opts['model']['link'] : '#', 
    	    ['class' => 'btn btn-default input-group-addon ms-link', 'target' => '_blank', 'disabled' => !$selected]); ?>
	<? endif; ?>
<?
echo Html::endTag('div');
if (!$widget->manualInit) {
    $this->registerJs("\$('#".$widget->id."').modelSelector(".Json::encode($opts).");", View::POS_READY, $widget->id);
}
?>