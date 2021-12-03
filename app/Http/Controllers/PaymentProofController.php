<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\ProofRequest;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\PaymentProofResource;
use App\Models\Konstants;
use App\Models\RoleManager;
use Carbon\Carbon;

class PaymentProofController extends Controller
{

    public function index()
    {
        //
        if (!RoleManager::checkUserRole(Konstants::ROLE_ADMIN)) {
            return response(ResponseBuilder::genErrorRes(Konstants::ERR_LACK_AUTH), Konstants::STATUS_401);
        }

        $allProofs = PaymentProof::all();
        return response()->json(ResponseBuilder::buildPaymentRes($allProofs), Konstants::STATUS_OK);
    }

    //
    //
    public function fetchPendingProofs()
    {
        //
        if (!RoleManager::checkUserRole(Konstants::ROLE_ADMIN)) {
            return response(ResponseBuilder::genErrorRes(Konstants::ERR_LACK_AUTH), Konstants::STATUS_401);
        }

        $proofs = PaymentProof::where('status', Konstants::PENDING)->get();
        return response()->json(ResponseBuilder::buildPaymentRes($proofs), Konstants::STATUS_OK);
    }


    public function userProofs()
    {
        $userId = auth()->id();
        $userProofs = PaymentProof::where('user_id', $userId)->get();
        return response()->json(ResponseBuilder::buildPaymentRes($userProofs), Konstants::STATUS_OK);
    }

    //
    //
    public function store(ProofRequest $request)
    {
        //
        $user = auth()->user();
        $file = $request->file('shot');
        $name = '/payment_shots/' . uniqid() . '.' . $file->extension();
        $file->move(public_path('payment_shots'), $name);
        $time = Carbon::now();

        $proof = PaymentProof::create([
            'uuid' => Str::uuid(), 'image' => $name, 'amount' => $request->amount,
            'user_id' => $user->id, 'created_ar' => $time, 'updated' => $time
        ]);


        return response()->json([
            'status' => 'successful',
            'type' => 'proofs',
            'data' => new PaymentProofResource($proof)
        ], 200);
    }
}
