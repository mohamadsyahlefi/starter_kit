<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !in_array(auth()->user()->role->name, ['admin', 'wartawan', 'editor'])) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::with(['category', 'user']);

        // Filter berdasarkan role
        if (auth()->user()->role->name === 'wartawan') {
            $query->where('user_id', auth()->id());
        }

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $news = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('news.index', compact('news', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!in_array(auth()->user()->role->name, ['admin', 'wartawan'])) {
            return redirect()->route('news.index')
                ->with('error', 'Hanya admin dan wartawan yang dapat menambah berita.');
        }

        $categories = Category::all();
        return view('news.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role->name, ['admin', 'wartawan'])) {
            return redirect()->route('news.index')
                ->with('error', 'Hanya admin dan wartawan yang dapat menambah berita.');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title);
        $data['status'] = 'draft'; // Always set status to draft on creation

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/news'), $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        News::create($data);

        return redirect()->route('news.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        // Wartawan hanya bisa melihat berita mereka sendiri, admin dan editor bisa melihat semua
        if (auth()->user()->role->name === 'wartawan' && $news->user_id !== auth()->id()) {
            return redirect()->route('news.index')
                ->with('error', 'Anda tidak memiliki akses ke berita ini.');
        }

        return view('news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        // Wartawan hanya bisa mengedit berita mereka sendiri, admin dan editor bisa mengedit semua
        if (auth()->user()->role->name === 'wartawan' && $news->user_id !== auth()->id()) {
            return redirect()->route('news.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit berita ini.');
        }

        $categories = Category::all();
        return view('news.edit', compact('news', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        // Wartawan hanya bisa mengupdate berita mereka sendiri
        if (auth()->user()->role->name === 'wartawan' && $news->user_id !== auth()->id()) {
            return redirect()->route('news.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengupdate berita ini.');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['image']);
        $data['slug'] = Str::slug($request->title);

        // Hanya admin dan editor yang dapat mengubah status
        if (in_array(auth()->user()->role->name, ['admin', 'editor'])) {
            $request->validate([
                'status' => 'required|in:draft,published'
            ]);
            $data['status'] = $request->status;
        } else {
            // Untuk wartawan, status tidak boleh diubah dari sisi mereka
            $data['status'] = $news->status;
        }

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($news->image && file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/news'), $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        $news->update($data);

        return redirect()->route('news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        // Wartawan hanya bisa menghapus berita mereka sendiri
        if (auth()->user()->role->name === 'wartawan' && $news->user_id !== auth()->id()) {
            return redirect()->route('news.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus berita ini.');
        }

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    public function publish(News $news)
    {
        if (!in_array(auth()->user()->role->name, ['admin', 'editor'])) {
            return redirect()->route('news.index')
                ->with('error', 'Hanya admin dan editor yang dapat mempublish berita.');
        }

        $news->update(['status' => 'published']);

        return redirect()->route('news.show', $news)
            ->with('success', 'Berita berhasil dipublish.');
    }

    public function unpublish(News $news)
    {
        if (!in_array(auth()->user()->role->name, ['admin', 'editor'])) {
            return redirect()->route('news.index')
                ->with('error', 'Hanya admin dan editor yang dapat unpublish berita.');
        }

        $news->update(['status' => 'draft']);

        return redirect()->route('news.show', $news)
            ->with('success', 'Berita berhasil diunpublish.');
    }
}
