<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserGenderEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'father_name',
        'national_code',
        'mobile',
        'mobile2',
        'email',
        'password',
        'avatar',
        'birth_date',
        'gender',
        'address',
        'tel',
        'job_type_id',
        'supervisor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:0',
        ];
    }


    public function blacklist(): HasOne
    {
        return $this->hasOne(BlackList::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function jobTypes()
    {
        return $this->morphToMany(JobType::class, 'job_typeable')->withTimestamps();;
    }

    public function owners()
    {
        return $this->hasMany(Owner::class);
    }

    public function occupants()
    {
        return $this->hasMany(Occupant::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable')->withTimestamps();
    }

    public function getRoles() {
        $roles = $this->roles()->get()->pluck('name');
        return $roles;
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    // دریافت تمام کارمندانی که زیر نظر این کاربر هستند
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function fromTransfers()
    {
        return $this->hasMany(Transfer::class, 'from_user_id');
    }

    public function toTransfers()
    {
        return $this->hasMany(Transfer::class, 'to_user_id');
    }

    protected function birthDateObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $dateExp = explode('-',verta()->instance($attributes['birth_date'])->format('Y-m-d'));
                return [
                    'jalali' => $attributes['birth_date'] ? verta()->instance($attributes['birth_date'])->format('Y/m/d') : null,
                    'gregorian' => $attributes['birth_date'] ? Carbon::parse($attributes['birth_date'])->format('Y/m/d') : null,
                    'object' => [
                        'year'  => $attributes['birth_date'] ? (int)removeFirstZeros($dateExp[0]) : null,
                        'month' => $attributes['birth_date'] ? (int)removeFirstZeros($dateExp[1]) : null,
                        'day'   => $attributes['birth_date'] ? (int)removeFirstZeros($dateExp[2]) : null,
                    ]
                ];
            }
        );
    }

    protected function genderObject(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = $attributes['gender'];
                return ['value' => $key ?? null, 'label' => $key ? UserGenderEnum::fromKey($key) : null];
            }
        );
    }


    public function customGetPerPage($userRole,$class): mixed
    {
        $perPage = Setting::get("$userRole.perPage");
        return $perPage[$this->id][$class] ?? perPages()[0];
    }


    public function customSetPerPage($userRole,$class, $value): void
    {
        $perPage = Setting::get("$userRole.perPage");
        $perPage[$this->id][$class] = $value;
        Setting::set("$userRole.perPage", $perPage);
    }

    public function perPage($userRole,$class)
    {
        if (request()->perPage) {
            $perPage = request()->perPage;
            $this->customSetPerPage($userRole,$class, $perPage);
        } else {
            $perPage = $this->customGetPerPage($userRole,$class);
        }
        return $perPage;
    }

}
