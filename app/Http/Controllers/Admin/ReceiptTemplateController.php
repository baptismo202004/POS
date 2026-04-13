<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceiptTemplate;
use Illuminate\Http\Request;

class ReceiptTemplateController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $templates = ReceiptTemplate::orderBy('created_at', 'desc')->get();

        return view('Admin.receipt-templates.index', compact('templates'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('Admin.receipt-templates.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ReceiptTemplate::create($request->only('name', 'content', 'is_default'));

        return redirect()->route('receipt-templates.index')->with('success', 'Template created.');
    }

    public function show(ReceiptTemplate $receiptTemplate): \Illuminate\View\View
    {
        return view('Admin.receipt-templates.show', compact('receiptTemplate'));
    }

    public function edit(ReceiptTemplate $receiptTemplate): \Illuminate\View\View
    {
        return view('Admin.receipt-templates.edit', compact('receiptTemplate'));
    }

    public function update(Request $request, ReceiptTemplate $receiptTemplate): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $receiptTemplate->update($request->only('name', 'content', 'is_default'));

        return redirect()->route('receipt-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(ReceiptTemplate $receiptTemplate): \Illuminate\Http\RedirectResponse
    {
        $receiptTemplate->delete();

        return redirect()->route('receipt-templates.index')->with('success', 'Template deleted.');
    }

    public function preview(ReceiptTemplate $receiptTemplate): \Illuminate\View\View
    {
        return view('Admin.receipt-templates.preview', compact('receiptTemplate'));
    }

    public function setDefault(ReceiptTemplate $receiptTemplate): \Illuminate\Http\RedirectResponse
    {
        ReceiptTemplate::query()->update(['is_default' => false]);
        $receiptTemplate->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default template set.');
    }
}
