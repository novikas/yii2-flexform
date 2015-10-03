<?php 

namespace vendor\novik\flexform\models;

use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "bank".
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
class FlexformTemplate extends Template
{	
	/**
	 * 
	 * @param string $class model tableName 
	 * @return \yii\db\ActiveQuery object with setted attributes for 
	 * form templates search.
	 */
	public static function formTemplatesQuery( $class )
	{
		return self::find()->where([ 'modelClass' => $class, 'type' => 1 ]);
	}	
	
	/**
	 * 
	 * @param string $class models tableName 
	 * @return FlexformTemplate[] of models with users templates
	 */
	public static function getUserFormTemplates( $class )
	{
		return self::formTemplatesQuery($class)->andWhere(['FK_user' => \Yii::$app->user->identity->id ])->all();
	}
	
	/**
	 *
	 * @param string $class models tableName
	 * @return FlexformTemplate[] of models with public templates
	 */
	public static function getPublicFormTemplates( $class )
	{
		return self::formTemplatesQuery($class)->andWhere(['public' => 1]);
	}
	
	public static function getCurrentFor( $name )
	{
		$model = self::find()->where(['current' => 1, 
									  'type' => 1, 
									  'FK_user' => \Yii::$app->user->identity->id,
									  'modelClass' => $name,
		 ])->one();
		
		if( isset($model) )
		{
			$model->refresh();
			return $model;
		}
		
		$model = self::find()->where(['current' => 1, 
									  'type' => 1, 
									  'public' => \Yii::$app->user->identity->id,
									  'modelClass' => $name,
				])->one();
		
		return isset($model)?$model:null;
	}
	
	/**
	 * 
	 */
	protected function ensureImOnlyCurrent()
	{
		return self::updateAll(['current' => 0],
				"current = 1
				AND	FK_user = $this->FK_user
				AND id != $this->id
				AND modelClass = '$this->modelClass'
				AND type = 1");
	}
	
	protected function randomTemplate()
	{
		return self::find()->where(['modelClass' => $this->modelClass, 'type' => 1 ])->andWhere("id != $this->id")->one();
	}
	
	
}

?>