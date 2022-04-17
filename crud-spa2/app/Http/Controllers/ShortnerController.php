<?php

namespace App\Http\Controllers;

use App\Models\Shortner;
use Illuminate\Http\Request;
use App\Services\ShortenerService;
class ShortnerController extends Controller
{
    use ShortenerService;

    public function store(Request $request)
    {
        try{
            $shortUrl = $this->urlToShortCode($request->long_url);
            return response()->json([
                "message" => "Successfully Short URL Created.".$shortUrl,
            ],200);
        }catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage(),
            ],500);
        }
    }
    public function redirectToLongUrl($shortCode)
    {
        $url = $this->shortCodeToUrl($shortCode);
        return redirect()->away($url);
    }

}
