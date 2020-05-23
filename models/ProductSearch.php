<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii;
use yii\db\Expression;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $content_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public static $sorts = [
        'sequence', // умолчание
        '-sequence',
        'price',
        '-price',
    ];

    public static function sortNames ()
    {
        return [
            'sequence' => Yii::t('common', 'by default'),
            'price' => Yii::t('common', 'by price'),
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function search($params)
    {
        $query = static::find()
            ->with(['picture', 'unit', 'content'])
            //->where('products.availability > 0') // наверное, в списке нужно выводить все, кроме умышленно скрытых
            ->where('products.status > 0');

        $subQuery = 'least(products.price, IF ((discount * 1) > 0, discount, products.price))';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24
            ],
            'sort' => [
                'defaultOrder' => ['sequence' => SORT_ASC],
                'attributes' => [
                    'sequence' => [
                        'asc' => ['products.sequence' => SORT_ASC],
                        'desc' => ['products.sequence' => SORT_DESC],
                    ],
                    'price' => [
                        'asc' => new Expression($subQuery),
                        'desc' => new Expression($subQuery . ' DESC'),
                    ],
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }

        if ($this->content_id == Content::MAIN_ID) {
            $query
                ->andWhere(['products.recommended' => 1])
                ->orderBy('RAND()');
        } else {
            $query
                ->innerJoin('product_content', 'products.id = product_content.product_id')
                ->andWhere(['product_content.content_id' => $this->content_id]);
        }

        return $dataProvider;
    }
}
