<?php

namespace App\src\Controllers;

use App\Http\Controllers\Controller;
use App\src\Models\Comment;
use function App\Http\Controllers\view;

class CommentController extends Controller
{
	public function index()
	{
		$comments = Comment::all();
		return view('comments.index', compact('comments'));
	}
}