<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Select from '@/components/ui/select/Select.vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useToast } from "vue-toastification";

const toast = useToast();

interface Flash {
    success?: string | null
    error?: string | null
}

const page = usePage()

watch(() => page.props.flash as Flash, (flash) => {
    if (flash?.success) {
        toast.success(flash?.success)
    } else if (flash?.error) {
        toast.error(flash?.error)
    }
})

const form = useForm({
    deal_name: '',
    deal_stage: '',
    account_name: '',
    account_website: '',
    account_phone: '',
});
const dealStages = ref<string[]>([]);
const loadingStages = ref(false);
const stagesError = ref('');

const fetchDealStages = async () => {
    loadingStages.value = true;
    try {
        const res = await axios.get('/api/zoho/deal-stages');
        dealStages.value = res.data;
    } catch (e: any) {
        stagesError.value = e?.response?.data?.message || 'Failed to load deal stages';
        toast.error(stagesError.value)
    } finally {
        loadingStages.value = false;
    }
};

const submit = () => {
    form.post(route('zoho.create-deal-account'), {
        onSuccess: () => {
            form.reset( 'deal_name', 'deal_stage', 'account_name', 'account_website', 'account_phone')
        },
    });
};

onMounted(fetchDealStages);
</script>

<template>

    <AuthLayout title="Web Form for Zoho CRM Deals and Accounts">
    <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="deal_name">Deal Name</Label>
                <Input id="deal_name" type="text" required autofocus :tabindex="1" v-model="form.deal_name" placeholder="Deal Name" @input="form.clearErrors('deal_name')" />
                <InputError :message="form.errors.deal_name" />
            </div>

            <div class="grid gap-2">
                <Label for="deal_stage">Deal Stage</Label>
                <Select v-model="form.deal_stage" id="deal_stage" required :tabindex="2" label="Select stage" :options='dealStages' @update:model-value="form.clearErrors('deal_stage')" />
                <InputError :message="form.errors.deal_stage" />
            </div>

            <div class="grid gap-2">
                <Label for="account_name">Account Name</Label>
                <Input
                    id="account_name"
                    type="text"
                    :tabindex="3"
                    required
                    v-model="form.account_name"
                    placeholder="Account Name"
                    @input="form.clearErrors('account_name')"
                />
                <InputError :message="form.errors.account_name" />
            </div>

            <div class="grid gap-2">
                <Label for="account_website">Account Website</Label>
                <Input
                    id="account_website"
                    type="url"
                    :tabindex="4"
                    required
                    v-model="form.account_website"
                    placeholder="Account Website"
                    @input="form.clearErrors('account_website')"
                />
                <InputError :message="form.errors.account_website" />
            </div>

            <div class="grid gap-2">
                <Label for="account_phone">Account Phone</Label>
                <Input
                    id="account_phone"
                    type="tel"
                    :tabindex="5"
                    required
                    v-model="form.account_phone"
                    placeholder="Account Phone"
                    @input="form.clearErrors('account_phone')"
                />
                <InputError :message="form.errors.account_phone" />
            </div>

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Create Deal and Account
            </Button>
        </div>
    </form>
    </AuthLayout>
</template>

<style scoped>

</style>
