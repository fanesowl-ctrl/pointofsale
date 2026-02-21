<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $members = Member::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($limit);

        return view('admin.members.index', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:members,code',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Member::create($request->all());

        return redirect()->route('admin.members.index')->with('success', 'Member berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|unique:members,code,' . $id,
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        // Handle checkbox logic for is_active if boolean doesn't catch it
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $member->update($data);

        return redirect()->route('admin.members.index')->with('success', 'Member berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Member::destroy($id);
        return redirect()->route('admin.members.index')->with('success', 'Member berhasil dihapus.');
    }
}
