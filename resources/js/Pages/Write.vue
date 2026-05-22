<script setup>
import { useForm, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({
    categories: Array
});

const page = usePage();

// 관리자 여부 확인 (ryeowon으로 아이디 통일)
const isAdmin = computed(() => page.props.Auth?.user?.user_login_id === 'ryeowon');

// 일반 유저에게는 공지사항을 제외한 카테고리만 노출
const filteredCategories = computed(() => {
    if (!props.categories) return [];
    return props.categories.filter(cat => 
        cat.category_name !== '공지사항' || isAdmin.value
    );
});

const form = useForm({
    category_id: '', // 초기화는 빈값으로 두고 마운트 시 채움
    title: '',
    content: ''
});

// [패치] 화면이 완벽히 그려지고 카테고리 리스트 데이터가 안착했을 때 첫 번째 값을 지정해 줌
onMounted(() => {
    if (filteredCategories.value.length > 0) {
        form.category_id = filteredCategories.value[0].category_id;
    }
});

const submit = () => form.post('/write');
</script>

<template>
    <Head title="데이터 생성" />
    <div class="min-h-screen bg-[#0f171a] text-gray-300 p-8 font-sans">
        <div class="max-w-3xl mx-auto">
            <nav class="mb-10">
                <Link href="/" class="text-[#4ade80] hover:underline flex items-center gap-2 text-sm font-bold">
                    <span>←</span> 목록으로 돌아가기 (작성 취소)
                </Link>
            </nav>

            <div class="bg-[#162124] border border-gray-800 rounded-lg shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-800 bg-[#1c2a2e]">
                    <h2 class="text-xl font-black text-white uppercase tracking-widest">💚 게시글 작성 중...</h2>
                    <p class="text-[10px] text-gray-500 mt-1 uppercase text-blue-400">새 게시글을 등록합니다.</p>
                </div>

                <form @submit.prevent="submit" class="p-8 space-y-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-widest">Category / 분류</label>
                        <select v-model="form.category_id" 
                                class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition cursor-pointer appearance-auto"
                                required>
                            <option value="" disabled v-if="filteredCategories.length === 0">등록된 카테고리가 없습니다.</option>
                            
                            <option v-for="cat in categories" :key="cat.category_id" :value="cat.category_id">
                                {{ cat.category_name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-widest">Subject / 제목</label>
                        <input v-model="form.title" type="text" placeholder="제목을 입력하세요..." class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition" required />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-widest">Content / 본문</label>
                        <textarea v-model="form.content" rows="12" placeholder="본문 내용을 입력하세요..." class="w-full bg-[#0f171a] border border-gray-800 rounded p-4 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition leading-relaxed" required></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" :disabled="form.processing" class="bg-[#4ade80] text-[#0f171a] px-12 py-3 rounded font-black hover:bg-[#22c55e] transition text-sm shadow-[0_0_15px_rgba(74,222,128,0.3)] uppercase">
                            {{ form.processing ? '전송 중...' : '게시글 등록' }}
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-6 flex justify-between items-center text-[10px] text-gray-600 font-mono tracking-widest uppercase">
                <span>Status: Online</span>
                <span class="text-[#4ade80]/50 animate-pulse">● Ready to Inject</span>
                <span>System: Ryeowon's board</span>
            </div>
        </div>
    </div>
</template>