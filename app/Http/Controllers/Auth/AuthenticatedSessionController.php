<?php

namespace App\Http\Controllers\Auth;

use App\Models\DataLayer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;

class AuthenticatedSessionController extends Controller
{
        
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Memorizza l'URL di provenienza se non è già la pagina di login
        if (!request()->session()->has('url.intended') && url()->previous() !== url()->current()) {
            request()->session()->put('url.intended', url()->previous());
        }
        
        return view('auth.authLogin');
    }
    
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->isAdmin()) {
            $request->session()->forget('admin_user_view');

            return redirect()->route('admin.dashboard')
                ->with('success', 'Login effettuato con successo. Benvenuto!');
        }
    
        // Ottiene l'URL intended dalla sessione
        $intendedUrl = $request->session()->pull('url.intended', '/');
        
        return redirect($intendedUrl)->with('success', 'Login effettuato con successo. Benvenuto!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout effettuato con successo. A presto!');
    }

    public function ajaxCheckForEmail(Request $request)
    {
        $dl = new DataLayer();

        if ($dl->findUserByEmail($request->input('email'))) {
            $response = array('found' => true);
        } else {
            $response = array('found' => false);
        }
        return response()->json($response);
    }
}
