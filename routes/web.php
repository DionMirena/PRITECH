<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueAssigneeController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);

Route::resource('issues', IssueController::class);

Route::resource('tags', TagController::class)->only(['index', 'store', 'destroy']);

Route::patch ('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

Route::prefix('issues/{issue}')->group(function () {
    Route::get('comments', [CommentController::class, 'index'])->name('issues.comments.index');
    Route::post('comments', [CommentController::class, 'store'])->name('issues.comments.store');

    Route::post('tags', [IssueTagController::class, 'store'])->name('issues.tags.store');
    Route::delete('tags/{tag}', [IssueTagController::class, 'destroy'])->name('issues.tags.destroy');

    Route::post('assignees', [IssueAssigneeController::class, 'store'])->name('issues.assignees.store');
    Route::delete('assignees/{user}', [IssueAssigneeController::class, 'destroy'])->name('issues.assignees.destroy');

    Route::patch('status', [IssueController::class, 'patchStatus'])->name('issues.status.update');
});
