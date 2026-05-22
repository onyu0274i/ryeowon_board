<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

const form = useForm({
    user_login_id: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="시스템 접속" />
    <div class="min-h-screen bg-[#0f171a] flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-[#162124] border border-gray-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800 bg-[#1c2a2e] text-center">
                <h2 class="text-2xl font-black text-[#4ade80] tracking-tighter">💚로그인💚</h2>
                <p class="text-[10px] text-gray-500 mt-1 uppercase">권한이 있는 사용자만 접속 가능합니다.</p>
            </div>

            <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-600 uppercase">ID / 사용자</label>
                    <input v-model="form.user_login_id" type="text" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition" required autofocus />
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-600 uppercase">Password / 암호</label>
                    <input v-model="form.password" type="password" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-3 text-sm text-gray-300 focus:border-[#4ade80] outline-none transition" required />
                </div>

                <div v-if="form.errors.login" class="text-red-500 text-xs font-bold">{{ form.errors.login }}</div>

                <button type="submit" :disabled="form.processing" class="w-full bg-[#4ade80] text-[#0f171a] py-3 rounded font-black hover:bg-[#22c55e] transition text-sm shadow-[0_0_15px_rgba(74,222,128,0.2)] uppercase">
                    로그인
                </button>

                <div class="text-center pt-4 border-t border-gray-800">
                    <Link href="/register" class="text-[10px] text-gray-500 hover:text-[#4ade80] transition underline uppercase">계정이 없나요? (회원가입)</Link>
                </div>
            </form>
        </div>
    </div>
</template>