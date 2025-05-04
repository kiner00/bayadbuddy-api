<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BorrowerRequest;
use App\Http\Resources\BorrowerResource;
use App\Models\Borrower;
use App\Services\PhoneNumberFormatter;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    protected $phoneFormatter;

    public function __construct(PhoneNumberFormatter $phoneFormatter)
    {
        $this->phoneFormatter = $phoneFormatter;
    }

    public function index(Request $request)
    {
        $borrowers = Borrower::with('debts')->where('user_id', $request->user()->id)->latest()->paginate();

        return BorrowerResource::collection($borrowers);
    }
    public function store(BorrowerRequest $request): BorrowerResource
    {
        $borrower = Borrower::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'mobile_number' => $this->phoneFormatter->normalizeTo639($request->mobile_number),
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
