<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'theme/css/materialize.min.css',
        'theme/css/style.css',
		'https://fonts.googleapis.com/icon?family=Material+Icons',
		//'theme/bootstrap-4.0.0-alpha.3/css/bootstrap.min.css',
    ];
    public $js = [
        'theme/js/materialize.min.js',
        'theme/js/init.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
