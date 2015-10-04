<?php 

namespace novikas\flexform\models;

use yii\helpers\ArrayHelper;

/**
 *
 * @property integer $id
 * @property string $name
 * @property boolean $public
 * @property integer $type
 * @property integer $FK_user
 * @property boolean $current
 * @property string  $modelClass
 *
 * @property FlexformField[] $flexFields
 * @property array $fields
 */
class FlexgridTemplate extends Template
{
	
	/**
	 * 
	 * @param string $class model tableName 
	 * @return \yii\db\ActiveQuery object with setted attributes for 
	 * form templates search.
	 */
	public static function gridTemplatesQuery( $class )
	{
		return self::find()->where([ 'modelClass' => $class, 'type' => 2 ]);
	}	
	
	/**
	 * 
	 * @param string $class models tableName 
	 * @return FlexformTemplate[] of models with users templates
	 */
	public static function getUserGridTemplates( $class )
	{
		return self::gridTemplatesQuery($class)->andWhere(['FK_user' => \Yii::$app->user->identity->id ])->all();
	}
	
	/**
	 *
	 * @param string $class models tableName
	 * @return FlexformTemplate[] of models with public templates
	 */
	public static function getPublicFormTemplates( $class )
	{
		return self::gridTemplatesQuery($class)->andWhere(['public' => 1]);
	}
	
	public static function getCurrentFor( $name )
	{
		$model = self::find()->where(['current' => 1,
				'type' => 2,
				'FK_user' => \Yii::$app->user->identity->id,
				'modelClass' => $name,
		])->one();
	
		if( isset($model) )
		{
			$model->refresh();
			return $model;
		}
	
		$model = self::find()->where(['current' => 1,
				'type' => 2,
				'public' => \Yii::$app->user->identity->id,
				'modelClass' => $name,
		])->one();
	
		return isset($model)?$model:null;
	}
	
	public function configure( $array )
	{
		FlexformField::deleteAll( "FK_template = $this->id AND name NOT IN ( '".implode('\', \'' , $array)."' )" );
		foreach ( $array as $key => $value )
		{
			if( FlexformField::find()->where(['FK_template' => $this->id, 
											  'name' => $value,
			])->one() == null )
			{
				$model = new FlexformField();
				
				$model->name = $value;
				$model->order = $key;
				$model->FK_template = $this->id;
				$model->save();
			}
		}
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see \vendor\novik\flexform\models\Template::ensureImOnlyCurrent()
	 */
	protected function ensureImOnlyCurrent()
	{
		return self::updateAll(['current' => 0],
				"current = 1
				AND	FK_user = $this->FK_user
				AND id != $this->id
				AND modelClass = '$this->modelClass'
				AND type = 2");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \vendor\novik\flexform\models\Template::randomTemplate()
	 */
	protected function randomTemplate()
	{
		return self::find()->where(['modelClass' => $this->modelClass, 'type' => 2 ])->andWhere("id != $this->id")->one();
	}
	
}

?>
