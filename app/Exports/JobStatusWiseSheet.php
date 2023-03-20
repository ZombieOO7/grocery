<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class JobStatusWiseSheet implements WithTitle , FromView
{
    private $jobs,$title;

    public function __construct($jobs,$title)
    {
        $this->jobs = $jobs;
        $this->title =$title;
    }

    public function view(): View
    {
        $jobs = $this->jobs;
        return view('admin.exports.jobReport', ['jobs' => @$this->jobs, 'title'=>$this->title]);
    }

    public function collection()
    {
        return $this->jobs;
    }
    public function title(): string
    {
        return $this->title;
    }
}

?>