<?php

namespace App\Http\Controllers;

use App\Models\NotificationApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()
            ->notificationsApp()
            ->latest('created_at');

        if ($request->filled('statut')) {
            if ($request->string('statut')->toString() === 'non_lues') {
                $query->whereNull('lue_le');
            }

            if ($request->string('statut')->toString() === 'lues') {
                $query->whereNotNull('lue_le');
            }
        }

        $summary = $request->user()->notificationSummary();

        $notifications = $query->paginate(30)->withQueryString();

        return view('governance.notifications_index', compact('notifications', 'summary'));
    }

    public function read(Request $request, NotificationApp $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->marquerCommeLue();

        if ($notification->lien) {
            return redirect($notification->lien);
        }

        return redirect()->route('notifications.index');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()
            ->notificationsNonLues()
            ->update(['lue_le' => now()]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
