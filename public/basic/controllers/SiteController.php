<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\models\User;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\ForbiddenHttpException;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            /*'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],*/
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->load(Yii::$app->request->post());
        if ($model->validate() && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            \Yii::$app->session->setFlash('activation', "Please check your e-box for activation email");
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!\Yii::$app->user->can('updateOwnProfile', ['profileId' => $id, 'action' => Yii::$app->controller->action->id])) {
            throw new ForbiddenHttpException('Access denied');
        }
        $model = $this->findModel($id);
        if(Yii::$app->request->post()){
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!\Yii::$app->user->can('updateOwnProfile', ['profileId' => $id, 'action' => Yii::$app->controller->action->id])) {
            throw new ForbiddenHttpException('Access denied');
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionConfirm(){
        $model = new User();
        if(Yii::$app->request->get('conf')){
            if($model->activation(Yii::$app->request->get('conf'))){
                \Yii::$app->session->setFlash('activation', 
                    "Your account has been activated, you can " . Html::a('Login', ['/login']) .".");
            }else{
                \Yii::$app->session->setFlash('activation', "Somesing goes wrong, please check your activation link.");
            }
        } else {
            \Yii::$app->session->setFlash('activation', "This link is no more active.");
        }
        return $this->render('confirm', [
            'model' => $model,
        ]);
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $flag = true;
            if (!Yii::$app->user->isGuest && !in_array($action->id,array('index','confirm','logout','error'))) {
                $id = Yii::$app->user->identity->id;
                $model = $this->findModel($id);
                if($model->ban == 1){
                    $flag = false;
                }
            }
            if (!\Yii::$app->user->can($action->id)) {
                $flag = false;
            }
            if($flag){
                return $flag;
            }
            throw new ForbiddenHttpException('Access denied');
        } else {
            return false;
        }
    }
    
    public function beforeSave($insert) {
        if($this->isNewRecord){
            $settings = Settings::model()->find()->where(array('id' => 1))->one();
            if ($settings->defaultStatusUser == 0) {
                $model->ban = 0;
            } else {
                $model->ban = 1;
            }
            $this->created = time();
            $this->role = 'user';
            $this->password = md5('sa1t' . $this->password . '4_pr0tect10n');
        }
        parent::beforeSave($insert);
        
        return parent::beforeSave($insert);
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
