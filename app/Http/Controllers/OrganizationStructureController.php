<?php

namespace App\Http\Controllers;

use App\Models\Team;

class OrganizationStructureController extends Controller
{
    public function index()
    {
        $teams = Team::with([
            'company',
            'parent',
            'members.employee.position',
        ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $levels = [];

        foreach ($teams as $team) {

            $level = 0;

            $parent = $team->parent;

            while ($parent) {

                $level++;

                $parent = $parent->parent;

            }

            $levels[$level][] = $team;
        }

        ksort($levels);

        return view(
            'organization.index',
            compact('levels')
        );
    }
}