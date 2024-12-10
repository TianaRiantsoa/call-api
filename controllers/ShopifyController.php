<?php

namespace app\controllers;

use app\models\Shopify;
use app\models\ShopifyCustomer;
use app\models\ShopifyOrder;
use app\models\ShopifySearch;
use app\models\ShopifyProduct;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShopifyController implements the CRUD actions for Shopify model.
 */
class ShopifyController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Shopify models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShopifySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shopify model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Shopify model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Shopify();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Shopify model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Shopify model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Shopify model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Shopify the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shopify::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Récupération des données Produits, Commandes et Clients
     */

    public function actionProducts($id)
    {
        $model = $this->findModel($id);

        $mod = new ShopifyProduct();

        if ($this->request->isPost && $model->load($this->request->post()) && $mod->load($this->request->post())) {
            return $this->redirect(['productresults', 'id' => $model->id, 'ref' => $mod->ref]);
        }

        return $this->render('products', [
            'model' => $model,
            'mod' => $mod,
        ]);
    }

    public function actionOrders($id)
    {
        $model = $this->findModel($id);

        $mod = new ShopifyOrder();

        if ($this->request->isPost && $model->load($this->request->post()) && $mod->load($this->request->post())) {
            return $this->redirect(['orderresults', 'id' => $model->id, 'ref' => $mod->ref]);
        }

        return $this->render('orders', [
            'model' => $model,
            'mod' => $mod,
        ]);
    }

    public function actionCustomers($id)
    {
        $model = $this->findModel($id);

        $mod = new ShopifyCustomer();

        if ($this->request->isPost && $model->load($this->request->post()) && $mod->load($this->request->post())) {
            return $this->redirect(['customerresults', 'id' => $model->id, 'ref' => $mod->ref]);
        }

        return $this->render('customers', [
            'model' => $model,
            'mod' => $mod,
        ]);
    }

    /**
     * Résultat des requêtes
     */

    public function actionProductresults($id, $ref)
    {
        $model = $this->findModel($id);

        return $this->render('productresults', [
            'model' => $model,
            'ref' => $ref,
        ]);
    }

    public function actionOrderresults($id, $ref)
    {
        $model = $this->findModel($id);

        return $this->render('orderresults', [
            'model' => $model,
            'ref' => $ref,
        ]);
    }

    public function actionCustomerresults($id, $ref)
    {
        $model = $this->findModel($id);

        return $this->render('customerresults', [
            'model' => $model,
            'ref' => $ref,
        ]);
    }
}
