<?php


use Illuminate\Support\Facades\App;
if (!function_exists('setLanguage')) {

    /**
     * Generic helper to store language data
     */
    function setLanguage($model, array $data = [])
    {
        if (!$model || empty($data)) {
            return false;
        }

        // Example: detect current locale
        $locale = App::getLocale();

        // If model has fillable fields
        if (method_exists($model, 'fill')) {
            $model->fill($data);
        } else {
            foreach ($data as $key => $value) {
                $model->$key = $value;
            }
        }

        // If model has language column (optional design)
        if ($model->lang()) {
            $model->lang()->create($data);
        }
        return $model->save();
    }
}