<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $topics = Topic::all();
        $tf_number = Topic::where([
            'question_type' => 'TF'
        ])->count();
        $mc_number = Topic::where([
            'question_type' => 'MC'
        ])->count();

        $title = '題庫管理';
        return response(
            view('root.topic', get_defined_vars()),
            Response::HTTP_OK
        );
    }
}
