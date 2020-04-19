<?php

namespace Defenestrator\Traits;

use Cake\Chronos\Chronos;
use Illuminate\Foundation;

trait Immutable
{   
    public static $hashCheck = true;
    
    /**
     * :::: Laravel Eloquent Immutable Trait Documentation ::::
     * 
     * This package is not optimal, but it may be sufficient.
     * 
     * Model and data schema considerations are as follows: 
     * The contentKey() method returns 'state' by default,
     * The hashKey() method returns 'hash' by default, 
     * you should configure them as needed.
     *  
     * Use of created_at is encouraged but not required.
     * Use of Blueprint::timestamps() is discouraged.
     * Use of updated_at implies mutability, don't.
     * 
     * The hash is validated at retrieval by default.
     * This may be overriden by setting 
     * static::$hashCheck = false in 
     * your models' boot() method.
     * this is not a good idea.
     *      
     * Typing is not the bottleneck.
     * Endeavor to be thoughtgful.
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
                $model->{$model->hashKey()} = sha1($model->{$model->contentKey()});
            }            
            $model->created_at = $model->freshTimestamp();
        });
        
        static::retrieved(function ($model) {
            if (static::$hashCheck) {
                if (sha1(strval($model->{$model->contentKey()})) != strval($model->{$model->hashKey()})) {
                    abort(500, 'Hash check failed. Immutable model data is corrupt.');
                }
            }   
        });
    }
    
    public function hashKey() : string
    {
        return 'hash';
    }

    public function contentKey() : string
    {
        return 'state';
    }
}