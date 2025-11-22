<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function filter_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('users as a')
        ->join('model_has_roles as b', 'a.id', '=', 'b.model_id')
        ->join('roles as c', 'b.role_id', '=', 'c.id')
        ->join('role_has_permissions as d', 'c.id', '=', 'd.role_id')
        ->join('permissions as e', 'd.permission_id', '=', 'e.id')
        ->select('e.*')
        ->where(['a.id' => $id, 'e.parent' => ''])
        ->orderBy('e.id')
        ->get();

    return $hak_akses;
}

function sub_menu()
{
    $id = Auth::user()->id;

    $hak_akses = DB::table('users as a')
        ->join('model_has_roles as b', 'a.id', '=', 'b.model_id')
        ->join('roles as c', 'b.role_id', '=', 'c.id')
        ->join('role_has_permissions as d', 'c.id', '=', 'd.role_id')
        ->join('permissions as e', 'd.permission_id', '=', 'e.id')
        ->select('e.*')
        ->where(['a.id' => $id])
        ->where('e.parent', '!=', '')
        ->orderBy('e.id')
        ->get();

    return $hak_akses;
}
