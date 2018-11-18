<?php

namespace Chatter\Middleware;


class FileRemoveExif
{
    public function __invoke($request, $response, $next)
    {
        $files = $request->getUploadedFiles();
        $newfile = $files['file'];
        $uploadFilename = $newfile->getClientFilename();
        $newfile_type = $newfile->getClientMediaType();
        $newfile->moveTo("assets/images/raw/" . $uploadFilename);
        $pngfile = "assets/image/" .substr($uploadFilename, 0 , -4). ".png";

        if('image/jpg' == $newfile_type)
        {
            $_img = imagecreatefromjpeg("assets/images/raw/" . $uploadFilename);
            imagepng($_img, $pngfile);
        }

        $response = $next($request, $response);

        return $response;
    }
}