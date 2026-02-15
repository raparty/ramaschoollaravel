<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;

class TransferCertificateController extends Controller
{
    /**
     * Display the transfer certificate for a specific student.
     *
     * @param string $regNo
     * @return \Illuminate\View\View
     */
    public function show($regNo)
    {
        // Find the student by registration number with their class
        $student = Admission::with('class')
            ->where('reg_no', $regNo)
            ->firstOrFail();

        return view('transfer-certificate.show', [
            'student' => $student,
        ]);
    }
}
