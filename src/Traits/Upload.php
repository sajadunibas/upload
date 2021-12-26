<?php


namespace Sajad\Upload\Traits;


trait Upload
{
    abstract function uploadColumnName(): string;

    public function extraColumn()
    {
        return [];
    }

    public static function upload($file)
    {
        return (new \Sajad\Upload\Upload(self::class))->upload($file);
    }

    public static function loadFile($table_id, $address = null)
    {
        return (new \Sajad\Upload\Upload(self::class))->loadFile($table_id , $address);
    }

    public static function deleteFile($table_id)
    {
        return (new \Sajad\Upload\Upload(self::class))->delete($table_id);
    }

}
