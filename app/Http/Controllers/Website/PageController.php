<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('website.home');
    }

    public function aPropos()
    {
        return view('website.a-propos');
    }

    public function historique()
    {
        return view('website.historique');
    }

    public function visionMission()
    {
        return view('website.vision-mission');
    }

    public function organes()
    {
        return view('website.organes');
    }

    public function president()
    {
        return view('website.mot-du-president');
    }

    public function etatsMembres()
    {
        return view('website.etats-membres');
    }

    public function domaine(string $slug)
    {
        $domaines = [
            'paix-securite'          => ['titre' => 'Paix & Sécurité',           'icon' => 'fa-shield-alt'],
            'integration-economique' => ['titre' => 'Intégration économique',     'icon' => 'fa-handshake'],
            'infrastructures'        => ['titre' => 'Infrastructures',            'icon' => 'fa-road'],
            'commerce-investissement'=> ['titre' => 'Commerce & Investissement',  'icon' => 'fa-chart-line'],
            'ressources-naturelles'  => ['titre' => 'Ressources naturelles',      'icon' => 'fa-leaf'],
            'developpement-humain'   => ['titre' => 'Développement humain',       'icon' => 'fa-users'],
        ];

        abort_unless(array_key_exists($slug, $domaines), 404);

        return view('website.domaines.show', [
            'slug'   => $slug,
            'domaine'=> $domaines[$slug],
        ]);
    }

    public function programmes()
    {
        return view('website.programmes');
    }

    public function actualites()
    {
        return view('website.actualites.index');
    }

    public function actualite(string $slug)
    {
        return view('website.actualites.show', ['slug' => $slug]);
    }

    public function publications()
    {
        return view('website.publications');
    }

    public function evenements()
    {
        return view('website.evenements');
    }

    public function communiques()
    {
        return view('website.communiques');
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'nom'     => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'sujet'   => 'required|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        // In production: send email via Mail facade
        return back()->with('success', 'Votre message a été envoyé. La Commission vous répondra dans les meilleurs délais.');
    }
}
