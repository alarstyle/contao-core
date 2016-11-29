<?php

namespace Grow\Controllers;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Controllers\BackendMain;
use Contao\Date;
use Contao\Environment;
use Contao\FileUpload;
use Contao\File;
use Contao\Folder;
use Contao\Input;
use Contao\Message;
use Contao\Session;
use Contao\Validator;

use Grow\ActionData;

use Symfony\Component\Finder\Finder;

class FileManagerController extends BackendMain
{


    protected $session;


    public function __construct($config = null)
    {
        parent::__construct();

        $this->session = Session::getInstance();
    }


    public function ajaxList()
    {
        $items = [];

        $uploadPath = Config::get('uploadPath');
        $path = Input::post('path');
        $dir = TL_ROOT . $path;


        if (empty($path) || !is_dir($dir)) {
            return;
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
                    'lastmodified' => Date::parse(Config::get('datimFormat'), $file->getMTime())
                ]);
            }

            $items[] = $info;
        }

        $this->session->set('filemanager_path', $path);

        ActionData::data('items', $items);
    }


    public function ajaxUpload()
    {
        $uploadPath = ltrim(Input::post('upload_path'), '/');
        $objUploader = new FileUpload();

        $arrUploaded = $objUploader->uploadTo($uploadPath);

        ActionData::data('aa', $uploadPath);

        if ($objUploader->hasError()) {
            ActionData::error('Error uploading');
        }
    }


    public function ajaxNewFolder()
    {
        $path = Input::post('path');
        $folderName = Input::post('folderName');
        $dir = TL_ROOT . $path . '/' . $folderName;

        if (!$this->validateFolderName($path, $folderName)) {
            return;
        }

        if (is_dir($dir)) {
            ActionData::error('Folder with this name already exists');
            return;
        }

        new Folder($path . '/' . $folderName);
    }


    public function ajaxRename()
    {
        $path = Input::post('path');
        $newName = Input::post('newName');

        if (empty($newName)) {
            ActionData::error('Name cannot be empty');
            return;
        }

        if (Validator::isInsecurePath($path)) {
            $this->log('Trying to rename with insecure path "'.$path.'"', __METHOD__, TL_ERROR);
            ActionData::error('Insecure path');
            return;
        }

        // Check whether the parent folder is within the files directory
        if (!preg_match('/^\/'.preg_quote(Config::get('uploadPath'), '/').'/i', $path)) {
            $this->log('Parent folder "'.$path.'" is not within the files directory', __METHOD__, TL_ERROR);
            ActionData::error('Invalid path');
            return;
        }

        if (Validator::isInsecurePath($newName) || !Validator::isValidFileName($newName) || $newName[0] === '.')
        {
            ActionData::error('Invalid name');
            return;
        }

        $fullPath = TL_ROOT . $path;
        $newFullPath = dirname($path) . '/' . $newName;

        if (is_file($fullPath)) {
            $strExtension = strtolower(substr($newName, strrpos($newName, '.') + 1));
            if (!in_array($strExtension, trimsplit(',', strtolower(Config::get('uploadTypes')))))
            {
                ActionData::error(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $strExtension));
                return;
            }
            $file = new File($path);
            $file->renameTo($newFullPath);
        }
        elseif (is_dir($fullPath)) {
            $folder = new Folder($path);
            $folder->renameTo($newFullPath);
        }
        else {
            ActionData::error('Cannot find original destination');
        }

    }


    public function ajaxDelete()
    {
        $path = Input::post('path');
        $fullPath = TL_ROOT . $path;

        if (Validator::isInsecurePath($path)) {
            $this->log('Trying to delete with insecure path "'.$path.'"', __METHOD__, TL_ERROR);
            ActionData::error('Insecure path');
            return;
        }

        // Check whether the parent folder is within the files directory
        if (!preg_match('/^\/'.preg_quote(Config::get('uploadPath'), '/').'/i', $path)) {
            $this->log('Parent folder "'.$path.'" is not within the files directory', __METHOD__, TL_ERROR);
            ActionData::error('Invalid path');
            return;
        }

        if (is_file($fullPath)) {
            $file = new File($path);
            $file->delete();
        }
        elseif (is_dir($fullPath)) {
            $folder = new Folder($path);
            $folder->delete();
        }
    }


    protected function generateMainSection()
    {
        $objTemplate = new BackendTemplate('be_filemanager');

        $savedPath = $this->session->get('filemanager_path');

        if (!is_dir(TL_ROOT . $savedPath)) {
            $savedPath = null;
        }

        $objTemplate->uploadPath = '/' . Config::get('uploadPath');
        $objTemplate->startPath = $savedPath ?: $objTemplate->uploadPath;
        $objTemplate->baseUrl = Environment::get('base');

        $this->Template->main = $objTemplate->parse();
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


    protected function validateFolderName($path, $folderName)
    {
        if (empty($path) || empty($folderName)) {
            ActionData::error('No path specified');
            return false;
        }

        if (Validator::isInsecurePath($path) || Validator::isInsecurePath($folderName) || !Validator::isValidFileName($folderName) || $folderName[0] === '.')
        {
            $this->log('Folder "'.$path.'" is not valid', __METHOD__, TL_ERROR);
            ActionData::error('Invalid folder name');
            return false;
        }

        // Check whether the parent folder is within the files directory
        if (!preg_match('/^\/'.preg_quote(Config::get('uploadPath'), '/').'/i', $path)) {
            $this->log('Parent folder "'.$path.'" is not within the files directory', __METHOD__, TL_ERROR);
            ActionData::error('Invalid folder path');
            return false;
        }

        return true;
    }

}