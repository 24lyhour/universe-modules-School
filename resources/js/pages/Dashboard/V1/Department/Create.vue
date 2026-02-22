<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import DepartmentForm from '../../../../Components/Dashboard/V1/DepartmentForm.vue';
import { departmentSchema } from '@school/validation/departmentSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { DepartmentFormData, DepartmentCreateProps } from '@school/types';

const props = defineProps<DepartmentCreateProps>();

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

const form = useForm<DepartmentFormData>({
    school_id: '',
    name: '',
    code: '',
    description: '',
    head_of_department: '',
    email: '',
    phone: '',
    office_location: '',
    established_year: null,
    total_staff: null,
    total_students: null,
    status: true,
});

// Auto-generate code from name
const generateCode = (name: string): string => {
    if (!name) return '';
    // Take first 4 letters uppercase + random 3 digits
    const prefix = name
        .replace(/[^a-zA-Z]/g, '')
        .substring(0, 4)
        .toUpperCase();
    const suffix = Math.floor(100 + Math.random() * 900).toString();
    return `${prefix}-${suffix}`;
};

// Watch name and auto-generate code if code is empty
watch(() => form.name, (newName) => {
    if (newName && !form.code) {
        form.code = generateCode(newName);
    }
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    departmentSchema,
    ['name', 'school_id']
);

const getFormData = () => ({
    school_id: form.school_id,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    head_of_department: form.head_of_department || null,
    email: form.email || null,
    phone: form.phone || null,
    office_location: form.office_location || null,
    established_year: form.established_year,
    total_staff: form.total_staff,
    total_students: form.total_students,
    status: form.status,
});

watch([() => form.name, () => form.school_id], () => {
    if (form.name || form.school_id) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/departments', {
            onSuccess: () => {
                toast.success('Department created successfully.');
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
        title="Create Department"
        description="Add a new department to a school"
        mode="create"
        size="lg"
        submit-text="Create Department"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <DepartmentForm
            v-model="form"
            :schools="props.schools"
        />
    </ModalForm>
</template>
