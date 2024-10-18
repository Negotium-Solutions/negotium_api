<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Tenant\Schema as TenantSchema;

class DynamicModel extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table; // This is set dynamically by the model using it [Profile, Process, etc]

    protected $appends = [
        'profile_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const PROFILE_TYPE_INDIVIDUAL = 1;

    public function getProfileNameAttribute()
    {
        return (int)($this->parent_id) === self::PROFILE_TYPE_INDIVIDUAL ? $this->first_name.' '.$this->last_name : $this->company_name;
    }

    const EMAIL = 13;

    public function getTable() : string
    {
        if (request()->has('schema_id')) {
            return TenantSchema::find(request()->get('schema_id'))->table_name;
        }

        return request()->has('table_name') ? request()->get('table_name') : Session::get('table_name'); // The dynamic table is passed as part of the session
    }

    /*
     * Transformed model properties
     */
    public function propertiesByGroup()
    {
        $properties = parent::toArray();

        $dynamicModelFieldGroups = DynamicModelFieldGroup::with(['fields.options', 'fields.validations'])->where('schema_id', $this->schema()->id)->get();

        foreach ($dynamicModelFieldGroups as $dynamicModelFieldGroup) {
            foreach ($dynamicModelFieldGroup->fields as $dynamicModelField) {
                if (self::EMAIL === $dynamicModelField->dynamic_model_field_type_id) {
                    $dynamicModelField->value = DynamicModelFieldEmail::find($properties[$dynamicModelField->field]);
                } else {
                    $dynamicModelField->value = $properties[$dynamicModelField->field];
                }
            }
        }

        return $dynamicModelFieldGroups;
    }

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class, 'schema_id');
    }

    public function createDynamicModel($name, $dynamic_model_category_id, $dynamic_model_type_id, $quick_capture)
    {
        $modelType = DynamicModelType::find($dynamic_model_type_id);
        $this->save();
        $this->name = $name;
        $this->table_name = strtolower(str_replace(' ', '_', trim($modelType->name).'_'.$this->id));
        $this->dynamic_model_category_id = $dynamic_model_category_id;
        $this->dynamic_model_type_id = $dynamic_model_type_id;
        $this->quick_capture = $quick_capture;
        $this->save();

        \Illuminate\Support\Facades\Schema::create($this->schema_table_name, function (Blueprint $table) use ($dynamic_model_type_id) {
            $table->uuid('id')->primary();
            if ($dynamic_model_type_id === 2) {
                $table->uuid('profile_id')->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function groups() : HasMany
    {
        return $this->hasMany(DynamicModelFieldGroup::class, 'parent_id');
    }

    public function log() : HasOne {
        return $this->hasOne(ProcessLog::class)
            ->join('profiles', 'process_logs.profile_id', '=', 'profiles.id')
            ->select('process_logs.*');
    }

    public function getRecord(Request $request, $id)
    {
        // return $request->input('schema_id');
        $query = Schema::where('id', $request->input('schema_id'));
        $dynamicModel = DynamicModel::find($id);

        // return $dynamicModel;

        if ($request->has('with') && ($request->input('with') != '')) {
            $_with = explode(',', $request->input('with'));
            $query = $query->with($_with)->first();

            if (in_array('groups.fields.validations', $_with)) {
                foreach ($query->groups as $group_key => $group) {
                    foreach ($group->fields as $key => $field) {
                        $field['value'] = $dynamicModel[$key];
                        $query->groups[$group_key]->fields[$key] = $field;
                    }
                }
            }
        } else {
            $query = $query->first();
        }

        return $query;
    }
}
