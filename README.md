# Laravel Eloquent Immutable Trait

## This package is not optimal, but it may be sufficient

### The `hashedContent()` method returns `'state'` by default

### The `hashColumn()` method returns `'hash'` by default
  
#### Use of `created_at` is acceptable but not required

#### Use of `updated_at` implies mutability, so using

#### `Blueprint::timestamps()` is discouraged

Hash checking may be disabled simply by

`setting static::$checkHash = false`

in the model's `boot()` method.

This may not be a good idea.

-----

## Typing is not the bottleneck, endeavor to be thoughtgful
