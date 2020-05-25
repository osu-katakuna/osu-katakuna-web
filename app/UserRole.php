<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = "user_roles";

    function permissions() {
      return $this->hasMany("App\RolePermission", "role_id");
    }

    function hasPermission($permission) {
      return count($this->permissions()->where("permission", "=", $permission)->get()) > 0;
    }
}
