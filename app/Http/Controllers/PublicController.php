<?php
namespace App\Http\Controllers;
use App\Models\SiteContent;
class PublicController extends Controller { public function landing(){ $contents=SiteContent::orderBy('sort_order')->get()->keyBy('key'); return view('public.landing',compact('contents')); } }
