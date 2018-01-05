<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->hasFile('file')) {
            //
            $file = $request->file('file');
            $data = $request->all();
            $rules = [
                'file'    => 'max:10240',
            ];
            $messages = [
                'file.max'    => '文件过大,文件大小不得超出10MB',
            ];


            $validator = Validator($data, $rules, $messages);
//            $validator = $this->validate($data, $rules, $messages);


            $res = ['errno'=>1,'message'=>'失败原因为：非法传参'];
            if ($validator->passes()) {
                $realPath = $file->getRealPath();
                $destPath = 'uploads/content/';
                $savePath = $destPath.''.date('Ymd', time());
                is_dir($savePath) || mkdir($savePath,'777',true);  //如果不存在则创建目录
                $name = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $check_ext = in_array($ext, ['gif', 'jpg', 'jpeg', 'png'], true);
                if ($check_ext) {
                    $uniqid = uniqid().'_'.date('s');
                    $oFile = $uniqid.'o.'.$ext;
                    $fullfilename = '/'.$savePath.'/'.$oFile;  //原始完整路径
                    if ($file->isValid()) {
                        $uploadSuccess = $file->move($savePath, $oFile);  //移动文件
                        $oFilePath = $savePath.'/'.$oFile;
                        $res = [
                            'errno'=>0,
                            'data'=>[
                                str_replace('/uploads','',config('filesystems.disks.admin.url'))
                                .$fullfilename
                            ]
                        ];
                    } else {
                        $res = ['errno'=>2,'message'=>'文件太大或格式错误，请重新编辑下图片'];
                    }
                } else {
                    $res = ['errno'=>3,'message'=>'文件类型不允许,请上传(gif、jpg、jpeg与png)图片'];
                }
            } else {
                $res = ['errno'=>4,'message'=>$validator->messages()->first()];
            }
        }
        return !empty($res)?$res:['errno'=>5,'message'=>'文件太大或格式错误，请重新编辑下图片.'];
    }
}
