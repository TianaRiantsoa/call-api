<?php

namespace app\controllers;

use app\models\Woocommerce;
use app\models\WoocommerceCustomer;
use app\models\WoocommerceOrder;
use app\models\WoocommerceProduct;
use app\models\WoocommerceSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WoocommerceController implements the CRUD actions for Woocommerce model.
 */
class WoocommerceController extends Controller
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
     * Lists all Woocommerce models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new WoocommerceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Woocommerce model.
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
     * Creates a new Woocommerce model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


    public function actionCreate()
    {
        $model = new Woocommerce();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Vérifier si des enregistrements existent déjà pour l'URL, la clé client et la clé secrète
                $existingUrlRecord = Woocommerce::find()->where(['url' => $model->url])->one();
                $existingConsumerKeyRecord = Woocommerce::find()->where(['consumer_key' => $model->consumer_key])->one();
                $existingConsumerSecretRecord = Woocommerce::find()->where(['consumer_secret' => $model->consumer_secret])->one();

                // Les trois champs existent : afficher une erreur et empêcher la création
                if ($existingUrlRecord && $existingConsumerKeyRecord && $existingConsumerSecretRecord) {
                    Yii::$app->session->setFlash(
                        'error',
                        "Un enregistrement avec cette URL et ces clés API existe déjà. Détails :<br>" .
                            "ID : {$existingUrlRecord->id}, URL : {$existingUrlRecord->url}, Clé Client : {$existingUrlRecord->consumer_key}, Clé Secrète : {$existingUrlRecord->consumer_secret}."
                    );
                    $this->sendSlackNotification('error', "Un enregistrement avec cette URL et ces clés API existe déjà. Détails :<br>" .
                        "ID : {$existingUrlRecord->id}, URL : {$existingUrlRecord->url}, Clé Client : {$existingUrlRecord->consumer_key}, Clé Secrète : {$existingUrlRecord->consumer_secret}.");
                    return $this->redirect(['create']);
                }

                // Sauvegarder la donnée et afficher un message adapté
                if ($model->save()) {
                    if ($existingUrlRecord || $existingConsumerKeyRecord || $existingConsumerSecretRecord) {
                        $warningMessages = [];

                        if ($existingUrlRecord) {
                            $warningMessages[] =
                                "URL déjà existante dans un autre enregistrement. Détails :<br>" .
                                "ID : {$existingUrlRecord->id}, URL : {$existingUrlRecord->url}, Clé Client : {$existingUrlRecord->consumer_key}, Clé Secrète : {$existingUrlRecord->consumer_secret}.";
                        }

                        if ($existingConsumerKeyRecord) {
                            $warningMessages[] =
                                "Clé Client déjà existante dans un autre enregistrement. Détails :<br>" .
                                "ID : {$existingConsumerKeyRecord->id}, URL : {$existingConsumerKeyRecord->url}, Clé Client : {$existingConsumerKeyRecord->consumer_key}, Clé Secrète : {$existingConsumerKeyRecord->consumer_secret}.";
                        }

                        if ($existingConsumerSecretRecord) {
                            $warningMessages[] =
                                "Clé Secrète déjà existante dans un autre enregistrement. Détails :<br>" .
                                "ID : {$existingConsumerSecretRecord->id}, URL : {$existingConsumerSecretRecord->url}, Clé Client : {$existingConsumerSecretRecord->consumer_key}, Clé Secrète : {$existingConsumerSecretRecord->consumer_secret}.";
                        }

                        Yii::$app->session->setFlash(
                            'warning',
                            "L’enregistrement a été créé, mais :<br>" . implode('<br>', $warningMessages)
                        );
                        $this->sendSlackNotification(
                            'warning',
                            "L’enregistrement a été créé, mais :<br>" . implode('<br>', $warningMessages)
                        );
                    } else {
                        Yii::$app->session->setFlash('success', 'L’enregistrement a été créé avec succès.');
                        $this->sendSlackNotification('success', "L’enregistrement a été créé avec succès. URL : {$model->url}, Clé Client : {$model->consumer_key}, Clé Secrète : {$model->consumer_secret}.");
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Une erreur est survenue lors de la sauvegarde.');
                    $this->sendSlackNotification('error', 'Une erreur est survenue lors de la sauvegarde.');
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
     * Updates an existing Woocommerce model.
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
     * Deletes an existing Woocommerce model.
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
     * Finds the Woocommerce model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Woocommerce the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Woocommerce::findOne(['id' => $id])) !== null) {
            // Récupérer l'URL depuis le modèle SQLite
            $url = $model->url;

            // Récupérer toutes les lignes correspondantes à l'URL depuis MySQL
            $mysqlConnection = Yii::$app->mysql; // Connexion MySQL
            $data = $mysqlConnection->createCommand('SELECT * FROM migrationOF WHERE url=:url')
                ->bindValue(':url', $url)
                ->queryAll(); // Utiliser queryAll() pour récupérer plusieurs lignes

            if ($data !== false) {
                // Boucler sur les résultats MySQL et les ajouter dynamiquement au modèle
                foreach ($data as $row) {
                    $model->config[] = $row['config'];
                    $model->erp[] = $row['erp'];
                    $model->type[] = $row['type'];
                    $model->serial_id[] = $row['serial_id'];
                    $model->slug[] = $row['slug'];
                    $model->client[] = $row['client'];
                    $model->ctsage[] = $row['ctsage'];
                }
            } else {
                // Si aucune donnée n'est trouvée dans MySQL, on peut gérer ce cas
                $model->config = null;
                $model->erp = null;
                $model->type = null;
                $model->serial_id = null;
                $model->slug = null;
                $model->client = null;
                $model->ctsage = null;
                $model->idconfig = null;
            }

            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionProducts($id)
    {
        $model = $this->findModel($id);

        $mod = new WoocommerceProduct();

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

        $mod = new WoocommerceOrder();

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

        $mod = new WoocommerceCustomer;

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

    private function sendSlackNotification($status, $message)
    {
        // Définir les éléments à ajouter en fonction du type de message
        if ($status === 'error') {
            $emoji = '❌❌❌'; // 3 X rouges pour l'erreur
            $title = 'ERREUR';
            $color = 'danger'; // Rouge
        } elseif ($status === 'warning') {
            $emoji = '⚠️⚠️⚠️'; // 3 panneaux jaunes pour l'alerte
            $title = 'ALERTE';
            $color = 'warning'; // Jaune
        } elseif ($status === 'success') {
            $emoji = '✅✅✅'; // 3 carrés verts avec le signe de validation pour le succès
            $title = 'SUCCÈS';
            $color = 'good'; // Vert pour succès
        } else {
            $emoji = '';
            $title = 'INCONNU';
            $color = 'danger'; // Rouge si le statut est inconnu
        }

        // Construire le message Slack
        $slackMessage = [
            'attachments' => [
                [
                    'fallback' => $message,
                    'pretext' => "WooCommerce : " . "$emoji $title $emoji", // Ajouter l'icône et le titre
                    'text' => $message,
                    'color' => $color,
                ]
            ]
        ];

        // URL de votre webhook Slack
        $webhookUrl = 'https://hooks.slack.com/services/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'; // Remplacez par votre URL de webhook Slack

        // Utiliser cURL pour envoyer le message à Slack
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($slackMessage));
        curl_exec($ch);
        curl_close($ch);
    }
}
