<?php
    $user = \app\entities\user\User::find()->select('name')->where(['id' => $model->created_by])->one();
?>
<tr>
    <td>
        <div class="checkbox">
        <label for="<?= $model->id ?>">
            <input type="checkbox" id="<?= $model->id ?>" name="Dubious[]" value="<?= $model->id ?>">
            ID <?= $model->id ?>
        </label>
        </div>
    </td>
    <td><div class="checkbox"><?= $model->mfo_cli ?></div></td>
    <td><div class="checkbox"><?= $model->name_cli ?></div></td>
    <td><div class="checkbox"><?= Yii::$app->formatter->asDate($model->created_at) ?></div></td>
    <td><div class="checkbox"><?= $user->name ?></div></td>
</tr>



