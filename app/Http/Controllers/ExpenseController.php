<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelRequest;
use App\Models\Expense;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpensesSubmitted;

class ExpenseController extends Controller
{
    public function store(Request $request, $tar)
    {
        $tr = TravelRequest::where('tar_id', $tar)->firstOrFail();
        $data = $request->validate([
            'accommodation' => 'numeric',
            'meals' => 'numeric',
            'transport' => 'numeric',
            'airfare' => 'numeric',
            'others' => 'numeric'
        ]);

        $data['total'] = array_sum(array_map('floatval', [
            $data['accommodation'] ?? 0,
            $data['meals'] ?? 0,
            $data['transport'] ?? 0,
            $data['airfare'] ?? 0,
            $data['others'] ?? 0
        ]));

        $tr->expenses()->create($data);
        $tr->update(['status' => 'completed']);

        try {
            Mail::to($tr->user->email)->send(new ExpensesSubmitted($tr));
        } catch (\Exception $e) {}

        return redirect()->route('dashboard')->with('success','Expenses submitted.');
    }
}
