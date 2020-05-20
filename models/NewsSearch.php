<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii;
use yii\db\Expression;
use app\helpers\Common as CH;

/**
 * NewsSearch represents the model behind the search form of `app\models\News`.
 */
class NewsSearch extends News
{
    public $content_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

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
        '-published_at',
    ];

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function search($params)
    {
        $query = static::find();

        if (!CH::isAdmin()) {
            $query->where(['show' => 1])->andWhere('published_at < ' . time());
        }

        $subQuery = 'least(created_at, IF (published_at > 0, published_at, created_at), '
            . 'IF (published_at IS NULL, created_at, published_at))';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12
            ],
            'sort' => [
                'defaultOrder' => ['published_at' => SORT_DESC],
                'attributes' => [
                    'published_at' => [
                        'asc' => new Expression($subQuery),
                        'desc' => new Expression($subQuery . ' DESC'),
                    ]
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }

        return $dataProvider;
    }
}
