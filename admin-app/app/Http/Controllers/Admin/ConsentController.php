<?php

namespace App\Http\Controllers\Admin;

use App\Models\Consent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsentController extends Controller
{
    public function index()
    {
        $consents = Consent::all();
        return view('admin.consents.index', compact('consents'));
    }

    public function create()
    {
        return view('admin.consents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'consent_privacy' => 'required|boolean',
            'consent_marketing' => 'required|boolean',
            'consent_data_personal' => 'required|boolean',
        ]);

        Consent::create($request->all());
        return redirect()->route('admin.consents.index')->with('success', 'Consent created successfully.');
    }

    public function edit(Consent $consent)
    {
        return view('admin.consents.edit', compact('consent'));
    }

    public function update(Request $request, Consent $consent)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'consent_privacy' => 'required|boolean',
            'consent_marketing' => 'required|boolean',
            'consent_data_personal' => 'required|boolean',
        ]);

        $consent->update($request->all());
        return redirect()->route('admin.consents.index')->with('success', 'Consent updated successfully.');
    }

    public function destroy(Consent $consent)
    {
        $consent->delete();
        return redirect()->route('admin.consents.index')->with('success', 'Consent deleted successfully.');
    }
}
