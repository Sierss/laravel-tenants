<?php

declare(strict_types=1);

namespace Rinvex\Tenants\Models;

use Spatie\Sluggable\SlugOptions;
use Rinvex\Support\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Support\Traits\HasTranslations;
use Rinvex\Support\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Rinvex\Tenants\Models\Tenant.
 *
 * @property int                                                $id
 * @property string                                             $slug
 * @property array                                              $title
 * @property array                                              $description
 * @property int                                                $owner_id
 * @property string                                             $owner_type
 * @property string                                             $email
 * @property string                                             $website
 * @property string                                             $phone
 * @property string                                             $language_code
 * @property string                                             $country_code
 * @property string                                             $state
 * @property string                                             $city
 * @property string                                             $address
 * @property string                                             $postal_code
 * @property string                                             $launch_date
 * @property string                                             $timezone
 * @property string                                             $currency
 * @property string                                             $group
 * @property bool                                               $is_active
 * @property \Carbon\Carbon|null                                $created_at
 * @property \Carbon\Carbon|null                                $updated_at
 * @property \Carbon\Carbon|null                                $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant ofOwner(\Illuminate\Database\Eloquent\Model $owner)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereLaunchDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tenants\Models\Tenant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tenant extends Model
{
    use HasSlug;
    use HasTranslations;
    use ValidatingTrait;
    use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'owner_id',
        'owner_type',
        'email',
        'website',
        'phone',
        'language_code',
        'country_code',
        'state',
        'city',
        'address',
        'postal_code',
        'launch_date',
        'timezone',
        'currency',
        'is_active',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'slug' => 'string',
        'owner_id' => 'integer',
        'owner_type' => 'string',
        'email' => 'string',
        'website' => 'string',
        'phone' => 'string',
        'country_code' => 'string',
        'language_code' => 'string',
        'state' => 'string',
        'city' => 'string',
        'address' => 'string',
        'postal_code' => 'string',
        'launch_date' => 'string',
        'timezone' => 'string',
        'currency' => 'string',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.tenants.tables.tenants'));
        $this->setRules([
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.tenants.tables.tenants').',slug',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:10000',
            'owner_id' => 'required|integer',
            'owner_type' => 'required|string',
            'email' => 'required|email|min:3|max:150|unique:'.config('rinvex.tenants.tables.tenants').',email',
            'website' => 'nullable|string|max:150',
            'phone' => 'nullable|numeric|phone',
            'country_code' => 'required|alpha|size:2|country',
            'language_code' => 'required|alpha|size:2|language',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'launch_date' => 'nullable|date_format:Y-m-d',
            'timezone' => 'required|string|timezone',
            'currency' => 'required|alpha|size:3',
            'is_active' => 'sometimes|boolean',
        ]);
    }

    /**
     * Get all attached models of the given class to the tenant.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function entries(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'tenantable', config('rinvex.tenants.tables.tenantables'), 'tenant_id', 'tenantable_id');
    }

    /**
     * Get the options for generating the slug.
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->doNotGenerateSlugsOnUpdate()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    /**
     * Get the tenant owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo('owner', 'owner_type', 'owner_id');
    }

    /**
     * Determine if the given model is owner of tenant.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function isOwner(Model $model): bool
    {
        return $model->getKey() === $this->owner->getKey();
    }

    /**
     * Determine if the given model is staff of tenant.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function isStaff(Model $model): bool
    {
        return $model->tenants->contains($this);
    }

    /**
     * Get tenants of the given owner.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $owner
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOwner(Builder $builder, Model $owner): Builder
    {
        return $builder->where('owner_type', $owner->getMorphClass())->where('owner_id', $owner->getKey());
    }

    /**
     * Activate the tenant.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the tenant.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }
}
