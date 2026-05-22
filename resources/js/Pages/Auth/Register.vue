<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

const form = useForm({
    user_login_id: '',
    nickname: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="💚회원가입💚" />
    <div class="min-h-screen bg-[#0f171a] flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-[#162124] border border-gray-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800 bg-[#1c2a2e] text-center">
                <h2 class="text-2xl font-black text-[#4ade80] tracking-tighter">💚회원가입💚</h2>
                <p class="text-[10px] text-gray-500 mt-1 uppercase">게시판에서 사용할 계정을 생성하세요.</p>
            </div>

            <form @submit.prevent="submit" class="p-8 space-y-5">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600">ID (로그인 아이디)</label>
                    <input v-model="form.user_login_id" type="text" placeholder="아이디" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-2 text-sm text-gray-300 focus:border-[#4ade80] outline-none" required />
                    
                    <div v-if="form.errors.user_login_id" class="text-red-400 text-xs font-semibold mt-1 pl-1">
                        {{ form.errors.user_login_id }}
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600">NICKNAME (닉네임)</label>
                    <input v-model="form.nickname" type="text" placeholder="닉네임" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-2 text-sm text-gray-300 focus:border-[#4ade80] outline-none" required />
                    
                    <div v-if="form.errors.nickname" class="text-red-400 text-xs font-semibold mt-1 pl-1">
                        {{ form.errors.nickname }}
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600">PASSWORD (암호)</label>
                    <input v-model="form.password" type="password" placeholder="비밀번호" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-2 text-sm text-gray-300 focus:border-[#4ade80] outline-none" required />
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600">CONFIRM (암호 확인)</label>
                    <input v-model="form.password_confirmation" type="password" placeholder="비밀번호 확인" class="w-full bg-[#0f171a] border border-gray-800 rounded px-4 py-2 text-sm text-gray-300 focus:border-[#4ade80] outline-none" required />
                    
                    <div v-if="form.errors.password" class="text-red-400 text-xs font-semibold mt-1 pl-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full bg-[#4ade80] text-[#0f171a] py-3 rounded font-black hover:bg-[#22c55e] transition text-sm uppercase mt-4 shadow-[0_0_15px_rgba(74,222,128,0.15)]">
                    {{ form.processing ? '가입 처리 중...' : '회원가입 완료' }}
                </button>

                <div class="text-center mt-4">
                    <Link href="/login" class="text-xs text-gray-500 hover:text-[#4ade80] transition font-bold">[ 이미 계정이 있으신가요? 로그인 ]</Link>
                </div>
            </form>
        </div>
    </div>
</template>