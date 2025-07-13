<?php

namespace App\Http\Controllers;

use App\Http\Requests\addTargetRequest;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TargetController extends Controller
{

    public function index()
    {
        $targets = Target::where('user_id', Auth::id())
            ->with('progres')
            ->latest()
            ->get()
            ->map(function ($target) {
                $totalSetoran = $target->progres->sum('setoran');
                $totalTarget = ($target->ticket ?? 0) + ($target->food ?? 0) + ($target->transport ?? 0) + ($target->others ?? 0);
                $progressPercentage = $totalTarget > 0
                    ? round(($totalSetoran / $totalTarget) * 100, 2)
                    : 0;

                return [
                    'id' => $target->id,
                    'user_id' => $target->user_id,
                    'title' => $target->title,
                    'ticket' => $target->ticket,
                    'food' => $target->food,
                    'transport' => $target->transport,
                    'others' => $target->others,
                    'total_target' => $totalTarget,
                    'total_setoran' => $totalSetoran,
                    'persentase_progres' => $progressPercentage,
                    'image_path' => $target->image_path ? asset('storage/' . $target->image_path) : null,
                    'location_name' => $target->location_name,
                    'latitude' => $target->latitude,
                    'longitude' => $target->longitude,
                    'status' => $target->status,
                    'created_at' => $target->created_at,
                    'updated_at' => $target->updated_at,
                    'progres' => $target->progres->map(function ($progres) {
                        return [
                            'id' => $progres->id,
                            'target_id' => $progres->target_id,
                            'setoran' => $progres->setoran,
                            'tanggal_setoran' => $progres->tanggal_setoran,
                            'waktu_setoran' => $progres->waktu_setoran,
                            'created_at' => $progres->created_at,
                            'updated_at' => $progres->updated_at,
                        ];
                    }),
                ];
            });

        return response()->json([
            'status_code' => 200,
            'message' => 'Semua data target dan progres berhasil diambil.',
            'data' => $targets,
        ]);
    }

    public function store(addTargetRequest $request)
    {
        $existingTarget = Target::where('user_id', Auth::id())->where('status', '!=', 'selesai')->first();

        if ($existingTarget) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Kamu masih memiliki target yang belum tercapai.'
            ], 403);
        }

        if ($request->hasFile('image_path')) {
            $filename = time() . '_' . Str::slug($request['title']) . '.' . $request->file('image_path')->extension();
            $path = $request->file('image_path')->storeAs('targets', $filename, 'public');
            $validated['image_path'] = $path;
        }

        $validated['user_id'] = Auth::id();

        $target = Target::create($validated);

        return response()->json([
            'status_code' => 201,
            'message' => 'Target berhasil dibuat.',
            'data' => $target
        ]);
    }

    public function update(Request $request, $id)
    {
        $target = Target::where('user_id', Auth::id())->find($id);
        Log::info('File upload detected.');
        if (!$target) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Target tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'ticket'        => 'nullable|numeric|min:0',
            'food'          => 'nullable|numeric|min:0',
            'transport'     => 'nullable|numeric|min:0',
            'others'        => 'nullable|numeric|min:0',
            'location_name' => 'sometimes|required|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'image_path'    => 'sometimes|required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image_path')) {
            if ($target->image_path && Storage::disk('public')->exists($target->image_path)) {
                Storage::disk('public')->delete($target->image_path);
                Log::info('Old image deleted: ' . $target->image_path);
            }

            $slugTitle = isset($validated['title']) ? Str::slug($validated['title']) : Str::slug($target->title);
            $filename = time() . '_' . $slugTitle . '.' . $request->file('image_path')->extension();
            $path = $request->file('image_path')->storeAs('targets', $filename, 'public');
            $validated['image_path'] = $path;

            Log::info('New image stored: ' . $path);
        }

        $target->update($validated);

        return response()->json([
            'status_code' => 200,
            'message' => 'Target berhasil diperbarui.',
            'data' => $target
        ]);
    }

    public function destroy($id)
    {
        $target = Target::where('user_id', Auth::id())->find($id);

        if (!$target) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Target tidak ditemukan.'
            ], 404);
        }

        if ($target->image_path && Storage::disk('public')->exists($target->image_path)) {
            Storage::disk('public')->delete($target->image_path);
        }

        $target->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Target berhasil dihapus.'
        ]);
    }

    public function showProgres($id)
    {
        $target = Target::where('user_id', Auth::id())->with('progres')->find($id);

        if (!$target) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Target tidak ditemukan.'
            ], 404);
        }

        $totalSetoran = $target->progres->sum('setoran');
        $totalTarget = $target->ticket + $target->food + $target->transport + $target->others;

        $persentase = $totalTarget > 0
            ? round(($totalSetoran / $totalTarget) * 100, 2)
            : 0;

        return response()->json([
            'status_code' => 200,
            'message' => 'Detail target ditemukan.',
            'data' => [
                'target' => $target,
                'total_setoran' => $totalSetoran,
                'total_target' => $totalTarget,
                'persentase_progres' => $persentase . '%',
            ]
        ]);
    }

    public function riwayatTabungan()
    {
        $userId = Auth::id();
        $query = Target::where('user_id', $userId)
            ->where('status', 'selesai')
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Daftar target berhasil diambil.',
            'data' => $query
        ]);
    }
}
