<?php
namespace app\controllers\base;



use app\controllers\base\Controller;

class SecurityController extends Controller
{
    public function beforeAction($action)
    {
        if(\Yii::$app->user->isGuest) {
            return $this->redirect('/auth/login');
        }
        return parent::beforeAction($action);
    }
}