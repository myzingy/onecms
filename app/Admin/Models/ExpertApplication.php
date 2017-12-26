<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertApplication extends Model
{
    protected $table = 'expert_application';
    protected $primaryKey = 'apid';

    const STATE_IN_CHECK = 1;//审核中
    const STATE_PASS = 2;//通过审核
    const STATE_REJECT = 3;//驳回审核

    const SVC_TYPE_NO_TEL = 1;//无有效联系方式
    const SVC_TYPE_NO_COMPLETE_DATA = 2;//未能提供完备资料
    const SVC_TYPE_NO_REQUEST = 3;//未能到达平台入驻要求

    const SERVICE_TYPE_QUESTION = 1;//问答
    const SERVICE_TYPE_SUBSCRIBE = 2;//订阅

    const ENABLE = 1;
    const DISABLE = -1;

    public static $statusOptions =[
        ExpertApplication::STATE_IN_CHECK=>'审核中',
        ExpertApplication::STATE_PASS=>'通过审核',
        ExpertApplication::STATE_REJECT=>'驳回审核'];

    public static $svcTypeOptions = [
        ExpertApplication::SVC_TYPE_NO_TEL=>'无有效联系方式',
        ExpertApplication::SVC_TYPE_NO_COMPLETE_DATA=>'未能提供完备资料',
        ExpertApplication::SVC_TYPE_NO_REQUEST=>'未能到达平台入驻要求'];

    public static $serviceTypeOptions = [
        ExpertApplication::SERVICE_TYPE_QUESTION=>'问答',
        ExpertApplication::SERVICE_TYPE_SUBSCRIBE=>'订阅',];

    public static $enableOptions = [
        ExpertApplication::ENABLE=>'是',
        ExpertApplication::DISABLE=>'否',];

}
