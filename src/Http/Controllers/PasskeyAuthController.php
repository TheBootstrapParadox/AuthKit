<?php

namespace BSPDX\AuthKit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Spatie\Passkeys\Facades\Passkey;

class PasskeyAuthController
{
    /**
     * Display the passkey registration view.
     */
    public function registerView(Request $request): View
    {
        return view('authkit::passkeys.register', [
            'user' => $request->user(),
            'passkeys' => $request->user()->passkeys,
        ]);
    }

    /**
     * Generate passkey registration options.
     */
    public function registerOptions(Request $request): JsonResponse
    {
        $options = Passkey::registerOptions($request->user());

        return response()->json($options);
    }

    /**
     * Store a new passkey for the authenticated user.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'credential' => ['required', 'array'],
        ]);

        try {
            Passkey::register($request->user(), $validated['credential'], $validated['name']);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Passkey registered successfully.',
                ]);
            }

            return redirect()->back()->with('status', 'passkey-registered');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Failed to register passkey: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withErrors([
                'passkey' => 'Failed to register passkey. Please try again.',
            ]);
        }
    }

    /**
     * Delete a passkey.
     */
    public function destroy(Request $request, string $passkeyId): RedirectResponse|JsonResponse
    {
        $passkey = $request->user()->passkeys()->findOrFail($passkeyId);
        $passkey->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Passkey deleted successfully.',
            ]);
        }

        return redirect()->back()->with('status', 'passkey-deleted');
    }

    /**
     * Display the passkey login view.
     */
    public function loginView(): View
    {
        return view('authkit::passkeys.login');
    }

    /**
     * Generate passkey authentication options.
     */
    public function loginOptions(Request $request): JsonResponse
    {
        $options = Passkey::authenticationOptions();

        return response()->json($options);
    }

    /**
     * Authenticate a user using a passkey.
     */
    public function authenticate(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'array'],
        ]);

        try {
            $user = Passkey::authenticate($validated['credential']);

            auth()->login($user, remember: true);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Authentication successful.',
                    'redirect' => config('authkit.redirects.login', '/dashboard'),
                ]);
            }

            return redirect()->intended(config('authkit.redirects.login', '/dashboard'));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Authentication failed: ' . $e->getMessage(),
                ], 401);
            }

            return redirect()->back()->withErrors([
                'passkey' => 'Authentication failed. Please try again.',
            ]);
        }
    }

    /**
     * Get all passkeys for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $passkeys = $request->user()->passkeys()->get()->map(function ($passkey) {
            return [
                'id' => $passkey->id,
                'name' => $passkey->name,
                'created_at' => $passkey->created_at->toDateTimeString(),
                'last_used_at' => $passkey->last_used_at?->toDateTimeString(),
            ];
        });

        return response()->json(['passkeys' => $passkeys]);
    }
}
