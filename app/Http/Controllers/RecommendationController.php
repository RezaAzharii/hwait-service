<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecommendationRequest;
use App\Http\Requests\UpdateRecommendationRequest;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecommendationController extends Controller
{
    /**
     * Menampilkan semua rekomendasi.
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Daftar recommendation berhasil diambil.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Rekomendasi A",
     *       "description": "Deskripsi A",
     *       "image_path": "recommendations/123.jpg",
     *       "created_at": "2025-07-08T08:00:00Z",
     *       "updated_at": "2025-07-08T08:00:00Z"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $recommendations = Recommendation::latest()->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Daftar recommendation berhasil diambil.',
            'data' => $recommendations
        ]);
    }

    /**
     * Menampilkan detail rekomendasi.
     *
     * @urlParam id string required ID rekomendasi. Example: 1
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Detail berhasil ditemukan.",
     *   "data": {
     *     "id": 1,
     *     "title": "Rekomendasi A",
     *     "description": "Deskripsi A",
     *     "image_path": "recommendations/123.jpg",
     *     "created_at": "2025-07-08T08:00:00Z",
     *     "updated_at": "2025-07-08T08:00:00Z"
     *   }
     * }
     *
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Recommendation tidak ditemukan."
     * }
     */
    public function show(string $id)
    {
        $recommendation = Recommendation::find($id);

        if (!$recommendation) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Recommendation tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Detail berhasil ditemukan.',
            'data' => $recommendation
        ]);
    }

    /**
     * Menambahkan rekomendasi baru.
     *
     * @bodyParam title string required Judul rekomendasi. Example: Wisata Gunung
     * @bodyParam description string required Deskripsi lengkap. Example: Tempat wisata gunung yang indah.
     * @bodyParam image_path file File gambar opsional (jpg/png). Example: (file)
     *
     * @response 201 {
     *   "status_code": 201,
     *   "message": "Rekomendasi berhasil ditambahkan.",
     *   "data": {
     *     "id": 5,
     *     "title": "Wisata Gunung",
     *     "description": "Tempat wisata gunung yang indah.",
     *     "image_path": "recommendations/168888999.jpg"
     *   }
     * }
     *
     * @response 500 {
     *   "status_code": 500,
     *   "message": "Terjadi kesalahan: error detail"
     * }
     */
    public function store(RecommendationRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image_path')) {
                $filename = time() . '_' . Str::slug($data['title']) . '.' . $request->file('image_path')->extension();
                $path = $request->file('image_path')->storeAs('recommendations', $filename, 'public');
                $data['image_path'] = $path;
            }

            $recommendation = Recommendation::create($data);

            return response()->json([
                'status_code' => 201,
                'message' => 'Rekomendasi berhasil ditambahkan.',
                'data' => $recommendation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah data rekomendasi.
     *
     * @urlParam id string required ID rekomendasi. Example: 1
     * @bodyParam title string required Judul rekomendasi. Example: Rekomendasi Baru
     * @bodyParam description string required Deskripsi rekomendasi. Example: Tempat yang sangat direkomendasikan.
     * @bodyParam image_path file Gambar baru (opsional). Example: (file)
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Rekomendasi berhasil diperbarui.",
     *   "data": {
     *     "id": 1,
     *     "title": "Rekomendasi Baru",
     *     "description": "Tempat yang sangat direkomendasikan.",
     *     "image_path": "recommendations/new_image.jpg"
     *   }
     * }
     *
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Rekomendasi tidak ditemukan."
     * }
     *
     * @response 500 {
     *   "status_code": 500,
     *   "message": "Terjadi kesalahan: error detail"
     * }
     */
    public function update(UpdateRecommendationRequest $request, string $id)
    {
        $recommendation = Recommendation::find($id);

        if (!$recommendation) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Rekomendasi tidak ditemukan.'
            ], 404);
        }

        try {
            $data = $request->validated();

            if ($request->hasFile('image_path')) {
                Log::info('File upload detected.');

                // Hapus file lama
                if ($recommendation->image_path && Storage::disk('public')->exists($recommendation->image_path)) {
                    Storage::disk('public')->delete($recommendation->image_path);
                    Log::info('Old image deleted: ' . $recommendation->image_path);
                }

                $slugTitle = isset($data['title']) ? Str::slug($data['title']) : Str::slug($recommendation->title);
                $filename = time() . '_' . $slugTitle . '.' . $request->file('image_path')->extension();
                $path = $request->file('image_path')->storeAs('recommendations', $filename, 'public');

                Log::info('New image stored: ' . $path);

                $data['image_path'] = $path;
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Rekomendasi berhasil diperbarui.',
                'data' => $recommendation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus rekomendasi berdasarkan ID.
     *
     * @urlParam id string required ID rekomendasi. Example: 1
     *
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Rekomendasi berhasil dihapus."
     * }
     *
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Rekomendasi tidak ditemukan."
     * }
     *
     * @response 500 {
     *   "status_code": 500,
     *   "message": "Terjadi kesalahan: error detail"
     * }
     */
    public function destroy(string $id)
    {
        $recommendation = Recommendation::find($id);

        if (!$recommendation) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Rekomendasi tidak ditemukan.'
            ], 404);
        }

        try {
            if ($recommendation->image_path && Storage::disk('public')->exists($recommendation->image_path)) {
                Storage::disk('public')->delete($recommendation->image_path);
            }

            $recommendation->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'Rekomendasi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
