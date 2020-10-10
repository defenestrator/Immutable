<?php

namespace Defenestrator\Traits;

use Cake\Chronos\Chronos;
use Illuminate\Foundation;

trait Immutable
{   
    public static $hashCheck = true;
    
    /**
     * :::: Laravel Eloquent Immutable Trait ::::
     * 
     * @return void
    */
    public static function bootImmutable()
    {
        static::updating(function ($model) {
            return abort(401, 'Immutable Model, update not permitted');
        });
        
        static::deleting(function ($model) {
            return abort(401, 'Immutable Model, delete not permitted');
        });

        static::creating(function ($model) {
            if (static::$hashCheck) {
                $model->{$model->hashColumn()} = sha1($model->{$model->hashedContent()});
            }            
            $model->created_at = $model->freshTimestamp();
        });
        
        static::retrieved(function ($model) {
            if (static::$hashCheck) {
                if (sha1(strval($model->{$model->hashedContent()})) != strval($model->{$model->hashColumn()})) {
                    abort(500, 'Hash check failed. Immutable model data is corrupt.');
                }
            }   
        });
    }
    
    public function hashColumn() : string
    {
        return 'hash';
    }

    public function hashedContent() : string
    {
        return 'state';
    }
}