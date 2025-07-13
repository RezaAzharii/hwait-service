<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\Progres;
use Illuminate\Support\Facades\Auth;

class ProgresController extends Controller
{
    /**
     * Menambahkan setoran tabungan.
     *
     * @bodyParam target_id int required ID target tabungan. Example: 2
     * @bodyParam setoran int required Jumlah uang yang disetor. Example: 50000
     * @bodyParam tanggal_setoran date required Tanggal setoran dalam format YYYY-MM-DD. Example: 2025-07-13
     * @bodyParam waktu_setoran string required Waktu setoran dalam format HH:mm (24 jam). Example: 14:30
     *
     * @response 201 {
     *   "status_code": 201,
     *   "message": "Setoran berhasil ditambahkan.",
     *   "data": {
     *     "id": 10,
     *     "user_id": 2,
     *     "target_id": 2,
     *     "setoran": "50000.00",
     *     "tanggal_setoran": "2025-07-13T00:00:00.000000Z",
     *     "waktu_setoran": "14:30",
     *     "created_at": "2025-07-13T10:22:00.000000Z",
     *     "updated_at": "2025-07-13T10:22:00.000000Z"
     *   }
     * }
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_id' => 'required|exists:target,id',
            'setoran' => 'required|numeric|min:1',
            'tanggal_setoran' => 'required|date',
            'waktu_setoran' => 'required|date_format:H:i',
        ]);

        $validated['user_id'] = Auth::id();
        $progres = Progres::create($validated);

        $target = Target::find($validated['target_id']);

        if (!$target) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Target tidak ditemukan.'
            ], 404);
        }

        $totalSetoran = Progres::where('target_id', $target->id)
            ->where('user_id', Auth::id())
            ->sum('setoran');

        $totalKebutuhan = floatval($target->ticket) + floatval($target->food)
            + floatval($target->transport) + floatval($target->others);

        if ($totalSetoran >= $totalKebutuhan && $target->status !== 'selesai') {
            $target->status = 'selesai';
            $target->save();
        }

        return response()->json([
            'status_code' => 201,
            'message' => 'Setoran berhasil ditambahkan.',
            'data' => $progres
        ], 201);
    }

    // public function history($targetId)
    // {
    //     $progresList = Progres::where('user_id', Auth::id())
    //         ->where('target_id', $targetId)
    //         ->orderBy('tanggal_setoran', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status_code' => 200,
    //         'message' => 'Riwayat setoran berhasil diambil.',
    //         'data' => $progresList
    //     ]);
    // }
}
