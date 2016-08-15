<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="white" role="navigation">
    <div class="nav-wrapper container">
      <a id="logo-container" href="<?php echo Yii::$app->urlManager->createUrl('site/index'); ?>" class="brand-logo"><img src="<?php echo Yii::$app->request->baseUrl; ?>/theme/images/logo_us.png" alt="Chat N Date" height="40" /></a>
      <ul class="right hide-on-med-and-down">
        <li><a href="<?php echo Yii::$app->urlManager->createUrl('site/index'); ?>">Home</a></li>
        <li><a href="#">Gallery</a></li>
        <li><a href="#">Pricing & Services</a></li>
        <li><a href="#">Events</a></li>
        <li><a href="#">Why Chat-nDate ™</a></li>
		
		<?php if(Yii::$app->user->isGuest): ?>
        <li><a href="<?php echo Yii::$app->urlManager->createUrl('user/security/login'); ?>">Login</a></li>
        <li><a href="<?php echo Yii::$app->urlManager->createUrl('user/registration/register'); ?>">Register</a></li>
		<?php else: ?>
        <li><a href="<?php echo Yii::$app->urlManager->createUrl('user/security/logout'); ?>">Logut</a></li>
		<?php endif; ?>
      </ul>

      <ul id="nav-mobile" class="side-nav">
        <li><a href="#">Navbar Link</a></li>
      </ul>
      <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
  </nav>
  
  <?php echo $content; ?>

 

  <footer class="page-footer teal">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Company Bio</h5>
          <p class="grey-text text-lighten-4">We are a team of college students working on this project like it's our full time job. Any amount would help support and continue development on this project and is greatly appreciated.</p>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Settings</h5>
          <ul>
            <li><a class="white-text" href="#!">Link 1</a></li>
            <li><a class="white-text" href="#!">Link 2</a></li>
            <li><a class="white-text" href="#!">Link 3</a></li>
            <li><a class="white-text" href="#!">Link 4</a></li>
          </ul>
        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Connect</h5>
          <ul>
            <li><a class="white-text" href="#!">Link 1</a></li>
            <li><a class="white-text" href="#!">Link 2</a></li>
            <li><a class="white-text" href="#!">Link 3</a></li>
            <li><a class="white-text" href="#!">Link 4</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Made by <a class="brown-text text-lighten-3" href="http://materializecss.com">Materialize</a>
      </div>
    </div>
  </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
