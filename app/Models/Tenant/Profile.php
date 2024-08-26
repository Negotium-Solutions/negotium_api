<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

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

    public function profile_type()
    {
        return $this->belongsTo(ProfileType::class);
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
            ->orderBy('reminder_datetime', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function communications() : HasMany
    {
        return $this->hasMany(Communication::class)
            ->orderBy('created_at', 'desc');
    }

    /*
    public function schema() : HasOneThrough
    {
        return $this->hasOneThrough(
            Schema::class,
            DynamicModelSchema::class,
            'dynamic_model_id',
            'id',
            'id',
            'schema_id'
        );
    }
    */

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
        $dynamicModelFields = DynamicModelField::with('dynamicModelFieldGroup')
                            ->where('schema_id', $this->schema_id)
                            ->orderBy('dynamic_model_field_group_id')->get();

        $_dynamicModelFields = [];
        foreach ($dynamicModelFields as $key => $dynamicModelField) {
            $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $dynamicModelField;
            if( $key === 0 ) {

                $field["schema_id"] = $dynamicModelField->scheme_id;
                $field["dynamic_model_field_group_id"] = $dynamicModelField->dynamic_model_field_group_id;
                $field["dynamic_model_field_group"]["id"] = $dynamicModelField->dynamicModelFieldGroup->id;
                $field["dynamic_model_field_group"]["name"] = $dynamicModelField->dynamicModelFieldGroup->name;

                if( $this->profile_type_id == 1 ) {
					$field["label"] = "First Name";
					$field["field"] = "first_name";
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                    $field["label"] = "Last Name";
                    $field["field"] = "last_name";
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                }

                if( $this->profile_type_id == 2 ) {
                    $field["label"] = "Company Name";
                    $field["field"] = "company_name";
                    $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
                }

                $field["label"] = "Email";
                $field["field"] = "email";
                $_dynamicModelFields[$dynamicModelField->dynamicModelFieldGroup->name][] = $field;
            }
        }

        return $_dynamicModelFields;
    }
}
