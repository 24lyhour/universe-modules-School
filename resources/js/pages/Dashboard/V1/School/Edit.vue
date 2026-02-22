<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import SchoolForm from '../../../../Components/Dashboard/V1/SchoolForm.vue';
import { schoolSchema } from '@school/validation/schoolSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { SchoolFormData, SchoolEditProps, SchoolType } from '@school/types';

const props = defineProps<SchoolEditProps>();

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
    name: props.school.name,
    code: props.school.code || '',
    type: props.school.type,
    description: props.school.description || '',
    address: props.school.address || '',
    city: props.school.city || '',
    country: props.school.country || '',
    phone: props.school.phone || '',
    email: props.school.email || '',
    website: props.school.website || '',
    logo: props.school.logo || '',
    established_year: props.school.established_year,
    accreditation: props.school.accreditation || '',
    status: props.school.status,
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

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/schools/${props.school.id}`, {
            onSuccess: () => {
                toast.success('School updated successfully.');
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
        title="Edit School"
        :description="`Editing: ${school.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <SchoolForm
            v-model="form"
            :types="props.types"
            mode="edit"
        />
    </ModalForm>
</template>
