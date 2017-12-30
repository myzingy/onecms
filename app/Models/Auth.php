<?php

namespace App\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Auth
{
   //超管
   public static function isAdministrator(){
      return Admin::user()->isRole('administrator');
   }

   //管理员
   public static function isManager(){
      return Admin::user()->isRole('manager');
   }

   //讲师
   public static function isLecturer($uid = false){
      if($uid!==false) return Admin::user()->id==$uid && Admin::user()->isRole('lecturer');
      return Admin::user()->isRole('lecturer');
   }

   //客服
   public static function isService(){
      return Admin::user()->isRole('service');
   }
}
