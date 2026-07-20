<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileStorageController extends Controller {
    public function ftp() {
        $pageTitle = "FTP Configuration";
        return view('admin.storage.ftp', compact('pageTitle'));
    }

    public function ftpUpdate(Request $request) {
        $request->validate([
            'ftp.host'     => 'required',
            'ftp.username' => 'required',
            'ftp.password' => 'required',
            'ftp.port'     => 'required',
            'ftp.root'     => 'required',
            'ftp.domain'   => 'required',
        ]);
        $setting      = gs();
        $setting->ftp = $request->ftp;
        $setting->save();

        $notify[] = ['success', 'FTP configuration updated successfully'];
        return back()->withNotify($notify);
    }
    public function wasabi() {
        $pageTitle = "Wasabi Configuration";
        return view('admin.storage.wasabi', compact('pageTitle'));
    }

    public function wasabiUpdate(Request $request) {
        $request->validate([
            'wasabi.driver'   => 'required',
            'wasabi.key'      => 'required',
            'wasabi.secret'   => 'required',
            'wasabi.region'   => 'required',
            'wasabi.bucket'   => 'required',
            'wasabi.endpoint' => 'required',
        ]);

        $setting         = gs();
        $setting->wasabi = $request->wasabi;
        $setting->save();

        $notify[] = ['success', 'Wasabi configuration updated successfully'];
        return back()->withNotify($notify);
    }
    public function digitalOcean() {
        $pageTitle = "Digital Ocean Configuration";
        $setting   = gs();
        return view('admin.storage.digital_ocean', compact('pageTitle', 'setting'));
    }

    public function digitalOceanUpdate(Request $request) {
        $request->validate([
            'digital_ocean.driver'   => 'required',
            'digital_ocean.key'      => 'required',
            'digital_ocean.secret'   => 'required',
            'digital_ocean.region'   => 'required',
            'digital_ocean.bucket'   => 'required',
            'digital_ocean.endpoint' => 'required',
        ]);
        $setting                = gs();
        $setting->digital_ocean = $request->digital_ocean;
        $setting->save();

        $notify[] = ['success', 'Digital ocean configuration updated successfully'];
        return back()->withNotify($notify);
    }

    public function backblaze() {
        $pageTitle = "Backblaze Configuration";
        $setting   = gs();
        return view('admin.storage.backblaze', compact('pageTitle', 'setting'));
    }

    public function backblazeUpdate(Request $request) {
        $request->validate([
            'backblaze.driver'   => 'required',
            'backblaze.key'      => 'required',
            'backblaze.secret'   => 'required',
            'backblaze.region'   => 'required',
            'backblaze.bucket'   => 'required',
            'backblaze.endpoint' => 'required',
        ]);
        $setting                = gs();
        $setting->backblaze = $request->backblaze;
        $setting->save();

        $notify[] = ['success', 'Backblaze configuration updated successfully'];
        return back()->withNotify($notify);
    }
}
