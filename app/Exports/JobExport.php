<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JobExport implements FromView
{
    protected $reportData, $totalJob,$status,$type;
    function __construct($reportData, $title=null, $name, $type) {
        $this->reportData = $reportData;
        $this->title = $title;
        $this->name = $name;
        $this->type = $type;
    }
    /**
     * ------------------------------------------------------
     * | Get Job Report                                     |
     * | @param Request $request                            |
     * | @return File                                       |
     * |-----------------------------------------------------
    */

    public function view(): View
    {
        $reportData = $this->reportData;
        return view('admin.exports.jobReport', ['reportData' => @$this->reportData, 'title'=>@$this->title,'name'=>@$this->name,'type'=>$this->type]);
    }
}
