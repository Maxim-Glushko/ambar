<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii;
use yii\db\Expression;
use app\helpers\Common as CH;

/**
 * ArticleSearch represents the model behind the search form of `app\models\Article`.
 */
class ArticleSearch extends Article
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
        '-sequence',
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12
            ],
            'sort' => [
                'defaultOrder' => ['sequence' => SORT_DESC],
                'attributes' => [
                    'sequence'
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
