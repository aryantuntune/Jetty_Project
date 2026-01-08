@extends('layouts.admin')

@section('page-title', 'Manage Rooms')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        @foreach($rooms as $room)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row">
                <!-- Image & Preview -->
                <div class="md:w-1/4 h-48 md:h-auto overflow-hidden relative group">
                    <img src="{{ $room->image_url }}" alt="{{ $room->name }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur text-white text-xs font-bold px-2 py-1 rounded">
                        ID: {{ $room->id }}
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="p-6 md:w-3/4">
                    <form action="{{ route('admin.houseboat.rooms.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <input type="text" name="name" value="{{ $room->name }}"
                                    class="text-xl font-bold text-slate-900 border-none p-0 focus:ring-0 w-full mb-1"
                                    placeholder="Room Name">
                                <p class="text-sm text-slate-500">{{ count($room->amenities ?? []) }} Amenities
                                    Listed</p>
                            </div>
                            <button type="submit"
                                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Save Changes
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Price
                                        per Night (₹)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">₹</span>
                                        <input type="number" name="price" value="{{ $room->price }}"
                                            class="w-full pl-8 pr-4 py-2 rounded-lg border border-slate-200 text-sm font-bold text-slate-800 focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total
                                        Rooms</label>
                                    <input type="number" name="total_rooms" value="{{ $room->total_rooms }}"
                                        class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Capacity</label>
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <span class="text-xs text-slate-400 block">Adults</span>
                                            <input type="number" name="capacity_adults" value="{{ $room->capacity_adults }}"
                                                class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                                        </div>
                                        <div class="flex-1">
                                            <span class="text-xs text-slate-400 block">Kids</span>
                                            <input type="number" name="capacity_kids" value="{{ $room->capacity_kids }}"
                                                class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                            <textarea name="description" rows="2"
                                class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-primary-500 focus:border-primary-500 resize-none">{{ $room->description }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection