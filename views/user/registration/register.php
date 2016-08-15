<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View              $this
 * @var dektrium\user\models\User $user
 * @var dektrium\user\Module      $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="section">
<div class="container">

<div class="row">
    <div class="col m6 s12 offset-m3">
			<h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
			
                <?php $form = ActiveForm::begin([
                    'id'                     => 'registration-form',
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?php if ($module->enableGeneratingPassword == false): ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                <?php endif ?>
				
				<p>
				<?= $form->field($model, 'gender')->radioList(
				['MALE' => 'Male', 'FEMALE' => 'Female'],
				[
					'item' => function($index, $label, $name, $checked, $value) {

						$return = '';
						$return .= '<input id="gender-'.$value.'" type="radio" name="' . $name . '" value="' . $value . '">';
						$return .= '<label for="gender-'.$value.'">' . ucwords($label) . '</label>';
						$return .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

						return $return;
					}
				]				
				
				)->label(false) ?>
				</p>

				<div class="clearfix">&nbsp;</div>
                <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
				
			<p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
			</p>
    </div>
</div>


</div>
</div>