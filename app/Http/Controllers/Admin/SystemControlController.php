<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SystemSetting;
use Inertia\Inertia;

class SystemControlController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SystemControl/Index', [
            'settings' => SystemSetting::orderBy('group')->get(),
        ]);
    }

    public function update(Request $request, SystemSetting $setting)
    {
        $validated = $request->validate([
            'value' => 'required',
        ]);

        $setting->update([
            'value' => $validated['value'],
        ]);

        return back()->with('success', "Setting '{$setting->label}' berhasil diperbarui.");
    }

    public function runCommand(Request $request)
    {
        $command = $request->input('command');

        try {
            switch ($command) {
                case 'optimize':
                    \Illuminate\Support\Facades\Artisan::call('optimize');
                    $msg = "Aplikasi berhasil di-optimasi!";
                    break;
                case 'clear-cache':
                    \Illuminate\Support\Facades\Artisan::call('cache:clear');
                    $msg = "Cache aplikasi berhasil dibersihkan!";
                    break;
                case 'clear-view':
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                    $msg = "Cache view berhasil dibersihkan!";
                    break;
                case 'clear-logs':
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        file_put_contents($logFile, '');
                    }
                    $msg = "File log berhasil dikosongkan!";
                    break;
                default:
                    throw new \Exception("Perintah tidak dikenal.");
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', "Gagal menjalankan perintah: " . $e->getMessage());
        }
    }
}
