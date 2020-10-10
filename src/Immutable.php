<?php

namespace Defenestrator\Traits;

use Cake\Chronos\Chronos;
use Illuminate\Foundation;
use Illuminate\Support\Hash;

trait Immutable
{   
    protected static $checkHash = true;
    
    /**
     * :::: Laravel Eloquent Immutable Trait Documentation ::::
     * 
     * This package is not optimal, but it may be sufficient.
     * 
     * The hashedContent() method returns 'state' by default,
     * The hashColumn() method returns 'hash' by default, 
     *  
     * Use of created_at is acceptable but not required.
     * Use of updated_at implies mutability, so using
     * Blueprint::timestamps() is discouraged.
     * 
     * Hash checking may be overriden simply by 
     * setting static::$checkHash = false 
     * in the model's boot() method,
     * this may not be a good idea.
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
            if (static::$checkHash) {
                $model->{$model->hashKey()} = Hash::make($model->{$model->contentKey()});
            }            
            $model->created_at = $model->freshTimestamp();
        });
        
        static::retrieved(function ($model) {
            if (static::$checkHash) {
                if ( Hash::check($model->contentKey()) ) {
                    abort(500, 'Hash check failed. Immutable model data is corrupt.');
                }
            }   
        });
    }
    
    protected function hashedColumn() : string
    {
        return 'hash';
    }

    protected function hashedContent() : string
    {
        return 'state';
    }
}