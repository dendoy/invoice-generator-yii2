<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Invoice */

$this->title = 'Update Invoice: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form',[
        'model'=>$model,
        'items'=>$items
    ]);?>

</div>
