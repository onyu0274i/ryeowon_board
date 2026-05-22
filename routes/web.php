<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. 누구나 접근 가능한 페이지
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

// 2. 인증(로그인)이 필요한 기능
Route::middleware('auth')->group(function () {
    
    // [글쓰기 화면] - 중복 제거 및 카테고리 필터 백엔드 일원화
    Route::get('/write', function () {
        $user = Auth::user();
        
        // 1. 관리자(ryeowon)인 경우: '공지사항' 포함 모든 카테고리 전송
        if ($user && $user->user_login_id === 'ryeowon') {
            $categories = \App\Models\Category::all();
        } 
        // 2. 일반 유저인 경우: '공지사항' 제외하고 전송
        else {
            $categories = \App\Models\Category::where('category_name', '!=', '공지사항')->get();
        }

        return Inertia::render('Write', [
            'categories' => $categories
        ]);
    })->name('write');
    
    // [글 저장]
    Route::post('/write', [PostController::class, 'store']);

    // [수정 및 삭제] 
    Route::get('/post/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::put('/post/{id}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('post.destroy');

    // [상호작용]
    Route::post('/post/{id}/like', [PostController::class, 'like'])->name('post.like');
    Route::post('/post/{id}/comment', [PostController::class, 'comment'])->name('post.comment');

    // [로그아웃]
    Route::post('/logout', [PostController::class, 'logout'])->name('logout');
});

// 3. 게스트(비로그인) 전용 페이지
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', [PostController::class, 'login']);

    Route::get('/register', fn() => Inertia::render('Auth/Register'))->name('register');
    Route::post('/register', [PostController::class, 'register']);
});


/*
// 1. 메인 화면 (게시글 목록)
Route::get('/', function () {
    $user = User::firstOrCreate(['user_login_id' => 'ryewon'], [
        'password' => bcrypt('1234'),
        'nickname' => '려원'
    ]);
    
    Category::firstOrCreate(['category_name' => '자유게시판']);
    Category::firstOrCreate(['category_name' => '비밀게시판']);

    $posts = Post::with(['user', 'category', 'comments'])->latest()->get();
    return view('board', ['posts' => $posts]);
}); */

// PHP에서 Vue(PostController)로 이동

/*
Route::get('/', [PostController::class, 'index']);


// 2. 게시글 상세 페이지 (조회수 중복 방지)
Route::get('/post/{id}', function ($id) {
    // 게시글과 작성자, 카테고리 정보를 한 번에 가져옴
    $post = Post::with(['user', 'category', 'comments'])->where('posts_id', $id)->firstOrFail();

    // 1. 비밀게시판인 경우 권한 체크 실행
    if ($post->category->category_name === '비밀게시판') {
        $currentUser = Auth::user();

        // 관리자 아이디 정의 ('려원' 아이디가 관리자라고 가정)
        $isAdmin = ($currentUser && $currentUser->user_login_id === 'ryewon');
        
        // 글 작성자 본인인지 확인
        $isAuthor = ($currentUser && $currentUser->user_id === $post->user_id);

        // 관리자도 아니고 작성자도 아니라면 차단
        if (!$isAdmin && !$isAuthor) {
            return "<script>alert('비밀글은 작성자와 관리자만 볼 수 있습니다.'); history.back();</script>";
        }
    } 

    // 2. 조회수 중복 증가 방지 (세션 활용)
    if (!Session::has('viewed_post_' . $id)) {
        $post->increment('view_count');
        Session::put('viewed_post_' . $id, true);
    }

    return view('post-detail', ['post' => $post]);
}); 


// 게시글 상세 페이지: 복잡한 로직은 PostController의 show 함수가 처리함
Route::get('/post/{id}', [PostController::class, 'show']);


// 3. 게시글 좋아요 (1인 1회 토글)
Route::post('/post/{id}/like', function ($id) {
    if (!Auth::check()) return back();

    $userId = Auth::id();
    $post = Post::where('posts_id', $id)->first();

    // 이미 눌렀는지 기록 확인
    $like = Like::where('user_id', $userId)->where('post_id', $id)->first();

    if ($like) {
        $like->delete(); // 이미 눌렀으면 좋아요 취소
    } else {
        Like::create(['user_id' => $userId, 'post_id' => $id]); // 등록
    }

    // 실제 Like 기록의 개수를 세어서 업데이트
    $post->update(['like_count' => Like::where('post_id', $id)->count()]);

    return redirect('/post/' . $id);
})->middleware('auth');

// 4. 댓글 좋아요 (1인 1회 토글)
Route::post('/comment/{id}/like', function   ($id) {
    if (!Auth::check()) return back();

    $userId = Auth::id();
    $comment = Comment::where('comment_id', $id)->first();

    $like = Like::where('user_id', $userId)->where('comment_id', $id)->first();

    if ($like) {
        $like->delete();
    } else {
        Like::create(['user_id' => $userId, 'comment_id' => $id]);
    }

    $comment->update(['like_count' => Like::where('comment_id', $id)->count()]);

    return back();
})->middleware('auth');

// 5. 댓글 작성
Route::post('/post/{id}/comment', function (Request $request, $id) {
    if (!Auth::check()) return redirect('/login');

    Comment::create([
        'post_id' => $id,
        'user_id' => Auth::id(),
        'content' => $request->content,
        'parent_id' => $request->parent_id
    ]);

    return redirect('/post/' . $id);
})->middleware('auth');

// 6. 글쓰기 (화면 & 저장)
Route::get('/write', function () {
    return view('write', ['categories' => Category::all()]);
})->middleware('auth');

Route::post('/write', function (Request $request) {
    Post::create([
        'user_id' => Auth::id(),
        'category_id' => $request->category_id,
        'title' => $request->title,
        'content' => $request->content,
        'view_count' => 0,
        'like_count' => 0,
    ]);
    return redirect('/');
})->middleware('auth');

// 7. 인증 (회원가입/로그인/로그아웃)
Route::get('/register', fn() => view('auth.register'));
Route::post('/register', function (Request $request) {
    $user = User::create([
        'user_login_id' => $request->user_login_id,
        'nickname' => $request->nickname,
        'password' => Hash::make($request->password),
    ]);
    Auth::login($user);
    return redirect('/');
});

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', function (Request $request) {
    if (Auth::attempt($request->only('user_login_id', 'password'))) {
        $request->session()->regenerate();
        return redirect('/');
    }
    return back()->withErrors(['login' => '아이디 또는 비밀번호가 틀렸습니다.']);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

*/

