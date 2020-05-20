<?php

namespace app\models\admin;

use app\models\Order;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\db\Expression;
use yii\db\Query;
use yii;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    public $orderCount;

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            // если есть здесь - по нему можно искать; исключить - и его поля не будет в заголовке для поиска
            [['id', /*'registered_at', 'confirmed_at', 'last_visit', 'ban_expiration', 'orderCount'*/], 'integer'],
            [['username', 'email', 'phone', 'address', 'role', 'auth_key', 'password_hash', 'email_confirm_token', 'password_reset_token'], 'safe'],
        ];
    }

    /** {@inheritdoc} */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'orderCount' => Yii::t('admin', 'OrderCount'),
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {        $params['sort'] = 'email';

        $subQuery = (new Query())
            ->select('count(*)')
            ->from('orders o1')
            ->where('o1.user_id = users.id')
            ->createCommand()->getRawSql();

        $query = self::find()
            ->select('users.*')
            ->addSelect('(' . $subQuery . ') as orderCount')
        ;

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'username',
                    'email',
                    'phone',
                    'address',
                    'role',
                    'registered_at',
                    'confirmed_at',
                    'last_visit',
                    'orderCount' => [
                        'asc' => ['(' . $subQuery . ')' => SORT_ASC],
                        'desc' => ['(' . $subQuery . ')' => SORT_DESC],
                    ],
                ]
            ],
        ]);

        /*$dataProvider->sort->attributes['orderCount'] = [
            'asc' => ['(' . $subQuery . ')' => SORT_ASC],
            'desc' => ['(' . $subQuery . ')' => SORT_DESC],
        ];*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'registered_at' => $this->registered_at,
            //'confirmed_at' => $this->confirmed_at,
            //'last_visit' => $this->last_visit,
            //'ban_expiration' => $this->ban_expiration,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'role', $this->role])
            //->andFilterWhere(['like', 'auth_key', $this->auth_key])
            //->andFilterWhere(['like', 'password_hash', $this->password_hash])
            //->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            //->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
        ;

        return $dataProvider;
    }
}
