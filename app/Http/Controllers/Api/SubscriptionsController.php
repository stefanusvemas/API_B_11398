<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriptions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscription = Subscriptions::all();

        if (count($subscription) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $subscription
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
            'category' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if ($storeData['category'] == 'Basic') {
            $storeData['price'] = 50000;
        } else if ($storeData['category'] == 'Standard') {
            $storeData['price'] = 100000;
        } else if ($storeData['category'] == 'Premium') {
            $storeData['price'] = 150000;
        } else {
            return response(['message' => 'Category not Found'], 400);
        }

        $findUser = User::find($storeData['id_user']);
        if (is_null($findUser)) {
            return response(['message' => 'id_user not found'], 400);
        } else if ($findUser->status == 1) {
            return response(['message' => 'User already have subscription'], 400);
        } else {
            $findUser->status = 1;
            $findUser->save();
        }

        $storeData['transaction_date'] = date('Y-m-d H:i:s');

        $subscription = Subscriptions::create($storeData);
        return response([
            'message' => 'Add Subscription Success',
            'data' => $subscription
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subcription = Subscriptions::find($id);

        if (!is_null($subcription)) {
            return response([
                'message' => 'Subscription Found',
                'data' => $subcription
            ], 200);
        }

        return response([
            'message' => 'Subscription Not Found',
            'data' => null
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subscription = Subscriptions::find($id);
        if (is_null($subscription)) {
            return response([
                'message' => 'Subscription Not Found',
                'data' => null
            ], 400);
        }
        $updateData = $request->all();

        $validate = Validator::make($updateData, [
            'id_user' => 'required',
            'category' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if ($updateData['category'] == 'Basic') {
            $updateData['price'] = 50000;
        } else if ($updateData['category'] == 'Standard') {
            $updateData['price'] = 100000;
        } else if ($updateData['category'] == 'Premium') {
            $updateData['price'] = 150000;
        } else {
            return response(['message' => 'Category not Found'], 400);
        }

        $findUser = User::find($updateData['id_user']);
        if (is_null($findUser)) {
            return response(['message' => 'id_user not found'], 400);
        } else if ($findUser->status == 0) {
            return response(['message' => 'User does not have a subscription'], 400);
        }

        $updateData['transaction_date'] = date('Y-m-d H:i:s');

        $subscription->id_user = $updateData['id_user'];
        $subscription->category = $updateData['category'];
        $subscription->price = $updateData['price'];
        $subscription->transaction_date = $updateData['transaction_date'];

        if ($subscription->save()) {
            return response([
                'message' => 'Update Subcription Success',
                'data' => $subscription
            ], 200);
        }

        return response([
            'message' => 'Update Subcription Failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscriptions::find($id);

        if (is_null($subscription)) {
            return response([
                'message' => 'Delete Not Found',
                'data' => null
            ], 404);
        }

        if ($subscription->delete()) {
            return response([
                'message' => 'Delete Subscription Success',
                'data' => $subscription
            ], 200);
        }

        return response([
            'message' => 'Delete Subscription Failed',
            'data' => null
        ], 400);
    }
}
