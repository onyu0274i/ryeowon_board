<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

// 컨트롤러에서 보낸 post와 categories 데이터를 받습니다.
const props = defineProps({
    post: Object,
    categories: Array
});

// 수정 폼 데이터 초기화
const form = useForm({
    title: props.post.title,
    content: props.post.content,
    category_id: props.post.category_id,
});

// 수정 실행 함수
const submit = () => {
    // web.php에 설정한 Route::put('/post/{id}', ...) 경로로 데이터 전송
    form.put(`/post/${props.post.posts_id}`, {
        onSuccess: () => alert('데이터가 성공적으로 수정되었습니다.'),
    });
};
</script>

<template>
    <Head title="Edit_Record" />
    <div class="min-h-screen bg-[#0f171a] text-gray-300 p-8 font-sans">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
                <h1 class="text-2xl font-black text-white uppercase tracking-tighter">Edit_Record</h1>
                <Link href="/" class="text-xs text-gray-500 hover:text-[#4ade80] transition uppercase font-bold">[ Return_to_Main ]</Link>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6 bg-[#162124] p-8 rounded-lg border border-gray-800 shadow-2xl">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">Classification</label>
                    <select v-model="form.category_id" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition">
                        <option v-for="cat in categories" :key="cat.category_id" :value="cat.category_id">
                            {{ cat.category_name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">Entry_Title</label>
                    <input v-model="form.title" type="text" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition" placeholder="제목을 입력하세요...">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">Data_Content</label>
                    <textarea v-model="form.content" rows="12" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition resize-none" placeholder="내용을 기록하세요..."></textarea>
                </div>

                <div class="flex justify-end gap-6 items-center pt-4">
                    <button type="button" @click="history.back()" class="text-gray-600 text-[10px] font-bold uppercase hover:text-white transition tracking-widest">
                        Abort_Changes
                    </button>
                    <button type="submit" :disabled="form.processing" class="bg-[#4ade80] text-[#0f171a] px-10 py-3 rounded font-black hover:bg-[#22c55e] transition text-xs uppercase shadow-[0_0_15px_rgba(74,222,128,0.1)]">
                        {{ form.processing ? 'Syncing...' : 'Update_Record' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>