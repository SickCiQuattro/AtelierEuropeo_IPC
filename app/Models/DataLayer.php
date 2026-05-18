<?php

namespace App\Models;

class DataLayer {

    public function listProjects() {
        $projectsList = Project::orderBy('created_at', 'asc')->get();
        return $projectsList;
    }

    public function listProjectsByStatus($status) {
        $projectsList = Project::where('status', $status)->orderBy('created_at', 'asc')->get();
        return $projectsList;
    }

    public function getFeaturedProjects($limit = 6)
    {
        // Recupera gli ultimi progetti pubblicati
        return Project::where('status', 'published')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get();
    }

    public function findAuthorById($id) {
        return User::find($id)->where('role', 'admin')->first();
    }

    public function findProjectById($id) {
        return Project::find($id);
    }

    public function addProject($data) {
        $project = new Project();
        $project->fill($data);
        $project->save();
        return $project;
    }

    public function editProject($id, $data) {
        $project = Project::find($id);
        if ($project) {
            $project->fill($data);
            $project->save();
            return $project;
        }
        return null;
    }

    public function deleteProject($id) {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            return true;
        }
        return false;
    }

    public function listCategories() {
        return Category::orderBy('tag', 'asc')->get();
    }

    public function listAssociations() {
        return Association::orderBy('name', 'asc')->get();
    }

    public function getRandomTestimonials($limit = 3) {
        // Recupera testimonianze casuali con i relativi autori e progetti
        return Testimonial::with(['author', 'project'])
                         ->inRandomOrder()
                         ->limit($limit)
                         ->get();
    } 

    public function findUserByemail($email) {
        $users = User::where('email', $email)->get();
        
        if (count($users) == 0) {
            return false;
        } else {
            return true;
        }
    }
}
