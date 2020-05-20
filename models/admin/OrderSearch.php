<?php

namespace app\models\admin;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use yii;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[/*'id',*/ 'status', 'user_id', /*'created_at', 'updated_at'*/ 'product_id'], 'integer'],
            [['key', 'name', /*'phone', 'address',*/ 'note', 'comment', /*'data',*/], 'safe'],
            [['sum'], 'number'],
        ];
    }

    public $product_id;

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'product_id' => Yii::t('admin', 'Products'),
        ], parent::attributeLabels());
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find()
            ->with(['user', 'orderProducts.product.unit', 'orderProducts.product.picture', 'orderProducts.product.content']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'sum',
                    'created_at',
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'sum' => $this->sum,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'key', $this->key])
            //->andFilterWhere(['like', 'name', $this->name])
            //->andFilterWhere(['like', 'phone', $this->phone])
            //->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'comment', $this->comment])
            //->andFilterWhere(['like', 'data', $this->data])
        ;

        if (!empty($this->name)) {
            $query
                ->andWhere(['OR',
                    ['like', 'name', $this->name],
                    ['like', 'phone', $this->name],
                    ['like', 'address', $this->name]
                ]);
        }

        if (!empty($this->product_id)) {
            $query
                ->innerJoin('order_product', 'order_product.order_id = orders.id')
                ->andWhere(['order_product.product_id' => $this->product_id]);
        }

        return $dataProvider;
    }
}
