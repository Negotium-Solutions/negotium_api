<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    const PROFILE_TYPE_INDIVIDUAL = 1;
    const PROFILE_TYPE_BUSINESS = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name'
    ];

    protected $appends = ['profile_name'];

    public function profile_type()
    {
        return $this->belongsTo(ProfileType::class);
    }

    public function getProfileNameAttribute()
    {
        return (int)($this->profile_type_id) === self::PROFILE_TYPE_INDIVIDUAL ? $this->first_name.' '.$this->last_name : $this->company_name;
    }

    public function processes() : HasManyThrough
    {
        return $this->hasManyThrough(
            Process::class,
            ProfileProcess::class,
            'profile_id',
            'id',
            'id',
            'process_id'
        );
    }

    public function documents() : HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function notes() : HasMany
    {
        return $this->hasMany(Note::class)
            ->orderBy('reminder_datetime', 'asc')
            ->orderBy('created_at', 'desc');
    }

    public function communications() : HasMany
    {
        return $this->hasMany(Communication::class)
            ->orderBy('created_at', 'desc');
    }

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }

    public function dynamicModel()
    {
        $this->schema->name;
        $dynamicModel = new DynamicModel();
        $dynamicModel->setTable($this->schema->name);

        return $dynamicModel->where('parent_id', $this->id)->first();
    }

    public function dynamicModelFields()
    {
        $dynamicModelFields = DynamicModelField::with(['dynamicModelFieldGroup', 'attributes'])
                            ->where('schema_id', $this->schema_id)
                            ->orderBy('dynamic_model_field_group_id')->get();

        $_dynamicModelFields = [];
        foreach ($dynamicModelFields as $key => $dynamicModelField) {
            if( $key === 0 ) {

                $field["schema_id"] = $dynamicModelField->scheme_id;
                $field["dynamic_model_field_group_id"] = $dynamicModelField->dynamic_model_field_group_id;
                $field["dynamic_model_field_group"]["id"] = $dynamicModelField->dynamicModelFieldGroup->id;
                $field["dynamic_model_field_group"]["name"] = $dynamicModelField->dynamicModelFieldGroup->name;

                if( $this->profile_type_id == self::PROFILE_TYPE_INDIVIDUAL ) {
                    $field = $this->addDynamicModelField('First Name', $dynamicModelField->dynamicModelFieldGroup, ['required', 'string']);
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                    $field = $this->addDynamicModelField('Last Name', $dynamicModelField->dynamicModelFieldGroup, ['required', 'string']);
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                }

                if( $this->profile_type_id == self::PROFILE_TYPE_BUSINESS ) {
                    $field = $this->addDynamicModelField('Company Name', $dynamicModelField->dynamicModelFieldGroup, ['required', 'string']);
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                }

                $field = $this->addDynamicModelField('Email', $dynamicModelField->dynamicModelFieldGroup, ['required', 'email']);
                $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;

                $field = $this->addDynamicModelField('Cell Number', $dynamicModelField->dynamicModelFieldGroup, ['required', 'sa_phone_number']);
                $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
            }
            $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $dynamicModelField;
        }

        return $_dynamicModelFields;
    }

    public function addDynamicModelField($field, $dynamic_model_field_group, $rules)
    {
        $attributes = [];
        foreach ($rules as $rule) {
            $attributes[] = [
                'label' => $rule,
                'name' => strtolower($rule)
            ];
        }

        $dynamicField = [
            'id' => rand(1000000, 2000000),
            'schema_id' => $this->schema->id,
            'label' => $field,
            'field' => strtolower(str_replace(' ', '_', $field)),
            'dynamic_model_field_group_id' => $dynamic_model_field_group->id,
            'dynamic_model_field_group' => $dynamic_model_field_group,
            'attributes' => $attributes
        ];

        return $dynamicField;
    }
}
