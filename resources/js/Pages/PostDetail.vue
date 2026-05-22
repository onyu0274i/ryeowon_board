<script setup>
import { Link, useForm, Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    post: Object
});

const page = usePage();

const commentForm = useForm({
    content: '',
    parent_id: null
});

// 댓글 등록
const submitComment = () => {
    if (!commentForm.content.trim()) return alert('내용을 입력해주세요.');
    commentForm.post(`/post/${props.post.posts_id}/comment`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('content', 'parent_id')
    });
};

// 좋아요 기능
const toggleLike = () => {
    useForm({}).post(`/post/${props.post.posts_id}/like`, { preserveScroll: true });
};

// 게시글 삭제 기능 추가
const deletePost = () => {
    if (confirm('이 게시글을 삭제할까요?')) {
        useForm({}).delete(`/post/${props.post.posts_id}`);
    }
};
</script>

<template>
    <Head :title="post.title" />
    <div class="min-h-screen bg-[#0f171a] text-gray-300 p-8 font-sans">
        <div class="max-w-4xl mx-auto">
            <nav class="mb-8 flex items-center justify-between">
                <Link href="/" class="text-[#4ade80] hover:underline flex items-center gap-2 text-sm font-bold">
                    <span>←</span> 게시판 목록으로 돌아가기
                </Link>
                
                <div v-if="page.props.Auth?.user?.user_login_id === 'ryeowon' || page.props.Auth?.user?.id === post.user_id" 
                     class="flex gap-3">
                    <Link :href="`/post/${post.posts_id}/edit`" class="text-[10px] font-bold text-blue-400 hover:text-blue-300 uppercase border border-blue-900 px-3 py-1 rounded transition">
                        수정하기
                    </Link>
                    <button @click="deletePost" class="text-[10px] font-bold text-red-500 hover:text-red-400 uppercase border border-red-900 px-3 py-1 rounded transition">
                        삭제하기
                    </button>
                </div>
            </nav>

            <article class="bg-[#162124] border border-gray-800 rounded-lg overflow-hidden shadow-2xl mb-10">
                <header class="p-8 border-b border-gray-800 bg-[#1c2a2e]">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-[10px] px-2 py-1 rounded bg-[#0f171a] text-[#4ade80] border border-[#4ade80]/30 font-bold uppercase tracking-tighter">
                            {{ post.category?.category_name }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-black text-white mb-6 tracking-tight">{{ post.title }}</h1>
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex gap-6">
                            <span class="text-gray-500"><b class="text-gray-600 text-[10px] mr-2 uppercase tracking-widest">유저</b> {{ post.user?.nickname }}</span>
                            <span class="text-gray-500"><b class="text-gray-600 text-[10px] mr-2 uppercase tracking-widest">조회</b> {{ post.view_count }}</span>
                        </div>
                        <button @click="toggleLike" class="flex items-center gap-2 px-5 py-2 bg-[#0f171a] rounded-full border border-gray-800 hover:border-[#4ade80]/50 transition group">
                            <span class="group-hover:scale-125 transition-transform">💚</span>
                            <span class="font-bold text-white">{{ post.like_count || 0 }}</span>
                        </button>
                    </div>
                </header>
                <div class="p-8 text-gray-300 leading-relaxed whitespace-pre-wrap min-h-[300px] selection:bg-[#4ade80] selection:text-[#0f171a]">
                    {{ post.content }}
                </div>
            </article>

            <section class="space-y-6 mb-20">
                <h3 class="text-lg font-black text-white flex items-center gap-2 uppercase tracking-widest">
                    댓글 피드 <span class="text-sm text-[#4ade80] font-mono">[{{ post.comments?.length || 0 }}]</span>
                </h3>

                <div v-for="comment in post.comments" :key="comment.comment_id" class="space-y-4">
                    <div class="bg-[#162124] p-5 rounded border border-gray-800 shadow-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[#4ade80] font-bold text-sm">{{ comment.user?.nickname }}</span>
                            <span class="text-[10px] text-gray-600 font-mono">{{ new Date(comment.created_at).toLocaleString() }}</span>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">{{ comment.content }}</p>
                        <button @click="commentForm.parent_id = comment.comment_id" class="mt-3 text-[10px] text-gray-500 hover:text-[#4ade80] font-bold uppercase tracking-widest">
                            [ 댓글 작성 ]
                        </button>
                    </div>

                    <div v-for="reply in comment.replies" :key="reply.comment_id" class="ml-10 bg-[#111a1d] p-4 rounded border-l-2 border-[#4ade80]/30 border-gray-800">
                        <div class="flex items-center gap-2 mb-2 text-xs">
                            <span class="text-[#4ade80] opacity-50 font-bold">↳</span>
                            <span class="text-gray-400 font-bold">{{ reply.user?.nickname }}</span>
                        </div>
                        <p class="text-gray-400 text-sm pl-5">{{ reply.content }}</p>
                    </div>
                </div>

                <div class="mt-10 bg-[#162124] p-6 rounded-lg border border-gray-800 shadow-xl">
                    <div v-if="commentForm.parent_id" class="mb-3 flex justify-between items-center text-[10px] bg-[#0f171a] p-2 rounded font-bold uppercase">
                        <span class="text-[#4ade80]">Replying to Record #{{ commentForm.parent_id }}</span>
                        <button @click="commentForm.parent_id = null" class="text-red-500 hover:underline">취소</button>
                    </div>
                    <textarea v-model="commentForm.content" placeholder="댓글을 입력하세요..." class="w-full bg-[#0f171a] border border-gray-800 rounded p-4 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition min-h-[100px]"></textarea>
                    <div class="flex justify-end mt-4">
                        <button @click="submitComment" :disabled="commentForm.processing" class="bg-[#4ade80] text-[#0f171a] px-8 py-2 rounded font-black hover:bg-[#22c55e] transition text-sm uppercase shadow-[0_0_10px_rgba(74,222,128,0.2)]">
                            {{ commentForm.processing ? '댓글 등록 중...' : '댓글 등록' }}
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>