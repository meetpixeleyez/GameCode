<?php

namespace App\Lib;

use Aws\S3\S3Client;
use App\Constants\Status;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FileUploader
{
    private $general;

    public $path;
    public $file;
    public $fileSize;
    public $oldFile;
    public $fileName;
    public $error;

    public function __construct()
    {
        $this->general = gs();
    }

    public function upload()
    {
        if ($this->general->file_server == Status::SERVER_CURRENT) {
            return $this->uploadToCurrentServer();
        }
        if ($this->general->file_server == Status::SERVER_FTP) {
            $this->uploadToServer('ftp');
        }
        if ($this->general->file_server == Status::SERVER_WASABI) {
            $this->uploadToServer('wasabi');
        }
        if ($this->general->file_server == Status::SERVER_DIGITAL_OCEAN) {
            $this->uploadToServer('digital_ocean');
        }
        if ($this->general->file_server == Status::SERVER_BACKBLAZE) {
            $this->uploadToServer('backblaze');
        }
    }

    public function configureFTP()
    {
        $general = $this->general;
        Config::set('filesystems.disks.ftp.driver', 'ftp');
        Config::set('filesystems.disks.ftp.domain', $general->ftp->domain);
        Config::set('filesystems.disks.ftp.host', $general->ftp->host);
        Config::set('filesystems.disks.ftp.username', $general->ftp->username);
        Config::set('filesystems.disks.ftp.password', $general->ftp->password);
        Config::set('filesystems.disks.ftp.port', 21);
        Config::set('filesystems.disks.ftp.root', $general->ftp->root);
    }

    public function configureDisk($server)
    {
        $general = $this->general;

        // Config::set('filesystems.disks.' . $server . '.visibility', 'public');
        Config::set('filesystems.disks.' . $server . '.driver', $general->$server->driver);
        Config::set('filesystems.disks.' . $server . '.key', $general->$server->key);
        Config::set('filesystems.disks.' . $server . '.secret', $general->$server->secret);
        Config::set('filesystems.disks.' . $server . '.region', $general->$server->region);
        Config::set('filesystems.disks.' . $server . '.bucket', $general->$server->bucket);
        Config::set('filesystems.disks.' . $server . '.endpoint', $general->$server->endpoint);
    }

    public function uploadToCurrentServer()
    {
        try {
            $this->fileName = fileUploader($this->file, $this->path);
        } catch (\Exception $exp) {
            $this->error = true;
        }
    }

    public function uploadToServer($server = null)
    {
        try {
            if ($server == 'wasabi' || $server == 'digital_ocean' || $server == 'backblaze') {
                $this->configureDisk($server);
                $disk = Storage::disk($server);
            } else {
                $this->configureFTP();
                $disk = Storage::disk($server);
            }

            $fileExtension = $this->file->getClientOriginalExtension();
            $file = File::get($this->file);

            if ($server != 'backblaze') {
                $this->makeDirectory($this->path . '/', $disk);
            }

            $this->fileName = uniqid() . time() . '.' . $fileExtension;
            $disk->put($this->path . '/' . $this->fileName, $file);
        } catch (\Exception $e) {
            $this->error = true;
        }
    }

    protected function makeDirectory($path, $disk)
    {
        if ($disk->exists($path)) {
            return true;
        }
        $disk->makeDirectory($path);
    }

    public function removeOldFile()
    {
        $currentServer = $this->general->file_server;
        if ($currentServer == Status::SERVER_CURRENT) {
            $location = $this->path . $this->oldFile;
            fileManager()->removeFile($location);
        } else {
            try {
                if ($currentServer == Status::SERVER_WASABI) {
                    $server = 'wasabi';
                    $this->configureDisk($server);
                } else if ($currentServer == Status::SERVER_DIGITAL_OCEAN) {
                    $server = 'digital_ocean';
                    $this->configureDisk($server);
                } else if ($currentServer == Status::SERVER_FTP) {
                    $this->configureFTP();
                    $server = 'ftp';
                } else if ($currentServer == Status::SERVER_BACKBLAZE) {
                    $server = 'backblaze';
                    $this->configureDisk($server);
                }
                $disk = Storage::disk($server);
                $disk->delete($this->path . '/' . $this->oldFile);
            } catch (\Exception $e) {
                $this->error = true;
            }
        }
    }

    public function downloadFile($product, $orderItem = null, $column = 'file')
    {
        $filePath      = getFilePath('productFile') . productFilePath($product, $column);
        $general       = gs();
        $currentServer = $general->file_server;

        if ($currentServer == Status::SERVER_CURRENT) {

            if (!file_exists($filePath) || !is_file($filePath)) {
                $notify[] = ['error', 'File does not exist'];
                return back()->withNotify($notify);
            }
            if (!auth()->guard('reviewer')->check() && $orderItem != NULL) {
                $orderItem->downloads = 1;
                $orderItem->save();
            }
            return response()->download($filePath);
        } else {
            try {
                if ($currentServer == Status::SERVER_WASABI) {
                    $server = 'wasabi';
                    $this->configureDisk($server);
                } else if ($currentServer == Status::SERVER_DIGITAL_OCEAN) {
                    $server = 'digital_ocean';
                    $this->configureDisk($server);
                } else if ($currentServer == Status::SERVER_FTP) {
                    $server = 'ftp';
                    $this->configureFTP();
                } else if ($currentServer == Status::SERVER_BACKBLAZE) {
                    $server = 'backblaze';
                    $this->configureDisk($server);
                }
                $disk = Storage::disk($server);
                $data = $disk->getConfig();

                if (!$disk->exists($filePath)) {
                    $notify[] = ['error', 'File does not exist'];
                    return back()->withNotify($notify);
                }

                if ($currentServer == Status::SERVER_FTP) {
                    $ftpConnection = ftp_connect($data['host']);
                    ftp_login($ftpConnection, $data['username'], $data['password']);
                    ftp_pasv($ftpConnection, true);
                    $downloadPath = tempnam(sys_get_temp_dir(), 'ftp_download_');
                    if (ftp_get($ftpConnection, $downloadPath, $data['root'] . '/' . $filePath, FTP_BINARY)) {
                        header('Content-type: application/octet-stream');
                        header("Content-Disposition: attachment; filename=" . $product->$column);
                    }
                } else {
                    $endpoint = $general->$server->endpoint;
                    $credentials = new Credentials($data['key'], $data['secret']);

                    $s3Client = new S3Client([
                        'version'     => 'latest',
                        'region'      => @$general->$server->region,
                        'endpoint'    => $endpoint,
                        'credentials' => $credentials
                    ]);
                    $command = $s3Client->getCommand('GetObject', [
                        'Bucket' => $data['bucket'],
                        'Key' => $filePath,
                    ]);
                    $downloadPath =  (string) $s3Client->createPresignedRequest($command, '+1 hour')->getUri();
                    header('Content-type: application/octet-stream');
                    header("Content-Disposition: attachment; filename=" . $product->$column);
                }

                while (ob_get_level()) {
                    ob_end_clean();
                }

                if (!auth()->guard('reviewer')->check() && $orderItem != NULL) {
                    $orderItem->downloads = 1;
                    $orderItem->save();
                }

                return readfile($downloadPath);
            } catch (\Exception $e) {
                $this->error = true;
            }
        }
    }
}
