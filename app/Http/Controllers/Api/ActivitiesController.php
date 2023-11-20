<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Activities;
use App\Models\Content;
use App\Models\User;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activities::with(['User', 'Content'])->get();

        if (count($activities) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $activities
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_user' => 'required',
            'id_content' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $user = User::find($storeData['id_user']);
        if (!$user) {
            return response(['message' => 'User not Found'], 400);
        }

        $content = Content::find($storeData['id_content']);
        if (!$content) {
            return response(['message' => 'Content not Found'], 400);
        }

        if ($content->type == 'Paid' && $user->status == 0) {
            return response(['message' => 'Content is Paid. User must have a subscription to access.'], 400);
        }

        $storeData['accessed_at'] = date('Y-m-d H:i:s');
        $activities = Activities::create($storeData);
        return response([
            'message' => $user->name . ' accessed ' . $content->title . ' at ' . $activities['accessed_at'] . '.', 'data' => $activities
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $activities = Activities::find($id);

        if (!is_null($activities)) {
            return response([
                'message' => 'Activities found',
                'data' => $activities
            ], 200);
        }

        return response([
            'message' => 'Activities Not Found',
            'data' => null
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateData = $request->all();
        $activities = Activities::find($id);
        if (is_null($activities)) {
            return response([
                'message' => 'Activities Not Found',
                'data' => null
            ], 400);
        }

        $validate = Validator::make($updateData, [
            'id_user' => 'required',
            'id_content' => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $user = User::find($updateData['id_user']);
        if (!$user) {
            return response(['message' => 'User Not Found'], 400);
        }

        $content = Content::find($updateData['id_content']);
        if (!$content) {
            return response(['message' => 'Content Not Found'], 400);
        }

        $activities->user_id = $updateData['id_user'];
        $content->content_id = $updateData['id_content'];
        $content->accessed_id = $updateData['accessed_at'];

        if ($activities->save()) {
            return response([
                'message' => 'Update Activities Success',
                'data' => $activities
            ], 200);
        }

        return response([
            'message' => 'Update Activities Failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $activities = Activities::find($id);

        if (is_null($activities)) {
            return response([
                'message' => 'Activities Not Found',
                'data' => null
            ], 404);
        }

        if ($activities->delete()) {
            return response([
                'message' => 'Delete Activities Success',
                'data' => $activities
            ], 200);
        }

        return response([
            'message' => 'Delete Activities Failed',
            'data' => null
        ], 400);
    }
}
