<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Media;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv,txt,jpg,jpeg,png,webp,docx,pdf|max:1024',
            ]);
            $file = $request->file('file');
            $media = Media::uploadFile($file);

            return responseJSon('success','با موفقیت آپلود شد',[
                'media_id' => $media->id,
                'patch'    => $media->file_path,
            ]);
        }
        catch (ValidationException $e){
            return responseJSon('error',$e->getMessage());
        }
        catch (\Exception $e){
            return responseJSon('error',$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $document = Document::create([
                'title'             => $request->title,
                'media_id'          => $request->media_id,
                'documentable_type' => $request->model_type,
                'documentable_id'   => $request->id,
            ]);
            $document->categories()->attach([$request->category]);
            Media::find($request->media_id)->confirmed();
            return redirectMessage('success','فایل با موفقیت ذخیره شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }

    }

    public function update(Document $document, Request $request)
    {
        $document->update(['title' => $request->title]);
        $document->categories()->sync([$request->category]);
        return redirectMessage('success','با موفقیت ویرایش شد');
    }
}
