<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BodyPart;

class BodyPartController extends Controller
{
    public function index()
    {
        $body_parts = BodyPart::paginate(config('constants.PAGINATION'));

        return view('body_part.index', ['body_parts' => $body_parts]);
    }

    public function edit(int $body_part_id)
    {
        $body_part = BodyPart::where('id', $body_part_id)->get()->first();

        return view('body_part.edit', ['body_part' => $body_part]);
    }

    public function update(Request $request, int $body_part_id)
    {
        $body_part = BodyPart::find($body_part_id);
        $body_part->name = $request->name;
        $body_part->update();

        return redirect()->route('body_part.index');
    }

}