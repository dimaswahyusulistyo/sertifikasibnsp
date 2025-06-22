<?php

namespace App\Http\Controllers;

use App\Models\InventarisModel;
use App\Models\Produk;
use Illuminate\Http\Request;
use App\Exports\InventarisExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $produks = InventarisModel::latest()->get();
        return view('page.dataInventaris', compact('produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('page.formInput');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi_produk' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok_barang' => 'required|integer|min:0'
        ]);

        InventarisModel::create($validated);

        return redirect()->route('data.inventaris')
                         ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventarisModel $produk)
    {
        return view('page.detailProduk', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = InventarisModel::findOrFail($id);
        return view('page.formEdit', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventarisModel $produk)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi_produk' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok_barang' => 'required|integer|min:0'
        ]);

        $produk->update($validated);

        return redirect()->route('data.inventaris')
                         ->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = InventarisModel::findOrFail($id);
        $produk->delete();

        return redirect()->route('data.inventaris')
                        ->with('success', 'Produk berhasil dihapus');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'latest');
        $type = $request->input('type', 'xlsx');
        
        $filename = 'Data_Inventaris_' . now()->timezone('Asia/Jakarta')->format('Y-m-d_His');
        
        $query = InventarisModel::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', '%'.$search.'%')
                ->orWhere('deskripsi_produk', 'like', '%'.$search.'%')
                ->orWhere('harga', 'like', '%'.$search.'%')
                ->orWhere('stok_barang', 'like', '%'.$search.'%');
            });
        }
        
        // Sorting logic yang lebih lengkap
        $sortParts = explode('-', $sort);
        $sortField = $sortParts[0];
        $sortDirection = $sortParts[1] ?? 'desc';
        
        switch ($sortField) {
            case 'name':
                $query->orderBy('nama_produk', $sortDirection);
                break;
            case 'price':
                $query->orderBy('harga', $sortDirection);
                break;
            case 'stock':
                $query->orderBy('stok_barang', $sortDirection);
                break;
            case 'created':
                $query->orderBy('created_at', $sortDirection);
                break;
            case 'updated':
                $query->orderBy('updated_at', $sortDirection);
                break;
            default:
                $query->latest();
        }
        
        $produks = $query->get();
        
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('exports.inventarisExport', compact('produks', 'search'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ]);
                
            return $pdf->download($filename . '.pdf');
        }
        
        // Default export Excel
        return Excel::download(new InventarisExport($produks), $filename . '.xlsx');
    }
}