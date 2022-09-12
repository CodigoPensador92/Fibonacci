<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return JSON
     */
    public function actionFibonacci()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');
        

        function fib(int $from, int $to):array
        {
            $cache = Yii::$app->redis;

            /**
            * Find the nearest number if it is not in the Fibonacci number
             * @param $from
             * @return $from
            */
            function checkFrom(int $from){

                $a = 0;
                $b = 1;
                do
                {
                $b += $a;
                $a = $b - $a;
                } while ($b <= $from);
                if ($b - $from < $from - $a) {
                  return $from = $b ;
                } else {
                    return $from = $a ;
                }
               
            }
            
            $from = checkFrom($from);

            $previous = round($from / ((1 + sqrt(5)) / 2));

            $cache->set($previous,$from);

            $i = $from;
            $result[] = $from;

            while($i < $to){
                $old = $cache->get($previous);
                if($i == (int)$old){
                    $n = (int) $old;
                } else {
                    $n = $i + $previous;
                }
               
                if($n <= $to){
                    $result[] = $n;
                }
                $n = $i + $previous;
                $previous = $i;
                $i = $n;
                $cache->set($n, $i);
            }
            return $result;
      }
      return fib($from, $to);
    }

    /**
     * Login action.
     *
     * @return Response|string
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

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
