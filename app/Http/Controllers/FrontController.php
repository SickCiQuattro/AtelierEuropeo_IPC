<?php

namespace App\Http\Controllers;

use App\Models\DataLayer;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function getHome() {
        
        $dl = new DataLayer();
        $featuredProjects = $dl->getFeaturedProjects();
        $randomTestimonials = $dl->getRandomTestimonials(9);
        return view('index')
            ->with('featuredProjects', $featuredProjects)
            ->with('randomTestimonials', $randomTestimonials);
    }

    public function getAbout() {
        return view('about')->with([
            'pageTitle' => __('about.about_title'),
            'pageDescription' => __('about.mission_text')
        ]);
    }

    public function getContact() {
        return view('contact')->with([
            'pageTitle' => __('contact.contact_title'),
            'pageDescription' => __('contact.contact_subtitle')
        ]);
    }

    public function getCorpoEuropeo() {
        return view('corpo-europeo-solidarieta');
    }

    public function getScambiGiovanili() {
        return view('scambi-giovanili');
    }

    public function getCorsiFormazione() {
        return view('corsi-formazione');
    }
}
