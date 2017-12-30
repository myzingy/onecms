<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpertApplication extends Model
{
    use SoftDeletes;
    protected $table = 'expert_application';
    protected $primaryKey = 'apid';
    public $timestamps = false;

    const STATE_IN_CHECK = 0;//审核中
    const STATE_PASS = 1;//通过审核
    const STATE_REJECT = 2;//驳回审核

    const REJECTED_NO_TEL = 1;//无有效联系方式
    const REJECTED_NO_COMPLETE_DATA = 2;//未能提供完备资料
    const REJECTED_NO_REQUEST = 3;//未能到达平台入驻要求

    const SVC_TYPE_QUESTION = 0;//问答
    const SVC_TYPE_SUBSCRIBE = 1;//订阅

    const ENABLE = 1;
    const DISABLE = 0;

    public static $statusOptions =[
        ExpertApplication::STATE_IN_CHECK=>'审核中',
        ExpertApplication::STATE_PASS=>'通过审核',
        ExpertApplication::STATE_REJECT=>'驳回审核'];

    public static $RejectedOptions = [
        ExpertApplication::REJECTED_NO_TEL=>'无有效联系方式',
        ExpertApplication::REJECTED_NO_COMPLETE_DATA=>'未能提供完备资料',
        ExpertApplication::REJECTED_NO_REQUEST=>'未能到达平台入驻要求'];

    public static $svcTypeOptions = [
        ExpertApplication::SVC_TYPE_QUESTION=>'问答',
        ExpertApplication::SVC_TYPE_SUBSCRIBE=>'订阅',];

    public static $enableOptions = [
        ExpertApplication::ENABLE=>'是',
        ExpertApplication::DISABLE=>'否',];

}
