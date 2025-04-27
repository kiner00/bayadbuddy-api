<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BorrowerRequest;
use App\Http\Resources\BorrowerResource;
use App\Models\Borrower;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    public function index(Request $request)
    {
        $borrowers = Borrower::where('user_id', $request->user()->id)->latest()->get();

        return BorrowerResource::collection($borrowers);
    }
    public function store(BorrowerRequest $request): BorrowerResource
    {
        $borrower = Borrower::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'notes' => $request->notes,
        ]);

        return new BorrowerResource($borrower);
    }

    public function update(BorrowerRequest $request, Borrower $borrower)
    {
        $borrower->update([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'notes' => $request->notes,
        ]);

        return new BorrowerResource($borrower);
    }

    public function show(Request $request, Borrower $borrower)
    {
        if ($request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        return new BorrowerResource($borrower);
    }

    public function destroy(Request $request, Borrower $borrower)
    {
        if ($request->user()->id !== $borrower->user_id) {
            abort(403, 'Unauthorized.');
        }

        $borrower->delete();

        return response()->noContent();
    }

}
