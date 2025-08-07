<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodo;
use App\Http\Requests\UpdateTodo;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Log;

class TodoController extends Controller
{
    /**
     * コントローラインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Todo::class);
    }

    /**
     * 一覧表示
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        $userId = Auth::id();
        $todos = Todo::with(['user:id,name,deleted_at'])
            ->where('user_id', $userId)
            ->paginate();

        return inertia('Todos', [
            'todos' => $todos,
        ]);
    }

    /**
     * 新規登録
     *
     * @param \App\Http\Requests\StoreTodo $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTodo $request): RedirectResponse
    {
        $todo = new Todo();
        $todo->fill(
            $request->safe()->merge(['user_id' => Auth::id()])->all()
        )->save();

        return redirect()->route('todos.index');
    }

    /**
     * 更新処理
     *
     * @param \App\Http\Requests\UpdateTodo $request
     * @param \App\Models\Todo $todo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTodo $request, Todo $todo): RedirectResponse
    {
        $todo->fill($request->validated())->save();

        return redirect()->route('todos.index');
    }

    public function toggleDone(Todo $todo): RedirectResponse
    {
        $this->authorize('update', $todo);

        $todo->is_done = !$todo->is_done;
        $todo->save();

        return redirect()->route('todos.index');
    }

    /**
     * 削除処理
     *
     * @param \App\Models\Todo $todo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Todo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()->route('todos.index');
    }
}
