<?php

namespace Defenestrator\Traits;

use Defenestrator\Exceptions\Immutability;
use Defenestrator\Exceptions\Corruption;

trait Immutable
{   
    public static $hashCheck = true;
    
    /**
     * :::: Laravel Eloquent Immutable trait documentation ::::
     * 
     * Model and data schema considerations are as follows: 
     * The contentKey() method returns 'state' by default,
     * The hashKey() method returns 'hash' by default, 
     * you should configure them as needed.
     * 
     * Do not use $timestamps() or $timestampsTz().
     * Do not use the softDeletes() column type.
     * 
     * Use of created_at is encouraged but not required.
     * 
     * The hash is validated at retrieval by default.
     * This may be overriden by setting 
     * static::$hashCheck = false in 
     * your models' boot() method.
     * this is not a good idea.
     * 
     * If you need to distribute some data over a large number 
     * of persistence servers, consider using uuids. This is 
     * maybe a good idea, and super cool; but probably not.
     * 
     * This is not cryptographically secure, but it is useful.
     * If you want crypto-secured data you should make it 
     * yourself so you know exactly what it does. Grok?
     *  
     * @return void
    */
    public static function bootImmutable()
    {
        static::updating(function ($model) {
            throw new Immutability;
        });
        
        static::deleting(function ($model) {
            throw new Immutability;
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
                    throw new Corruption;
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