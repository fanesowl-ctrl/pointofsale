<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $qrisImage = DB::table('payment_settings')->where('setting_key', 'qris_image')->value('setting_value');
        return view('admin.payment_settings.index', compact('qrisImage'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'qris_image.required' => 'Wajib memilih file gambar untuk diupload.',
            'qris_image.image' => 'File harus berupa gambar.',
            'qris_image.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'qris_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($request->hasFile('qris_image')) {
            // Delete old image if exists
            $oldImage = DB::table('payment_settings')->where('setting_key', 'qris_image')->value('setting_value');
            if ($oldImage && Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }

            // Store new image
            $path = $request->file('qris_image')->store('payment_images', 'public');

            // Update database
            DB::table('payment_settings')->updateOrInsert(
                ['setting_key' => 'qris_image'],
                ['setting_value' => $path, 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan QRIS berhasil diperbarui!');
    }
    public function destroy()
    {
        $oldImage = DB::table('payment_settings')->where('setting_key', 'qris_image')->value('setting_value');

        if ($oldImage) {
            if (Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }
            
            DB::table('payment_settings')->where('setting_key', 'qris_image')->delete();
            
            return redirect()->back()->with('success', 'Gambar QRIS berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Tidak ada gambar QRIS untuk dihapus.');
    }
}
