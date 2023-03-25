<?php

namespace App\Observers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Blameable;
use File;

class BlameableObserver
{
    public function creating(Model $model)
    {
        $model->created_by = Auth::user()->id;
    }

    public function updating(Model $model)
    {
        $model->updated_by = Auth::user()->id;
        // if($model->isDirty('url_image')){
        //     dd(public_path());
        //      File::delete( public_path() .'/'. $model->getOriginal('url_image'));
        // };
    }

    public function deleted(Model $model)
    {
        $model->deleted_by = Auth::user()->id;
        $model->save();
        // File::delete( public_path() .'/'. $model->url_image);
    }
}
