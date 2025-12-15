<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\ValidateVoucherRequest;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display voucher list
     */
    public function index(): View
    {
        Gate::authorize('manage-vouchers');
        $vouchers = Voucher::latest()->paginate(15);
        return view('vouchers.index', compact('vouchers'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        Gate::authorize('manage-vouchers');
        return view('vouchers.create');
    }

    /**
     * Admin create voucher
     */
    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        Gate::authorize('manage-vouchers');

        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['code'] = Str::upper((string) $data['code']);
        
        // Set ends_at to 23:59:59 if provided
        if (!empty($data['ends_at'])) {
            $data['ends_at'] = \Carbon\Carbon::parse($data['ends_at'])
                ->setTime(23, 59, 59)
                ->setTimezone(config('app.timezone', 'Asia/Jakarta'));
        }

        Voucher::create($data);

        return redirect()->route('vouchers.index')->with('success', 'Voucher created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Voucher $voucher): View
    {
        Gate::authorize('manage-vouchers');

        return view('vouchers.edit', compact('voucher'));
    }

    /**
     * Update voucher
     */
    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        Gate::authorize('manage-vouchers');

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'per_user_limit' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $data['code'] = Str::upper((string) $data['code']);
        
        if (!empty($data['ends_at'])) {
            $data['ends_at'] = \Carbon\Carbon::parse($data['ends_at'])
                ->setTime(23, 59, 59)
                ->setTimezone(config('app.timezone', 'Asia/Jakarta'));
        }

        $voucher->update($data);

        return redirect()->route('vouchers.index')->with('success', 'Voucher updated successfully');
    }

    /**
     * Delete voucher
     */
    public function destroy(Voucher $voucher): RedirectResponse
    {
        Gate::authorize('manage-vouchers');

        $voucher->delete();
        return redirect()->route('vouchers.index')->with('success', 'Voucher deleted successfully');
    }

    /**
     * Public validate voucher (API)
     */
    public function validateCode(ValidateVoucherRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $code        = Str::upper((string) $data['code']);
        $orderAmount = (int) $data['order_amount'];
        $userId      = optional($request->user())->id;

        $voucher = Voucher::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'reason' => 'NOT_FOUND'], 404);
        }

        // Use timezone aware comparison
        $now = now()->setTimezone(config('app.timezone', 'Asia/Jakarta'));
        
        if ($voucher->starts_at) {
            $startsAt = \Carbon\Carbon::parse($voucher->starts_at)->setTimezone(config('app.timezone', 'Asia/Jakarta'));
            if ($now->lt($startsAt)) {
                return response()->json(['valid' => false, 'reason' => 'NOT_STARTED'], 400);
            }
        }

        if ($voucher->ends_at) {
            $endsAt = \Carbon\Carbon::parse($voucher->ends_at)->setTimezone(config('app.timezone', 'Asia/Jakarta'));
            if ($now->gt($endsAt)) {
                return response()->json(['valid' => false, 'reason' => 'EXPIRED'], 400);
            }
        }

        if ($orderAmount < (int) $voucher->min_order_amount) {
            return response()->json(['valid' => false, 'reason' => 'MIN_ORDER'], 400);
        }

        if ($voucher->max_uses > 0) {
            $used = VoucherRedemption::where('voucher_id', $voucher->id)->count();
            if ($used >= $voucher->max_uses) {
                return response()->json(['valid' => false, 'reason' => 'QUOTA_EXHAUSTED'], 400);
            }
        }

        if ($userId && $voucher->per_user_limit > 0) {
            $userUsed = VoucherRedemption::where('voucher_id', $voucher->id)
                ->where('user_id', $userId)
                ->count();
            if ($userUsed >= $voucher->per_user_limit) {
                return response()->json(['valid' => false, 'reason' => 'USER_LIMIT'], 400);
            }
        }

        $discount = $voucher->type === 'percentage'
            ? intdiv($orderAmount * min((int) $voucher->value, 100), 100)
            : (int) $voucher->value;

        return response()->json([
            'valid'           => true,
            'code'            => $voucher->code,
            'discount_amount' => min($discount, $orderAmount),
            'type'            => $voucher->type,
            'value'           => $voucher->value,
        ]);
    }
}
