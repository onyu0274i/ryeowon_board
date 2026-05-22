<script setup>
import { Link, useForm, Head, usePage } from '@inertiajs/vue3';

defineProps({
    posts: Array,
    // 컨트롤러에서 대문자 Auth로 보낼 경우를 대비해 usePage를 활용하는 것이 안전합니다.
    auth: Object 
});

const page = usePage();
const form = useForm({});
const logout = () => form.post('/logout');
</script>

<template>
    <Head title="메인 게시판" />
    <div class="min-h-screen bg-[#0f171a] text-gray-300 font-sans p-8">
        <div class="max-w-7xl mx-auto">
            <header class="flex justify-between items-center mb-10 border-b border-gray-800 pb-6">
                <h1 class="text-3xl font-black text-[#4ade80] tracking-tighter">RYEO-WON'S BOARD</h1>
                <div class="flex items-center gap-6 text-sm">
                    <div v-if="page.props.Auth?.user || auth?.user" class="flex items-center gap-4">
                        <span class="text-gray-500 text-xs">접속자:</span>
                        <span class="font-mono text-[#4ade80]">{{ (page.props.Auth?.user || auth?.user).nickname }}님</span>
                        <button @click="logout" class="hover:text-white transition">로그아웃 ⏻</button>
                    </div>
                    <div v-else class="flex gap-4">
                        <Link href="/login" class="hover:text-[#4ade80]">로그인</Link>
                        <Link href="/register" class="hover:text-[#4ade80]">회원가입</Link>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <main class="lg:col-span-3">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-white">전체 게시글</h2>
                        <Link href="/write" class="bg-[#4ade80] text-[#0f171a] px-5 py-2 rounded font-bold hover:bg-[#22c55e] transition text-sm">
                            + 새 글 작성
                        </Link>
                    </div>

                    <div class="bg-[#162124] rounded-lg border border-gray-800 overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead class="bg-[#1c2a2e] text-[11px] uppercase tracking-widest text-gray-500 border-b border-gray-800">
                                <tr>
                                    <th class="p-4">분류</th>
                                    <th class="p-4">제목</th>
                                    <th class="p-4 text-center">작성자</th>
                                    <th class="p-4 text-center">조회</th>
                                    <th class="p-4 text-center">좋아요</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-sm">
                                <tr v-for="post in posts" :key="post.posts_id" class="hover:bg-[#1c2a2e] transition">
                                    <td class="p-4">
                                        <span class="text-[10px] px-2 py-1 rounded bg-[#0f171a] text-[#4ade80] border border-[#4ade80]/30 font-bold">
                                            {{ post.category?.category_name }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <Link :href="`/post/${post.posts_id}`" class="text-gray-200 hover:text-[#4ade80] font-medium">
                                            {{ post.title }}
                                        </Link>
                                    </td>
                                    <td class="p-4 text-center text-gray-500">{{ post.user?.nickname }}</td>
                                    <td class="p-4 text-center font-mono">{{ post.view_count }}</td>
                                    <td class="p-4 text-center text-[#4ade80] font-mono">💚 {{ post.like_count || 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </main>

                <aside class="space-y-6">
                    <div class="bg-[#162124] p-6 rounded-lg border border-gray-800">
                        <h3 class="text-gray-500 text-xs font-bold uppercase mb-4 text-center">사용자 프로필</h3>
                        <div class="flex flex-col items-center gap-3" v-if="page.props.Auth?.user || auth?.user">
                            <div class="w-16 h-16 rounded-full bg-[#1c2a2e] border-2 border-[#4ade80]/20 flex items-center justify-center text-2xl">👤</div>
                            <div class="text-center">
                                <p class="text-white font-bold text-lg">{{ (page.props.Auth?.user || auth?.user).nickname }}</p>
                                <p class="text-[10px] text-gray-500">PostgreSQL 데이터 관리자</p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500 text-center py-4 text-xs">로그인이 필요한 시스템입니다.</p>
                    </div>

                    <div class="bg-[#162124] p-6 rounded-lg border border-gray-800">
                        <h3 class="text-gray-500 text-xs font-bold uppercase mb-4 text-center">시스템 정보</h3>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="text-gray-600">엔진</span>
                                <span class="text-[#4ade80]">PHP / Laravel</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="text-gray-600">프론트엔드</span>
                                <span class="text-[#4ade80]">Vue.js / Inertia</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-800 pb-2">
                                <span class="text-gray-600">상태</span>
                                <span class="text-blue-400">온라인</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>