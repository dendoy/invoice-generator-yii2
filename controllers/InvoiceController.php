<?php

namespace app\controllers;

use app\models\InvoiceItem;
use app\models\Model;
use Yii;
use app\models\Invoice;
use app\models\InvoiceSearch;
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
     * Displays a single Invoice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
}
