<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function index()
    {
        $reports = array();
            $reports = Report::when(request('type'), function ($q) {
                if (request('type') == 'user') {
                    $q->where('type', 'user');
                }
                if (request('type') == 'post') {
                    $q->where('type', 'post');
                }
            })->get();
        return view('report.index', compact('reports'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $report = Report::find($id);
        $other_reports = Report::where('report_id', $report->report_id)->where('id', "!=", $report->id)->get();
        return view('report.show', compact('report', 'other_reports'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id, Request $request)
    {
        $report = Report::find($id);
        if ($request->type == '1') {
            if ($report->type == 'post') {
                Post::where('id',$report->report_id)->delete();
            } else if ($report->type == 'user') {
                User::deleteUser($report->report_id);
            }
            Report::where('report_id', $report->report_id)->delete();
            if ($report->type == 'post') {
                return response()->json(['success' => 'All Report & Post Deleted'], 200);
            } else if ($report->type == 'user') {
                return response()->json(['success' => 'All Report & User Deleted'], 200);
            }
        } else if ($request->type == '3') {
            Report::where('report_id', $report->report_id)->delete();
            return response()->json(['success' => 'All Report Deleted'], 200);
        } else {
            Report::where('id',$id)->delete();
            return response()->json(['success' => 'Deleted'], 200);
        }
    }
}
