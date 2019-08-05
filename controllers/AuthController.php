<?php
namespace app\controllers;

use app\controllers\base\Controller;
use app\models\LoginForm;
use app\models\RegForm;
use app\models\Users;
use yii\helpers\Url;

use Yii;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = Users::find()->where('login=:login', [':login' => $model->login])->one();
                if (!empty($user)) {
                    if (Yii::$app->getSecurity()->validatePassword($model->password, $user->pass)) {
                        Yii::$app->user->login($user, $model->remember_me?3600*24*30:null);
                        if(Yii::$app->user->identity->type_user == Users::ROLE_CLIENT) {
                            return $this->redirect('/client/show-order-client');
                        } else {
                            return $this->redirect('/notary/notary-order');
                        }
                    } else {
                        $model->addError('password', 'Invalid password');
                    }
                } else {
                    $model->addError('login', 'Invalid login. Please, sign up.');
                }
            }
        }
        return $this->render('loginPage', ['model' => $model]);
    }
    public function actionRegister()
    {
        $model = new RegForm();
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $newUser = new Users();
                $newUser->login = $model->login;
                $newUser->pass = $model->setPassword($model->password);
                $newUser->type_user = $model->typeUser;
                if ($newUser->save()) {
                    Yii::$app->user->login($newUser, $model->remember_me?3600*24*30:null);
                    if(Yii::$app->user->identity->type_user == Users::ROLE_CLIENT) {
                        return $this->redirect('/client/show-order-client');
                    } else {
                        return $this->redirect('/notary/notary-order');
                    }
                }
            }
        }
        return $this->render('regPage', ['model' => $model]);
    }
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/auth/login');
    }
}