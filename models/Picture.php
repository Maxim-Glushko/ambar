<?php

namespace app\models;

use app\helpers\Common as CH;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pictures".
 *
 * @property int $id
 * @property int $item_type к какой сущности привязана галерея рисунков (пока планируется только продукт)
 * @property int $item_id
 * @property string $src
 * @property int $sequence очередность в показе
 * @property string $description_ua
 * @property string $description_ru
 * @property int $type пропорциональное уменьшение, с деформацией, с обрезанием
 * @property int $created_at
 * @property int $updated_at
 */
class Picture extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pictures';
    }

    /** @return array */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    const FOLDER = 'img';
    const THUMB_W = 700;
    const THUMB_H = 700;
    const UPLOAD_FOLDER = 'uploads';


    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['item_type', 'item_id', 'sequence'], 'integer'],
            [['description_ua', 'description_ru'], 'string'],
            [['description_ua', 'description_ru'], 'filter', 'filter' => function($value) {
                return substr($value, 0, 65535);
            }],
            [['src'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'item_type' => Yii::t('admin', 'Item Type'),
            'item_id' => Yii::t('admin', 'Item ID'),
            'src' => Yii::t('admin', 'Src'),
            'sequence' => Yii::t('admin', 'Sequence'),
            'description_ua' => Yii::t('admin', 'Description Ua'),
            'description_ru' => Yii::t('admin', 'Description Ru'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }

    /**
     * @param $url
     * @return string
     * получение адреса миниатюры рисунка
     * он мне нужен статическим, чтобы применять с любому адресу, а не только к экземпляру Picture
     */
    public static function getThumb($url) {
        try {
            $url = substr($url, 1);
            if (!file_exists($url)) {
                Yii::error('File "' . $url . '" exists in db, but do not in folder.');
                return '';
            }
            $parts = explode('/', $url);
            $filename = array_pop($parts);
            $thumbFolder = implode('/', $parts) . '/' . static::THUMB_W . 'x' . static::THUMB_W;
            if (!file_exists($thumbFolder))
                mkdir($thumbFolder, 0755);
            $thumbUrl = $thumbFolder . '/' . $filename;
            if (!file_exists($thumbUrl)) {
                $imagine = new Imagine;
                $imagine->open($url)
                    ->thumbnail(new Box(static::THUMB_W, static::THUMB_H))
                    ->save($thumbUrl, ['quality' => 95]);
            }
            return '/' . $thumbUrl;
        } catch (\Exception $e) {
            Yii::error('File "' . $url . '" produced exception: ' . $e->getMessage());
            return '';
        }
    }

    public function v($field) {
        return CH::getField($this, $field);
    }


    // загрузка оригинала картинки в папку /uploads
    // без записи в базу данных
    public static function upload() {
        if (!Yii::$app->request->post('uploading_files', false)) {
            return ['error' => 'Загрузите файл...'];
        }
        $folder = static::UPLOAD_FOLDER;
        if (!file_exists($folder) && !mkdir($folder, 0755, true)) {
            return ['error' => 'Не удалось создать папку.'];
        }
        if (count($_FILES) > 1) {
            return ['error' => 'Только один файл.'];
        }

        $uploadingFile = $_FILES[0];
        if ($uploadingFile['size'] > 5*1024*1024) {
            return ['error' => 'Файл не должен быть больше 5Mb'];
        }
        if (!$uploadingFile['type']) {
            return ['error' => 'Что-то не так с качеством картинки.'];
        }
        if (!in_array($uploadingFile['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
            return ['error' => 'Файл должен иметь формат: jpg или jpeg или png. У вас он ' . $uploadingFile['type']];
        }
        if ($uploadingFile['error']) {
            return ['error' => $uploadingFile['error']];
        }

        $fileName = time() . '.' . str_replace('image/', '', $uploadingFile['type']);
        $url = $folder . '/' . $fileName;
        if (!move_uploaded_file($uploadingFile['tmp_name'], $url)) {
            unlink($url);
            return ['error' => 'Возникла проблема с файлом "' . $uploadingFile['name'] . '".'];
        }

        return ['src' => '/' . $url];
    }

    public static function sync($type, $id, $datas) {
        static::deleteAll(['item_type' => $type, 'item_id' => $id]);
        if (!empty($datas) && is_array($datas)) {
            $sequence = 1;
            foreach ($datas as $data) {
                $picture = new static;
                $picture->load($data, '');
                $picture->sequence = $sequence++;
                $picture->item_type = $type;
                $picture->item_id = $id;
                $picture->save(false);
            }
        }
    }
}
