<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $issues = Issue::query()
            ->with(['project:id,name', 'tags:id,name,color'])
            ->status($request->string('status')->toString() ?: null)
            ->priority($request->string('priority')->toString() ?: null)
            ->tag($request->integer('tag') ?: null)
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('issues._table', compact('issues'))->render(),
                'pagination' => (string) $issues->links(),
            ]);
        }

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $tags     = Tag::orderBy('name')->get(['id', 'name', 'color']);

        return view('issues.index', compact('issues', 'projects', 'tags'));
    }

    public function create(Request $request): View
    {
        $issue = new Issue([
            'project_id' => $request->integer('project_id') ?: null,
            'status'     => 'open',
            'priority'   => 'medium',
        ]);

        return view('issues.create', [
            'issue'    => $issue,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'tags'     => Tag::orderBy('name')->get(['id', 'name', 'color']),
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $issue = Issue::create($data);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue created.');
    }

    public function show(Issue $issue): View
    {
        $issue->load([
            'project:id,name',
            'tags:id,name,color',
            'assignees:id,name',
        ]);

        $allTags     = Tag::orderBy('name')->get(['id', 'name', 'color']);
        $allUsers    = User::orderBy('name')->get(['id', 'name']);
        $attachedIds = $issue->tags->pluck('id')->all();
        $assigneeIds = $issue->assignees->pluck('id')->all();

        return view('issues.show', compact('issue', 'allTags', 'allUsers', 'attachedIds', 'assigneeIds'));
    }

    public function edit(Issue $issue): View
    {
        return view('issues.edit', [
            'issue'    => $issue,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $issue->update($request->validated());

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue updated.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $projectId = $issue->project_id;
        $issue->delete();

        return redirect()
            ->route('projects.show', $projectId)
            ->with('status', 'Issue deleted.');
    }
}
