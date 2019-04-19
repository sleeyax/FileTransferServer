<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class FileController extends Controller
{
    private $storagePath = 'files';

    public function __construct() {}

    /**
     * Upload a file to the storage & database
     * method: POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'max_downloads' => 'required|numeric|min:0|max:999',
        ]);
        if ($validator->fails()) {
            return response()->json(array_merge($validator->errors()->toArray(), ['status' => Response::HTTP_BAD_REQUEST]));
        }

        // Save file to storage
        $uploadedFile = $request->file('file');
        $uploadedFile->store($this->storagePath);

        // Save file data to database
        $file = new File;
        $file->name = $uploadedFile->hashName();
        $file->original_name = $uploadedFile->getClientOriginalName();
        $file->max_downloads = $request->get('max_downloads');
        $file->key = $this->generateKey();
        $file->size = $uploadedFile->getSize();
        $file->save();

        return response()->json(['status' => Response::HTTP_OK, 'message' => 'Success', 'key' => $file->key]);
    }

    /**
     * Download a file
     * @param string   $key
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function download(string $key) {
        $file = File::getByKey($key);

        // Show error message if key doesn't exist
        if ($file === null) {
            return response()->json(['status' => Response::HTTP_NOT_FOUND, 'message' => 'File not found!']);
        }

        $fileStream = Storage::download("$this->storagePath/$file->name", $file->original_name);

        $file->downloads++;
        $file->save();

        // If max amount of downloads reached, delete the file
        if ($file->max_downloads != 0 && $file->downloads >= $file->max_downloads) {
            $file->delete();
            Storage::delete("$this->storagePath/$file->name");
        }

        return $fileStream;
    }


    /**
     * Return info about file with specified key
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(string $key) {
        $file = File::getByKey($key);

        // Show error message if key doesn't exist
        if ($file === null) {
            return response()->json(['status' => Response::HTTP_NOT_FOUND, 'message' => 'File not found!']);
        }

        return response()->json(['status' => Response::HTTP_OK, 'file' => [
            'name' => $file->original_name,
            'downloads' => $file->downloads,
            'max_downloads' => $file->max_downloads,
            'last_modified' => $file->updated_at,
            'size' => $this->makeHumanReadable($file->size)
        ]]);
    }

    /**
     * Check if the key exists
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(string $key) {
        return response()->json(['status' => Response::HTTP_OK, 'found' => File::getByKey($key) !== null]);
    }

    /**
     * Generates a random key
     * @param int $length
     * @return string
     */
    private function generateKey(int $length = 10) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for ($i=0; $i<$length; $i++) {
            $key .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Generate new one if key already exists
        if (File::getByKey($key) !== null) {
            return $this->generateKey($length);
        }

        return $key;
    }

    /**
     * @param int $size
     * @param int $precision
     * @return string
     */
    private function makeHumanReadable(int $size, int $precision = 2){
        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}
        return round($size, $precision) . ['B','kB','MB','GB','TB','PB','EB','ZB','YB'][$i];
    }
}
