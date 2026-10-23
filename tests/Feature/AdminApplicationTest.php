<?php

use App\Models\Application;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function applicationFor(Project $project, string $status = 'pending'): Application
{
    return Application::factory()->create([
        'project_id' => $project->id,
        'user_id' => User::factory()->create()->id,
        'status' => $status,
        'updated_by_admin_id' => null,
    ]);
}

it('denies non-admins access to the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.applications.all'))
        ->assertForbidden();
});

it('refuses to approve beyond the requested participants', function () {
    $admin = User::factory()->admin()->create();
    $project = Project::factory()->create(['status' => 'published', 'requested_people' => 1]);
    applicationFor($project, 'approved');
    $extra = applicationFor($project);

    $this->actingAs($admin)
        ->patch(route('admin.applications.approve', $extra))
        ->assertSessionHas('error');

    expect($extra->fresh()->status)->toBe('pending');
});

it('approves while seats remain and records the admin', function () {
    $admin = User::factory()->admin()->create();
    $project = Project::factory()->create(['status' => 'published', 'requested_people' => 2]);
    $application = applicationFor($project);

    $this->actingAs($admin)
        ->patch(route('admin.applications.approve', $application))
        ->assertSessionHas('success');

    expect($application->fresh())
        ->status->toBe('approved')
        ->updated_by_admin_id->toBe($admin->id);
});

it('keeps the status filter when searching', function () {
    $admin = User::factory()->admin()->create();
    $project = Project::factory()->create(['status' => 'published', 'title' => 'Zebra Camp']);
    applicationFor($project, 'approved');
    $pending = applicationFor($project, 'pending');

    $this->actingAs($admin)
        ->get(route('admin.applications.all', ['status' => 'approved', 'search' => 'Zebra']))
        ->assertOk()
        ->assertDontSee($pending->user->email);
});

it('serves the PDF only to its owner or an admin', function () {
    Storage::fake('local');
    Storage::disk('local')->put('applications/cv.pdf', 'pdf');
    $project = Project::factory()->create(['status' => 'published']);
    $application = applicationFor($project);
    $application->update(['document_path' => 'applications/cv.pdf', 'document_name' => 'cv.pdf']);

    $this->get(route('applications.document', $application))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())
        ->get(route('applications.document', $application))->assertForbidden();
    $this->actingAs($application->user)
        ->get(route('applications.document', $application))->assertOk();
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('applications.document', $application))->assertOk();
});

it('rejects a second application to the same project', function () {
    $project = Project::factory()->create(['status' => 'published']);
    $application = applicationFor($project);

    expect(fn () => applicationFor($project)->update(['user_id' => $application->user_id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('removes uploaded files when a project is deleted', function () {
    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('public')->put('projects/cover.jpg', 'x');
    Storage::disk('local')->put('applications/cv.pdf', 'x');
    $project = Project::factory()->create(['image_path' => 'projects/cover.jpg']);
    applicationFor($project)->update(['document_path' => 'applications/cv.pdf']);

    $project->delete();

    Storage::disk('public')->assertMissing('projects/cover.jpg');
    Storage::disk('local')->assertMissing('applications/cv.pdf');
});
