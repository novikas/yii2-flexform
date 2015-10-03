<?php

use yii\bootstrap\ButtonDropdown;

/* @var $formModel Model a model that used to build widget */
/* @var $displayedAttributes array an attributes that client code intented to display */
/* @var $fieldNames array fields that current template has to display */
/* @var $templateController string route to template controller */
/* @var $submitTitle string title of submit button */

	$attrLabels = $formModel->attributeLabels();
	$dropdownItems = [];
	
	foreach ( $displayedAttributes as $key => $value  )
	{
		$isChecked = in_array($value, $fieldNames)?'checked':'';
		$label = isset($attrLabels[$value])?$attrLabels[$value]:$formModel->generateAttributeLabel($value);
		$dropdownItems[] = 	"<li><input type = 'checkbox' class = 'cloak-switcher' id = '$value-check' value = '$value' $isChecked><label for = '$value-check'>$label</label>";
	}
	
	$menuItems =[
		[
				'label' => '<span class = "glyphicon glyphicon-save"></span> Сохранить', 
				'url' => '', 
			    'linkOptions' =>[
			    					'id' => 'save-template',
			    					'class' => 'flexform-menu-item',
			        				'title' => 'Сохранить значения',
			                    ],
		],
		[
			'label' => '<span class = "glyphicon glyphicon-remove"></span> Удалить',
			'url' => '',
			'linkOptions' =>[
					'id' => 'remove-template',
					'class' => 'flexform-menu-item',
					'title' => 'Удалить',
			],
		],
	];
	
	
	echo '<div style = "margin-bottom: 3px;">'
		."<button id = 'flexform-submit' class = 'btn btn-primary'>$submitTitle</button>"
					.ButtonDropdown::widget([
							'label' => '<span class = "glyphicon glyphicon-plus"></span>',
							'encodeLabel' => false,
							'options' => [
									'id' => 'field-dropdown',
									'value' => $templateController,
									'data-placeholder' => 'false',
							],
							'containerOptions' =>[
									'style' => 'float:right;margin:2px;',
							],
							'dropdown' => [
									'options' => [
											'class' => 'noclose bullet',
									],
									'items' => $dropdownItems
							],
					])
					.ButtonDropdown::widget([
							'label' => '<span class = "glyphicon glyphicon-list"></span>',
							'encodeLabel' => false,
							'options' => [
									'id' => 'flexform-menu',
									'value' => $templateController,
									'data-placeholder' => 'false',
							],
							'containerOptions' =>[
									'style' => 'float:right;margin:2px;',
							],
							'dropdown' => [
									'encodeLabels' => false,
									'items' => $menuItems
							],
					]).'</div>';
?>