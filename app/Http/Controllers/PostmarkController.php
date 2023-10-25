<?php

namespace App\Http\Controllers;

use App\Models\PostmarkMailLog;
use Inertia\Inertia;
use Mpdf\Http\Request;

class PostmarkController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $mails = PostmarkMailLog::orderBy('ReceivedAt', 'desc')->get();

        return Inertia::render('Postmark', [
            'mails' => $mails
        ]);
    }
}
