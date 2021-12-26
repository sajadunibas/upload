<?php

namespace Sajad\Upload;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Sajad\Upload\Models\Upload as ModelUpload;

class Upload
{
    private $file, $path, $disk, $model, $createDataTable;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function upload($file)
    {
        $this->file = $file;

        return $this;
    }

    public function path($path)
    {
        $this->path = $path;

        return $this;
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function create($createData)
    {
        $this->createDataTable = $createData;

        return $this;
    }

    public function save()
    {
        $modelPk = app($this->model)->getKeyName();

        $disk = $this->disk ?? config('upload.disk');
        $path = $this->path ?? config('upload.path');

        $path .= '/' . time() . '_' . $this->file->getClientOriginalName();

        Storage::disk($disk)
            ->put($path, file_get_contents($this->file));

        $createData = [
            'save_name' => time() . '_' . $this->file->getClientOriginalName(),
            'current_name' => $this->file->getClientOriginalName(),
            'path' => $path,
            'format' => File::extension($this->file->getClientOriginalName()),
            'size' => $this->file->getSize(),
            'table_id' => $this->createDataTable[$modelPk],
            'table_type' => $this->model,
        ];

        $createData = array_merge($createData , app($this->model)::extraColumn());
        $uploadModel = ModelUpload::query()->create($createData);

        $update = [app($this->model)::uploadColumnName() => $uploadModel['id']];

        app($this->model)::where($modelPk , $this->createDataTable[$modelPk])->update($update);
        
        return [];
    }

    public function loadFile($table_id , $address = null)
    {
        $data = ModelUpload::query()->where([
            'table_type' => $this->model,
            'table_id'=> $table_id
        ])->first();

        $root = config('upload.root') != '' ? config('upload.root') . '/' : config('upload.root');
        $root = is_null($address) ? $root : $address;

        return $root . $data['path'];
    }

    public function delete($table_id)
    {
        $disk = $this->disk ?? config('upload.disk');

        $data = ModelUpload::query()->where([
            'table_type' => $this->model,
            'table_id'=> $table_id
        ]);

        Storage::disk($disk)->delete('/' . $data->first(['path']));

        return $data->delete();
    }
}
