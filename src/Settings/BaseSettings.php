<?php

namespace Guysolamour\Administrable\Settings;

use Spatie\LaravelSettings\Settings;

abstract class BaseSettings extends Settings
{

    /**
     *
     * @return self
     */
    public static function get()
    {
        return app(static::class);
    }


    /**
     * @param array $data
     * @return void
     */
    public function update(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $this->save();
    }
}
