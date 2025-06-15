@extends('adminlte::page')

@section('title', $news->title)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $news->title }}</h1>
        <div>
            @if(auth()->user()->role->name === 'wartawan' && $news->user_id === auth()->id())
                <a href="{{ route('news.edit', $news->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('news.destroy', $news->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            @endif
            @if(auth()->user()->role->name === 'editor')
                @if($news->status === 'draft')
                    <form action="{{ route('news.publish', $news->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Publish
                        </button>
                    </form>
                @else
                    <form action="{{ route('news.unpublish', $news->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-times"></i> Unpublish
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <strong>Kategori:</strong> {{ $news->category->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Penulis:</strong> {{ $news->user->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Tanggal:</strong> {{ $news->created_at->format('d F Y H:i') }}
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <span class="badge badge-{{ $news->status === 'published' ? 'success' : 'warning' }}">
                                    {{ $news->status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                        @if($news->image)
                            <div class="col-md-4">
                                <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded">
                            </div>
                        @endif
                    </div>

                    <div class="content">
                        {!! $news->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .content img {
            max-width: 100%;
            height: auto;
        }
        .content table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .content table td,
        .content table th {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
        }
    </style>
@stop 