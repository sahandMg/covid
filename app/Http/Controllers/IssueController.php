<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IssueResponsables\CreateIssue;
use App\Services\Issue\RequestValidationService;
use Illuminate\Http\Request;

class IssueController extends Controller
{


    //    ============ Save issues that users are reporting ============

    /*
     * Data Needed : token,title,img,desc
     * Data returns : message
     *
     */

    public function create(Request $request,RequestValidationService $rq){

        $val = $rq->create($request);
        if(!is_null($val)){

            return $val;
        }
        return new CreateIssue();
    }
}
