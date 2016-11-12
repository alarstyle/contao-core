<?php

namespace Contao\Controllers;

use Contao\Config;
use Contao\FileUpload;
use Contao\Input;
use Contao\Message;

use Symfony\Component\Finder\Finder;

class FileManagerController extends BackendMain
{

    public function run() {

        header('Content-type: application/json');

        if (Input::post('action')) {
            switch (Input::post('action')) {

                case 'upload':

                    $uploadPath = ltrim(Input::post('upload_path'), '/');
                    $objUploader = new FileUpload();

                    $arrUploaded = $objUploader->uploadTo($uploadPath);

                    if ($objUploader->hasError()) {
                        die(json_encode('error'));
                    }

                    die(json_encode('ok'));
                    break;

                default:
                    die(json_encode('no such action'));
                    break;

            }
        }

        $data = [
            'items' => []
        ];

        $path = Input::get('path');
        $dir = TL_ROOT . $path;

        if (empty($path) || !is_dir($dir)) {
            die(json_encode($data));
        }

        $finder = new Finder();
        $finder->depth(0)->in($dir);

        foreach ($finder as $file) {

            $info = [
                'name'     => $file->getFilename(),
                'mime'     => 'application/'.($file->isDir() ? 'folder':'file'),
                'path'     => $this->normalizePath($path.'/'.$file->getFilename()),
                //'url'      => ltrim(App::url()->getStatic($file->getPathname(), [], 'base'), '/'),
                //'writable' => $mode == 'w'
            ];

            if (!$file->isDir()) {
                $info = array_merge($info, [
                    'size'         => $this->formatFileSize($file->getSize()),
                    'lastmodified' => date(\DateTime::ATOM, $file->getMTime())
                ]);
            }

            $data['items'][] = $info;
        }


        die(json_encode($data));
    }



    /**
     * Normalizes the given path
     *
     * @param  string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        $path   = str_replace(['\\', '//'], '/', $path);
        $prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
        $path   = substr($path, strlen($prefix));
        $parts  = array_filter(explode('/', $path), 'strlen');
        $tokens = [];

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($tokens);
            } elseif ('.' !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode('/', $tokens);
    }


    protected function formatFileSize($size)
    {
        if ($size == 0) {
            return 'n/a';
        }

        $sizes = ['%d B', '%d  KB', '%d  MB', '%d  GB', '%d TB', '%d PB', '%d EB', '%d ZB', '%d YB'];
        $size  = round($size/pow(1024, ($i = floor(log($size, 1024)))), 2);
        return sprintf($sizes[$i], $size);
    }

}