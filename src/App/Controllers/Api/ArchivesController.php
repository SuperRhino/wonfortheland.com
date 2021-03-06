<?php
namespace App\Controllers\Api;

use App\Models\User;
use App\Models\Archive;
use Core\BaseApiController;
use Core\Http\Exception\BadRequestException;
use Core\Http\Exception\NotFoundException;

class ArchivesController extends BaseApiController
{
    public function add()
    {
        $data = $this->json();

        $user = $this->app->getCurrentUser();
        if ($user) {
            $data['author_user_id'] = $user->id;
        }

        if (empty($data['title'])) {
            throw new BadRequestException('Title is required.');
        }

        // specials:
        $data['status'] = 1;

        $archive = (new Archive($data))->save();

        return $this->success($archive->toArray());
    }

    public function uploadFile()
    {
        if (empty($_FILES)) {
            throw new BadRequestException('Missing file upload');
        }

        $fileKey    = 'file';
        $tmpFile    = $_FILES[$fileKey]['tmp_name'];
        $targetExt  = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
        $targetName = md5(basename($_FILES[$fileKey]['name']) . time()) . '.' . $targetExt;
        $targetPath = $this->app->getSetting('base_path') . $this->app->getSetting('app.paths.upload_path');
        $targetFile = $targetPath .'/'. $targetName;
        $fileUri    = $this->app->getSetting('app.paths.upload_dir') .'/'. $targetName;

        if (! move_uploaded_file($tmpFile, $targetFile)) {
            // Possible file upload attack!!
            throw new BadRequestException('Upload failed');
        }

        // File is valid, and was successfully uploaded
        // TODO Insert Into DB

        return $this->success([
            'target_name' => $targetName,
            'file_uri' => $fileUri,
        ]);
    }
}