@extends('layouts.admin', ['title' => 'Edit Category | AURA Admin'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Edit Category: {{ $category->name }}</h1>
            <p class="text-xs text-slate-500">Update category details and storefront status.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-500 hover:text-slate-900 font-semibold">&larr; Back to Categories</a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white border border-slate-200 rounded-xl p-6 space-y-5 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Name *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 font-mono text-xs focus:outline-none focus:border-slate-900">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cover Image URL</label>
            <input type="url" name="image" value="{{ old('image', $category->image) }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="flex items-center space-x-2 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-0">
            <label for="is_active" class="text-xs text-slate-700 font-semibold">Active on storefront</label>
        </div>

        <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-900 hover:bg-slate-800 text-white transition shadow-sm">Save Changes</button>
        </div>
    </form>
</div>
@endsection
