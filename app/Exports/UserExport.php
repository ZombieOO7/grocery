<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\User;

class UserExport implements FromView
{
    
    public function view(): View
    {
        $users = User::orderBy('created_at','desc')->get();
        
        return view('admin.exports.user', [
            'users' => $users
        ]);
    }
}
