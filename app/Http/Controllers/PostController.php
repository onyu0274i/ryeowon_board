<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class PostController extends Controller
{
    /* 1. 게시판 메인 목록 */
    public function index()
    {
        $posts = Post::with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'Auth' => ['user' => Auth::user()], 
            'flash' => ['error' => session('error')]
        ]);
    }

    /* 2. 게시글 상세 보기 */
    public function show($id)
    {
        $post = Post::with([
            'user', 
            'category', 
            'comments' => function($query) {
                $query->whereNull('parent_id')
                      ->with([
                          'user', 
                          'replies.user', 
                          'replies.parent.user'
                      ])
                      ->latest();
            }
        ])->where('posts_id', $id)->firstOrFail();

        // 권한 체크: 비밀게시판
        if ($post->category && $post->category->category_name === '비밀게시판') {
            $user = Auth::user();
            if (!$user || ($user->user_id !== $post->user_id && $user->user_login_id !== 'ryeowon')) {
                return redirect()->route('home')->with('error', '비밀게시판은 작성자와 관리자만 접근 가능합니다.');
            }
        }

        // 최초 진입 시에만 조회수 증가 (댓글/좋아요로 인한 새로고침 방지)
        $viewedPosts = session('viewed_posts', []);

        if (!in_array($id, $viewedPosts)) {
            // 아직 읽지 않은 글이라면 조회수 증가 후 세션에 기록
            $post->increment('view_count');
            session()->push('viewed_posts', $id);
        }
        // -----------------------------------------------------------------

        return Inertia::render('PostDetail', [
            'post' => $post,
            'Auth' => ['user' => Auth::user()]
        ]);
    }


    /* 3. 게시글 저장 (글쓰기 완료) */
    public function store(Request $request)
    {
        // 필수 필드 유효성 검사
        $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $category = Category::findOrFail($request->category_id);

        // [보안 추가] 공지사항은 'ryeowon' 관리자만 작성할 수 있도록 백엔드 강제 차단
        if ($category->category_name === '공지사항') {
            if (!$user || $user->user_login_id !== 'ryeowon') {
                abort(403, '공지사항은 관리자만 작성할 수 있는 영역입니다.');
            }
        }

        Post::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            'view_count' => 0,
            'like_count' => 0,
        ]);

        return redirect()->route('home');
    }

    /* 4. 게시글 수정 화면 */
    public function edit($id)
    {
        $post = Post::where('posts_id', $id)->firstOrFail();
        $user = Auth::user();

        // 본인이 쓴 글이거나, 관리자(ryeowon)인 경우에만 통과 (타입 에러 방지를 위해 (int) 캐스팅 추가)
        if ($user->user_login_id !== 'ryeowon' && (int)$user->user_id !== (int)$post->user_id) {
            abort(403, '본인의 게시글만 수정할 수 있습니다.');
        }

        return Inertia::render('Edit', [
            'post' => $post,
            'categories' => Category::all()
        ]);
    }

    /* 5. 게시글 수정 실행 */
    public function update(Request $request, $id)
    {
        $post = Post::where('posts_id', $id)->firstOrFail();
        $user = Auth::user();

        // 본인이 쓴 글이거나, 관리자(ryeowon)인 경우에만 통과
        if ($user->user_login_id !== 'ryeowon' && (int)$user->user_id !== (int)$post->user_id) {
            abort(403, '본인의 게시글만 수정할 수 있습니다.');
        }

        $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // 보안 검증: 일반 유저가 악의적으로 공지사항으로 카테고리를 바꾸는 것 차단
        $newCategory = Category::findOrFail($request->category_id);
        if ($newCategory->category_name === '공지사항' && $user->user_login_id !== 'ryeowon') {
            abort(403, '일반 유저는 공지사항 카테고리로 변경할 수 없습니다.');
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('post.show', $id);
    }

    /* 6. 게시글 삭제 */
    public function destroy($id)
    {
        $post = Post::where('posts_id', $id)->firstOrFail();
        $user = Auth::user();

        // 본인이 쓴 글이거나, 관리자(ryeowon)인 경우에만 통과
        if ($user->user_login_id !== 'ryeowon' && (int)$user->user_id !== (int)$post->user_id) {
            abort(403, '본인의 게시글만 삭제할 수 있습니다.');
        }

        $post->delete();

        return redirect()->route('home');
    }

    /* 7. 댓글/대댓글 및 좋아요 기능 */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        Comment::create([
            'post_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        return back();
    }

    public function like($id)
    {
        $userId = Auth::id();
        $postId = $id;

        $existingLike = Like::where('user_id', $userId)
                            ->where('post_id', $postId)
                            ->first();

        $post = Post::where('posts_id', $postId)->firstOrFail();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('like_count');
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId
            ]);
            $post->increment('like_count');
        }

        return back();
    }

    /* 8. 유저 인증 */
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('user_login_id', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }
        return back()->withErrors(['login' => '아이디 또는 비밀번호가 틀렸습니다.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function register(Request $request)
    {
        // 회원가입 입력값 검증 (Validation)
        $request->validate([
            // unique:테이블명,컬럼명 규칙 -> 라라벨 -> DB를 조회해 중복을 걸러냄
            'user_login_id' => 'required|string|max:255|unique:users,user_login_id',
            'nickname' => 'required|string|max:255|unique:users,nickname', // 닉네임 중복 검사 추가
            'password' => 'required|string|min:4|confirmed', 
        ], [
            // 에러 메시지
            'user_login_id.required' => '아이디를 입력해 주세요.',
            'user_login_id.unique' => '이미 등록된 아이디입니다.',
            'nickname.required' => '닉네임을 입력해 주세요.',
            'nickname.unique' => '이미 사용 중인 닉네임입니다.',
            'password.required' => '비밀번호를 입력해 주세요.',
            'password.min' => '비밀번호는 최소 4자리 이상이어야 합니다.',
            'password.confirmed' => '비밀번호가 일치하지 않습니다.',
        ]);

        // 위의 유효성 검사를 통과해야만 아래 데이터베이스 저장이 실행됨
        $user = User::create([
            'user_login_id' => $request->user_login_id,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }
}