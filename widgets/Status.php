<?php
namespace app\widgets;

use app\models\User;
use Yii;
use yii\bootstrap\Alert;

class Status extends \yii\bootstrap\Widget
{
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];

    public $closeButton = [];

    public function run()
    {
        $statuses = [];

        if (!Yii::$app->user->isGuest) {
            /* @var User $current_user */
            $current_user = Yii::$app->user->identity;
            $is_banned = $current_user->isBanned();
            if ($is_banned) {
                $statuses[] = [
                    'message' => Yii::t('login', 'Your account has been banned till {date}.',
                        ['date' => $is_banned]),
                    'type' => 'danger'
                ];
            }
            $is_not_confirmed = $current_user->isNotConfirmed();
            if ($is_not_confirmed) {
                $statuses[] = [
                    'message' => Yii::t('login','You are logged in, but without email confirmation, the '
                        . 'account functionality is limited. Without verification, the account will be deleted {date}.',
                        ['date' => $is_not_confirmed]),
                    'type' => 'warning'
                ];
            }
        }

        // можно ввести ещё события, например, разные сообщения от админа или других юзеров,
        // или инфу о чём-то, недавно произошедшем на сайте

        $i = 0;
        if (!empty($statuses)) {
            foreach ($statuses as $status) {
                $i++;
                $appendClass = $this->options['class'] ?? '';
                echo Alert::widget([
                    'body' => $status['message'],
                    'closeButton' => false,
                    'options' => array_merge($this->options, [
                        'id' => $this->getId() . '-' . $status['type'] . '-7' . $i,
                        'class' => $appendClass . ' ' . $this->alertTypes[$status['type']],
                    ]),
                ]);
            }
        }
    }
}
