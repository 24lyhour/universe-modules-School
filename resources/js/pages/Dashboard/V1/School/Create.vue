<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import SchoolForm from '../../../../Components/Dashboard/V1/SchoolForm.vue';
import { schoolSchema } from '@school/validation/schoolSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { SchoolFormData, SchoolCreateProps, SchoolType } from '@school/types';

const props = defineProps<SchoolCreateProps>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const form = useForm<SchoolFormData>({
    name: '',
    code: '',
    type: 'school',
    description: '',
    address: '',
    city: '',
    country: '',
    phone: '',
    email: '',
    website: '',
    logo: '',
    established_year: null,
    accreditation: '',
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    schoolSchema,
    ['name', 'type']
);

const getFormData = () => ({
    name: form.name,
    code: form.code || null,
    type: form.type,
    description: form.description || null,
    address: form.address || null,
    city: form.city || null,
    country: form.country || null,
    phone: form.phone || null,
    email: form.email || null,
    website: form.website || null,
    logo: form.logo || null,
    established_year: form.established_year,
    accreditation: form.accreditation || null,
    status: form.status,
});

watch(() => form.name, () => {
    if (form.name) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/schools', {
            onSuccess: () => {
                toast.success('School created successfully.');
                setTimeout(() => {
                    close();
                    redirect();
                }, 100);
            },
        });
    });
};

const handleCancel = () => {
    close();
    redirect();
};

const isActive = computed({
    get: () => form.status,
    set: (value: boolean) => {
        form.status = value;
    },
});

</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create School"
        description="Add a new school or institution"
        mode="create"
        size="lg"
        submit-text="Create School"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <SchoolForm
            v-model="form"
            :types="props.types"
        />
    </ModalForm>
</template>
