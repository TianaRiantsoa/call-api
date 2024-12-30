<?php

namespace app\controllers;

use app\models\Prestashop;
use app\models\PrestashopCustomer;
use app\models\PrestashopProduct;
use app\models\PrestashopOrder;
use app\models\PrestashopSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * PrestashopController implements the CRUD actions for Prestashop model.
 */
class PrestashopController extends Controller
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

    public function actionError() {}

    /**
     * Lists all Prestashop models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PrestashopSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Prestashop model.
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
     * Creates a new Prestashop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Prestashop();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Vérifier si des enregistrements existent déjà pour l'URL et la clé API
                $existingUrlRecord = Prestashop::find()->where(['url' => $model->url])->one();
                $existingApiKeyRecord = Prestashop::find()->where(['api_key' => $model->api_key])->one();

                // Les deux champs existent : afficher une erreur avec les détails
                if ($existingUrlRecord && $existingApiKeyRecord) {
                    Yii::$app->session->setFlash(
                        'error',
                        "Un enregistrement avec cette URL et cette clé API existe déjà. Détails :<br>" .
                            "ID : {$existingUrlRecord->id}, URL : {$existingUrlRecord->url}, Clé API : {$existingUrlRecord->api_key}."
                    );
                } else {
                    // Sauvegarder la donnée, mais inclure les détails si une des deux valeurs existe déjà
                    if ($model->save()) {
                        if ($existingUrlRecord) {
                            Yii::$app->session->setFlash(
                                'warning',
                                "La donnée a été créée, mais l’URL existe déjà dans un autre enregistrement. Détails :<br>" .
                                    "ID : {$existingUrlRecord->id}, URL : {$existingUrlRecord->url}, Clé API : {$existingUrlRecord->api_key}."
                            );
                        } elseif ($existingApiKeyRecord) {
                            Yii::$app->session->setFlash(
                                'warning',
                                "La donnée a été créée, mais la Clé API existe déjà dans un autre enregistrement. Détails :<br>" .
                                    "ID : {$existingApiKeyRecord->id}, URL : {$existingApiKeyRecord->url}, Clé API : {$existingApiKeyRecord->api_key}."
                            );
                        } else {
                            Yii::$app->session->setFlash('success', 'L’enregistrement a été créé avec succès.');
                        }
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('error', 'Une erreur est survenue lors de la sauvegarde.');
                    }
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Prestashop model.
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
     * Deletes an existing Prestashop model.
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
     * Finds the Prestashop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Prestashop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prestashop::findOne(['id' => $id])) !== null) {
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

        $mod = new PrestashopProduct();

        if ($this->request->isPost && $model->load($this->request->post()) && $mod->load($this->request->post())) {
            return $this->redirect(['productresults', 'id' => $model->id, 'ref' => $mod->ref, 'type' => $mod->type,'variation_type' => $mod->variation_type]);
        }

        return $this->render('products', [
            'model' => $model,
            'mod' => $mod,
        ]);
    }

    public function actionOrders($id)
    {
        $model = $this->findModel($id);

        $mod = new PrestashopOrder();

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

        $mod = new PrestashopCustomer();

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

    public function actionProductresults($id, $ref, $type, $variation_type)
    {
        $model = $this->findModel($id);

        return $this->render('productresults', [
            'model' => $model,
            'type' => $type,
            'variation_type' => $variation_type,
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
