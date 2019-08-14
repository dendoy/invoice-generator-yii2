<?php

namespace app\controllers;

use app\models\InvoiceItem;
use app\models\Model;
use kartik\mpdf\Pdf;
use Yii;
use app\models\Invoice;
use app\models\InvoiceSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * generate pdf .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // get your HTML raw content without any layouts or scripts
        $model = $this->findModel($id);
        $items = InvoiceItem::find()->where(['id_invoice'=>$id])->all();
        $content = $this->renderPartial('view',['model'=>$model,'items'=>$items]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Invoice number'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>false,
                'SetFooter'=>false,
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();
        $items = [new InvoiceItem()];

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = date('Y-m-d H:i:s');
            $model->status = Invoice::STATUS_CREATED;

            $items = Model::createMultiple(InvoiceItem::classname());
            Model::loadMultiple($items, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validate($model)
                );
            }

            // validate all invoice
            $valid = $model->validate();
            // validate item invoice
            $valid = Model::validateMultiple($items) && $valid;

            if ($valid) {
                $amount = 0;
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    //jika invoice berhasil disave, save semua item
                    if ($flag = $model->save(false)) {
                        foreach ($items as $m) {
                            $m->id_invoice = $model->id;
                            if (! ($flag = $m->save(false))) {
                                $transaction->rollBack();
                                break;
                            }else{
                                $amount +=$m->total;
                            }
                        }
                        //update amout invoice sesuai total item
                        $model->amount = $amount;
                        $model->save(false);
                    }

                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Invoice telah diterbitkan');
                        return $this->redirect(['index']);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->invoice_number = '';
                    Yii::$app->session->setFlash('error', 'Uups! rollback!');
                }
            }else{
                Yii::$app->session->setFlash('error', 'Uups! ada kesalahan sistem!');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'items' => (empty($items)) ? [new InvoiceItem()] : $items
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $items = InvoiceItem::find()->where(['id_invoice'=>$id])->all(); //$model->items;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($items, 'id', 'id');
            $items = Model::createMultiple(InvoiceItem::classname(),$items);
            Model::loadMultiple($items, Yii::$app->request->post());
            //list item yang akan didelete
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($items, 'id', 'id')));


            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validate($model)
                );
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;

            if ($valid) {
                $amount = 0;
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            InvoiceItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($items as $m) {
                            $m->id_invoice = $model->id;
                            if (! ($flag = $m->save(false))) {
                                $transaction->rollBack();
                                break;
                            }else{
                                $amount +=$m->total;
                            }
                        }

                        //update amount sesuai item baru
                        $model->amount = $amount;
                        $model->save(false);
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Invoice telah diterbitkan');
                        return $this->redirect(['index']);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Uups! rollback!');
                }
            }else{
                Yii::$app->session->setFlash('error', 'Uups! ada kesalahan sistem!');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'items' => (empty($items)) ? [new InvoiceItem()] : $items
        ]);
    }

    /**
     * Deletes an existing Invoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //untuk proses payment
    public function actionPayment($id){
        //cek apakah status invoice masih 'created'
        $model = $this->findModel($id);
        if($model->status !== Invoice::STATUS_CREATED)
            throw new NotFoundHttpException('The requested page does not exist.');

        //tambahkan skenario payment untuk mengaktifkan rule pada model
        $model->scenario = 'payment';
        $model->transaction_date = date('Y-m-d');
        $model->payment = $model->amount;

        //query data item invoice untuk ditampilkan
        $items = new ActiveDataProvider([
            'query' => InvoiceItem::find()->where(['id_invoice'=>$id]),
        ]);

        if($model->load(Yii::$app->request->post())){
            //ganti status menjadi 'paid'
            $model->status = Invoice::STATUS_PAID;
            if($model->save()){
                Yii::$app->session->setFlash('success', 'Data payment telah berhasil disimpan');
                return $this->redirect(['invoice/index']);
            }
        }

        return $this->render('payment', [
            'model' => $model,
            'items' => $items
        ]);
    }
}
