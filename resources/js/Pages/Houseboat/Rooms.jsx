import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Layout from '@/Layouts/Layout';
import {
    Home,
    Save,
    IndianRupee,
    Users,
    Baby,
} from 'lucide-react';

export default function HouseboatRooms({ rooms }) {
    const [roomData, setRoomData] = useState(
        rooms?.reduce((acc, room) => {
            acc[room.id] = {
                name: room.name || '',
                price: room.price || '',
                total_rooms: room.total_rooms || '',
                capacity_adults: room.capacity_adults || '',
                capacity_kids: room.capacity_kids || '',
                description: room.description || '',
            };
            return acc;
        }, {}) || {}
    );
    const [loading, setLoading] = useState({});

    const handleChange = (roomId, field, value) => {
        setRoomData((prev) => ({
            ...prev,
            [roomId]: {
                ...prev[roomId],
                [field]: value,
            },
        }));
    };

    const handleSubmit = (e, roomId) => {
        e.preventDefault();
        setLoading((prev) => ({ ...prev, [roomId]: true }));

        router.put(route('houseboat.rooms.update', roomId), roomData[roomId], {
            preserveScroll: true,
            onFinish: () => setLoading((prev) => ({ ...prev, [roomId]: false })),
        });
    };

    return (
        <>
            <Head>
                <title>Manage Rooms - Houseboat Admin</title>
            </Head>

            {/* Page Header */}
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-slate-800">Manage Rooms</h1>
                <p className="mt-1 text-sm text-slate-500">Edit houseboat room details and pricing</p>
            </div>

            {/* Room Cards */}
            <div className="grid grid-cols-1 gap-6">
                {rooms?.map((room) => (
                    <div
                        key={room.id}
                        className="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col md:flex-row"
                    >
                        {/* Image & Preview */}
                        <div className="md:w-1/4 h-48 md:h-auto overflow-hidden relative group">
                            <img
                                src={room.image_url}
                                alt={room.name}
                                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                            />
                            <div className="absolute top-2 left-2 bg-black/60 backdrop-blur text-white text-xs font-bold px-2 py-1 rounded">
                                ID: {room.id}
                            </div>
                        </div>

                        {/* Edit Form */}
                        <div className="p-6 md:w-3/4">
                            <form onSubmit={(e) => handleSubmit(e, room.id)}>
                                <div className="flex justify-between items-start mb-4">
                                    <div className="flex-1 mr-4">
                                        <input
                                            type="text"
                                            value={roomData[room.id]?.name || ''}
                                            onChange={(e) => handleChange(room.id, 'name', e.target.value)}
                                            className="text-xl font-bold text-slate-900 border-none p-0 focus:ring-0 w-full mb-1 bg-transparent outline-none"
                                            placeholder="Room Name"
                                        />
                                        <p className="text-sm text-slate-500">
                                            {(room.amenities || []).length} Amenities Listed
                                        </p>
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={loading[room.id]}
                                        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 disabled:opacity-70"
                                    >
                                        {loading[room.id] ? (
                                            <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                        ) : (
                                            <Save className="w-4 h-4" />
                                        )}
                                        Save Changes
                                    </button>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="space-y-4">
                                        <div>
                                            <label className="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                                Price per Night
                                            </label>
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                                                    <IndianRupee className="w-4 h-4" />
                                                </span>
                                                <input
                                                    type="number"
                                                    value={roomData[room.id]?.price || ''}
                                                    onChange={(e) => handleChange(room.id, 'price', e.target.value)}
                                                    className="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 text-sm font-bold text-slate-800 focus:ring-indigo-500 focus:border-indigo-500"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label className="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                                Total Rooms
                                            </label>
                                            <div className="relative">
                                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                                                    <Home className="w-4 h-4" />
                                                </span>
                                                <input
                                                    type="number"
                                                    value={roomData[room.id]?.total_rooms || ''}
                                                    onChange={(e) => handleChange(room.id, 'total_rooms', e.target.value)}
                                                    className="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div className="space-y-4">
                                        <div>
                                            <label className="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                                Capacity
                                            </label>
                                            <div className="flex gap-4">
                                                <div className="flex-1">
                                                    <span className="text-xs text-slate-400 block mb-1 flex items-center gap-1">
                                                        <Users className="w-3 h-3" /> Adults
                                                    </span>
                                                    <input
                                                        type="number"
                                                        value={roomData[room.id]?.capacity_adults || ''}
                                                        onChange={(e) => handleChange(room.id, 'capacity_adults', e.target.value)}
                                                        className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm"
                                                    />
                                                </div>
                                                <div className="flex-1">
                                                    <span className="text-xs text-slate-400 block mb-1 flex items-center gap-1">
                                                        <Baby className="w-3 h-3" /> Kids
                                                    </span>
                                                    <input
                                                        type="number"
                                                        value={roomData[room.id]?.capacity_kids || ''}
                                                        onChange={(e) => handleChange(room.id, 'capacity_kids', e.target.value)}
                                                        className="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-4 pt-4 border-t border-slate-100">
                                    <label className="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                        Description
                                    </label>
                                    <textarea
                                        rows="2"
                                        value={roomData[room.id]?.description || ''}
                                        onChange={(e) => handleChange(room.id, 'description', e.target.value)}
                                        className="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                ))}

                {(!rooms || rooms.length === 0) && (
                    <div className="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <div className="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4 mx-auto">
                            <Home className="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 className="text-lg font-medium text-slate-800 mb-1">No rooms found</h3>
                        <p className="text-sm text-slate-500">Add rooms to the houseboat to manage them here.</p>
                    </div>
                )}
            </div>
        </>
    );
}

HouseboatRooms.layout = (page) => <Layout>{page}</Layout>;
