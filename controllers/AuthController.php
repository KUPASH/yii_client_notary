<?php
namespace app\controllers;

use app\models\LoginForm;
use app\models\RegForm;
use app\models\Users;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = Users::find()->where(['login' => $model->login])->one();
                if (!empty($user)) {
                    if (Yii::$app->getSecurity()->validatePassword($model->password, $user->pass)) {
                        Yii::$app->session->set('id', $user->id);
                        Yii::$app->session->set('login', $user->login);
                        Yii::$app->session->set('type_user', $user->type_user);
                        if($user->type_user == 1) {
                            return $this->redirect('/order/show-order-client');
                        } else {
                            return $this->redirect('/order/notary-order');
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
                $newUser->save();
                if ($newUser->save()) {
                    Yii::$app->session->set('id', $newUser->id);
                    Yii::$app->session->set('login', $newUser->login);
                    Yii::$app->session->set('type_user', $newUser->type_user);
                    if($newUser->type_user == 1) {
                        return $this->redirect('/order/show-order-client');
                    } else {
                        return $this->redirect('/order/notary-order');
                    }
                }
            }
        }
        return $this->render('regPage', ['model' => $model]);
    }
}