<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\ProofRequest;
use App\Models\PaymentProof;
use Illuminate\Support\Str;
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
        return response()->json(
            ResponseBuilder::buildResourceCol(PaymentProofResource::collection($allProofs)),
            Konstants::STATUS_OK
        );
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
        return response()->json(
            ResponseBuilder::buildResourceCol(PaymentProofResource::collection($proofs)),
            Konstants::STATUS_OK
        );
    }


    public function userProofs()
    {
        $userId = auth()->id();
        $userProofs = PaymentProof::where('user_id', $userId)->get();
        return response()->json(
            ResponseBuilder::buildResourceCol(PaymentProofResource::collection($userProofs)),
            Konstants::STATUS_OK
        );
    }

    //
    //
    public function store(ProofRequest $request)
    {
        //prpare
        $user = auth()->user();
        $time = Carbon::now();
        // Execute
        $proof = PaymentProof::create([
            'uuid' => Str::uuid(), 'image' => Helpers::runImageUpload($request->file('shot'), "payments"),
            'amount' => $request->amount, 'user_id' => $user->id, 'created_ar' => $time, 'updated' => $time
        ]);
        // return response
        return response(ResponseBuilder::buildRes(new PaymentProofResource($proof)), Konstants::STATUS_OK);
    }
}
