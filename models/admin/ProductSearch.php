<?php

namespace app\models\admin;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

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
            [['id', 'unit_id', 'availability', /*'recommended',*/ 'sequence', 'status', /*'created_at', 'updated_at',*/ 'content_id'], 'integer'],
            [['slug', 'name_ua', 'name_ru', /*'title_ua', 'title_ru', 'keywords_ua', 'keywords_ru', 'description_ua',
                'description_ru', 'text_ua', 'text_ru',*/ 'vendorcode'], 'safe'],
            [['price', 'discount', 'measure'], 'number'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()
            ->with(['picture', 'productContentMain', 'content', 'contents']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'price' => $this->price,
            'discount' => $this->discount,
            'measure' => $this->measure,
            'unit_id' => $this->unit_id,
            'availability' => $this->availability,
            'recommended' => $this->recommended,
            'sequence' => $this->sequence,
            'status' => $this->status,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'name_ua', $this->name_ua])
            ->andFilterWhere(['like', 'name_ru', $this->name_ru])
            //->andFilterWhere(['like', 'title_ua', $this->title_ua])
            //->andFilterWhere(['like', 'title_ru', $this->title_ru])
            //->andFilterWhere(['like', 'keywords_ua', $this->keywords_ua])
            //->andFilterWhere(['like', 'keywords_ru', $this->keywords_ru])
            //->andFilterWhere(['like', 'description_ua', $this->description_ua])
            //->andFilterWhere(['like', 'description_ru', $this->description_ru])
            //->andFilterWhere(['like', 'text_ua', $this->text_ua])
            //->andFilterWhere(['like', 'text_ru', $this->text_ru])
            ->andFilterWhere(['like', 'vendorcode', $this->vendorcode]);

        if (!empty($this->content_id)) {
            $query
                ->innerJoin('product_content', 'product_content.product_id = products.id')
                ->andFilterWhere(['product_content.content_id' => $this->content_id]);
        }

        return $dataProvider;
    }
}
