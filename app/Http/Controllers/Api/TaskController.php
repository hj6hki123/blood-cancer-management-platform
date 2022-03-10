<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\CaseTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * @OA\Get (path="/api/tasks/account/{account}", tags={"每週任務"}, summary="取得每週任務",
     *     description="取得每週的任務相關資訊，如：週數、任務 id、該資料的獨立 id（在新增個案任務的完成狀態時會用到）",
     *     @OA\Parameter (name="account", description="個案帳號", required=true, in="path", example="user1",
     *          @OA\Schema(type="string",)
     *     ),
     *     @OA\Response(response="200", description="success",)
     * )
     */

    public function account(Request $request, $account)
    {
        $case = CaseModel::where('account', $account)->first();
        $case_task = $case->case_tasks;
        if (!Auth::check()) {
            $case_id = CaseModel::where('account', $request->get('auth_account'))->first()->toArray()['id'];
            $case_task = $case_task->where('case_id', $case_id);
        }
        foreach ($case_task as $val){
            $_ = $val->task;
        }
        return response(['data' => $case_task], Response::HTTP_OK);
    }

    /**
     * @OA\Patch (path="/api/tasks/state/{id}", tags={"每週任務"}, summary="更新每週任務完成狀態",
     *     description="更新每週任務完成狀態。<p>先用 GET 該個案的所有任務後，再用任務 id 使用此方法。</p>",
     *     @OA\Parameter (name="id", description="任務 id", required=true, in="path", example="1",
     *          @OA\Schema(type="integer",)
     *     ),
     *      @OA\RequestBody (
     *          @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(required={"state"},
     *                  @OA\Property(property="state", type="string", enum={"completed","uncompleted"}, example="completed"),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response="200", description="success",)
     * )
     */

    public function state(Request $request ,$case_task_id){
        $rules = [
            'state' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        $case_task = CaseTask::where('id', $case_task_id)->get();
        if (!Auth::check()) {
            $case_id = CaseModel::where('account', $request->get('auth_account'))->first()->toArray()['id'];
            $case_task = $case_task->where('case_id', $case_id)->first();
        }
        $case_task->update($validator->validate());
        $case_task = $case_task->refresh();
        return response(['data' => $case_task], Response::HTTP_OK);
    }
}
