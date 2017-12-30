<?php

namespace App\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Auth
{
   public static function isAdministrator(){
      return Admin::user()->isRole('administrator');
   }
}
