<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class WhatsAppWebController extends Controller
{
    public function index()
    {
        $baseUrl = Setting::getValue('whatsapp_webjs_base_url', env('WHATSAPP_WEBJS_BASE_URL', 'http://127.0.0.1:3001'));
        $provider = Setting::getValue('whatsapp_provider', env('WHATSAPP_PROVIDER', 'meta'));

        return view('admin.whatsapp-web.index', compact('baseUrl', 'provider'));
    }
}
