<?php
$this->title = 'Create Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= \yii\helpers\Html::encode($this->title) ?></h1>

<?php echo $this->render('_form',[
    'model'=>$model,
    'items'=>$items
]);?>


