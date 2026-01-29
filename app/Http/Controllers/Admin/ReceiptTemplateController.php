<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceiptTemplate;
use Illuminate\Http\Request;

class ReceiptTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = ReceiptTemplate::orderBy('type')->orderBy('name')->paginate(15);
        return view('Admin.receipt-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templateTypes = [
            'sale' => 'Sales Receipt',
            'refund' => 'Refund Receipt',
            'purchase' => 'Purchase Order',
            'expense' => 'Expense Receipt',
            'credit' => 'Credit Note',
        ];
        
        $paperSizes = [
            '80mm' => '80mm (Thermal Printer)',
            'A4' => 'A4',
            'Letter' => 'Letter',
        ];
        
        $orientations = [
            'portrait' => 'Portrait',
            'landscape' => 'Landscape',
        ];
        
        return view('Admin.receipt-templates.create', compact('templateTypes', 'paperSizes', 'orientations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sale,refund,purchase,expense,credit',
            'header_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
            'css_styles' => 'nullable|string',
            'settings' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'paper_size' => 'required|in:80mm,A4,Letter',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $data = $request->all();
        
        // Handle settings as JSON
        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings']);
        }

        $template = ReceiptTemplate::create($data);

        // Set as default if requested
        if ($request->boolean('is_default')) {
            $template->setAsDefault();
        }

        return redirect()->route('superadmin.receipt-templates.index')
            ->with('success', '🎉 Receipt template "' . $template->name . '" has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceiptTemplate $receiptTemplate)
    {
        return view('Admin.receipt-templates.show', compact('receiptTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReceiptTemplate $receiptTemplate)
    {
        $templateTypes = [
            'sale' => 'Sales Receipt',
            'refund' => 'Refund Receipt',
            'purchase' => 'Purchase Order',
            'expense' => 'Expense Receipt',
            'credit' => 'Credit Note',
        ];
        
        $paperSizes = [
            '80mm' => '80mm (Thermal Printer)',
            'A4' => 'A4',
            'Letter' => 'Letter',
        ];
        
        $orientations = [
            'portrait' => 'Portrait',
            'landscape' => 'Landscape',
        ];
        
        return view('Admin.receipt-templates.edit', compact('receiptTemplate', 'templateTypes', 'paperSizes', 'orientations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceiptTemplate $receiptTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sale,refund,purchase,expense,credit',
            'header_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
            'css_styles' => 'nullable|string',
            'settings' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'paper_size' => 'required|in:80mm,A4,Letter',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $data = $request->all();
        
        // Handle settings as JSON
        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings']);
        }

        $receiptTemplate->update($data);

        // Set as default if requested
        if ($request->boolean('is_default')) {
            $receiptTemplate->setAsDefault();
        }

        return redirect()->route('superadmin.receipt-templates.index')
            ->with('success', '✅ Receipt template "' . $receiptTemplate->name . '" has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceiptTemplate $receiptTemplate)
    {
        $receiptTemplate->delete();

        return redirect()->route('superadmin.receipt-templates.index')
            ->with('success', '🗑️ Receipt template "' . $receiptTemplate->name . '" has been deleted successfully!');
    }

    /**
     * Preview the template
     */
    public function preview(ReceiptTemplate $receiptTemplate)
    {
        return view('Admin.receipt-templates.preview', compact('receiptTemplate'));
    }

    /**
     * Set template as default
     */
    public function setDefault(ReceiptTemplate $receiptTemplate)
    {
        $receiptTemplate->setAsDefault();
        
        return redirect()->route('superadmin.receipt-templates.index')
            ->with('success', '⭐ "' . $receiptTemplate->name . '" has been set as the default ' . $receiptTemplate->type . ' template!');
    }
}
