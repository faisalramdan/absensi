<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $fillable = [

        'company_id',

        'name',

        'parent_id',

        'description',

        'sort_order',

        'is_active',

        'created_by',

        'updated_by',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(
            Company::class
        );
    }



    public function members()
    {
        return $this->hasMany(
            TeamMember::class
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
    public function children()
    {
        return $this->hasMany(
            Team::class,
            'parent_id'
        )->orderBy('sort_order');
    }

    public function parent()
    {
        return $this->belongsTo(
            Team::class,
            'parent_id'
        );
    }

    public function getApprovers()
    {
        $approvers = collect();

        $team = $this;

        while ($team) {

            $team->loadMissing('members.employee');

            foreach ($team->members as $member) {

                if (
                    !$member->is_active
                ) {
                    continue;
                }

                if (
                    in_array(
                        $member->member_role,
                        ['Leader', 'Co Leader']
                    )
                ) {

                    $approvers->push([

                        'team_id' => $team->id,

                        'team_name' => $team->name,

                        'role' => $member->member_role,

                        'employee' => $member->employee,

                    ]);

                }

            }

            $team = $team->parent;

        }

        return $approvers;
    }
}