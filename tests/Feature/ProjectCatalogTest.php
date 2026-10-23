<?php

use App\Models\Project;

function makeProject(array $attrs = []): Project
{
    return Project::factory()->create(array_merge(['status' => 'published'], $attrs));
}

it('lists only published projects by default', function () {
    makeProject(['title' => 'Visible one']);
    makeProject(['title' => 'Hidden draft', 'status' => 'draft']);

    $this->get(route('project.index'))
        ->assertOk()
        ->assertSee('Visible one')
        ->assertDontSee('Hidden draft');
});

it('blocks guests from the draft listing', function () {
    $this->get(route('project.index', ['status' => 'draft']))->assertForbidden();
});

it('filters by duration on any database driver', function () {
    makeProject(['title' => 'Short stay', 'start_date' => '2030-01-01', 'end_date' => '2030-01-08']);
    makeProject(['title' => 'Long stay', 'start_date' => '2030-01-01', 'end_date' => '2030-12-01']);

    $this->get(route('project.index', ['duration' => ['short']]))
        ->assertOk()
        ->assertSee('Short stay')
        ->assertDontSee('Long stay');
});

it('only allows forward status transitions', function () {
    expect(Project::canTransitionStatus('draft', 'published'))->toBeTrue()
        ->and(Project::canTransitionStatus('draft', 'completed'))->toBeFalse()
        ->and(Project::canTransitionStatus('published', 'draft'))->toBeFalse()
        ->and(Project::canTransitionStatus('completed', 'published'))->toBeFalse();
});
