<?php
/*
* Author: Arup Kumer Bose
* Email: arupkumerbose@gmail.com
* Company Name: Brainchild Software <brainchildsoft@gmail.com>
*/

namespace App\Traits;


use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasAttachmentTrait
{
    /**
     * Morph Many relation with Attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachmentable')
                ->withDefault([
                    'id'=>null,
                    'image_path'=>$this->_defaultImagePath()
                ]);
    }


    protected static function bootHasAttachmentTrait()
    {
        self::deleting(function ($model) {
            Storage::disk(config('filesystems.default'))->delete($model->attachment->url);
            $model->attachment()->delete();
            /*if (method_exists($model->attachments())){
                $model->attachments()->delete();
            }*/

        });
    }

    public function updateAttachment(UploadedFile $photo)
    {
        $url= $photo->storePublicly(
            $this->table, ['disk' => config('filesystems.default')]
        );
        tap($this->attachment, function ($previous) use ($url) {
            try {
                $this->attachment()->create([
                    'url'=>$url
                ]);

                if ($previous) {
                    $previous->delete();
                }
            }catch (\Exception $exception){
                $this->attachment()->delete();
            }
        });
    }
    public function updateInventoryExcel(UploadedFile $photo)
    {
        $url= $photo->storePublicly(
            $this->table, ['disk' => config('filesystems.default')]
        );
        $this->attachments()->create([
            'url'=>$url
        ]);
        return $url;
    }

    public function updateBase64Attachment($image)
    {
        if($image){
            $ImageData = base64_decode($image);
            // $ImageData = file_get_contents($image);
            $imageInfo = getimagesizefromstring($ImageData);
            $ext = image_type_to_extension($imageInfo[2]);
            $name =  md5(rand(111111, 999999). time()).$ext;
            $url = $this->table.'/'.$name;
            Storage::disk(config('filesystems.default'))->put($url,$ImageData);
            tap($this->attachment, function ($previous) use ($url) {
                try {
                    $this->attachment()->create([
                        'url'=>$url
                    ]);

                    if ($previous) {
                        $previous->delete();
                    }
                }catch (\Exception $exception){
                    $this->attachment()->delete();
                }
            });
        }
    }

    private function _defaultImagePath(){
        return asset('images/no-image-available.jpg');
    }

    /**
     * Morph Many relation with Attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable')->orderBy('id','asc');
    }

    public function updateAttachment2(UploadedFile $photo,$attachment=null)
    {
        $url= $photo->storePublicly(
            $this->table, ['disk' => config('filesystems.default')]
        );
        if($attachment != null){
            tap($this->attachment, function ($previous) use ($url,$attachment) {
                $attachment->update([
                    'url'=>$url
                ]);
            });
        }else{
            $this->attachment()->create(['url'=>$url]);
        }
    }
}
