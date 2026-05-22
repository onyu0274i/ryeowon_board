<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    post: Object
});

const page = usePage();
const form = useForm({});

// 삭제 기능 
const deletePost = () => {
    if (confirm('이 게시글을 삭제 하시겠습니까?')) {
        form.delete(`/post/${props.post.posts_id}`);
    }
};
</script>

<template>
    <Head :title="post.title" />
    <div class="min-h-screen bg-[#0f171a] text-gray-300 p-8 font-sans">
        <div class="max-w-4xl mx-auto">
            <nav class="mb-10 flex justify-between items-center">
                <Link href="/" class="text-[#4ade80] hover:underline flex items-center gap-2 text-sm font-bold uppercase">
                    <span>←</span> Back to Terminal
                </Link>
                
                <div v-if="page.props.Auth?.user?.user_login_id === 'ryeowon' || page.props.Auth?.user?.user_id === post.user_id" class="flex gap-4">
                    <Link :href="`/post/${post.posts_id}/edit`" class="text-xs font-bold text-blue-400 hover:text-blue-300 uppercase border border-blue-900 px-3 py-1 rounded transition">
                        수정하기
                    </Link>
                    <button @click="deletePost" class="text-xs font-bold text-red-500 hover:text-red-400 uppercase border border-red-900 px-3 py-1 rounded transition font-mono">
                        삭제하기
                    </button>
                </div>
            </nav>

            <article class="bg-[#162124] border border-gray-800 rounded-lg shadow-2xl overflow-hidden">
                <header class="p-8 border-b border-gray-800 bg-[#1c2a2e]">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-2 py-1 bg-[#4ade80]/10 text-[#4ade80] text-[10px] font-mono border border-[#4ade80]/30 rounded">
                            ID: {{ post.posts_id }} / {{ post.category?.category_name || 'GENERAL' }}
                        </span>
                        <span class="text-[10px] text-gray-500 font-mono italic">TIMESTAMP: {{ post.created_at }}</span>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tighter">{{ post.title }}</h1>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="w-6 h-6 bg-[#4ade80] rounded-full flex items-center justify-center text-[10px] text-[#0f171a] font-bold">
                            {{ post.user?.user_login_id?.substring(0, 1).toUpperCase() }}
                        </div>
                        <span class="text-sm text-gray-400 font-bold">{{ post.user?.user_login_id }}</span>
                    </div>
                </header>

                <div class="p-8 prose prose-invert max-w-none">
                    <p class="whitespace-pre-wrap leading-relaxed text-gray-300 selection:bg-[#4ade80] selection:text-[#0f171a]">
                        {{ post.content }}
                    </p>
                </div>

                <footer class="p-6 bg-[#0f171a]/50 border-t border-gray-800 flex gap-6 text-[11px] font-mono text-gray-500">
                    <span>VIEWS: {{ post.view_count }}</span>
                    <span>LIKES: {{ post.like_count }}</span>
                </footer>
            </article>

            <div class="mt-6 flex justify-between items-center text-[10px] text-gray-600 font-mono uppercase tracking-widest">
                <span>Access_Level: {{ page.props.Auth?.user?.user_login_id === 'ryeowon' ? 'Root_Admin' : 'Authorized_User' }}</span>
                <span class="text-[#4ade80]/50">Encryption: AES-256-GCM</span>
            </div>
        </div>
    </div>
</template>