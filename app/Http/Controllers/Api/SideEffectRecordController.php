<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\SideEffectRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class SideEffectRecordController extends Controller
{
    /**
     * @OA\Get (path="/api/side-effects/account/{account}", tags={"副作用紀錄"}, summary="取得副作用紀錄",
     *      description="取得副作用紀錄",
     *      @OA\Parameter (name="account", description="個案帳號", required=true, in="path", example="user1",
     *          @OA\Schema(type="string",)
     *     ),
     *     @OA\Response(response="200", description="success",
     *          @OA\MediaType(mediaType="application/json",
     *              @OA\Schema (
     *                  allOf={
     *                      @OA\Schema (
     *                          @OA\Property(property="id", type="integer", description="副作用紀錄編號", example=1),
     *                          @OA\Property(property="case_id", type="integer", description="個案編號", example=1),
     *                      ),
     *                      @OA\Schema (ref="#/components/schemas/effect"),
     *                  }
     *              )
     *          )
     *      )
     * )
     */

    public function account(Request $request, $account){
        $case = CaseModel::where([
            'account' => $account,
        ])->first();
        if (!Auth::check()){
            $case = CaseModel::where([
                'account' => $request->get('auth_account'),
            ])->first();
        }

        return response(['data' => $case->side_effect_records], Response::HTTP_OK);
    }

    /**
     * @OA\Post (path="/api/side-effects", tags={"副作用紀錄"}, summary="新增副作用紀錄",
     *      description="新增副作用紀錄",
     *      @OA\RequestBody (
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  allOf={
     *                      @OA\Schema (
     *                          required={"account"},
     *                          @OA\Property(property="account", type="string", description="個案帳號", example="user1"),
     *                      ),
     *                      @OA\Schema (ref="#/components/schemas/effect"),
     *                  }
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="success",
     *          @OA\MediaType(mediaType="application/json",
     *              @OA\Schema (
     *                  allOf={
     *                      @OA\Schema (
     *                          @OA\Property(property="id", type="integer", description="副作用紀錄編號", example=1),
     *                          @OA\Property(property="case_id", type="integer", description="個案編號", example=1),
     *                      ),
     *                      @OA\Schema (ref="#/components/schemas/effect"),
     *                  }
     *              )
     *          )
     *      )
     * )
     */

    public function store(Request $request){
        $rules = [
            'account' => ['required'],
            'date' => ['required'],
            'symptom' => ['required'],
            'difficulty' => ['nullable'],
            'severity' => ['required'],
            'has_image' => ['required'],
            'image' => ['nullable'],
            'caption' => ['nullable'],
        ];
        $validator = Validator::make($request->all(), $rules);

        if (!Auth::check()) {
            $case_id = CaseModel::where('account', $request->get('auth_account'))->first()->toArray()['id'];
        } else {
            $case_id = CaseModel::where('account', $validator->validate()['account'])->first()->toArray()['id'];
        }

        $file = $request->file('image');
        if (!is_null($file)) {
            $path = $file->storeAs(
                'effect',
                $file->hashName(),
                'public'
            );
        }else{
            $path = null;
            if ($request['has_image'] == 1){
                return response(null, Response::HTTP_BAD_REQUEST);
            }
        }

        $data = [
            'case_id' => $case_id,
            'date' => $request['date'],
            'symptom' => $request['symptom'],
            'difficulty' => $request['difficulty'],
            'severity' => $request['severity'],
            'has_image' => $request['has_image'],
            'caption' => $request['caption'],
            'path' => $path,
        ];
        $side_effect_record = SideEffectRecord::create($data);
        $side_effect_record = $side_effect_record->refresh();
        return response(['data' => $side_effect_record], Response::HTTP_CREATED);
    }
}
